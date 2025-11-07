@extends('layouts.admin')

@section('meta')
<title>Create Task | Admin</title>
@endsection

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col">
      <a href="{{ $creativeRequest ? route('admin.creative.requests.show', $creativeRequest->id) : route('admin.creative.requests.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-1"></i> Back
      </a>
      <h2 class="page-title section-title mb-0">{{ __('Create Task') }}</h2>
      @if($creativeRequest)
        <p class="text-muted">For request: {{ $creativeRequest->title }}</p>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <form action="{{ route('admin.creative.tasks.store') }}" method="POST">
            @csrf
            
            @if($creativeRequest)
              <input type="hidden" name="request_id" value="{{ $creativeRequest->id }}">
            @else
              <div class="mb-3">
                <label for="request_id" class="form-label">Request ID</label>
                <input type="number" class="form-control" id="request_id" name="request_id" required>
              </div>
            @endif

            <div class="mb-3">
              <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                <select class="form-select" id="priority" name="priority" required>
                  <option value="low">Low</option>
                  <option value="normal" selected>Normal</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="estimated_minutes" class="form-label">Estimated Time (minutes)</label>
                <input type="number" class="form-control" id="estimated_minutes" name="estimated_minutes" min="0">
              </div>

              <div class="col-md-4">
                <label for="due_at" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_at" name="due_at">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Assign To</label>
              <select class="form-select" name="assignees[]" multiple size="8">
                @foreach($volunteers as $volunteer)
                  <option value="{{ $volunteer->id }}">{{ $volunteer->firstname }} {{ $volunteer->lastname }}</option>
                @endforeach
              </select>
              <small class="text-muted">Hold Ctrl/Cmd to select multiple volunteers</small>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Task
              </button>
              <a href="{{ $creativeRequest ? route('admin.creative.requests.show', $creativeRequest->id) : route('admin.creative.requests.index') }}" class="btn btn-secondary">
                Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      @if($creativeRequest)
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="mb-3">Request Details</h6>
          <div class="mb-2">
            <label class="text-muted small">Title</label>
            <div>{{ $creativeRequest->title }}</div>
          </div>
          <div class="mb-2">
            <label class="text-muted small">Type</label>
            <div><span class="badge bg-info">{{ ucfirst($creativeRequest->request_type) }}</span></div>
          </div>
          <div class="mb-2">
            <label class="text-muted small">Requester</label>
            <div>{{ $creativeRequest->requester_name }}</div>
          </div>
          <div class="mb-2">
            <label class="text-muted small">Desired Due Date</label>
            <div>
              @if($creativeRequest->desired_due_at)
                {{ \Carbon\Carbon::parse($creativeRequest->desired_due_at)->format('M d, Y') }}
              @else
                <span class="text-muted">Not specified</span>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endif

      <div class="card shadow-sm mt-3">
        <div class="card-body">
          <h6 class="mb-3">Tips</h6>
          <ul class="small mb-0">
            <li>Break large requests into smaller, manageable tasks</li>
            <li>Assign tasks based on volunteer skills and availability</li>
            <li>Set realistic due dates considering estimated time</li>
            <li>Use priority levels to help volunteers focus on urgent work</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
