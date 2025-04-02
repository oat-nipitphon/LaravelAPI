<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    protected $table = 'post_comments';
    protected $fillable = [
        'id',
        'post_id',
        'user_id',
        'comment',
        'status',
        'created_at',
        'updated_at'
    ];

    public function post () : BelongsTo {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function postCommentPopularity () : HasMany {
        return $this->hasMany(PostCommentPopularity::class);
    }

}
