<?php

namespace App\Http\Controllers\Wellness;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowupsAdminController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->query('status','');
        $severity = $request->query('severity','');
        $leaderId = $request->query('leader','');
        $from     = $request->query('from','');
        $to       = $request->query('to','');
        $q        = trim($request->query('q',''));
        $category = trim($request->query('category',''));

        // Align aliases with blade (open/inprog/closed)
        $kpis = DB::table('volunteer_followups')
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(status='open') as open")
            ->selectRaw("SUM(status='in_progress') as inprog")
            ->selectRaw("SUM(status='resolved') as closed")
            ->first();

        $base = DB::table('volunteer_followups as f')
            ->leftJoin('tbl_people as p','p.id','=','f.volunteer_id')
            ->leftJoin('small_groups as g','g.id','=','f.group_id')
            ->leftJoin('users as u','u.id','=','f.leader_id')
            ->select(
                'f.*',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name'),
                'g.name as group_name',
                'u.name as leader_name'
            );

        if ($status!=='')   $base->where('f.status',$status);
        if ($severity!=='') $base->where('f.severity',$severity);
        if ($leaderId!=='') $base->where('f.leader_id',$leaderId);
        if ($from!=='')     $base->whereDate('f.meeting_date','>=',$from);
        if ($to!=='')       $base->whereDate('f.meeting_date','<=',$to);
        if ($q!=='') {
            $base->where(function($w) use ($q){
                $w->where('f.notes','like',"%$q%")
                  ->orWhere('g.name','like',"%$q%")
                  ->orWhere(DB::raw('TRIM(CONCAT(p.firstname,\" \",p.lastname))'),'like',"%$q%");
            });
        }
        if ($category!=='') {
            $base->where('f.reasons_json','like','%'.addcslashes($category,'"\\').'%');
        }

        $followups = $base->orderByDesc('f.meeting_date')->paginate(25)->appends($request->query());

        $statusBreakdown = DB::table('volunteer_followups')
            ->select('status as current_status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')->get();

        $assignable = DB::table('users')->select('id','name')->orderBy('name')->get();

        return view('wellness.followups.admin', compact(
            'followups','kpis','statusBreakdown','assignable',
            'status','severity','leaderId','from','to','q','category'
        ));
    }

    public function assign(Request $request, int $id)
    {
        $request->validate(['assigned_to_id'=>'nullable|integer|exists:users,id']);
        // Gate check (admin-only)
        abort_unless(\Gate::allows('assign-followup', $id), 403);

        DB::table('volunteer_followups')->where('id',$id)->update([
            'assigned_to_id'=>$request->assigned_to_id,
            'updated_at'=>now(),
        ]);
        return back()->with('success','Assignment updated.');
    }
}