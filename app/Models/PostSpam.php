<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostSpam extends Model
{
    protected $table = 'post_spams';
    protected $fillable = [
        'id',
        'post_id',
        'user_id',
        'message',
        'date_time_spam',
        'created_at',
        'updated_at',
    ];
}
