<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyDigitalGift extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'theme_heading',
        'welcome_subtext',
        'sermon_title',
        'sermon_date',
        'sermon_description',
        'sermon_audio_url',
        'worship_title',
        'worship_leader',
        'worship_theme_note',
        'worship_audio_url',
        'testimony_type',
        'testimony_body',
        'testimony_image_path',
        'verse_reference',
        'verse_text',
        'verse_reflection',
        'meditation_paragraphs',
        'artwork_image_path',
        'artwork_caption',
        'wallpaper_phone_url',
        'wallpaper_desktop_url',
        'closing_blessing',
    ];

    protected $casts = [
        'month' => 'date',
        'sermon_date' => 'date',
        'meditation_paragraphs' => 'array',
    ];

    public function getMonthLabelAttribute(): string
    {
        return $this->month
            ? $this->month->translatedFormat('F Y')
            : '';
    }
}
