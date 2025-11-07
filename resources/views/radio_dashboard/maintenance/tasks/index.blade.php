@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Maintenance</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Tasks</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a href="javascript:;" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTask">
          <i class="material-icons-outlined">add</i> New Task
        </a>
        <a href="{{ route('maintenance.calendar.index') }}" class="btn btn-outline-primary btn-sm">
          <i class="material-icons-outlined">event_available</i> Calendar
        </a>
      </div>
    </div>

    {{-- Flash / errors --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Top stats --}}
    <div class="row g-3 mb-1">
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">Open</div>
              <h4 class="mb-0">{{ $stats['open'] }}</h4>
            </div>
            <i class="material-icons-outlined fs-2">assignment</i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">Overdue</div>
              <h4 class="mb-0">{{ $stats['overdue'] }}</h4>
            </div>
            <i class="material-icons-outlined fs-2">warning_amber</i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">Scheduled</div>
              <h4 class="mb-0">{{ $stats['scheduled'] }}</h4>
            </div>
            <i class="material-icons-outlined fs-2">event</i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">Done (7d)</div>
              <h4 class="mb-0">{{ $stats['doneThisWeek'] }}</h4>
            </div>
            <i class="material-icons-outlined fs-2">task_alt</i>
          </div>
        </div>
      </div>
    </div>

    {{-- Filters --}}
    {{-- ===== Toolbar (quick chips + sort) ===== --}}
<div class="d-flex flex-wrap gap-2 align-items-center mb-2">
  <div class="btn-group btn-group-sm" role="group" aria-label="Quick states">
    @php $state = request('state'); @endphp
    <a class="btn {{ $state==='open'?'btn-primary':'btn-outline-secondary' }}"
       href="{{ request()->fullUrlWithQuery(['state'=>'open']) }}">Open</a>
    <a class="btn {{ $state==='scheduled'?'btn-primary':'btn-outline-secondary' }}"
       href="{{ request()->fullUrlWithQuery(['state'=>'scheduled']) }}">Scheduled</a>
    <a class="btn {{ $state==='overdue'?'btn-primary':'btn-outline-secondary' }}"
       href="{{ request()->fullUrlWithQuery(['state'=>'overdue']) }}">Overdue</a>
    <a class="btn {{ $state==='done'?'btn-primary':'btn-outline-secondary' }}"
       href="{{ request()->fullUrlWithQuery(['state'=>'done']) }}">Closed</a>
  </div>

  <span class="vr mx-1 d-none d-sm-inline"></span>

  {{-- Sort --}}
  @php $sort = request('sort','due_asc'); @endphp
  <form method="get" action="{{ route('maintenance.tasks.index') }}" id="sortForm" class="d-inline">
    {{-- keep current filters on sort change --}}
    @foreach(request()->except(['sort']) as $k=>$v)
      @if(is_array($v))
        @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
      @else
        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
      @endif
    @endforeach
    <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('sortForm').submit()">
      <option value="due_asc"  {{ $sort==='due_asc'?'selected':'' }}>Due ↑</option>
      <option value="due_desc" {{ $sort==='due_desc'?'selected':'' }}>Due ↓</option>
      <option value="prio_desc"{{ $sort==='prio_desc'?'selected':'' }}>Priority</option>
      <option value="created_desc"{{ $sort==='created_desc'?'selected':'' }}>Newest</option>
    </select>
  </form>

  <div class="ms-auto d-flex gap-2">
    <a href="{{ route('maintenance.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
      Reset
    </a>
    <a href="javascript:;" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTask">
      <i class="material-icons-outlined">add</i> New
    </a>
  </div>
</div>

{{-- ===== Filters line ===== --}}
<div class="card rounded-4 mb-3">
  <div class="card-body">
    <form method="get" action="{{ route('maintenance.tasks.index') }}" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label small text-secondary mb-0">Site</label>
        <select name="site_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Sites</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" {{ (string)request('site_id')===(string)$s->id?'selected':'' }}>
              {{ $s->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label small text-secondary mb-0">Type</label>
        @php $types = ['Preventive','Corrective']; @endphp
        <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Types</option>
          @foreach($types as $t)
            <option value="{{ $t }}" {{ request('type')===$t?'selected':'' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label small text-secondary mb-0">Search</label>
        <div class="d-flex gap-2">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                 placeholder="Title or notes…">
          <button class="btn btn-outline-secondary btn-sm" type="submit" title="Apply">
            <i class="material-icons-outlined">search</i>
          </button>
        </div>
      </div>
    </form>

    {{-- Active filter chips --}}
    @php
      $chips = [];
      if (request('state'))   $chips[] = 'State: '.ucfirst(request('state'));
      if (request('site_id')) $chips[] = 'Site: '.optional($sites->firstWhere('id',request('site_id')))->name;
      if (request('type'))    $chips[] = 'Type: '.request('type');
      if (request('q'))       $chips[] = 'Search: “'.e(request('q')).'”';
    @endphp
    @if($chips)
      <div class="d-flex flex-wrap gap-2 mt-2">
        @foreach($chips as $c)
          <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border">{{ $c }}</span>
        @endforeach
      </div>
    @endif
  </div>
</div>

    {{-- Task cards --}}
    <div class="row g-3">
      @forelse($tasks as $task)
        <div class="col-12 col-lg-6">
  <div class="card rounded-4 h-100">
    <div class="card-header d-flex justify-content-between align-items-center border-0">
      <div class="d-flex align-items-center gap-2">
        <span class="badge
          @if($task->status==='Open') bg-secondary
          @elseif($task->status==='Scheduled') bg-primary
          @elseif($task->status==='Overdue') bg-danger
          @elseif($task->status==='Closed') bg-success
          @else bg-light text-dark @endif">
          {{ $task->status }}
        </span>
        <h6 class="mb-0 text-truncate" title="{{ $task->title }}">{{ $task->title }}</h6>
      </div>

      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
          <i class="material-icons-outlined">more_horiz</i>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
          <a class="dropdown-item" href="#"><i class="material-icons-outlined me-1">info</i>Details</a>
          <a class="dropdown-item" href="#"><i class="material-icons-outlined me-1">attach_file</i>Files</a>
        </div>
      </div>
    </div>

    <div class="card-body pt-0">
      <div class="small text-secondary mb-2">
        <i class="material-icons-outlined align-middle me-1">place</i>{{ $task->site ?? '—' }}
        <span class="mx-2">•</span>
        <i class="material-icons-outlined align-middle me-1">person</i>{{ $task->assignee ?? 'Unassigned' }}
      </div>

      <div class="d-flex flex-wrap gap-3 mb-2">
        <div><span class="fw-semibold">Type:</span> {{ $task->type }}</div>
        <div><span class="fw-semibold">Priority:</span> {{ $task->priority }}</div>
        <div><span class="fw-semibold">Due:</span> {{ $task->due }}</div>
      </div>

      @if($task->notes)
        <p class="mb-3 text-break">{{ Str::limit($task->notes, 180) }}</p>
      @endif

      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-2">
          {{-- Primary action --}}
          @if($task->status!=='Closed')
            <form method="post" action="{{ route('maintenance.tasks.done', $task->id) }}" class="d-inline">
              @csrf
              <button class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="material-icons-outlined fs-6">task_alt</i> Done
              </button>
            </form>
            <form method="post" action="{{ route('maintenance.tasks.reschedule', $task->id) }}" class="d-inline d-flex gap-2">
              @csrf
              <input type="date" name="due" class="form-control form-control-sm" style="width:150px" required>
              <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="material-icons-outlined fs-6">schedule</i> Reschedule
              </button>
            </form>
          @else
            <span class="badge bg-success">Completed</span>
          @endif
        </div>

        <div class="small text-secondary">
          Updated {{ \Carbon\Carbon::parse($task->updated_at ?? $task->created_at)->diffForHumans() }}
        </div>
      </div>
    </div>
  </div>
</div>
      @empty
        <div class="col-12">
          <div class="alert alert-secondary mb-0">No tasks match your filters.</div>
        </div>
      @endforelse
    </div>

  </div>
</main>

{{-- ================= New Task Modal ================= --}}
<div class="modal fade" id="modalTask" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header border-0">
        <h6 class="modal-title"><i class="material-icons-outlined me-2">assignment_add</i> New Task</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="post" action="{{ route('maintenance.tasks.store') }}">
        @csrf
        {{-- Reopen modal on error --}}
        <input type="hidden" name="_from_modal" value="new_task">

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" required maxlength="200" value="{{ old('title') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Site</label>
              <select name="station_id" class="form-select" required>
                <option value="">Select site…</option>
                @foreach($sites as $s)
                  <option value="{{ $s->id }}" {{ old('station_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Type</label>
              <select name="type" class="form-select" required>
                @php $types = ['Preventive','Corrective']; @endphp
                @foreach($types as $t)
                  <option value="{{ $t }}" {{ old('type',$types[0])==$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select" required>
                @foreach(['Low','Medium','High','Critical'] as $p)
                  <option {{ old('priority','Medium')==$p?'selected':'' }}>{{ $p }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Due date</label>
              <input type="date" name="due" class="form-control" required value="{{ old('due') }}">
            </div>

            <div class="col-md-6">
              <label class="form-label">Assignee</label>
              <select name="assignee_id" class="form-select">
                <option value="">Unassigned</option>
                @foreach($assignees as $u)
                  <option value="{{ $u->id }}" {{ old('assignee_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                @foreach(['Open','Scheduled','Overdue','Closed'] as $st)
                  <option {{ old('status','Open')==$st?'selected':'' }} value="{{ $st }}">{{ $st }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea name="notes" rows="3" class="form-control" placeholder="Details…">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-soft-outline" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="material-icons-outlined me-1">save</i> Save Task
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Reopen modal when validation failed --}}
@if ($errors->any() && old('_from_modal') === 'new_task')
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const m = new bootstrap.Modal(document.getElementById('modalTask'));
      m.show();
    });
  </script>
@endif