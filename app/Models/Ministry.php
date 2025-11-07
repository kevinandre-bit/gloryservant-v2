<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    protected $table = 'tbl_form_ministry';
    protected $fillable = ['ministry','department', 'status'];
    public $timestamps = false;

    protected $casts = [
        'status' => 'integer',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 1 ? 'Active' : 'Inactive';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 1;
    }
}