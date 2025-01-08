<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'id',
        'post_type_id',
        'post_title',
        'post_content',
        'post_ref',
        'spam_post_status',
        'created_at',
        'updated_at',
        'user_id',

    ];

    public function user () : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function post_types () : BelongsTo {
        return $this->belongsTo(PostType::class, 'post_type_id', 'id');
    }

}
