<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardImage extends Model
{
    protected $table = 'reward_images';
    protected $fillable = [
        'id',
        'reward_id',
        'image_path',
        'image_name',
        'image_data',
        'created_at',
        'updated_at'
    ];

    public function reward () : BelongsTo {
        return $this->belongsTo(Reward::class, 'reward_id', 'id');
    }

}
