<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Post extends Model
{

    use HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';
    protected $fillable = [
        'id',
        'post_title',
        'post_content',
        'type_id',
        'refer',
        'user_id',
        'deletetion_status',
        'spam_post_status',
        'created_at',
        'updated_at',

    ];

    public function user () : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function post_types () : BelongsTo {
        return $this->belongsTo(PostType::class, 'type_id', 'id');
    }

    public function post_deletetion () : BelongsTo {
        return $this->belongsTo(PostDeletetion::class, 'post_id', 'id');
    }

    public function postPopularity () : HasMany {
        return $this->hasMany(PostPopularity::class);
    }

    public function postComment () : HasMany {
        return $this->MasMany(PostComment::class);
    }

}
