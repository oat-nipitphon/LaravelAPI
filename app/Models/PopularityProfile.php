<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopularityProfile extends Model
{
    protected $table = "popularity_profiles";
    protected $fillable = [
        'id',
        'user_id',
        'user_id_pop',
        'status_pop',
        'created_at',
        'updated_at'
        // อื่น ๆ เช่น score, likes ฯลฯ
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
