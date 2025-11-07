<?php
namespace App\Support;

use Carbon\CarbonImmutable;

class Time
{
    public static function toSeconds(string $hms): int
    {
        // Accepts "HH:MM:SS" or "MM:SS" or "SS"
        $parts = array_map('intval', explode(':', $hms));
        if (count($parts) === 3) return $parts[0]*3600 + $parts[1]*60 + $parts[2];
        if (count($parts) === 2) return $parts[0]*60 + $parts[1];
        return (int)$parts[0];
    }

    public static function formatHms(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    /**
     * @param CarbonImmutable $start
     * @param array<array{duration:int,id:int}> $items
     * @return array<array{id:int,start:CarbonImmutable,end:CarbonImmutable}>
     */
    public static function schedule(CarbonImmutable $start, array $items): array
    {
        $out = [];
        $cursor = $start;
        foreach ($items as $it) {
            $begin = $cursor;
            $end = $cursor->addSeconds($it['duration'] ?? 0);
            $out[] = ['id'=>$it['id'], 'start'=>$begin, 'end'=>$end];
            $cursor = $end;
        }
        return $out;
    }
}