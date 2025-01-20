<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{

    use HasApiTokens, HasFactory;

    protected $table = 'user_profiles';
    protected $fillable = [
        'id',
        'user_id',
        'title_name',
        'full_name',
        'nick_name',
        'tel_phone',
        'birth_day',
        'created_at',
        'updated_at'
    ];

    public function user () : HasOne {
        return $this->HasOne(User::class);
    }

    public function userProfileContact () : HasMany {
        return $this->HasMany(UserProfileContact::class, 'profile_id', 'id');
    }

    public function userProfileImage () : HasMany {
        return $this->HasMany(UserProfileImage::class, 'profile_id', 'id');
    }


}
