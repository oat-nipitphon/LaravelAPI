<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostCommentPopularity extends Model
{
    protected $table = 'post_comment_popularitys';
    protected $fillable = [
        'id',
        'comment_id',
        'like_don_like',
        'created_at',
        'updated_at'
    ];

    public function postComment () : BelongsTo {
        return $this->belongsTo(PostCommentPopularity::class, 'comment_id', 'id');
    }

}
