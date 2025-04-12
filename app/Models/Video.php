<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Video extends Model
{
    use HasFactory,HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'videos';
    protected $fillable = [
        'id',
        'post_id',
        'file_name',
        'mime_type',
        'video_data',
        'created_at',
        'updated_at',

    ];

    public function post () : BelongsTo {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

}
