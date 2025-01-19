<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFollowersAccount extends Model
{
    protected $table = "user_followers_account";
    protected $fillable = [[
        'id',
        'account_user_id',
        'followers_user_id'
    ]];

    public function accountUser () : BelongsTo {
        return $this->belongsTo(User::class, 'account_user_id', 'id');
    }

    public function followersUser () : BelongsTo {
        return $this->belongsTo(User::class, 'followers_user_id', 'id');
    }

}
