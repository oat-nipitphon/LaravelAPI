<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostInage extends Model
{
    protected $table = 'post_images';
    protected $fillable = [
        'id',
        'post_id',
        'image_path',
        'image_name',
        'image_data',
        'image_url',
        'created_at',
        'updated_at'
    ];
}
