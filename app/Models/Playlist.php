<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['name','status','created_by','start_at','youtube_peak',];
    protected $casts = ['start_at' => 'datetime'];


    // Owner/creator of the playlist
    public function user()
    {
        // uses playlists.created_by -> users.id
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\PlaylistItem::class);
    }

public function getTrendLabelAttribute(): string
{
    $p = (int) $this->youtube_peak;

    if ($p < 4000) {
        return 'Urgent action needed';
    } elseif ($p >= 5000 && $p < 6000) {
        return 'Needs attention';
    } elseif ($p >= 6000 && $p < 7000) {
        return 'Needs attention';
    } elseif ($p >= 7000 && $p < 8000) {
        return 'Good growth';
    } elseif ($p >= 8000 && $p < 9000) {
        return 'Solid growth';
    } elseif ($p >= 9000 && $p < 10000) {
        return 'Very strong';
    } elseif ($p >= 10000) {
        return 'Excellent performance';
    }

    return 'Unknown';
}

public function getTrendColorClassAttribute(): string
{
    $p = (int) $this->youtube_peak;

    if ($p < 4000) {
        return 'text-danger'; // red
    } elseif ($p >= 5000 && $p < 7000) {
        return 'text-warning'; // yellow/orange
    } elseif ($p >= 7000 && $p < 8000) {
        return 'text-success'; // green
    } elseif ($p >= 8000 && $p < 9000) {
        return 'text-primary'; // blue
    } elseif ($p >= 9000 && $p < 10000) {
        return 'text-info'; // light blue
    } elseif ($p >= 10000) {
        return 'text-purple fw-bold'; // custom if you define purple
    }

    return 'text-secondary';
}
}