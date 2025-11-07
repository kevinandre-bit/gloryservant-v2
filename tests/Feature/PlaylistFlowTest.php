<?php
use App\Models\{Track,Playlist,PlaylistItem,User};

it('search returns expected tracks', function () {
    Track::factory()->create(['filename'=>'luna.wav','performer'=>'Luna','category'=>'News','theme'=>'Morning','year'=>2022]);
    $res = $this->actingAs(User::factory()->create())
        ->get('/radio/dashboard/program/library.json?q=Luna');
    $res->assertOk()->assertJsonFragment(['performer'=>'Luna']);
});

it('add to playlist works', function () {
    $u = User::factory()->create();
    $pl = Playlist::create(['name'=>'T','created_by'=>$u->id]);
    $t  = Track::factory()->create();
    $res = $this->actingAs($u)->post("/radio/dashboard/program/playlists/{$pl->id}/items", ['track_id'=>$t->id]);
    $res->assertOk();
    expect(PlaylistItem::where('playlist_id',$pl->id)->where('track_id',$t->id)->exists())->toBeTrue();
});

it('reorder persists', function () {
    $u = User::factory()->create();
    $pl = Playlist::create(['name'=>'T','created_by'=>$u->id]);
    $t1 = Track::factory()->create(); $t2 = Track::factory()->create();
    $i1 = PlaylistItem::create(['playlist_id'=>$pl->id,'track_id'=>$t1->id,'position'=>0]);
    $i2 = PlaylistItem::create(['playlist_id'=>$pl->id,'track_id'=>$t2->id,'position'=>1]);
    $res = $this->actingAs($u)->patch("/radio/dashboard/program/playlists/{$pl->id}/items/reorder", [
        'items' => [['id'=>$i1->id,'position'=>1], ['id'=>$i2->id,'position'=>0]]
    ]);
    $res->assertOk();
    expect(PlaylistItem::find($i1->id)->position)->toBe(1);
    expect(PlaylistItem::find($i2->id)->position)->toBe(0);
});

it('schedule math is correct', function () {
    $start = \Carbon\CarbonImmutable::parse('2025-09-18 09:00:00', config('app.timezone'));
    $rows = [
        ['id'=>1,'duration'=>185],
        ['id'=>2,'duration'=>92],
        ['id'=>3,'duration'=>360],
    ];
    $sched = \App\Support\Time::schedule($start, $rows);
    expect($sched[0]['start']->format('H:i:s'))->toBe('09:00:00');
    expect($sched[0]['end']->format('H:i:s'))->toBe('09:03:05');
    expect($sched[1]['start']->format('H:i:s'))->toBe('09:03:05');
    expect($sched[1]['end']->format('H:i:s'))->toBe('09:04:37');
    expect($sched[2]['start']->format('H:i:s'))->toBe('09:04:37');
    expect($sched[2]['end']->format('H:i:s'))->toBe('09:10:37');
});