<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Classes\table;

class CreativeRequestController extends Controller
{
    /**
     * Display a listing of the creative requests.
     */
    public function index()
    {
        // Use the scoped helper from table.php to automatically filter by campus
        try {
            $requests = table::creativeRequests()
                ->leftJoin('tbl_people', 'tbl_creative_requests.requester_people_id', '=', 'tbl_people.id')
                ->leftJoin('tbl_creative_tasks', 'tbl_creative_requests.id', '=', 'tbl_creative_tasks.request_id')
                ->select(
                    'tbl_creative_requests.id',
                    'tbl_creative_requests.title',
                    'tbl_creative_requests.request_type',
                    'tbl_creative_requests.priority',
                    'tbl_creative_requests.status',
                    'tbl_creative_requests.desired_due_at',
                    'tbl_creative_requests.created_at',
                    'tbl_people.firstname',
                    'tbl_people.lastname',
                    \DB::raw('COUNT(tbl_creative_tasks.id) as tasks_count')
                )
                ->groupBy(
                    'tbl_creative_requests.id',
                    'tbl_creative_requests.title',
                    'tbl_creative_requests.request_type',
                    'tbl_creative_requests.priority',
                    'tbl_creative_requests.status',
                    'tbl_creative_requests.desired_due_at',
                    'tbl_creative_requests.created_at',
                    'tbl_people.firstname',
                    'tbl_people.lastname'
                )
                ->orderBy('tbl_creative_requests.created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            // Fallback to simple query without task count
            $requests = table::creativeRequests()
                ->leftJoin('tbl_people', 'tbl_creative_requests.requester_people_id', '=', 'tbl_people.id')
                ->select('tbl_creative_requests.*', 'tbl_people.firstname', 'tbl_people.lastname')
                ->orderBy('tbl_creative_requests.created_at', 'desc')
                ->paginate(20);
            
            // Add tasks_count manually
            $requests->getCollection()->transform(function ($request) {
                $request->tasks_count = table::creativeTasks()->where('request_id', $request->id)->count();
                return $request;
            });
        }

        return view('admin.creative.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new creative request.
     */
    public function create()
    {
        // You can pass dropdown options here if needed
        $campuses = \DB::table('tbl_form_campus')->orderBy('campus')->pluck('campus', 'id');

        return view('admin.creative.requests.create', compact('campuses'));
    }

    /**
     * Store a newly created creative request in storage.
     */
    public function store(Request $request)
    {
        $baseValidation = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'request_type' => 'required|in:video,graphic',
            'admin_approved' => 'required|boolean',
            'requester_name' => 'required|string|max:255',
            'ministry' => 'required|string|max:255',
            'tg_email' => 'required|email',
            'date_requested' => 'required|date',
            'campus' => 'required|array',
            'campus.*' => 'string',
            'project_type' => 'required|array',
            'project_type.*' => 'string'
        ];

        $data = $request->validate($baseValidation);
        
        // Store as JSON in form_data field
        $formData = $request->except(['_token', 'title', 'description', 'request_type']);
        
        $user = Auth::user();
        
        table::creativeRequests()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'request_type' => $data['request_type'],
            'priority' => 'normal',
            'status' => $data['admin_approved'] ? 'pending' : 'awaiting_approval',
            'requester_people_id' => $user->reference ?? 1,
            'admin_approved' => $data['admin_approved'],
            'form_data' => json_encode($formData),
            'desired_due_at' => null
        ]);

        return redirect()->route('admin.creative.requests.index')
                         ->with('success', ucfirst($data['request_type']) . ' request submitted successfully.');
    }

    /**
     * Display the specified creative request.
     */
    public function show($id)
    {
        $request = CreativeRequest::with(['requester', 'tasks', 'attachments'])->findOrFail($id);

        // Basic access check: ensure user is in the same campus or has full access
        if (!\App\Classes\permission::hasFullAccess()) {
            $userCampusId = \App\Classes\permission::getScopeData()['campus'] ?? null;
            if ($request->campus_id != $userCampusId) {
                abort(403, 'You do not have permission to view this request.');
            }
        }

        return view('admin.creative.requests.show', compact('request'));
    }

    /**
     * Update the status of the specified creative request.
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed,on_hold,cancelled',
        ]);

        $creativeRequest = CreativeRequest::findOrFail($id);
        $creativeRequest->status = $data['status'];
        $creativeRequest->save();

        // Optional: Log this change in the task_events table
        // ...

        return back()->with('success', 'Request status updated.');
    }
}