<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;

class ApiFileController extends Controller {

    /**
     * Get information for one file
     *
     * @param string $code File code
     * @return array Information or error
     */
    function getFile(string $code) {
        $file = File::where('code', $code)->first();
        if ($file == null) {
            return response()->json(['error' => 'No such file'], 404);
        }
        return $file;
    }

    /**
     * Get file thumbnail
     *
     * @param string $code File code
     * @return mixed HTTP Response contaning the thumbnail image
     */
    function getFileThumbnail(string $code) {
        return FileHelper::getThumbnail($code);
    }

    /**
     * Upload file and return information
     *
     * @param Request $request HTTP Request
     * @return File|\Illuminate\Http\JsonResponse File information
     */
    function upload(Request $request) {

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file given'], 400);
        }
        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file'], 400);
        }

        $fileObj = FileHelper::createFromUpload($file);

        // Create URL
        $fileObj->url = config('app.url').'/'.$fileObj->location;

        return $fileObj;
    }

}
