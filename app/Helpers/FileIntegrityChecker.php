<?php

namespace App\Helpers;

use App\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileIntegrityChecker
 * Checks uploaded file checksums and reports back broken files
 *
 * @package App\Helpers
 */
class FileIntegrityChecker {

    /**
     * Check uploaded file integrity and return broken files
     *
     * @return array Broken \App\File instances
     */
    public static function checkIntegrity() {
        $files = File::withTrashed()->get();

        $broken = [];

        foreach ($files as $file) {
            // Get storage path
            $path = storage_path('app/data/'.$file->location);
            // Check file existence
            if (!Storage::disk('upload')->exists($file->location)) {
                // File doesn't exist
                $file->currentCRC = 'File missing';
                $broken[] = $file;
                continue;
            }
            // Calculate CRC32
            $calcCRC = hash_file('crc32b', $path);
            $savedCRC = $file->crc;
            if ($calcCRC != $savedCRC) {
                $file->currentCRC = $calcCRC;
                $broken[] = $file;
            }
        }

        return $broken;
    }

}