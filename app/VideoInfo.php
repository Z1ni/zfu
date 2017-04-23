<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoInfo extends Model {

    public $timestamps = false;
    protected $fillable = ['width', 'height', 'fps', 'length', 'codec'];
    protected $primaryKey = 'file_id';

    public function file() {
        return $this->belongsTo(File::class);
    }
}
