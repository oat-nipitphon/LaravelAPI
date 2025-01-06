<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StatusUser extends Model
{
    protected $fillable = [
        'id',
        'status_name'
    ];

    public function user () : HasOne {
        return $this->HasOne(User::class);
    }

}
