<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileEntry extends Model
{
    protected $fillable = ['filename', 'mime', 'path', 'size'];
}
