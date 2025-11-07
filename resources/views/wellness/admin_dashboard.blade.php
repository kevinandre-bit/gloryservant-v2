@extends('layouts.admin')

@section('meta')
  <title>Wellness | Admin Dashboard</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">insights</i> Wellness – Admin Dashboard</h5>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <strong>There was a problem:</strong>
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    {{-- KPI cards --}}
    <div class="row g-3 mb-3">
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class=" small">Open (All)</div>
          <div class="display-6 fw-semibold">{{ (int)($kpis->open_total ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class=" small">Closed</div>
          <div class="display-6 fw-semibold">{{ (int)($kpis->closed_total ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-4 col-xl-2">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class=" small">High</div>
          <div class="display-6 fw-semibold text-danger">{{ (int)($kpis->high_total ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-4 col-xl-2">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class=" small">Medium</div>
          <div class="display-6 fw-semibold text-warning">{{ (int)($kpis->med_total ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-4 col-xl-2">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class=" small">Low</div>
          <div class="display-6 fw-semibold text-success">{{ (int)($kpis->low_total ?? 0) }}</div>
        </div></div>
      </div>
    </div>

    {{-- Filters --}}
    <form class="card rounded-4 mb-3" method="get" action="{{ route('wellness.admin.dashboard') }}">
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="">All</option>
              @foreach(['OPEN','UNDER_REVIEW','IN_PROGRESS','WAITING_CONSULT','CLOSED'] as $s)
                <option value="{{ $s }}" {{ $status===$s?'selected':'' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Stage</label>
            <select class="form-select" name="stage">
              <option value="">All</option>
              @foreach(['ml'=>'Ministry Leader','wtl'=>'Wellness Team Lead','mo'=>'Overseer'] as $key=>$lbl)
                <option value="{{ $key }}" {{ $stage===$key?'selected':'' }}>{{ $lbl }}</option>
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
            <label class="form-label">From</label>
            <input type="date" class="form-control" name="from" value="{{ $from }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">To</label>
            <input type="date" class="form-control" name="to" value="{{ $to }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Search</label>
            <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="name / group / text">
          </div>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">Apply</button>
            <a class="btn btn-outline-secondary" href="{{ route('wellness.admin.dashboard') }}">Reset</a>
          </div>
        </div>
      </div>
    </form>

    {{-- Status breakdown --}}
    <div class="card rounded-4 mb-3">
      <div class="card-body">
        <div class="d-flex flex-wrap gap-3">
          @foreach($statusBreakdown as $row)
            <span class="badge bg-light ">
              {{ $row->current_status }}: <strong>{{ $row->c }}</strong>
            </span>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Cases table --}}
    <div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="wellnessCasesTable" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Case #</th>
            <th>Volunteer</th>
            <th>Group</th>
            <th>Status</th>
            <th>Stage</th>
            <th>Severity</th>
            <th>ETA</th>
            <th>Assigned</th>
            <th>Opened</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cases as $c)
            @php
              $sevClass = $c->severity==='high' ? 'bg-danger' : ($c->severity==='medium'?'bg-warning ':'bg-success');
              $etaBadge = $c->is_overdue ? 'bg-danger' : 'bg-light ';
            @endphp
            <tr>
              <td>
                <a href="{{ route('wellness.cases.show',$c->id) }}" class="fw-semibold">#{{ $c->id }}</a>
                @if(!empty($c->title))
                  <div class="small ">{{ $c->title }}</div>
                @elseif(!empty($c->summary))
                  <div class="small ">{{ \Illuminate\Support\Str::limit($c->summary, 40) }}</div>
                @endif
              </td>
              <td>{{ $c->volunteer_name }}</td>
              <td>{{ $c->group_name }}</td>
              <td><span class="badge bg-light ">{{ $c->current_status }}</span></td>
              <td><span class="badge bg-secondary">{{ strtoupper($c->current_stage) }}</span></td>
              <td><span class="badge {{ $sevClass }}">{{ strtoupper($c->severity) }}</span></td>
              <td><span class="badge {{ $etaBadge }}">{{ $c->eta_date ?? '—' }}</span></td>
              <td>
                <form method="post" action="{{ route('wellness.admin.cases.assign',$c->id) }}">
                  @csrf
                  <select name="assigned_to_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">—</option>
                    @foreach($assignable as $u)
                      <option value="{{ $u->id }}" {{ (int)$c->assigned_to_id===(int)$u->id?'selected':'' }}>
                        {{ $u->name }}
                      </option>
                    @endforeach
                  </select>
                </form>
              </td>
              <td>
                <div class="small">{{ \Carbon\Carbon::parse($c->created_at)->diffForHumans() }}</div>
                <div class="small ">
                  Last: {{ $c->last_activity_at ? \Carbon\Carbon::parse($c->last_activity_at)->diffForHumans() : '—' }}
                </div>
              </td>
              <td>
                <a href="{{ route('wellness.cases.show',$c->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th>Case #</th>
            <th>Volunteer</th>
            <th>Group</th>
            <th>Status</th>
            <th>Stage</th>
            <th>Severity</th>
            <th>ETA</th>
            <th>Assigned</th>
            <th>Opened</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

  </div>
</main>
@endsection