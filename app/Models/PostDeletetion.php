<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostDeletetion extends Model
{
    protected $table = 'post_deletetions';
    protected $fillable = [
        'id',
        'post_id',
        'date_time_delete',
        'total_date_time_delete',
        'status_delete',
        'created_at',
        'updated_at'
    ];


    public function post () : HasOne {
        return $this->hasOne(Post::class);
    }

}
