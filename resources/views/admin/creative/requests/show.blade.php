@extends('layouts.crw_layout')

@section('meta')
    <title>Request Details</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<main class="main-wrapper">
    <div class="main-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Creative Workload</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.requests.index') }}">All Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Request Details</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="row">
            <div class="col-lg-8">
                {{-- Request Details --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">{{ $request->title }}</h4>
                        @php
                            $createdAt = $request->created_at ?? null;
                            try {
                                $createdAtLabel = $createdAt ? \Carbon\Carbon::parse($createdAt)->format('M d, Y') : '';
                            } catch (\Throwable $e) {
                                $createdAtLabel = is_string($createdAt) ? $createdAt : '';
                            }

                            $updatedAt = $request->updated_at ?? null;
                            try {
                                $updatedAtLabel = $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('M d, Y') : '';
                            } catch (\Throwable $e) {
                                $updatedAtLabel = is_string($updatedAt) ? $updatedAt : '';
                            }

                            $desiredDue = $request->desired_due_at ?? null;
                            try {
                                $desiredDueLabel = $desiredDue ? \Carbon\Carbon::parse($desiredDue)->format('M d, Y') : 'N/A';
                            } catch (\Throwable $e) {
                                $desiredDueLabel = is_string($desiredDue) ? $desiredDue : 'N/A';
                            }
                        @endphp

                        <p class="text-muted">
                            Requested by {{ $request->requester->firstname ?? ($request->requester_name ?? 'N/A') }} on {{ $createdAtLabel }}
                        </p>
                        @if(!empty($updatedAtLabel))
                            <p class="text-muted small">Last updated: {{ $updatedAtLabel }}</p>
                        @endif
                        <p>{{ $request->description }}</p>
                    </div>
                </div>

                {{-- Tasks --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tasks</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">Add Task</button>
                    </div>
                    <div class="card-body">
                        @forelse($request->tasks as $task)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0">{{ $task->title }}</h6>
                                        @php
                                            $priorityColors = [
                                                'low' => 'success',
                                                'normal' => 'secondary', 
                                                'high' => 'warning',
                                                'urgent' => 'danger'
                                            ];
                                            $priorityColor = $priorityColors[$task->priority] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $priorityColor }}">{{ ucfirst($task->priority) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-muted">{{ ucwords(str_replace('_', ' ', $task->status)) }}</small>
                                        @if($task->assignments && $task->assignments->count() > 0)
                                            <small class="text-info">• Assigned to: 
                                                @foreach($task->assignments as $assignment)
                                                    {{ $assignment->person->firstname }} {{ $assignment->person->lastname }}@if(!$loop->last), @endif
                                                @endforeach
                                            </small>
                                        @else
                                            <small class="text-warning">• Unassigned</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.creative.tasks.destroy', $task->id) }}" style="display:inline;" onsubmit="return confirm('Delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No tasks have been created for this request yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Attachments --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Attachments</h5>
                        <a href="#" class="btn btn-sm btn-primary">Upload File</a>
                    </div>
                    <div class="card-body">
                         @forelse($request->attachments as $attachment)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $attachment->filename }}</h6>
                                    <small class="text-muted">Uploaded by {{ $attachment->uploader->firstname ?? 'N/A' }}</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-light">Download</a>
                            </div>
                        @empty
                            <p class="text-muted">No files have been attached to this request.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Requester Info (admin-only) --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-2">Requester</h5>
                        <p class="mb-1"><strong>{{ ($request->requester->firstname ?? $request->requester_name ?? '') . ' ' . ($request->requester->lastname ?? '') }}</strong></p>
                        @php
                            $reqEmail = $request->requester->email ?? $request->requester_email ?? null;
                            $reqId = $request->requester->idno ?? null;
                            $reqPhone = $request->requester->phone ?? null;
                            $reqDept = $request->requester->department->department ?? ($request->department ?? null);
                            $reqJob = $request->requester->jobTitle->jobtitle ?? $request->requester->jobTitle->title ?? $request->requester->jobTitle->name ?? null;
                            $reqCampus = $request->requester->currentCampusData->campus->campus ?? ($request->campus ?? null);
                            $reqMinistry = $request->requester_ministry ?? ($request->ministry ?? null);
                        @endphp

                        @if(!empty($reqEmail))
                            <p class="mb-1 small text-muted">Email: {{ $reqEmail }}</p>
                        @endif
                        @if(!empty($reqId))
                            <p class="mb-1 small text-muted">ID: {{ $reqId }}</p>
                        @endif
                        @if(!empty($reqPhone))
                            <p class="mb-1 small text-muted">Phone: {{ $reqPhone }}</p>
                        @endif
                        @if(!empty($reqJob))
                            <p class="mb-1 small text-muted">Job Title: {{ $reqJob }}</p>
                        @endif
                        @if(!empty($reqCampus))
                            <p class="mb-1 small text-muted">Campus: {{ $reqCampus }}</p>
                        @endif
                        @if(!empty($reqMinistry))
                            <p class="mb-1 small text-muted">Ministry: {{ $reqMinistry }}</p>
                        @endif
                        @if(!empty($reqDept))
                            <p class="mb-0 small text-muted">Department: {{ $reqDept }}</p>
                        @endif
                    </div>
                </div>
                {{-- Status & Metadata --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Details</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Status <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Priority 
                                @php
                                    $requestPriorityColors = [
                                        'low' => 'success',
                                        'normal' => 'secondary', 
                                        'high' => 'warning',
                                        'urgent' => 'danger'
                                    ];
                                    $requestPriorityColor = $requestPriorityColors[$request->priority] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $requestPriorityColor }}">{{ ucfirst($request->priority) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Type <span class="badge bg-primary">{{ ucfirst($request->request_type) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Due Date <span>{{ $desiredDueLabel }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Admin Approved <span class="badge bg-{{ $request->admin_approved ? 'success' : 'secondary' }}">{{ $request->admin_approved ? 'Yes' : 'No' }}</span>
                            </li>
                        </ul>

                        <hr>
                        @if(!empty($request->form_data) && is_array($request->form_data))
                            <h6 class="mb-2">Form Submission</h6>
                            <div class="mb-3">
                                @foreach($request->form_data as $key => $val)
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <div class="small text-muted">{{ ucwords(str_replace(['_', '-'], ' ', $key)) }}</div>
                                        <div class="small">@if(is_array($val) || is_object($val)){{ json_encode($val) }}@else{{ $val }}@endif</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <form action="{{ route('admin.creative.requests.updateStatus', $request->id) }}" method="POST">
                            @csrf
                            <label class="form-label">Update Status</label>
                            <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                                <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ $request->status == 'review' ? 'selected' : '' }}>In Review</option>
                                <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="on_hold" {{ $request->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.creative.tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="request_id" value="{{ $request->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Task Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Time (minutes)</label>
                                <input type="number" name="estimated_minutes" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_at" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assignees[]" class="form-select" multiple>
                            @foreach(\App\Models\Person::orderBy('firstname')->get() as $person)
                            <option value="{{ $person->id }}">{{ $person->firstname }} {{ $person->lastname }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple people</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($request->tasks as $task)
<!-- Edit Task Modal for Task {{ $task->id }} -->
<div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.creative.tasks.update', $task->id) }}">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Task Title</label>
                        <input type="text" name="title" value="{{ $task->title }}" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $task->description }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select" required>
                                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ $task->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ $task->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Time (minutes)</label>
                                <input type="number" name="estimated_minutes" value="{{ $task->estimated_minutes }}" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_at" value="{{ $task->due_at }}" class="form-control">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


@endsection