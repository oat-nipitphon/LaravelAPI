<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $table = 'user_points';
    protected $fillable = [
        'id',
        'user_id',
        'point',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function userPointCounters(): HasMany
    {
        return $this->hasMany(UserPointCounter::class, 'user_id', 'id');
    }

}
