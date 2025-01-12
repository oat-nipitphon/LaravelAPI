<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StatusUser extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_users';
    protected $fillable = [
        'id',
        'status_name'
    ];

    public function user () : HasOne {
        return $this->HasOne(User::class);
    }

}
