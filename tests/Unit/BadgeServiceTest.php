<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class BadgeServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Minimal tables for badge checks
        Schema::create('tbl_people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->timestamps();
        });

        Schema::create('tbl_creative_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('criteria')->nullable();
            $table->timestamps();
        });

        Schema::create('tbl_creative_task_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id')->nullable();
            $table->unsignedInteger('people_id');
            $table->string('event');
            $table->text('meta')->nullable();
            $table->timestamp('occurred_at')->nullable();
        });

        Schema::create('tbl_creative_user_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('people_id');
            $table->unsignedInteger('badge_id');
            $table->timestamp('awarded_at')->nullable();
            $table->timestamp('earned_at')->nullable();
        });
    }

    public function test_sprinter_badge_awarded_when_criteria_met()
    {
        // Arrange
        $personId = DB::table('tbl_people')->insertGetId(['firstname' => 'Badge', 'lastname' => 'Tester']);

        $badgeId = DB::table('tbl_creative_badges')->insertGetId([
            'code' => 'sprinter',
            'name' => 'Sprinter',
            'description' => 'Complete 5 tasks in 7 days',
            'criteria' => json_encode(['type' => 'sprinter']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create 5 completed events within the last 7 days
        for ($i = 0; $i < 5; $i++) {
            DB::table('tbl_creative_task_events')->insert([
                'task_id' => 0,
                'people_id' => $personId,
                'event' => 'completed',
                'meta' => json_encode([]),
                'occurred_at' => now(),
            ]);
        }

        // Act
        app(\App\Services\BadgeService::class)->checkAndAwardBadges($personId);

        // Assert - badge row exists
        $exists = DB::table('tbl_creative_user_badges')
            ->where('people_id', $personId)
            ->where('badge_id', $badgeId)
            ->exists();

        $this->assertTrue($exists, 'Expected sprinter badge to be awarded');

        $row = DB::table('tbl_creative_user_badges')
            ->where('people_id', $personId)
            ->where('badge_id', $badgeId)
            ->first();

        $this->assertNotNull($row->earned_at ?? $row->awarded_at, 'earned_at or awarded_at should be present');
    }
}
