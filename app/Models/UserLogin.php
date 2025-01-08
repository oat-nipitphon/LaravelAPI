<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class UserLogin extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'user_status_login_number',
        'user_status_login_name',
        'user_date_time_login',
        'user_date_time_logout',
        'user_date_time_total_online'
    ];

    public function users () : belongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
