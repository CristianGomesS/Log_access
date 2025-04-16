<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'id_user',
        'status',
        'path',
        'method',
        'request_log',
        'response_log',
        'before_log'
    ];
}
