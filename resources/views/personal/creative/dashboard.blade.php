@extends('layouts.personal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Creative Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('personal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Creative</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Total Points</p>
                            <h4 class="mb-2">{{ number_format($totalPoints) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                    {{ $pointsBreakdown->get('task_completed', 0) }} from tasks
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-trophy-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">This Month</p>
                            <h4 class="mb-2">{{ $thisMonth['tasks_completed'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    +{{ $thisMonth['points_earned'] }} points
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-task-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Active Tasks</p>
                            <h4 class="mb-2">{{ $myTasks->count() }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-warning fw-bold font-size-12 me-2">
                                    {{ $myTasks->where('due_at', '<=', now()->addDays(3))->count() }} due soon
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ri-time-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Badges Earned</p>
                            <h4 class="mb-2">{{ $myBadges->count() }}</h4>
                            <p class="text-muted mb-0">
                                @if($myBadges->count() > 0)
                                    <span class="text-info fw-bold font-size-12 me-2">
                                        Latest: {{ $myBadges->first()->name }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-medal-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Tasks -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">My Active Tasks</h4>
                </div>
                <div class="card-body">
                    @forelse($myTasks as $task)
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div class="flex-1">
                            <h6 class="mb-1">{{ $task->title }}</h6>
                            <p class="text-muted mb-1">{{ $task->request->title }}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-soft-{{ $task->status === 'pending' ? 'warning' : ($task->status === 'completed' ? 'success' : 'info') }} me-2">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                                @if($task->due_at)
                                <small class="text-muted">Due: {{ $task->due_at->format('M d, Y') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Update Status
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">In Progress</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'review')">Ready for Review</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'completed')">Completed</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ri-task-line font-size-48 text-muted"></i>
                        <h5 class="mt-3">No active tasks</h5>
                        <p class="text-muted">You don't have any active creative tasks assigned.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Badges & Activity -->
        <div class="col-xl-4">
            <!-- My Badges -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">My Badges</h4>
                </div>
                <div class="card-body">
                    @forelse($myBadges as $badge)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="ri-medal-line"></i>
                            </span>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1">{{ $badge->name }}</h6>
                            <p class="text-muted mb-0 font-size-13">{{ $badge->description }}</p>
                            <small class="text-muted">Earned {{ \Carbon\Carbon::parse($badge->earned_at)->diffForHumans() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="ri-medal-line font-size-36 text-muted"></i>
                        <p class="text-muted mt-2">No badges earned yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Recent Activity</h4>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-xs">
                                <div class="avatar-title bg-light text-primary rounded-circle">
                                    <i class="ri-check-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 ms-3">
                            <h6 class="mb-1 font-size-14">{{ ucfirst(str_replace('_', ' ', $activity->event)) }}</h6>
                            <p class="text-muted mb-1 font-size-13">{{ $activity->task_title }}</p>
                            <p class="text-muted mb-0 font-size-12">{{ \Carbon\Carbon::parse($activity->occurred_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <p class="text-muted">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
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