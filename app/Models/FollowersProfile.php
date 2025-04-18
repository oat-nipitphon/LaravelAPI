<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowersProfile extends Model
{

    protected $table = "followers_profiles";
    protected $fillable = [
        'profile_user_id',
        'followers_user_id',
        'status_followers',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profile_user_id', 'id');
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followers_user_id', 'id');
    }

}
