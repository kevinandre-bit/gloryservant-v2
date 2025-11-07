<?php
// app/Http/Controllers/Radio/TrackController.php
namespace App\Http\Controllers\Radio;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index(Request $req)
    {
        $q = Track::query();

if ($s = $req->input('s')) {
    $needle = '%'.str_replace(' ', '%', $s).'%';

    $q->where(function ($qq) use ($needle) {
        $qq->where('performer', 'like', $needle)
           ->orWhere('title', 'like', $needle)
           ->orWhere('category', 'like', $needle)
           ->orWhere('theme', 'like', $needle)
           ->orWhere('relative_path', 'like', $needle)
           ->orWhereRaw('CAST(`year` AS CHAR) LIKE ?', [$needle]);
    });
}

// ⚡ no custom orderBy — preserve DB order
$tracks = $q->paginate(50);
        return view('radio_dashboard.Program.tracks.index', compact('tracks'));
    }
}
