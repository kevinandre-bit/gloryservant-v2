<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Track::insert([
            // Track A — 185s — Performer “Luna” — Category “News” — Theme “Morning” — Year 2022 — Path shows/morning/
            [
                'filename'         => 'track_a.wav',
                'relative_path'    => 'shows/morning/',
                'duration_seconds' => 185,
                'performer'        => 'Luna',
                'category'         => 'News',
                'theme'            => 'Morning',
                'year'             => 2022,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            // Track B — 92s — Performer “Kai” — Category “Music” — Theme “Chill” — Year 2020 — Path music/chill/
            [
                'filename'         => 'track_b.mp3',
                'relative_path'    => 'music/chill/',
                'duration_seconds' => 92,
                'performer'        => 'Kai',
                'category'         => 'Music',
                'theme'            => 'Chill',
                'year'             => 2020,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            // Track C — 360s — Performer “Mara” — Category “Interview” — Theme “Tech” — Year 2024 — Path segments/tech/
            [
                'filename'         => 'track_c.wav',
                'relative_path'    => 'segments/tech/',
                'duration_seconds' => 360,
                'performer'        => 'Mara',
                'category'         => 'Interview',
                'theme'            => 'Tech',
                'year'             => 2024,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}