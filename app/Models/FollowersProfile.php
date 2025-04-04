<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowersProfile extends Model
{

    protected $table = "followers_profiles";
    protected $fillable = [
        'id',
        'profile_user_id',
        'followers_user_id'
    ];

    public function user () : BelongsTo {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

}
