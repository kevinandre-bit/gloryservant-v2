<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyToken extends Model
{
    // If your table is named "daily_tokens" this is inferred; otherwise set $table here.

    // Disable created_at / updated_at
    public $timestamps = false;

    // Allow mass assignment on these fields
    protected $fillable = [
        'date',
        'token',
    ];

    // Cast `date` to a Carbon date instance
    protected $casts = [
        'date' => 'date',
    ];

    protected $table = 'daily_tokens';
}