<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TriggerZohoFlow extends Command
{
    protected $signature = 'zoho:trigger-flow';
    protected $description = 'Send data to Zoho Flow webhook';

    public function handle()
    {
        $url = config('services.zoho.flow_url');

        $payload = [
            'station_id' => 0, // or loop through stations if needed
            'status'     => 'online',
            'note'       => 'auto trigger',
        ];

        $resp = Http::asForm()->post($url, $payload);

        if ($resp->successful()) {
            $this->info('Zoho Flow trigger sent successfully.');
        } else {
            $this->error('Zoho Flow failed: '.$resp->body());
        }

        return Command::SUCCESS;
    }
}