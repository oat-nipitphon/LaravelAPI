<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostImage extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = "post_images";
    protected $fillable = [
        'id',
        'post_id',
        'image_path',
        'image_name',
        'image_url',
        'image_data',
        'created_at',
        'updated_at'
    ];

    public function posts () : BelongsTo {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

}
