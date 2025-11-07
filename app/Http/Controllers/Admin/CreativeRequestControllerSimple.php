<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\table;

class CreativeRequestControllerSimple extends Controller
{
    public function index()
    {
        try {
            $requests = table::creativeRequests()
                ->leftJoin('tbl_people', 'tbl_creative_requests.requester_people_id', '=', 'tbl_people.id')
                ->select('tbl_creative_requests.*', 'tbl_people.firstname', 'tbl_people.lastname')
                ->orderBy('tbl_creative_requests.created_at', 'desc')
                ->paginate(20);

            // Add task count manually
            foreach ($requests as $request) {
                $request->tasks_count = table::creativeTasks()->where('request_id', $request->id)->count();
            }

            return view('admin.creative.requests.index', compact('requests'));
        } catch (\Exception $e) {
            return view('admin.creative.requests.index', ['requests' => collect(), 'error' => $e->getMessage()]);
        }
    }
}