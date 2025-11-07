@extends('layouts.admin')

@section('meta')
  <title>Time Tracking — KPI Dashboard</title>
@endsection

@section('content')
@php
  // ---- Safe fallbacks so the view renders even if controller is not fully wired (remove later) ----
  $filters = $filters ?? [
    'from'        => now()->startOfWeek()->toDateString(),
    'to'          => now()->endOfWeek()->toDateString(),
    'group_by'    => 'day',
    'ministry_id' => null,
    'campus_id'   => null,
    'status'      => 'all',
  ];

  // Accept stdClass or array for lookups (define ONCE)
  $rowId = function($r){ return is_array($r) ? ($r['id'] ?? null) : ($r->id ?? null); };
  $rowName = function($r){ return is_array($r) ? ($r['name'] ?? ($r['campus'] ?? null)) : ($r->name ?? ($r->campus ?? null)); };

  // Minimal defaults (remove once controller sends real metrics)
  $kpi = $kpi ?? [
    'unique_people'    => ['value'=>128, 'delta'=>null],
    'on_time_rate'     => ['value'=>89.3, 'delta'=>null],
    'late_count'       => ['value'=>16,  'delta'=>null],
    'avg_minutes_late' => ['value'=>5.8, 'delta'=>null],
    'missed_out'       => ['value'=>7,   'delta'=>null],
    'total_hours'      => ['value'=>412.5,'delta'=>null],
  ];

  $trend = $trend ?? [
    'labels' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    'series' => [
      ['label'=>'Unique People','data'=>[36,42,48,51,59,50,46]],
    ]
  ];

  $stackedPunctuality = $stackedPunctuality ?? [
    'labels' => $trend['labels'],
    'series' => [
      ['label'=>'On-time','data'=>[31,35,41,43,48,42,40]],
      ['label'=>'Late','data'=>   [4, 6, 5, 6, 8, 6, 5]],
      ['label'=>'Early','data'=>  [1, 1, 2, 2, 3, 2, 1]],
    ]
  ];

  if (!isset($heatmap)) {
    $hrs = ['06','07','08','09','10','11','12','13','14','15','16','17'];
    $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    $vals = [];
    foreach ($days as $di => $_d) {
      $row = [];
      foreach ($hrs as $hi => $_h) $row[] = (($di+1)*($hi%4)) + ($di%2 ? 3 : 1);
      $vals[] = $row;
    }
    $heatmap = ['hours'=>$hrs,'days'=>$days,'values'=>$vals];
  }

  // Breakdown defaults — match controller keys (teamBreakdown with 'team')
  $teamColumnTips = [
    'team'           => 'Name of the ministry or team.',
    'unique_people'  => 'Distinct team members with attendance in the period.',
    'checkins'       => 'Total attendance records captured for the team.',
    'on_time_rate'   => 'Percent of team clock-ins at or before the on-time threshold.',
    'avg_minutes_late'=> 'Average lateness in minutes for late arrivals within the team.',
    'total_hours'    => 'Total hours clocked by the team (sum of minutes between in/out).',
    'clock_display'  => 'Total clocked time formatted as hours and minutes.',
    'missed_out'     => 'Count of team records missing a clock-out.',
  ];
  $teamBreakdown = $teamBreakdown ?? [
    ['team'=>'Usher','unique_people'=>33,'checkins'=>118,'on_time_rate'=>92.1,'avg_minutes_late'=>4.0,'total_hours'=>352.4,'total_minutes'=>21144,'missed_out'=>1],
    ['team'=>'Media','unique_people'=>26,'checkins'=> 96,'on_time_rate'=>86.4,'avg_minutes_late'=>6.9,'total_hours'=>285.7,'total_minutes'=>17142,'missed_out'=>3],
  ];

  // Exceptions defaults — match controller keys (team & location)
  $exceptionColumnTips = [
    'when'     => 'Timestamp of the attendance exception.',
    'idno'     => 'Employee or volunteer identifier.',
    'name'     => 'Person involved in the exception.',
    'team'     => 'Team or ministry the person belongs to.',
    'location' => 'Campus or location associated with the record.',
    'issue'    => 'Reason flagged (e.g., missing clock-out or late).',
  ];
  $exceptions = $exceptions ?? [
    ['when'=>'2025-10-25 09:03','idno'=>'P001','name'=>'John Doe','team'=>'Usher','location'=>'Main Campus','issue'=>'Late 10 min'],
    ['when'=>'2025-10-25 12:47','idno'=>'P008','name'=>'Jane Smith','team'=>'Media','location'=>'Downtown','issue'=>'Missing clock-out'],
  ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <div class="d-flex align-items-center gap-2">
        <i class="material-icons-outlined text-primary">query_stats</i>
        <h5 class="mb-0">Time Tracking — KPI Dashboard</h5>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="post"
              action="{{ route('admin.time_tracking.clockout_open') }}"
              class="d-inline">
          @csrf
          <button type="submit"
                  class="btn btn-danger btn-sm"
                  onclick="return confirm('Clock out everyone currently clocked in?');">
            <i class="material-icons-outlined me-1">logout</i> Clock Out All
          </button>
        </form>
        <a href="{{ route('admin.time_tracking.dashboard.export', request()->query()) }}"
           class="btn btn-outline-secondary btn-sm">
          <i class="material-icons-outlined me-1">file_download</i> Export CSV
        </a>
      </div>
    </div>

    @if(session('clockout_ok'))
      <div class="alert alert-success alert-dismissible fade show small py-2 px-3 mb-3" role="alert">
        {{ session('clockout_ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @elseif(session('clockout_info'))
      <div class="alert alert-info alert-dismissible fade show small py-2 px-3 mb-3" role="alert">
        {{ session('clockout_info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    {{-- Filters: single line, sticky --}}
    <form class="card rounded-4 mb-3 shadow-sm sticky-top" style="top:70px;z-index:2;"
      method="get" action="{{ route('admin.time_tracking.dashboard') }}">
  <div class="card-body py-2">
    <div class="d-flex flex-wrap align-items-end gap-2 w-100">

      {{-- From --}}
      <div class="flex-fill" style="min-width:140px;">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="from" class="form-control form-control-sm"
               value="{{ $filters['from'] ?? '' }}">
      </div>

      {{-- To --}}
      <div class="flex-fill" style="min-width:140px;">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="to" class="form-control form-control-sm"
               value="{{ $filters['to'] ?? '' }}">
      </div>

      {{-- Group by --}}
      <div class="flex-fill" style="min-width:130px;">
        <label class="form-label small mb-1">Group by</label>
        <select name="group_by" class="form-select form-select-sm">
          @foreach(['day'=>'Day','week'=>'Week','month'=>'Month'] as $k=>$lbl)
            <option value="{{ $k }}" @selected(($filters['group_by'] ?? 'day')===$k)>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>

      {{-- Ministry --}}
      <div class="flex-fill" style="min-width:200px;">
        <label class="form-label small mb-1">Ministry</label>
        <select name="ministry_id" class="form-select form-select-sm">
          <option value="">All ministries</option>
          @foreach(($ministries ?? []) as $m)
            @php $mid = $rowId($m); @endphp
            <option value="{{ $mid }}"
              @selected((string)($filters['ministry_id'] ?? '') === (string)$mid)>
              {{ $rowName($m) }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Campus --}}
      <div class="flex-fill" style="min-width:200px;">
        <label class="form-label small mb-1">Campus</label>
        <select name="campus_id" class="form-select form-select-sm">
          <option value="">All campuses</option>
          @foreach(($campuses ?? []) as $c)
            @php
              $cid   = $rowId($c);
              $cname = $rowName($c) ?? $cid;
            @endphp
            <option value="{{ $cid }}"
              @selected((string)($filters['campus_id'] ?? '') === (string)$cid)>
              {{ $cname }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Status --}}
      <div class="flex-fill" style="min-width:160px;">
        <label class="form-label small mb-1">Status</label>
        @php $status = $filters['status'] ?? 'all'; @endphp
        <select name="status" class="form-select form-select-sm">
          @foreach([
            'all'=>'All',
            'on_time'=>'On time',
            'late'=>'Late',
            'early'=>'Early',
            'missing_in'=>'Missed clock-in',
            'missing_out'=>'Missed clock-out'
          ] as $k=>$lbl)
            <option value="{{ $k }}" @selected($status===$k)>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>

      {{-- Apply --}}
      <div class="flex-shrink-0 ms-auto">
        <button class="btn btn-primary btn-sm px-3" style="margin-top:1.5rem;">
          <i class="material-icons-outlined me-1" style="font-size:16px;">filter_alt</i> Apply
        </button>
      </div>

    </div>
  </div>
</form>

    {{-- KPI cards --}}
    @php
  $cardTips = [
    'unique_people' => 'Distinct people who clocked in during the selected period.',
    'on_time_rate'  => 'Percentage of valid clock-ins at or before the on-time threshold.',
    'late_count'    => 'Total number of late clock-ins (after the on-time threshold).',
    'avg_minutes_late' => 'Average minutes late for late arrivals only.',
    'missed_out'    => 'Count of records missing a clock-out time.',
    'total_hours'   => 'Sum of clocked minutes converted to hours for the filtered period.',
  ];
  $cards = [
    ['title'=>'Unique People',       'key'=>'unique_people'],
    ['title'=>'On-time Rate %',      'key'=>'on_time_rate'],
    ['title'=>'Late (count)',        'key'=>'late_count'],
    ['title'=>'Avg Minutes Late',    'key'=>'avg_minutes_late'],
    ['title'=>'Missed clock-out',    'key'=>'missed_out'],
    ['title'=>'Total Hours Clocked', 'key'=>'total_hours'],
  ];
    @endphp
    <div class="row g-3 mb-3">
      @foreach($cards as $c)
        @php
          $val   = $kpi[$c['key']]['value'] ?? null;
          $delta = $kpi[$c['key']]['delta'] ?? null;
          $valTxt   = is_null($val) ? '—' : (is_numeric($val) ? number_format($val, is_float($val)?1:0) : $val);
          $deltaTxt = is_null($delta) ? '' : (($delta>0?'▲ ':'▼ ').number_format(abs($delta), is_float($delta)?1:0));
          $deltaCls = is_null($delta) ? 'text-muted' : ($delta>=0 ? 'text-success':'text-danger');
        @endphp
        <div class="col-6 col-lg-4 col-xl-2">
          <div class="card rounded-4 h-100 shadow-sm">
            <div class="card-body">
              <div class="small text-muted d-flex align-items-center gap-1">
                <span>{{ $c['title'] }}</span>
                <i class="material-icons-outlined text-muted small align-middle info-icon"
                   data-bs-toggle="tooltip"
                   title="{{ $cardTips[$c['key']] ?? 'Metric description' }}">info</i>
              </div>
              <div class="d-flex align-items-end justify-content-between mt-1">
                <div class="display-6 fw-semibold">{{ $valTxt }}</div>
                <span class="small {{ $deltaCls }}">{{ $deltaTxt }}</span>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-3">
      <div class="col-lg-7">
        <div class="card rounded-4 h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0 d-flex align-items-center gap-1">
                <span>Unique People Trend</span>
                <i class="material-icons-outlined text-muted small info-icon"
                   data-bs-toggle="tooltip"
                   title="Distinct people clocking in grouped by the selected interval.">info</i>
              </h6>
              <span class="text-muted small">{{ strtoupper($filters['group_by'] ?? 'DAY') }}</span>
            </div>
            <div class="chart-area chart-area-md">
              <canvas id="chartTrend"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card rounded-4 h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0 d-flex align-items-center gap-1">
                <span>On-time / Late / Early</span>
                <i class="material-icons-outlined text-muted small info-icon"
                   data-bs-toggle="tooltip"
                   title="Stacked counts of clock-ins classified as on-time, late, or early by the selected interval.">info</i>
              </h6>
              <span class="text-muted small">{{ strtoupper($filters['group_by'] ?? 'DAY') }}</span>
            </div>
            <div class="chart-area chart-area-md">
              <canvas id="chartStacked"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Heatmap & Donut --}}
    <div class="row g-3 mb-3">
      <div class="col-lg-7">
        <div class="card rounded-4 h-100 shadow-sm">
          <div class="card-body">
            <h6 class="mb-2 d-flex align-items-center gap-1">
              <span>Check-ins by Hour (Heatmap)</span>
              <i class="material-icons-outlined text-muted small info-icon"
                 data-bs-toggle="tooltip"
                 title="Intensity of clock-ins across days of week and hours of day.">info</i>
            </h6>
            <div class="table-responsive">
              <table class="table table-bordered align-middle mb-0" id="heatTable">
                <thead>
                  <tr class="table-light">
                    <th class="text-nowrap">Day \ Hour</th>
                    @foreach(($heatmap['hours'] ?? []) as $h)
                      <th class="text-center small text-muted">{{ $h }}</th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  @foreach(($heatmap['days'] ?? []) as $di => $day)
                    <tr>
                      <th class="text-nowrap">{{ $day }}</th>
                      @foreach(($heatmap['hours'] ?? []) as $hi => $h)
                        @php $v = $heatmap['values'][$di][$hi] ?? 0; @endphp
                        <td class="text-center heat-cell" data-val="{{ $v }}">
                          <span class="small">{{ $v ?: '' }}</span>
                        </td>
                      @endforeach
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="small text-muted mt-1">Darker = more activity</div>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card rounded-4 h-100 shadow-sm">
          <div class="card-body">
            <h6 class="mb-2 d-flex align-items-center gap-1">
              <span>Punctuality Split</span>
              <i class="material-icons-outlined text-muted small info-icon"
                 data-bs-toggle="tooltip"
                 title="Share of on-time, late, and early clock-ins for the filtered period.">info</i>
            </h6>
            <div class="chart-area chart-area-sm">
              <canvas id="chartDonut"></canvas>
            </div>
            <div class="text-muted small mt-2">Share of on-time, late, and early in the selected period.</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Ministry (Team) breakdown --}}
    <div class="card rounded-4 mb-3 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>
                  Ministry
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['team'] }}">info</i>
                </th>
                <th class="text-end">
                  People
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['unique_people'] }}">info</i>
                </th>
                <th class="text-end">
                  Check-ins
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['checkins'] }}">info</i>
                </th>
                <th class="text-end">
                  On-time %
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['on_time_rate'] }}">info</i>
                </th>
                <th class="text-end">
                  Avg min late
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['avg_minutes_late'] }}">info</i>
                </th>
                <th class="text-end">
                  Total hrs
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['total_hours'] }}">info</i>
                </th>
                <th class="text-end">
                  Clocked time
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['clock_display'] }}">info</i>
                </th>
                <th class="text-end">
                  Missed out
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $teamColumnTips['missed_out'] }}">info</i>
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse(($teamBreakdown ?? []) as $row)
                @php
                  $clockMinutes = (int)($row['total_minutes'] ?? 0);
                  $clockDisplay = $clockMinutes > 0
                    ? sprintf('%d:%02d', intdiv($clockMinutes, 60), $clockMinutes % 60)
                    : '0:00';
                @endphp
                <tr>
                  <td>{{ $row['team'] }}</td>
                  <td class="text-end">{{ number_format($row['unique_people'] ?? 0) }}</td>
                  <td class="text-end">{{ number_format($row['checkins'] ?? 0) }}</td>
                  <td class="text-end">{{ is_numeric($row['on_time_rate'] ?? null) ? number_format($row['on_time_rate'],1) : '—' }}</td>
                  <td class="text-end">{{ is_numeric($row['avg_minutes_late'] ?? null) ? number_format($row['avg_minutes_late'],1) : '—' }}</td>
                  <td class="text-end">{{ is_numeric($row['total_hours'] ?? null) ? number_format($row['total_hours'],1) : '—' }}</td>
                  <td class="text-end">{{ $clockDisplay }}</td>
                  <td class="text-end">{{ number_format($row['missed_out'] ?? 0) }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center text-muted p-4">No data for the selected filters.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Exceptions --}}
    <div class="card rounded-4 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>
                  When
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $exceptionColumnTips['when'] }}">info</i>
                </th>
                <th>
                  ID
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $exceptionColumnTips['idno'] }}">info</i>
                </th>
                <th>
                  Name
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $exceptionColumnTips['name'] }}">info</i>
                </th>
                <th>
                  Ministry
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $exceptionColumnTips['team'] }}">info</i>
                </th>
                <th>
                  Campus
                  <i class="material-icons-outlined text-muted small align-middle info-icon"
                     data-bs-toggle="tooltip"
                     title="{{ $exceptionColumnTips['location'] }}">info</i>
                </th>
                <th>
                  Issue
                  <i class="material-icons-outlined text-muted small align-middle"
                     data-bs-toggle="tooltip"
                     title="{{ $exceptionColumnTips['issue'] }}">info</i>
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse(($exceptions ?? []) as $e)
                @php
                  $issue = strtolower($e['issue'] ?? '');
                  $cls = str_contains($issue,'late') ? 'bg-warning text-dark'
                       : (str_contains($issue,'missing') ? 'bg-danger' : 'bg-secondary');
                @endphp
                <tr>
                  <td class="text-nowrap">{{ $e['when'] }}</td>
                  <td>{{ $e['idno'] }}</td>
                  <td>{{ $e['name'] }}</td>
                  <td>{{ $e['team'] }}</td>
                  <td>{{ $e['location'] }}</td>
                  <td><span class="badge {{ $cls }}">{{ $e['issue'] }}</span></td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted p-4">No exceptions detected for this period.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>
@endsection

@push('styles')
<style>
  .heat-cell{ background:#f8f9fa; transition: background-color .2s ease, color .2s ease; }
  .sticky-top { backdrop-filter: blur(4px); }
  .chart-area{ position:relative; width:100%; }
  .chart-area-md{ min-height:260px; height:260px; }
  .chart-area-sm{ min-height:220px; height:220px; }
  .chart-area canvas{ width:100% !important; height:100% !important; }
  .filter-col{ min-width:140px; flex:1 1 auto; }
  .filter-col.wide{ min-width:200px; }
  .filter-col-btn{ flex:0 0 auto; }
  .info-icon{ font-size:16px; cursor:help; }
  @media (max-width: 991.98px){
    .chart-area-md{ height:220px; }
    .chart-area-sm{ height:200px; }
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(() => {
  const trend   = @json($trend ?? ['labels'=>[],'series'=>[['label'=>'Unique People','data'=>[]]]]);
  const stacked = @json($stackedPunctuality ?? ['labels'=>[],'series'=>[]]);
  const donutEl = document.getElementById('chartDonut');

  // Trend (line)
  const trendEl = document.getElementById('chartTrend');
  if (trendEl && Array.isArray(trend.labels) && trend.labels.length) {
    new Chart(trendEl, {
      type: 'line',
      data: {
        labels: trend.labels,
        datasets: (trend.series || []).map(s => ({
          label: s.label,
          data: s.data,
          tension: .35,
          borderWidth: 2,
          pointRadius: 2,
          fill: false
        }))
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { ticks: { autoSkip: true } }, y: { beginAtZero: true } }
      }
    });
  }

  // Stacked (bar)
  const stackEl = document.getElementById('chartStacked');
  if (stackEl && Array.isArray(stacked.labels) && stacked.labels.length && Array.isArray(stacked.series) && stacked.series.length) {
    new Chart(stackEl, {
      type: 'bar',
      data: {
        labels: stacked.labels,
        datasets: stacked.series.map(s => ({ label: s.label, data: s.data, stack: 'p', borderWidth: 0 }))
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
      }
    });

    // Donut from totals
    if (donutEl) {
      const sums = {};
      stacked.series.forEach(ds => { sums[ds.label] = (ds.data || []).reduce((a,b)=>a+(+b||0),0); });
      const labels = Object.keys(sums);
      const data = labels.map(k => sums[k]);
      if (data.some(v => v>0)) {
        new Chart(donutEl, {
          type: 'doughnut',
          data: { labels, datasets: [{ data }] },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position:'bottom' } } }
        });
      }
    }
  }

  // Heatmap color
  (function(){
    const cells = document.querySelectorAll('#heatTable td.heat-cell');
    if (!cells.length) return;
    let max = 0;
    cells.forEach(td => { const v = +td.dataset.val || 0; if (v>max) max = v; });
    cells.forEach(td => {
      const v = +td.dataset.val || 0;
      const op = max ? (0.08 + 0.92 * (v / max)) : 0;
      td.style.backgroundColor = `rgba(13,110,253,${op})`; // Bootstrap primary with alpha
      td.style.color = op > .5 ? '#fff' : '';
    });
  })();

  // Enable Bootstrap tooltips
  if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }

})();
</script>
@endpush
