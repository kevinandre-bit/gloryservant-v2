<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadioSite extends Model
{
    protected $table = 'radio_sites';

    protected $fillable = [
        'department_id','arrondissement_id','commune_id',
        'owner',
        'rep_name','rep_phone','rep_email',
        'contract_start','contract_end','contract_link',
        'notes','nickname',
    ];
}