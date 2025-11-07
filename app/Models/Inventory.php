<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Formcampus;

class Inventory extends Model
{
    // Tell Laravel to use your tbl_equipments table:
    protected $table = 'tbl_equipment';
// Cast acquired_at to a Carbon date
    protected $casts = [
        'acquired_at' => 'date',
    ];
    // If your primary key is not 'id', specify it here:
    // protected $primaryKey = 'your_pk_name';
    //$campuses = Formcampus::orderBy('campus')->pluck('campus','id');

    // Now list all the columns you want to allow mass‐assignment on:
    protected $fillable = [
        'name',
        'serial_number',
        'description',
        'photo',
        'category',
        'location',
        'location',
        'status',
        'acquired_at',
        'quantity',
        'cost',
    ];


    // If you’ve turned off timestamps on your table, you can disable them:
    // public $timestamps = false;
}