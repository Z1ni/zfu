<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageInfo extends Model {

    public $timestamps = false;
    protected $fillable = ['width', 'height'];
    protected $primaryKey = 'file_id';

    public function file() {
        return $this->belongsTo(File::class);
    }
}
