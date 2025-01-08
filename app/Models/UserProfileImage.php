<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfileImage extends Model
{
    protected $fillable = [
        'id',
        'profile_id',
        'image_path',
        'image_name',
        'image_data',
        'image_url',
    ];

    public function user_profile () : BelongsTo {
        return $this->belongsTo(UserProfile::class);
    }

}
