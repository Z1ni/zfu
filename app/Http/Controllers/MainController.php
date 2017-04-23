<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller {

    public function index() {
        $files = [];
        $filesPerPage = config('upload.files_per_page', 44);
        if (Auth::check()) {
            $files = File::orderBy('created_at', 'desc')->paginate($filesPerPage);  // TODO: Use simplePaginate?
        } else {
            // Show only visible files for guests
            $files = File::where('visible', true)->orderBy('created_at', 'desc')->paginate($filesPerPage);  // TODO: Use simplePaginate?
        }
        return view('main.index', ['files' => $files]);
    }

    public function trash() {
        $filesPerPage = config('upload.files_per_page', 44);
        $files = File::onlyTrashed()->paginate($filesPerPage);

        return view('main.trash', ['files' => $files]);
    }

}
