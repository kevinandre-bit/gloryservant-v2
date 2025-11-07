@extends('layouts.personal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $task->title }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('personal.creative.dashboard') }}">Creative</a></li>
                        <li class="breadcrumb-item active">Task Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">{{ $task->title }}</h5>
                        <span class="badge badge-soft-{{ $task->status === 'pending' ? 'warning' : ($task->status === 'completed' ? 'success' : 'info') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Request</h6>
                        <p class="text-muted">{{ $task->request->title }}</p>
                        @if($task->request->description)
                        <p>{{ $task->request->description }}</p>
                        @endif
                    </div>

                    @if($task->description)
                    <div class="mb-4">
                        <h6>Task Description</h6>
                        <p>{{ $task->description }}</p>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Priority</h6>
                            <span class="badge badge-soft-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        @if($task->due_at)
                        <div class="col-md-6">
                            <h6>Due Date</h6>
                            <p class="mb-0">{{ $task->due_at->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($task->estimated_minutes)
                    <div class="mb-4">
                        <h6>Estimated Time</h6>
                        <p>{{ floor($task->estimated_minutes / 60) }}h {{ $task->estimated_minutes % 60 }}m</p>
                    </div>
                    @endif

                    <div class="mb-4">
                        <h6>Update Status</h6>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">
                                In Progress
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="updateTaskStatus({{ $task->id }}, 'review')">
                                Ready for Review
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                Completed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assignees</h5>
                </div>
                <div class="card-body">
                    @foreach($task->assignees as $assignee)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3">
                            <span class="avatar-title bg-primary rounded-circle">
                                {{ substr($assignee->firstname, 0, 1) }}{{ substr($assignee->lastname, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $assignee->firstname }} {{ $assignee->lastname }}</h6>
                            <p class="text-muted mb-0 font-size-13">{{ $assignee->pivot->role ?? 'Owner' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($task->events->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity</h5>
                </div>
                <div class="card-body">
                    @foreach($task->events->take(10) as $event)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-xs">
                                <div class="avatar-title bg-light text-primary rounded-circle">
                                    <i class="ri-check-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 ms-3">
                            <h6 class="mb-1 font-size-14">{{ ucfirst(str_replace('_', ' ', $event->event)) }}</h6>
                            <p class="text-muted mb-0 font-size-12">{{ $event->occurred_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateTaskStatus(taskId, status) {
    fetch(`/personal/creative/tasks/${taskId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating task status');
        }
    });
}
</script>
@endsection