<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeBadge extends Model
{
    protected $table = 'tbl_creative_badges';

    protected $fillable = ['code', 'name', 'description', 'criteria'];

    protected $casts = [
        'criteria' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(Person::class, 'tbl_creative_user_badges', 'badge_id', 'people_id')
            ->withPivot('awarded_at');
    }
}
