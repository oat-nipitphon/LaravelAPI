<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostType extends Model
{
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

    public function post () : HasOne {
        return $this->HasOne(Post::class);
    }

}
