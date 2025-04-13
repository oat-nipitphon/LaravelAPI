<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileContact extends Model
{

    use HasFactory;

    protected $table = 'user_profile_contacts';
    protected $fillable = [
        'id',
        'user_id',
        'profile_id',
        'name',
        'url',
        'icon_data',
        'created_at',
        'updated_at'
    ];

    public function userProfile () : BelongsTo {
        return $this->belongsTo(UserProfile::class, 'profile_id', 'id');
    }

}
