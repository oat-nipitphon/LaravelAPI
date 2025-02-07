<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostPopularity extends Model
{
    protected $table = 'post_popularitys';
    protected $fillable = [
        'id',
        'post_id',
        'user_id',
        'pop_status',
        'created_at',
        'updated_at',
    ];

    public function post () : BelongsTo {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

}
