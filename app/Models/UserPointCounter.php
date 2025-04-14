<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPointCounter extends Model
{
    protected $table = 'user_point_counters';
    protected $fillable = [
        'id',
        'user_id',
        'reward_id',
        'point_status',
        'detail_counter',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reward () : HasMany {
        return $this->hasMany(Reward::class, 'id', 'reward_id');
    }

    public function userPoint(): BelongsTo
    {
        return $this->belongsTo(UserPoint::class, 'user_id', 'id');
    }

}
