<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserStatus extends Model
{
            /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_status';
    protected $fillable = [
        'id',
        'status_code',
        'status_name'
    ];

    public function user () : HasOne {
        return $this->HasOne(User::class);
    }
}
