<?php

namespace App\Helpers;


use App\File;
use App\StatisticsEntry;
use Illuminate\Support\Facades\Storage;

class StatisticsUpdater {

    public static function update() {

        // Get the entry or create new
        $entry = StatisticsEntry::firstOrNew([]);

        // Gather all statistics
        $uploaded_files = File::count();

        $visible_files = File::where('visible', true)->count();
        $hidden_files = File::where('visible', false)->count();
        $trashed_files = File::onlyTrashed()->count();

        // TODO: Handle cases where the file is optimized
        $used_disk_space_files = 0;
        $used_disk_space_thumbs = 0;
        $files_storage = Storage::disk('upload')->allFiles();
        $thumbs_storage = Storage::disk('thumbnail')->allFiles();
        foreach ($files_storage as $file) {
            $used_disk_space_files += Storage::disk('upload')->size($file);
        }
        foreach ($thumbs_storage as $thumb) {
            $used_disk_space_thumbs += Storage::disk('thumbnail')->size($thumb);
        }

        // TODO: Calculate disk space saved by optimizing
        $totalFilesizeWithoutOpt = intval(File::withTrashed()->sum('size'));
        $optimized_files_savings = $totalFilesizeWithoutOpt - $used_disk_space_files;
        //dd($used_disk_space_files);

        $entry->uploaded_files = $uploaded_files;
        $entry->visible_files = $visible_files;
        $entry->hidden_files = $hidden_files;
        $entry->trashed_files = $trashed_files;
        $entry->used_disk_space_files = $used_disk_space_files;
        $entry->used_disk_space_thumbs = $used_disk_space_thumbs;
        $entry->optimized_files_savings = $optimized_files_savings;
        $entry->save();
    }

    public static function addTotalUploadedFiles(int $count = 1) {
        $entry = StatisticsEntry::firstOrNew([]);
        $entry->uploaded_files_total += $count;
        $entry->save();
    }

    public static function addTotalDeletedFiles(int $count = 1) {
        $entry = StatisticsEntry::firstOrNew([]);
        $entry->deleted_files_total += $count;
        $entry->save();
    }

    public static function addTotalOptimizedFiles(int $count = 1) {
        $entry = StatisticsEntry::firstOrNew([]);
        $entry->optimized_files_total += $count;
        $entry->save();
    }

}