<?php
class LibraryApiController extends Controller
{
    public function index(Request $req)
    {
        $q = \App\Models\Track::query();
        if ($s = $req->query('q')) {
            $needle = '%'.str_replace(' ','%',$s).'%';
            $q->where(function($b) use ($needle){
                $b->where('filename','like',$needle)
                  ->orWhere('performer','like',$needle)
                  ->orWhere('category','like',$needle)
                  ->orWhere('theme','like',$needle)
                  ->orWhere('relative_path','like',$needle)
                  ->orWhere('year','like',$needle);
            });
        }
        if ($cat = $req->query('category')) $q->where('category',$cat);
        if ($year= $req->query('year'))     $q->where('year',$year);

        $tracks = $q->paginate(15);
        return response()->json($tracks);
    }
}