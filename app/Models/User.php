<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'username',
        'password',
        'status_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // Polymorphic Relationships :: https://laravel.com/docs/11.x/eloquent-relationships

    // One to One $this->hasOne use model 1 - 1
    // One to Many $this->hasMany use model 1 - N
    // One of Many $this->belongsTo use model M - 1

    // Many to Many $this->hasManyThrough use model N-M or M-M
    //
    // use Illuminate\Database\Eloquent\Relations\HasManyThrough;
    // public function deployments(): HasManyThrough
    // {
    //     return $this->hasManyThrough(Deployment::class, Environment::class);
    // }

    public function status_user () : BelongsTo {
        return $this->belongsTo(StatusUser::class, 'status_id', 'id');
    }

    public function user_profile () : HasOne {
        return $this->HasOne(UserProfile::class);
    }

    public function user_logins () : HasOne {
        return $this->HasOne(UserLogin::class);
    }

    public function posts () : HasMany {
        return $this->HasMany(Post::class);
    }

    public function userFollowersProfile () : HasMany {
        return $this->HasMany(UserFollowersProfile::class);
    }

    public function userFollowersAccount () : HasMany {
        return $this->HasMany(UserFollowersAccount::class);
    }

}
