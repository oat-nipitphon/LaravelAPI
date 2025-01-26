<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImageFileUpload extends Model
{
    use HasFactory;
    protected $table = "image_file_uploads";
    protected $fillable = [
        'id',
        'profile_id',
        'image_path',
        'image_name',
        'image_data',
        'image_url',
        'created_at',
        'updated_at'
    ];
}
