<?php 

class PlaylistApiController extends Controller
{
    public function store(Request $req, \App\Models\Playlist $playlist)
    {
        $data = $req->validate(['track_id'=>'required|exists:tracks,id']);
        $exists = $playlist->items()->where('track_id',$data['track_id'])->exists();
        if ($exists) return response()->json(['error'=>'Duplicate'], 409);
        $next = ($playlist->items()->max('position') ?? -1) + 1;
        $item = $playlist->items()->create(['track_id'=>$data['track_id'], 'position'=>$next]);
        return response()->json(['ok'=>true, 'id'=>$item->id]);
    }

    public function reorder(Request $req, \App\Models\Playlist $playlist)
    {
        $arr = $req->validate(['items'=>'required|array']); // items: [{id,position}]
        foreach ($arr['items'] as $row) {
            \App\Models\PlaylistItem::where('id',$row['id'])->where('playlist_id',$playlist->id)->update(['position'=>$row['position']]);
        }
        return response()->json(['ok'=>true]);
    }

    public function destroy(\App\Models\Playlist $playlist, \App\Models\PlaylistItem $item)
    {
        $item->delete();
        return response()->json(['ok'=>true]);
    }
}