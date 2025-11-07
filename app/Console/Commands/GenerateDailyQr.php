<?php
// app/Console/Commands/GenerateDailyQr.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyToken;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Storage;

class GenerateDailyQr extends Command
{
    protected $signature = 'qr:generate-daily';
    protected $description = 'Generate a daily QR code for clock-in';

    public function handle()
    {
        $today = now()->toDateString();
        // Create or update today’s token
        $token = DailyToken::updateOrCreate(
            ['date' => $today],
            ['token' => Str::random(32)]
        )->token;

        // Generate QR code SVG (or PNG)
        $svg = QrCode::size(300)
                     ->format('svg')
                     ->generate(route('attendance.scan', ['token' => $token]));

        Storage::put("public/qrcodes/{$today}.svg", $svg);

        $this->info("QR code for {$today} generated.");
    }
}