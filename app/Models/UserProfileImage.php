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
        'user_id',
        'image_name',
        'image_path',
        'image_url',
        'image_data',
        'created_at',
        'updated_at'
    ];

    public function users () : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
