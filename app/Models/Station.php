<?php

// app/Models/Station.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'radio_stations';

    protected $fillable = [
        'name',
        'department_id',
        'arrondissement_id',
        'commune_id',
        'frequency',
        'frequency_status',
        'on_air',
        'notes',
    ];

    protected $casts = [
        'on_air' => 'boolean',
    ];
}