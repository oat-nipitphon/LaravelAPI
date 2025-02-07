<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Post extends Model
{

    use HasFactory,HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';
    protected $fillable = [
        'id',
        'user_id',
        'type_id',
        'post_title',
        'post_content',
        'refer',
        'block_status',
        'deletetion_status',
        'created_at',
        'updated_at',

    ];

    public function user () : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function postImage () : HasMany {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    public function postType () : BelongsTo {
        return $this->belongsTo(PostType::class, 'type_id', 'id');
    }

    public function postDeletetion () : BelongsTo {
        return $this->belongsTo(PostDeletetion::class, 'post_id', 'id');
    }

    public function postPopularity () : HasMany {
        return $this->HasMany(PostPopularity::class);
    }

    public function postComment () : BelongsTo {
        return $this->belongsTo(PostComment::class, 'post_id', 'id');
    }

}
