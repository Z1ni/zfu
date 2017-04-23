<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ErrorResponseHelper {

    public static function fileNotFound() {
        return ErrorResponseHelper::respondWithImage(config('upload.misc_resource.file_not_found'));
    }

    public static function noThumbnail() {
        return ErrorResponseHelper::respondWithImage(config('upload.misc_resource.no_thumbnail'));
    }

    public static function unknownError() {
        return ErrorResponseHelper::respondWithImage(config('upload.misc_resource.unknown_error'));
    }

    private static function respondWithImage(string $name) {
        return response(
            Storage::disk('system')
                ->get($name)
        )->header('Content-Type', config('upload.misc_resource.resource_filetype'))
         ->header('Content-Length', Storage::disk('system')->size($name));
    }

}