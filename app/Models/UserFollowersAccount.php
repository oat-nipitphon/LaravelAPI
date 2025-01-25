<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFollowersAccount extends Model
{
    protected $table = "user_followers_accounts";
    protected $fillable = [
        'id',
        'user_id',
        'followers_user_id'
    ];

    public function user () : BelongsTo {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

}
