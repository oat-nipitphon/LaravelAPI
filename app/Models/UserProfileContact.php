<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfileContact extends Model
{
    protected $table = "user_profile_contacts";
    protected $fillable = [
        'id',
        'user_id',
        'contact_name',
        'contact_link_address',
        'contact_link_path',
        'contact_icon_name',
        'contact_icon_url',
        'contact_icon_data',
        'created_at',
        'updated_at',
    ];

    public function userProfile () : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
