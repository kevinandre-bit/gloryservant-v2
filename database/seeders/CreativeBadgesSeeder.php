<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreativeBadgesSeeder extends Seeder
{
    public function run()
    {
        $badges = [
            [
                'code' => 'sprinter',
                'name' => 'Sprinter',
                'description' => 'Complete 5 tasks in 7 days',
                'criteria' => json_encode(['tasks' => 5, 'days' => 7]),
            ],
            [
                'code' => 'closer',
                'name' => 'Closer',
                'description' => 'Complete 20 tasks in a month',
                'criteria' => json_encode(['tasks' => 20, 'period' => 'month']),
            ],
            [
                'code' => 'loyal',
                'name' => 'Loyal',
                'description' => 'Log 40 hours in a month',
                'criteria' => json_encode(['minutes' => 2400, 'period' => 'month']),
            ],
            [
                'code' => 'creative_pro',
                'name' => 'Creative Pro',
                'description' => 'Complete 100 total tasks',
                'criteria' => json_encode(['tasks' => 100, 'lifetime' => true]),
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('tbl_creative_badges')->updateOrInsert(
                ['code' => $badge['code']],
                array_merge($badge, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
