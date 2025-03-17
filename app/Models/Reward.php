<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    protected $table = 'rewards';
    protected $fillable = [
        'id',
        'name',
        'point',
        'quantity',
        'status',
        'created_at',
        'updated_at'
    ];

    public function rewardImage () : HasMany {
        return $this->hasMany(RewardImage::class);
    }

}
