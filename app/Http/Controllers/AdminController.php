<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\FileIntegrityChecker;
use App\Helpers\StatisticsUpdater;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller {

    public function updateStats() {
        // TODO: Use a Job?
        StatisticsUpdater::update();
        return back()->with('status', 'Statistics updated');
    }

    public function checkIntegrity() {
        $brokenFiles = FileIntegrityChecker::checkIntegrity();
        // TODO: Render a view
        if (count($brokenFiles) > 0) {
            return view('admin.integrity', ['files' => $brokenFiles]);
        }
        return back()->with('status', 'All files are valid');
    }

    public function deleteFile(string $code) {
        $file = File::withTrashed()->where('code', $code)->first();
        if ($file == null) {
            return back()->with('error', 'No such file!');
        }
        $file->forceDelete();
        return back()->with('status', 'File deleted');
    }

    public function deleteAllCorruptedFiles() {
        Log::info('Deleting all corrupted files');
        $brokenFiles = FileIntegrityChecker::checkIntegrity();
        foreach ($brokenFiles as $file) {
            $file->forceDelete();
        }
        Log::info('Corrupted files deleted');
        \Session::flash('status', 'All corrupted files deleted');
        return redirect()->route('user');
    }

}
