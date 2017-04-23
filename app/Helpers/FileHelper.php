<?php

namespace App\Helpers;

use App\File;
use App\Jobs\OptimizeFile;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileHelper {

    /**
     * Move uploaded file and create DB entry for it
     *
     * @param \Illuminate\Http\UploadedFile $file Uploaded file
     * @return \App\File Created File instance
     */
    public static function createFromUpload(\Illuminate\Http\UploadedFile $file) {
        // Generate file code
        // Contains chars a-f and numbers 0-9, max names count with length 5 is 1048576
        $code = substr(str_shuffle(uniqid()), 0, config('upload.file_code_length'));

        $filename = $code . '.' . $file->getClientOriginalExtension();

        // Calculate CRC32 to ensure file integrity later on
        $crc = hash_file('crc32b', $file->getRealPath());

        // Check if the database already contains this file
        $possibleFirst = File::where('crc_original', $crc)->first();
        if ($possibleFirst != null) {
            // This file has been uploaded and it's available
            // Return the previous file
            Log::info('Uploaded file already in the database, code: '.$possibleFirst->code.', CRC: '.$possibleFirst->crc_original);
            $possibleFirst->reupload = true;
            return $possibleFirst;
        }

        // Store uploaded file
        $storeName = $file->storeAs('', $filename, 'upload');
        $storePath = storage_path('app/data/' . $storeName);

        $mimetype = $file->getClientMimeType();
        $type = explode('/', $mimetype)[0];

        $fileObj = File::create([
            'code' => $code,
            'location' => $storeName,
            'mimetype' => $mimetype,
            'type' => $type,
            'visible' => config('upload.file_default_visible'), // Visibility status comes from the config file
            'size' => $file->getSize(),
            'crc' => $crc,
            'crc_original' => $crc,
            'user_id' => Auth::user()->id
        ]);

        // TODO: Check if database operation was successful and delete file if it wasn't

        // Get metadata
        if (config('upload.gather_metadata', false)) {
            $type = $fileObj->type;
            if ($type == 'image') {
                // Get image width & height
                $img = Image::make($storePath);
                $fileObj->width = $img->width();
                $fileObj->height = $img->height();
                $fileObj->save();

            } else if ($type == 'video') {
                // Get video width, height and codec
                $ffprobe = FFProbe::create([
                    'ffmpeg.binaries'  => config('upload.extra.ffmpeg_path'),
                    'ffprobe.binaries' => config('upload.extra.ffprobe_path')
                ]);

                $video = $ffprobe->streams($storePath)->videos()->first();
                $width = $video->get('width');
                $height = $video->get('height');
                $codec = $video->get('codec_name');
                $rawFps = explode('/', $video->get('avg_frame_rate'));
                $fps = intval($rawFps[0]) / intval($rawFps[1]);

                $fileObj->width = $width;
                $fileObj->height = $height;
                $fileObj->vid_codec = $codec;
                $fileObj->vid_fps = round($fps, 2);
                $fileObj->save();

            }
        }

        StatisticsUpdater::addTotalUploadedFiles();

        if ($fileObj->mimetype == 'image/png' && config('upload.optimize.png.enabled', false)) {
            dispatch(new OptimizeFile($fileObj));
        } else if ($fileObj->mimetype == 'image/jpeg' && config('upload.optimize.jpeg.enabled', false)) {
            dispatch(new OptimizeFile($fileObj));
        }

        return $fileObj;
    }

    /**
     * Get a thumbnail for given file
     * Generates thumbnail if it doesn't exist
     *
     * @param string $code File code
     * @return mixed HTTP Response containing the thumbnail image
     */
    public static function getThumbnail(string $code) {
        // Get file instance
        $fileObj = File::withTrashed()->where('code', $code)->first();
        // Derive filesystem paths
        if ($fileObj == null) { // || ($fileObj != null && !$fileObj->visible && Auth::guest())) {
            // No such file // or file is hidden
            return ErrorResponseHelper::unknownError();
        }
        $thumbName = $fileObj->code . '.png';
        $storePath = storage_path('app/data/'.$fileObj->location);
        $thumbStorePath = storage_path('app/thumb/'.$thumbName);

        if (\Illuminate\Support\Facades\File::exists($thumbStorePath)) {
            // Thumbnail exists, return it
            // TODO: Let the browser cache these
            return response(
                Storage::disk('thumbnail')
                    ->get($thumbName)
            )->header('Content-Type', 'image/png');
        }
        // Thumbnail doesn't exist
        // Does the source file exist?
        if (!Storage::disk('upload')->exists($fileObj->location)) {
            // Source file doesn't exist
            Log::error('Uploaded file missing, code: '.$fileObj->code.', FS path: '.$storePath);
            return ErrorResponseHelper::unknownError();
        }
        // Generate thumbnail
        $type = $fileObj->type;
        if ($type == 'image') {
            // Generate image thumbnail
            $img = Image::make($storePath);
            $img->fit(config('upload.thumb_width', 150), config('upload.thumb_height', 150));
            $img->save($thumbStorePath);

        } else if ($type == 'video') {
            // Generate video thumbnail
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => config('upload.extra.ffmpeg_path'),
                'ffprobe.binaries' => config('upload.extra.ffprobe_path')
            ]);
            $vid = $ffmpeg->open($storePath);
            $frame = $vid->frame(TimeCode::fromSeconds(1));
            $frame->save($thumbStorePath);
            // Resize image
            $img = Image::make($thumbStorePath);
            $img->fit(config('upload.thumb_width', 150), config('upload.thumb_height', 150));
            $img->save($thumbStorePath);

        } else if ($type == 'audio') {
            // Generate audio thumbnail (waveform!)
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => config('upload.extra.ffmpeg_path'),
                'ffprobe.binaries' => config('upload.extra.ffprobe_path')
            ]);
            $audio = $ffmpeg->open($storePath);
            $waveform = $audio->waveform(150, 150);
            $waveform->save($thumbStorePath);
            // Resize image
            $img = Image::make($thumbStorePath);
            $img->fit(config('upload.thumb_width', 150), config('upload.thumb_height', 150));
            $img->save($thumbStorePath);

        } else {
            // Use default thumbnail
            return ErrorResponseHelper::noThumbnail();
        }

        // Return created thumbnail
        return response(
            Storage::disk('thumbnail')
                ->get($thumbName)
        )->header('Content-Type', 'image/png')
         ->header('Content-Type', Storage::disk('thumbnail')->size($thumbName));
    }

}