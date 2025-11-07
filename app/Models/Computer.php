<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Computer extends Model
{
    // Tell Laravel to use your tbl_equipments table:
    protected $table = 'tbl_computers';

    // If your primary key is not 'id', specify it here:
    // protected $primaryKey = 'your_pk_name';

    // Now list all the columns you want to allow mass‐assignment on:
    protected $fillable = [
        'hostname',
        'asset_tag',
        'serial_number',
        'manufacturer',
        'model',
        'bios_version',
        'cpu_model',
        'cpu_cores',
        'ram_gb',
        'storage_type',
        'storage_capacity_gb',
        'operating_system',
        'os_version',
        'installed_applications',
        'assigned_user',
        'ministry',
        'purchase_date',
        'warranty_expiration',
    ];

    // If you’ve turned off timestamps on your table, you can disable them:
    // public $timestamps = false;
}