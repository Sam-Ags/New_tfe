<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedMedia extends Model
{
    protected $table = 'uploaded_media';

    protected $fillable = [
        'uuid',
        'original_name',
        'mime_type' ,
        'size',
        'contents',
    ];
}
