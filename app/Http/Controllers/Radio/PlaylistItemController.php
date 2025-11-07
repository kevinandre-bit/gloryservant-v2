<?php
// app/Http/Controllers/Radio/PlaylistItemController.php

namespace App\Http\Controllers\Radio;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlaylistItemController extends Controller
{
    /** Decide which column we should use for ordering */
    protected function orderCol(): string
    {
        return Schema::hasColumn('playlist_items', 'order_index') ? 'order_index' : 'position';
    }

    public function store(Request $request, Playlist $playlist)
{
    $data = $request->validate([
        'track_id' => ['required','exists:tracks,id'],
    ]);

    // place at the end (allow duplicates)
    $nextPos = (int) $playlist->items()->max('position');
    $playlist->items()->create([
        'track_id' => $data['track_id'],
        'position' => $nextPos + 1,
        'notes'    => null,
    ]);

    return back()->with('success', __('Added to playlist.'));
}

    public function destroy(Playlist $playlist, PlaylistItem $item)
    {
        abort_unless($item->playlist_id === $playlist->id, 404);
        $item->delete();

        // re-normalize indices
        $this->reindex($playlist);

        return back()->with('success', __('Removed.'));
    }

    public function moveUp(Playlist $playlist, PlaylistItem $item)
    {
        $this->move($playlist, $item, -1);
        return back();
    }

    public function moveDown(Playlist $playlist, PlaylistItem $item)
    {
        $this->move($playlist, $item, +1);
        return back();
    }

    /** Swap with neighbor using a spare slot (works with UNSIGNED + unique index) */
    protected function move(Playlist $playlist, PlaylistItem $item, int $delta): void
    {
        $col = $this->orderCol();

        DB::transaction(function () use ($playlist, $item, $delta, $col) {
            // Lock all indices for this playlist to compute a spare slot
            $max   = PlaylistItem::where('playlist_id', $playlist->id)->lockForUpdate()->max($col);
            $spare = (int)($max ?? -1) + 1;

            /** @var PlaylistItem $current */
            $current = PlaylistItem::whereKey($item->id)
                ->where('playlist_id', $playlist->id)
                ->lockForUpdate()
                ->firstOrFail();

            $old = (int)$current->{$col};
            $new = $old + $delta;
            if ($new < 0) return; // already at top

            $neighbor = PlaylistItem::where('playlist_id', $playlist->id)
                ->where($col, $new)
                ->lockForUpdate()
                ->first();

            if (!$neighbor) {
                $current->update([$col => $new]); // move into gap
                return;
            }

            // Park neighbor at spare, move current to target, then neighbor to old
            $neighbor->update([$col => $spare]);
            $current->update([$col => $new]);
            $neighbor->update([$col => $old]);
        });
    }

    /** Reindex to 0..n-1 in DB order */
    public function reindex(Playlist $playlist)
    {
        $col = $this->orderCol();

        DB::transaction(function () use ($playlist, $col) {
            $rows = DB::table('playlist_items')
                ->where('playlist_id', $playlist->id)
                ->orderBy($col)
                ->get(['id']);
            $i = 0;
            foreach ($rows as $r) {
                DB::table('playlist_items')->where('id', $r->id)->update([$col => $i++]);
            }
        });

        return back()->with('success', __('Playlist order normalized.'));
    }

    /** Clear all items */
    public function clear(Playlist $playlist)
    {
        $playlist->items()->delete();
        return back()->with('success', __('Playlist cleared.'));
    }
}
