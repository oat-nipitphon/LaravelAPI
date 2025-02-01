<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfileImage extends Model
{

    use HasApiTokens, HasFactory;

    protected $table = 'user_profile_images';
    protected $fillable = [
        'id',
        'profile_id',
        'image_path',
        'image_name',
        'image_data',
        'image_url',
    ];

    public function userProfile () : BelongsTo {
        return $this->belongsTo(UserProfile::class, 'profile_id', 'id');
    }

}
