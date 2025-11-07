@extends('layouts.admin')

@section('meta')
  <title>Follow-ups | Admin Dashboard</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">insights</i> Follow-ups — Admin</h5>
    </div>

    {{-- KPI cards --}}
    <div class="row g-3 mb-3">
      <div class="col-6 col-xl-3"><div class="card rounded-4 h-100"><div class="card-body">
        <div class="small">Total</div>
        <div class="display-6 fw-semibold">{{ (int)($kpis->total ?? 0) }}</div>
      </div></div></div>
      <div class="col-6 col-xl-3"><div class="card rounded-4 h-100"><div class="card-body">
        <div class="small">Open</div>
        <div class="display-6 fw-semibold text-primary">{{ (int)($kpis->open ?? 0) }}</div>
      </div></div></div>
      <div class="col-6 col-xl-3"><div class="card rounded-4 h-100"><div class="card-body">
        <div class="small">In Progress</div>
        <div class="display-6 fw-semibold text-warning">{{ (int)($kpis->inprog ?? 0) }}</div>
      </div></div></div>
      <div class="col-6 col-xl-3"><div class="card rounded-4 h-100"><div class="card-body">
        <div class="small">Resolved</div>
        <div class="display-6 fw-semibold text-success">{{ (int)($kpis->closed ?? 0) }}</div>
      </div></div></div>
    </div> 

    {{-- Filters --}}
    <form class="card rounded-4 mb-3" method="get" action="{{ route('admin.followups.index') }}">
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="">All</option>
              @foreach(['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved'] as $key=>$lbl)
                <option value="{{ $key }}" {{ $status===$key?'selected':'' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Severity</label>
            <select class="form-select" name="severity">
              <option value="">All</option>
              @foreach(['low','medium','high'] as $sev)
                <option value="{{ $sev }}" {{ $severity===$sev?'selected':'' }}>{{ ucfirst($sev) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Leader</label>
            <select class="form-select" name="leader">
              <option value="">All</option>
              @foreach($assignable as $u)
                <option value="{{ $u->id }}" {{ (string)$leaderId===(string)$u->id?'selected':'' }}>{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">From</label>
            <input type="date" class="form-control" name="from" value="{{ $from }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">To</label>
            <input type="date" class="form-control" name="to" value="{{ $to }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Category contains</label>
            <input type="text" class="form-control" name="category" value="{{ $category }}" placeholder="e.g. devotion">
          </div>
          <div class="col-12">
            <label class="form-label">Search</label>
            <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="name / group / notes">
          </div>
          <div class="col-12 d-flex gap-2 mt-2">
            <button class="btn btn-primary">Apply</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.followups.index') }}">Reset</a>
          </div>
        </div>
      </div>
    </form>

    {{-- Status breakdown --}}
    <div class="card rounded-4 mb-3">
      <div class="card-body">
        <div class="d-flex flex-wrap gap-3">
          @foreach($statusBreakdown as $row)
            <span class="badge bg-light text-secondary">
              {{ strtoupper($row->current_status) }}: <strong>{{ $row->c }}</strong>
            </span>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Table --}}
    <div class="card rounded-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Volunteer</th>
                <th>Group</th>
                <th>Status</th>
                <th>Severity</th>
                <th>Leader</th>
                <th>Next Due</th>
                <th>Overdue</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($followups as $f)
                @php
                  $sev = $f->severity ?? 'medium';
                  $cls = $sev==='high' ? 'bg-danger' : ($sev==='medium' ? 'bg-warning text-secondary' : 'bg-success');
                  $isResolved = ($f->status ?? '') === 'resolved';
                  $isOverdue = !$isResolved && $f->followup_due_on && \Carbon\Carbon::parse($f->followup_due_on)->isPast();
                @endphp
                <tr>
                  <td>{{ \Carbon\Carbon::parse($f->meeting_date)->toFormattedDateString() }}</td>
                  <td>{{ $f->volunteer_name }}</td>
                  <td>{{ $f->group_name ?: '—' }}</td>
                  <td><span class="badge bg-light text-secondary">{{ strtoupper($f->status) }}</span></td>
                  <td><span class="badge {{ $cls }}">{{ strtoupper($sev) }}</span></td>
                  <td>{{ $f->leader_name }}</td>
                  <td>{{ $f->followup_due_on ?: '—' }}</td>
                  <td>
                    @if($isOverdue)
                      <span class="badge bg-danger">OVERDUE</span>
                    @else
                      —
                    @endif
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#followupViewModal"
                            data-id="{{ $f->id }}">View</button>

                    <form method="post" action="{{ route('admin.followups.assign',$f->id) }}" class="d-inline">
                      @csrf
                      <select name="assigned_to_id" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                        <option value="">—</option>
                        @foreach($assignable as $u)
                          <option value="{{ $u->id }}" {{ (int)$f->assigned_to_id===(int)$u->id?'selected':'' }}>{{ $u->name }}</option>
                        @endforeach
                      </select>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="9" class="text-center text-muted p-4">No follow-ups match your filters.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-3">
          {{ $followups->links() }}
        </div>
      </div>
    </div>

  </div>
</main>

{{-- Reuse the same modal as leader page --}}
@include('wellness.followups._view_modal_reuse')
@endsection