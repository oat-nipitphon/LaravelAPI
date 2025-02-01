<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class UserLogin extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_logins';

    /**
     * user_status_login_number
     * user_status_login_name
     * 1 = online , 0 = offline
     */
    protected $fillable = [
        'id',
        'user_id',
        'status_login',
        'created_at',
        'updated_at',
        'total_time_login'
    ];

    public function users () : belongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
