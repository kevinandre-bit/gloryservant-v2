<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class PointsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create minimal tables required for the PointsService logic
        Schema::create('tbl_people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->timestamps();
        });

        Schema::create('tbl_creative_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('request_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('tbl_creative_points_ledger', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('people_id');
            $table->integer('points');
            $table->string('reason')->nullable();
            $table->string('ref_table')->nullable();
            $table->unsignedInteger('ref_id')->nullable();
            $table->string('idempotency_key')->nullable()->unique();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_award_task_points_creates_ledger_entries_and_is_idempotent()
    {
        // Arrange: create minimal person and task records
    $personId = DB::table('tbl_people')->insertGetId(['firstname' => 'Unit', 'lastname' => 'Tester']);

    $taskId = DB::table('tbl_creative_tasks')->insertGetId([
            'request_id' => 0,
            'title' => 'Unit test task',
            'description' => 'Testing points awarding',
            'status' => 'completed',
            'priority' => 'high',
            'estimated_minutes' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Act - first award
    app(\App\Services\PointsService::class)->awardTaskPoints($taskId, $personId);

        // Assert
    $total = DB::table('tbl_creative_points_ledger')->where('people_id', $personId)->sum('points');
        $this->assertEquals(15, $total, 'Expected base + high priority bonus (10 + 5)');

        // Act - second award (should be idempotent)
    app(\App\Services\PointsService::class)->awardTaskPoints($taskId, $personId);

        // Assert unchanged
    $total2 = DB::table('tbl_creative_points_ledger')->where('people_id', $personId)->sum('points');
        $this->assertEquals(15, $total2, 'Idempotency: awarding twice should not duplicate points');
    }
}
