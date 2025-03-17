<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPointCounter extends Model
{
    protected $table = 'user_point_counters';
    protected $fillable = [
        'id',
        'user_point_id',
        'user_id',
        'reward_id',
        'point_import',
        'point_export',
        'detail_counter',
        'created_at',
        'updated_at'
    ];

    public function userPoint(): BelongsTo
    {
        return $this->belongsTo(UserPoint::class, 'user_point_id', 'id');
    }

}
