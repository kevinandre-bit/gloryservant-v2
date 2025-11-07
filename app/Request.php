<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'requests';

    protected $fillable = [
    'reference',
    'idno',
    'employee',
    'type',
    'message',
    'status',
    'admin_response',
];

}
