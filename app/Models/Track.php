<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    // adjust fillable to your schema
    protected $fillable = [
        'title',
        'filename',
        'relative_path',
        'duration_seconds',
        'performer',
        'category',
        'theme',
        'year',
    ];

    /* ----------------- Accessors for display ----------------- */

    // Use as: $track->display_title
    public function getDisplayTitleAttribute(): string
    {
        // Prefer human "title" (CSV “Name”), then filename, then "—"
        return $this->title ?: ($this->filename ?: '—');
    }

    // Use as: $track->display_path
    public function getDisplayPathAttribute(): string
    {
        $path = $this->relative_path ? rtrim(str_replace('\\','/',$this->relative_path), '/').'/' : '';
        return $path.($this->filename ?? '');
    }

    /* ----------------- (Optional) relationships --------------- */

    // If you reference tracks from playlist_items:
    // public function playlistItems() { return $this->hasMany(\App\Models\PlaylistItem::class); }
}