<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativePointsLedger extends Model
{
    protected $table = 'tbl_creative_points_ledger';
    public $timestamps = false;

    protected $fillable = [
        'people_id', 'points', 'reason', 'ref_table', 'ref_id', 'idempotency_key', 'occurred_at'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'people_id');
    }
}
