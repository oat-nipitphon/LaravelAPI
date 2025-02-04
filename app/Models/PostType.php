<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostType extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_types';
    protected $fillabel = [
        'id',
        'post_type_name',
        'type_image_path',
        'type_image_name',
        'type_image_data',
        'type_image_url',
        'number_of_followers_type',
        'created_at',
        'updated_at'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'type_id', 'id'); // Fixing the relationship
    }

}
