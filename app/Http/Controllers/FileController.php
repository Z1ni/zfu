<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\ErrorResponseHelper;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileController
 * Handles pretty much everything that has to do with File instances
 *
 * @package App\Http\Controllers
 */
class FileController extends Controller {

    /**
     * Upload view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function upload() {
        return view('file.upload');
    }

    /**
     * Unhides the file
     *
     * @param string $code File code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(string $code) {
        $file = File::where('code', $code)->first();
        if ($file == null) {
            \Session::flash('error', 'No such file!');
            return redirect()->route('main');
        }
        $file->visible = true;
        $file->save();
        \Session::flash('status', 'File is now visible');
        return redirect()->route('main');
    }

    /**
     * Hides the file
     *
     * @param string $code File code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hide(string $code) {
        $file = File::where('code', $code)->first();
        if ($file == null) {
            \Session::flash('error', 'No such file!');
            return redirect()->route('main');
        }
        $file->visible = false;
        $file->save();
        \Session::flash('status', 'File is now hidden');
        return redirect()->route('main');
    }

    /**
     * Deletes the file permanently
     *
     * @param string $code File code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(string $code) {
        $file = File::withTrashed()->where('code', $code)->first();
        if ($file == null) {
            \Session::flash('error', 'No such file!');
            return redirect()->route('main');
        }
        $file->forceDelete();
        \Session::flash('status', 'File deleted');
        return redirect()->route('main');
    }

    /**
     * Permanently deletes all trashed files
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAllTrash() {
        $trash = File::onlyTrashed()->get();
        // Don't use mass delete because then the "deleting" event isn't called
        foreach ($trash as $trashFile) {
            $trashFile->forceDelete();
        }
        \Session::flash('status', 'Files deleted');
        return redirect()->route('main.trash');
    }

    /**
     * Moves the file to a "recycle bin", doesn't really delete anything
     *
     * @param string $code File code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function trash(string $code) {
        $file = File::where('code', $code)->first();
        if ($file == null) {
            \Session::flash('error', 'No such file!');
            return redirect()->route('main');
        }
        $file->delete();
        \Session::flash('status', 'File moved to the recycle bin');
        return redirect()->route('main');
    }

    /**
     * Restores the file from the recycle bin
     *
     * @param string $code File code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $code) {
        $file = File::onlyTrashed()->where('code', $code)->first();
        if ($file == null) {
            \Session::flash('error', 'No such file!');
            return redirect()->route('main.trash');
        }
        $file->restore();
        \Session::flash('status', 'File "'.$code.'" restored"');
        return redirect()->route('main.trash');
    }

    /**
     * Restores all trashed files
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreAllTrash() {
        File::onlyTrashed()->restore();
        \Session::flash('status', 'Files restored');
        return redirect()->route('main.trash');
    }


    /**
     * Deletes ALL files, trashed or not
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAllFiles() {
        $files = File::withTrashed()->get();
        foreach ($files as $file) {
            $file->forceDelete();
        }
        return back()->with('status', 'All files deleted!');
    }

    /**
     * Get uploaded file
     *
     * @param string $code File code
     * @param string $ext File extension
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function get(string $code, string $ext) {
        return $this->getByCode($code);
    }

    public function getCompat(string $c, string $code, string $ext) {
        return $this->getByCode($code);
    }

    /**
     * Get uploaded file by code
     *
     * @param string $code File code
     * @return mixed HTTP Response
     */
    public function getByCode(string $code) {
        // Get File instance from database
        Log::debug('Getting file ' . $code);
        $file = File::where('code', $code)->first();
        if ($file === null) { // || ($file != null && !$file->visible && Auth::guest())) {
            // File doesn't exist // or is hidden
            // Return placeholder image
            return ErrorResponseHelper::fileNotFound();
        }
        // Get file from storage
        Log::debug('Getting from storage');
        if (!Storage::disk('upload')->exists($file->location)) {
            // File doesn't exist
            Log::error('Uploaded file missing, code: '.$file->code.', FS path: '.storage_path('app/data/'.$file->location));
            return ErrorResponseHelper::fileNotFound();
        }
        $file_data = Storage::disk('upload')->get($file->location);
        // Update view count
        Log::debug('Update views');
        $file->views++;
        $file->save();
        // Return raw file
        // TODO: Let the browser cache the files
        Log::debug('Return data');
        return response($file_data)
            ->header('Content-Type', $file->mimetype)
            ->header('Content-Length', Storage::disk('upload')->size($file->location));
    }

    /**
     * Return thumbnail for the file
     *
     * @param string $code File code
     * @return mixed HTTP Response containing the thumbnail image
     */
    public function getThumbnail(string $code) {
        return FileHelper::getThumbnail($code);
    }

    /**
     * Save uploaded file
     *
     * @param Request $request HTTP Request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View HTTP Response
     */
    public function create(Request $request) {

        if (!$request->hasFile('file')) {
            return back()->with('error', 'No file given!');
        }
        $file = $request->file('file');

        if (!$file->isValid()) {
            return back()->with('error', 'Invalid file!');
        }

        $fileObj = FileHelper::createFromUpload($file);
        // TODO: Check if fileObj is null

        if (empty($fileObj->reupload)) {
            $request->session()->flash('status', 'File uploaded as "' . $fileObj->location . '"!');
        } else {
            $request->session()->flash('status', 'File reuploaded, old file: "' . $fileObj->location . '"!');
        }
        return view('file.upload');
    }

}
