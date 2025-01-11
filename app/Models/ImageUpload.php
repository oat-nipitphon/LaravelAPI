<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageUpload extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'profile_id',
        'image_path',
        'image_name',
        'image_data',
        'image_url',
    ];


}
