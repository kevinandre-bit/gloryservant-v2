<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations'; // explicitly define the table if needed

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius',
    ];

    public $timestamps = false; // disable timestamps if your table doesn't use created_at/updated_at
}
