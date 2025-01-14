<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostPopularity extends Model
{
    protected $table = 'post_popularitys';
    protected $fillable = [
        'id',
        'post_id',
        'user_id',
        'like_don_like',
        'created_at',
        'updated_at',
    ];
}
