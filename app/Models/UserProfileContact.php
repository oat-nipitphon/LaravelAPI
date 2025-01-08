<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfileContact extends Model
{
    protected $fillable = [
        'id',
        'profile_id',
        'contact_name',
        'contact_link_path',
        'contact_icon_name',
        'contact_icon_url',
        'contact_icon_data',
        'created_at',
        'updated_at',
    ];

    public function user_profile () : BelongsTo {
        return $this->belongsTo(UserProfile::class);
    }

}
