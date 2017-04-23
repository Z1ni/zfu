<?php

namespace App;

use App\Helpers\StatisticsUpdater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class File extends Model {

    use SoftDeletes, Notifiable;

    protected $fillable = [
        "code", "location", "mimetype", "type", "visible", "size", "size_optimized", "views", "crc", "crc_original", "user_id",
        "width", "height", "vid_fps", "vid_codec"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function currentRealSize() {
        // TODO: Return filesystem size instead?
        if ($this->size_optimized != null) {
            return $this->size_optimized;
        } else {
            return $this->size;
        }
    }

    protected static function boot() {
        parent::boot();

        // This gets called before deletion
        static::deleting(function(File $file) {
            if ($file->isForceDeleting()) {
                Log::info('File ' . $file->code . ' is deleting');
                // Remove thumbnail
                $thumbPath = explode('.', $file->location, 2)[0] . '.png';
                Storage::disk('thumbnail')->delete($thumbPath);
                // Remove file
                Storage::disk('upload')->delete($file->location);
                // Update statistics
                StatisticsUpdater::addTotalDeletedFiles();
            } else {
                Log::info('File ' . $file->code . ' is moving to trash');
            }
        });
    }

}
