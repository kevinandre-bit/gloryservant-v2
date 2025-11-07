<?php

namespace App\Http\Controllers\Radio;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\Track;
use App\Support\Time;
use Illuminate\Http\Request;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /** List playlists (paginated) */
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $status  = $request->input('status');
        $owner   = trim((string) $request->input('owner', ''));

        $playlists = Playlist::query()
            ->with('user')
            ->withCount('items')
            ->select('playlists.*')
            ->selectSub(function ($sub) {
                $sub->from('playlist_items')
                    ->join('tracks', 'tracks.id', '=', 'playlist_items.track_id')
                    ->whereColumn('playlist_items.playlist_id', 'playlists.id')
                    ->selectRaw('COALESCE(SUM(tracks.duration_seconds), 0)');
            }, 'total_seconds')
            ->when($q !== '', function ($qb) use ($q) {
                $needle = '%'.str_replace(' ', '%', $q).'%';
                $qb->where(function ($w) use ($needle) {
                    $w->where('playlists.name', 'like', $needle)
                      ->orWhereHas('user', function ($u) use ($needle) {
                          $u->where('name', 'like', $needle)
                            ->orWhere('email', 'like', $needle);
                      });
                });
            })
            ->when($status, fn($qb) => $qb->where('status', $status))
            ->when($owner !== '', function ($qb) use ($owner) {
                $needle = '%'.str_replace(' ', '%', $owner).'%';
                $qb->whereHas('user', function ($u) use ($needle) {
                    $u->where('name', 'like', $needle)
                      ->orWhere('email', 'like', $needle);
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->appends($request->query());

        return view('radio_dashboard.Program.playlists.index', compact('playlists'));
    }

    public function show(Playlist $playlist, Request $request)
    {
        $playlist->load(['items.track' => fn($q) => $q->select('*')]);

        $startAt = $request->query('start_at') ?: now()->format('Y-m-d\TH:i');
        $start   = CarbonImmutable::parse($startAt, config('app.timezone', 'UTC'));

        $rows = $playlist->items->map(fn($it) => [
            'id'       => $it->id,
            'duration' => (int)($it->track->duration_seconds ?? 0),
        ])->values()->all();

        $schedule     = Time::schedule($start, $rows);
        $totalSeconds = array_sum(array_column($rows, 'duration'));
        $startDisplay = $start->format('Y-m-d H:i');
        $endDisplay   = $start->addSeconds($totalSeconds)->format('Y-m-d H:i');
        $totalHms     = Time::formatHms($totalSeconds);

        $scheduleById = collect($schedule)->keyBy('id');

        return view('radio_dashboard.Program.playlists.show', compact(
            'playlist', 'scheduleById', 'startAt', 'startDisplay', 'endDisplay', 'totalHms'
        ));
    }

    /** Show create form */
    public function create()
    {
        return view('radio_dashboard.Program.playlists.create');
    }

    /** Create a new playlist */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required','string','max:120'],
            'status'       => ['nullable','in:draft,approved,exported'],
            'youtube_peak' => ['nullable','integer','min:0'],   // NEW
        ]);

        $playlist = Playlist::create([
            'name'         => $data['name'],
            'status'       => $data['status'] ?? 'draft',
            'youtube_peak' => $data['youtube_peak'] ?? null,    // NEW
            'created_by'   => Auth::id(),
        ]);

        return redirect()
            ->route('program.playlists.edit', $playlist)
            ->with('success', __('Playlist created'));
    }

    /** Edit page */
public function edit(Playlist $playlist, Request $request)
{
    // ---------- Filters (left pane) ----------
    $q         = trim((string) $request->query('q', ''));
    $category  = ($v = $request->query('category'))  !== '' ? $v : null;
    $year      = ($v = $request->query('year'))      !== '' ? $v : null;
    $theme     = ($v = $request->query('theme'))     !== '' ? $v : null;
    $performer = ($v = $request->query('performer')) !== '' ? $v : null;

    // Library (left pane list)
    $tracks = Track::query()
        ->when($q !== '', function ($qb) use ($q) {
            $needle = '%'.str_replace(' ', '%', $q).'%';
            $qb->where(function ($w) use ($needle) {
                $w->where('title', 'like', $needle)
                  ->orWhere('relative_path', 'like', $needle)
                  ->orWhere('performer', 'like', $needle)
                  ->orWhere('category', 'like', $needle)
                  ->orWhere('theme', 'like', $needle)
                  ->orWhereRaw('CAST(`year` AS CHAR) LIKE ?', [$needle]);
            });
        })
        ->when($category,  fn ($qb) => $qb->where('category',  $category))
        ->when($year,      fn ($qb) => $qb->where('year',      $year))
        ->when($theme,     fn ($qb) => $qb->where('theme',     $theme))
        ->when($performer, fn ($qb) => $qb->where('performer', $performer))
        ->orderByDesc('id')
        ->paginate(15)
        ->appends($request->query());

    // Facets for filters
    $categories = Track::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
    $years      = Track::whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');
    $themes     = Track::whereNotNull('theme')->distinct()->orderBy('theme')->pluck('theme');
    $performers = Track::whereNotNull('performer')->distinct()->orderBy('performer')->pluck('performer');

    // ---------- Right pane: playlist items (PAGINATED) ----------
    // IMPORTANT: we paginate here and pass `$items` to Blade
    $items = $playlist->items()
        ->with('track')
        ->orderBy('position')
        ->paginate(150) // page size used by your infinite scroll
        ->appends($request->query());

    // ---------- Schedule ----------
    $stored  = $playlist->start_at ? $playlist->start_at->timezone(config('app.timezone')) : null;
    $startAt = $request->query('start_at') ?: ($stored ? $stored->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
    $start   = \Carbon\CarbonImmutable::parse($startAt, config('app.timezone', 'UTC'));

    // Build schedule only for the current page (what’s visible)
    $rows = collect($items->items())->map(fn ($it) => [
        'id'       => $it->id,
        'duration' => (int) ($it->track->duration_seconds ?? 0),
    ])->values()->all();

    $schedule     = \App\Support\Time::schedule($start, $rows);
    $totalSeconds = array_sum(array_column($rows, 'duration'));

    return view('radio_dashboard.Program.playlists.edit', [
        'playlist'   => $playlist,
        'tracks'     => $tracks,
        'categories' => $categories,
        'years'      => $years,
        'themes'     => $themes,
        'performers' => $performers,

        // filters echo-back
        'q'         => $q,
        'category'  => (string) ($category  ?? ''),
        'year'      => (string) ($year      ?? ''),
        'theme'     => (string) ($theme     ?? ''),
        'performer' => (string) ($performer ?? ''),

        // right pane
        'items'        => $items,                // <— send paginator
        'schedule'     => $schedule,

        // schedule display
        'startAt'      => $startAt,
        'totalHms'     => \App\Support\Time::formatHms($totalSeconds),
        'startDisplay' => $start->format('Y-m-d H:i'),
        'endDisplay'   => $start->addSeconds($totalSeconds)->format('Y-m-d H:i'),
    ]);
}
    /** Update basic fields */
    public function update(Request $request, Playlist $playlist)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'status'       => ['required', 'in:draft,approved,exported'],
            'start_at'     => ['nullable', 'date'],
            'youtube_peak' => ['nullable','integer','min:0'],   // NEW
        ]);

        $playlist->update($data);

        return back()->with('success', __('Playlist saved.'));
    }

    /** Duplicate a playlist */
    public function duplicate(Playlist $playlist)
    {
        $copy = $playlist->replicate(['name','status','youtube_peak']);
        $copy->name       = $playlist->name.' (Copy)';
        $copy->created_by = Auth::id();
        $copy->save();

        $pos = 0;
        foreach ($playlist->items()->orderBy('position')->get() as $it) {
            $copy->items()->create([
                'track_id' => $it->track_id,
                'position' => $pos++,
                'notes'    => $it->notes,
            ]);
        }

        return redirect()->route('program.playlists.edit', $copy)
            ->with('success', __('Duplicated.'));
    }

    /** Delete playlist */
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();

        return redirect()
            ->route('program.playlists.index')
            ->with('success', __('Playlist deleted'));
    }
}