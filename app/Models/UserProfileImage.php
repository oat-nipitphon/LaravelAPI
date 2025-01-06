<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfileImage extends Model
{
    protected $fillable = [
        'id',
        'profile_id',
        'image_path',
        'image_name',
        'image_data',
        'image_url',
    ];
}
