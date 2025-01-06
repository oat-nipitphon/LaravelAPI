<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'title_name',
        'full_name',
        'nick_name',
        'birth_day',
        'created_at',
        'updated_at'
    ];
}
