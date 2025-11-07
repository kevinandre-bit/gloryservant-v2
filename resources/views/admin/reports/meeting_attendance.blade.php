@extends('layouts.admin')

@section('content')
<main class="main-wrapper">
  <div class="main-content">


    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-3 align-items-end">
      <div class="col-12 col-md-2">
        <label class="form-label">Campus</label>
        <select name="campus" class="form-select">
          <option value="">All Campuses</option>
          @foreach(($campusOptions ?? []) as $camp)
            <option value="{{ $camp }}" {{ request('campus')===$camp ? 'selected' : '' }}>{{ $camp }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-2">
        <label class="form-label">Department</label>
        <select name="department" class="form-select">
          <option value="">All Departments</option>
          @foreach(($deptOptions ?? []) as $dep)
            <option value="{{ $dep }}" {{ request('department')===$dep ? 'selected' : '' }}>{{ $dep }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-2">
        <label class="form-label">Ministry</label>
        <select name="ministry" class="form-select">
          <option value="">All Ministries</option>
          @foreach(($ministryOptions ?? []) as $min)
            <option value="{{ $min }}" {{ request('ministry')===$min ? 'selected' : '' }}>{{ $min }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-md-1">
        <label class="form-label">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
      </div>

      <div class="col-6 col-md-1">
        <label class="form-label">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
      </div>

      {{-- NEW: Meeting (single select) --}}
      <div class="col-12 col-md-3">
        <label class="form-label">Meeting</label>
        <select name="meeting" class="form-select">
          <option value="">All Meetings</option>
          @foreach(($meetingOptions ?? []) as $opt)
            <option value="{{ $opt }}" {{ (string)request('meeting') === (string)$opt ? 'selected' : '' }}>
              {{ $opt }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-1 d-grid">
        <button class="btn btn-outline-primary w-100 h-100">
          <i class="bi bi-funnel"></i>
          <span class="d-none d-lg-inline ms-1">Filter</span>
        </button>
      </div>
    </form>


{{-- Insight Cards --}}
<div class="row row-cols-1 row-cols-xl-3 mb-4">

  {{-- 🟩 Card 1 – Attendance Coverage --}}
  <div class="col">
    <div class="card rounded-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h2 class="mb-0">{{ $summary['coveragePct'] ?? 0 }}%</h2>
          <span class="dash-lable d-flex align-items-center gap-1 rounded mb-0
                       {{ ($summary['coveragePct'] ?? 0) >= 90 ? 'bg-success text-success bg-opacity-10' :
                          (($summary['coveragePct'] ?? 0) >= 70 ? 'bg-warning text-warning bg-opacity-10' : 'bg-danger text-danger bg-opacity-10') }}">
            {{ $summary['coverageCountsText'] ?? '0 of 0' }}
          </span>
        </div>
        <p class="mb-0">People who attended at least once</p>
        <div class="mt-4">
          <p class="mb-2 d-flex align-items-center justify-content-between">
            <span>Sessions in range</span>
            <span>{{ $summary['sessionsSpan'] ?? '—' }}</span>
          </p>
          <div class="progress w-100" style="height:6px;">
            <div class="progress-bar bg-grd-success"
                 style="width: {{ $summary['coveragePct'] ?? 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 🟨 Card 2 – Consistency --}}
  <div class="col">
    <div class="card rounded-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h2 class="mb-0">{{ $summary['consistencyPct'] ?? 0 }}%</h2>
          <span class="dash-lable d-flex align-items-center gap-1 rounded mb-0
                       {{ ($summary['consistencyPct'] ?? 0) >= 90 ? 'bg-success text-success bg-opacity-10' :
                          (($summary['consistencyPct'] ?? 0) >= 70 ? 'bg-warning text-warning bg-opacity-10' : 'bg-danger text-danger bg-opacity-10') }}">
            {{ $summary['consistencyText'] ?? 'Met: 0 | Left: 0' }}
          </span>
        </div>
        <p class="mb-0">People meeting attendance target (≥ {{ $summary['targetSessions'] ?? '—' }} sessions)</p>
        <div class="mt-4">
          <p class="mb-2 d-flex align-items-center justify-content-between">
            <span>Targeted people</span>
            <span>{{ $summary['targetedPeople'] ?? '—' }}</span>
          </p>
          <div class="progress w-100" style="height:6px;">
            <div class="progress-bar bg-grd-warning"
                 style="width: {{ $summary['consistencyPct'] ?? 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 🟦 Card 3 – Session Strength --}}
  <div class="col">
    <div class="card rounded-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h2 class="mb-0">{{ $summary['avgPerSession'] ?? 0 }}</h2>
          <span class="dash-lable bg-primary text-primary bg-opacity-10">
            {{ $summary['strengthText'] ?? 'Δ vs prev: —' }}
          </span>
        </div>
        <p class="mb-0">Average attendees per session</p>
        <div class="mt-4">
          <p class="mb-2 d-flex align-items-center justify-content-between">
            <span>Capacity goal</span>
            <span>{{ $summary['sessionGoal'] ?? '—' }}</span>
          </p>
          <div class="progress w-100" style="height:6px;">
            <div class="progress-bar bg-grd-primary"
                 style="width: {{ $summary['strengthPctOfGoal'] ?? 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
    <hr>

    {{-- (Your summary cards can stay as you already had them) --}}

    {{-- Table --}}
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="attendanceTable" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Meeting</th>
                <th>Volunteer</th>
                <th>Campus</th>
                <th>Department</th>
                <th>Ministry</th>
                <th>Status</th>
                <th>Recorded</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($attendanceRows as $row)
                <tr>
                  <td>{{ \Illuminate\Support\Carbon::parse($row->meeting_date ?? $row->created_at)->format('Y-m-d') }}</td>
                  <td>{{ $row->meeting   ?? '—' }}</td>
                  <td>{{ $row->employee  ?? '—' }}</td>
                  <td>{{ $row->campus    ?? '—' }}</td>
                  <td>{{ $row->dept      ?? '—' }}</td>
                  <td>{{ $row->ministry  ?? '—' }}</td>
                  <td>
                    @php $st = $row->status ?? '—'; @endphp
                    @if($st === 'late')
                      <span class="badge bg-warning text-dark">Late</span>
                    @elseif(in_array($st, ['present','on_time'], true))
                      <span class="badge bg-success">Present</span>
                    @elseif($st === 'absent')
                      <span class="badge bg-danger">Absent</span>
                    @else
                      <span class="badge bg-secondary">{{ $st }}</span>
                    @endif
                  </td>
                  <td>{{ \Illuminate\Support\Carbon::parse($row->created_at ?? $row->meeting_date)->format('Y-m-d H:i') }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th>Date</th>
                <th>Meeting</th>
                <th>Volunteer</th>
                <th>Campus</th>
                <th>Department</th>
                <th>Ministry</th>
                <th>Status</th>
                <th>Recorded</th>
              </tr>
            </tfoot>
          </table>
        </div>
        @if(method_exists($attendanceRows,'links'))
          <div class="mt-2">{{ $attendanceRows->appends(request()->query())->links() }}</div>
        @endif
      </div>
    </div>

  </div>
</main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && $.fn.DataTable) {
      $('#attendanceTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [{ targets: [2,3,4,5,6], orderable: false }]
      });
    } else {
      console.warn('DataTables not found. Ensure JS/CSS are loaded.');
    }
  });
</script>
@endpush