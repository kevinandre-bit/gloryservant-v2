@extends('layouts.radio_layout')

@section('content')

<main class="main-wrapper radio-dashboard-skin">
  <div class="main-content radio-dashboard-content">
    @php
      $total          = $kpis['total'] ?? 0;
      $onAir          = $kpis['onAir'] ?? 0;
      $onAirPct       = $kpis['onAirPct'] ?? 0;
      $checkinsToday  = $kpis['checkinsToday'] ?? 0;
      $checkinsYday   = $kpis['checkinsYesterday'] ?? 0;
      $incidentCount  = $kpis['incidentCount'] ?? 0;
      $topIssue       = $kpis['topIssue'] ?? '—';
      $lastStation    = $kpis['lastCheckStation'] ?? '—';
      $lastAt         = optional($kpis['lastCheckAt'] ?? null)->format('Y-m-d H:i') ?? '—';
      $lastUser       = $kpis['lastCheckUser'] ?? '—';
      $staleCount     = $kpis['staleCount'] ?? 0;
    @endphp

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
      <div class="breadcrumb-title pe-3">Radio Report</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"></li>
          <li class="breadcrumb-item active" aria-current="page">Check-ins</li>
        </ol>
      </div>
    </div>

    <form method="get" class="card radio-panel p-4 mb-4">
      <div class="d-flex flex-wrap align-items-end gap-3 w-100">
        <div class="d-flex flex-column gap-2">
          <label class="form-label small text-secondary mb-0 text-uppercase">Date range</label>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            @php $preset = request('preset'); @endphp
            <div class="btn-group btn-group-sm" role="group" aria-label="Presets">
              <a href="{{ request()->fullUrlWithQuery(['preset'=>'today','from'=>null,'to'=>null]) }}"
                 class="btn {{ $preset==='today'?'btn-radio-primary':'btn-radio-outline' }}">Today</a>
              <a href="{{ request()->fullUrlWithQuery(['preset'=>'7d','from'=>null,'to'=>null]) }}"
                 class="btn {{ $preset==='7d'?'btn-radio-primary':'btn-radio-outline' }}">7d</a>
              <a href="{{ request()->fullUrlWithQuery(['preset'=>'30d','from'=>null,'to'=>null]) }}"
                 class="btn {{ $preset==='30d'?'btn-radio-primary':'btn-radio-outline' }}">30d</a>
            </div>
            <span class="vr text-muted"></span>
            <div class="input-group input-group-sm" style="width: 220px;">
              <span class="input-group-text"><i class="material-icons-outlined fs-6">event</i></span>
              <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="input-group input-group-sm" style="width: 220px;">
              <span class="input-group-text"><i class="material-icons-outlined fs-6">event</i></span>
              <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
          </div>
        </div>

        <div class="d-flex flex-column gap-1" style="min-width: 180px;">
          <label class="form-label small text-secondary mb-0 text-uppercase">Group</label>
          @php $g = request('group'); @endphp
          <select class="form-select form-select-sm" name="group">
            <option value="">All</option>
            <option value="shekinah" {{ $g==='shekinah'?'selected':'' }}>Shekinah</option>
            <option value="tekno"    {{ $g==='tekno'?'selected':'' }}>Tekno</option>
            <option value="other"    {{ $g==='other'?'selected':'' }}>Other</option>
          </select>
        </div>

        <div class="d-flex flex-column gap-1">
          <label class="form-label small text-secondary mb-0 text-uppercase">Stations</label>
          @php $selStations = (array) (request('stations') ?? []); @endphp
          <div class="dropdown">
            <button class="btn btn-sm btn-radio-outline dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btnStations">
              Stations (<span id="stationsCount">{{ count($selStations) }}</span>)
            </button>
            <div class="dropdown-menu p-2 shadow-lg" style="max-height: 260px; overflow:auto; min-width: 280px;">
              <div class="mb-2">
                <input type="text" class="form-control form-control-sm" placeholder="Search…" oninput="filterList(this, '#stationsList li')">
              </div>
              <ul id="stationsList" class="list-unstyled mb-0">
                @foreach(($filters['stationsAll'] ?? collect()) as $st)
                  <li class="mb-1">
                    <label class="d-flex align-items-center gap-2">
                      <input type="checkbox" name="stations[]" value="{{ $st->id }}" class="form-check-input"
                             {{ in_array($st->id, $selStations)?'checked':'' }}
                             onchange="updateCount('#stationsCount','input[name=\'stations[]\']')">
                      <span class="small">{{ $st->name }}</span>
                    </label>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>

        <div class="d-flex flex-column gap-1">
          <label class="form-label small text-secondary mb-0 text-uppercase">Status</label>
          @php
            $statusOpts = ['on_air','link','power','internet','no_frequency','computer_issue','payment_issue','unspecified','issue','offline'];
            $selStatus  = (array) (request('status') ?? []);
          @endphp
          <div class="dropdown">
            <button class="btn btn-sm btn-radio-outline dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btnStatus">
              Status (<span id="statusCount">{{ count($selStatus) }}</span>)
            </button>
            <div class="dropdown-menu p-2 shadow-lg" style="max-height: 260px; overflow:auto; min-width: 220px;">
              @foreach($statusOpts as $s)
                <label class="dropdown-item d-flex align-items-center gap-2">
                  <input type="checkbox" name="status[]" value="{{ $s }}" class="form-check-input"
                         {{ in_array($s,$selStatus)?'checked':'' }}
                         onchange="updateCount('#statusCount','input[name=\'status[]\']')">
                  <span class="small">{{ ucwords(str_replace('_',' ',$s)) }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>

        <div class="ms-auto d-flex align-items-end gap-2">
          <a href="{{ route('tech.checkins.index') }}" class="btn btn-radio-outline btn-sm">Reset</a>
          <button class="btn btn-radio-primary btn-sm">Apply Filters</button>
        </div>
      </div>

      @php
        $chips = [];
        $fromReq = request('from');
        $toReq   = request('to');
        if ($preset)               $chips[] = ['label'=>"Preset: ".strtoupper($preset)];
        if ($fromReq || $toReq)    $chips[] = ['label'=>"From: ".($fromReq ?? '—')." · To: ".($toReq ?? '—')];
        if ($g)                    $chips[] = ['label'=>"Group: ".ucfirst($g)];
        if (!empty($selStations))  $chips[] = ['label'=>"Stations: ".count($selStations)];
        if (!empty($selStatus))    $chips[] = ['label'=>"Status: ".count($selStatus)];
      @endphp

      @if(count($chips))
        <div class="d-flex flex-wrap gap-2 mt-3">
          @foreach($chips as $c)
            <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border">{{ $c['label'] }}</span>
          @endforeach
        </div>
      @endif
    </form>

    <div class="row g-3 mb-4">
      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Stations On Air</span>
            <span class="radio-metric-icon icon-success">
              <i class="material-icons-outlined">broadcast_on_home</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $onAir }} / {{ $total }}</div>
          <div class="radio-metric-subtext">{{ $onAirPct }}% active</div>
          <div class="progress" style="height:6px;">
            <div class="progress-bar bg-success" style="width: {{ $onAirPct }}%"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Check-ins</span>
            <span class="radio-metric-icon icon-primary">
              <i class="material-icons-outlined">pending_actions</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $checkinsToday }}</div>
          <div class="radio-metric-subtext">Today · Yesterday {{ $checkinsYday }}</div>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Incidents (7d)</span>
            <span class="radio-metric-icon icon-warning">
              <i class="material-icons-outlined">report_gmailerrorred</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $incidentCount }}</div>
          <div class="radio-metric-subtext">Top issue: {{ ucwords(str_replace('_',' ',$topIssue)) }}</div>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Last Check-in</span>
            <span class="radio-metric-icon icon-info">
              <i class="material-icons-outlined">update</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $lastStation }}</div>
          <div class="radio-metric-subtext">{{ $lastAt }} · {{ $lastUser }} | Stale >24h: {{ $staleCount }}</div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-12 col-xl-6">
        <div class="card radio-panel p-4 h-100">
          <h6 class="mb-3 d-flex align-items-center gap-2">
            <span>Incident Trend (Daily)</span>
            <i class="material-icons-outlined text-muted info-icon" data-bs-toggle="tooltip" title="Daily count of incidents by status across the selected period.">info</i>
          </h6>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="text-muted">
                <tr>
                  <th>Date</th>
                  <th class="text-end">Link</th>
                  <th class="text-end">Power</th>
                  <th class="text-end">Internet</th>
                  <th class="text-end">Issue</th>
                  <th class="text-end">No Freq</th>
                  <th class="text-end">PC Issue</th>
                  <th class="text-end">Payment</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($trendDays ?? []) as $d)
                  <tr>
                    <td>{{ $d['date'] }}</td>
                    <td class="text-end">{{ $d['link'] ?? 0 }}</td>
                    <td class="text-end">{{ $d['power'] ?? 0 }}</td>
                    <td class="text-end">{{ $d['internet'] ?? 0 }}</td>
                    <td class="text-end">{{ $d['issue'] ?? 0 }}</td>
                    <td class="text-end">{{ $d['no_frequency'] ?? 0 }}</td>
                    <td class="text-end">{{ $d['computer_issue'] ?? 0 }}</td>
                    <td class="text-end">{{ $d['payment_issue'] ?? 0 }}</td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="text-secondary">No incidents in range.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-6">
        <div class="card radio-panel p-4 h-100">
          <h6 class="mb-3 d-flex align-items-center gap-2">
            <span>Incident Breakdown</span>
            <i class="material-icons-outlined text-muted info-icon" data-bs-toggle="tooltip" title="Distribution of incidents by status category and by responsibility.">info</i>
          </h6>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="small text-secondary mb-2">By Status</div>
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead class="text-muted">
                    <tr>
                      <th>Status</th>
                      <th class="text-end">Count</th>
                      <th class="text-end">%</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $totalInc = array_sum(array_values($breakdownStatus ?? []));
                      $order = ['link','power','internet','issue','no_frequency','computer_issue','payment_issue','unspecified','offline','on_air'];
                    @endphp
                    @forelse($order as $s)
                      @php $c = (int) (($breakdownStatus ?? [])[$s] ?? 0); @endphp
                      <tr>
                        <td>{{ ucwords(str_replace('_',' ',$s)) }}</td>
                        <td class="text-end">{{ $c }}</td>
                        <td class="text-end">{{ $totalInc? number_format($c*100/$totalInc,1):0 }}%</td>
                      </tr>
                    @empty
                      <tr><td colspan="3" class="text-secondary">No data.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="small text-secondary mb-2">By Responsibility</div>
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead class="text-muted">
                    <tr>
                      <th>Owner</th>
                      <th class="text-end">Count</th>
                      <th class="text-end">%</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $totR = array_sum(array_values($breakdownResp ?? []));
                      $owners = ['shekinah'=>'Shekinah','landlord'=>'Landlord','unknown'=>'Unknown'];
                    @endphp
                    @foreach($owners as $k=>$lbl)
                      @php $v = (int) (($breakdownResp ?? [])[$k] ?? 0); @endphp
                      <tr>
                        <td>{{ $lbl }}</td>
                        <td class="text-end">{{ $v }}</td>
                        <td class="text-end">{{ $totR? number_format($v*100/$totR,1):0 }}%</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-12 col-xl-7">
        <div class="card radio-panel p-4 h-100">
          <h6 class="mb-3 d-flex align-items-center gap-2">
            <span>Uptime by Station</span>
            <i class="material-icons-outlined text-muted info-icon" data-bs-toggle="tooltip" title="Average on-air percentage per station for the selected range.">info</i>
          </h6>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead class="text-muted">
                <tr>
                  <th>Station</th>
                  <th class="text-end">Check-ins</th>
                  <th class="text-end">On Air %</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($uptime ?? []) as $row)
                  @php
                    $stationName = data_get($row, 'station', data_get($row, 'station_name', '—'));
                    $checkinCount = data_get($row, 'count', data_get($row, 'checkins', data_get($row, 'total', 0)));
                    $pctValue = (float) data_get($row, 'pct', data_get($row, 'percentage', 0));
                  @endphp
                  <tr>
                    <td>{{ $stationName }}</td>
                    <td class="text-end">{{ $checkinCount }}</td>
                    <td class="text-end">{{ number_format($pctValue, 1) }}%</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-secondary">No data available.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-5">
        <div class="card radio-panel p-4 h-100">
          <h6 class="mb-3 d-flex align-items-center gap-2">
            <span>Stale Stations (&gt;24h)</span>
            <i class="material-icons-outlined text-muted info-icon" data-bs-toggle="tooltip" title="Stations without a check-in in the last 24 hours.">info</i>
          </h6>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="text-muted">
                <tr>
                  <th>Station</th>
                  <th class="text-end">Last Check-in</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($staleStations ?? []) as $row)
                  <tr>
                    <td>{{ $row['station'] }}</td>
                    <td class="text-end">{{ $row['last'] ?? '—' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-secondary">All stations healthy in this window.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="card radio-panel p-4 mb-3">
      <h6 class="mb-3 d-flex align-items-center gap-2">
        <span>Latest 20 Incidents</span>
        <i class="material-icons-outlined text-muted info-icon" data-bs-toggle="tooltip" title="Most recent incident reports with status, notes, and technicians.">info</i>
      </h6>
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead class="text-muted">
            <tr>
              <th>Time</th>
              <th>Station</th>
              <th>Status</th>
              <th>Responsibility</th>
              <th>Note</th>
              <th>Technician</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($latestIncidents ?? []) as $i)
              @php
                $s = strtolower(str_replace([' ','-'],'_', $i['status'] ?? ''));
                if ($s==='online') $s='on_air';
                $badge='bg-light text-secondary';
                if     ($s==='on_air')         $badge='bg-success';
                elseif ($s==='link')           $badge='bg-danger';
                elseif ($s==='power')          $badge='bg-dark';
                elseif ($s==='issue')          $badge='bg-warning text-secondary';
                elseif ($s==='internet')       $badge='bg-primary';
                elseif ($s==='no_frequency')   $badge='bg-secondary';
                elseif ($s==='computer_issue') $badge='bg-info';
                elseif ($s==='payment_issue')  $badge='bg-danger';
              @endphp
              <tr>
                <td>{{ $i['time'] ?? '—' }}</td>
                <td>{{ $i['station'] ?? '—' }}</td>
                <td><span class="badge {{ $badge }}">{{ strtoupper(str_replace('_',' ',$s)) }}</span></td>
                <td>{{ $i['responsibility'] ?? '—' }}</td>
                <td class="text-truncate" style="max-width: 340px;" title="{{ $i['note'] ?? '' }}">{{ $i['note'] ?? '' }}</td>
                <td>{{ $i['tech'] ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-secondary">No incidents found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card radio-panel p-4">
      <h6 class="mb-3 d-flex align-items-center gap-2">
        <span>All Check-ins (detailed)</span>
        <i class="material-icons-outlined text-muted info-icon" data-bs-toggle="tooltip" title="Complete history of check-ins captured in the selected range.">info</i>
      </h6>
      <div class="table-responsive">
        <table id="checkinsTable" class="table table-striped align-middle">
          <thead class="text-muted">
            <tr>
              <th>Date</th>
              <th>Time</th>
              <th>Station</th>
              <th>Group</th>
              <th>Status</th>
              <th>Responsibility</th>
              <th>Technician</th>
              <th>Note</th>
            </tr>
          </thead>
          <tbody>
            @foreach($checkins as $c)
              @php
                $dt = \Carbon\Carbon::parse($c->created_at);
                $st = strtolower(str_replace([' ','-'],'_', $c->status));
                if ($st==='online') $st='on_air';
                $label = strtoupper(str_replace('_',' ', $st));
                $cls = 'bg-light text-secondary';
                if     ($st==='on_air')         $cls='bg-success';
                elseif ($st==='link')           $cls='bg-danger';
                elseif ($st==='power')          $cls='bg-dark';
                elseif ($st==='issue')          $cls='bg-warning text-secondary';
                elseif ($st==='internet')       $cls='bg-primary';
                elseif ($st==='no_frequency')   $cls='bg-secondary';
                elseif ($st==='computer_issue') $cls='bg-info';
                elseif ($st==='payment_issue')  $cls='bg-danger';
                $nm = strtolower($c->station_name);
                $grp = str_starts_with($nm, 'radio shekinah') ? 'Shekinah' : (str_starts_with($nm,'radio tekno')?'Tekno':'Other');
              @endphp
              <tr>
                <td>{{ $dt->format('Y-m-d') }}</td>
                <td>{{ $dt->format('H:i') }}</td>
                <td>{{ $c->station_name }}</td>
                <td><span class="badge bg-light text-secondary">{{ $grp }}</span></td>
                <td><span class="badge {{ $cls }}">{{ $label }}</span></td>
                <td>{{ $c->responsibility ?? '—' }}</td>
                <td>{{ $c->tech ?? '—' }}</td>
                <td class="text-truncate" style="max-width:360px;" title="{{ $c->note }}">{{ $c->note }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

@endsection

@push('scripts')
<script>
  function initRadioTooltips(){
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>{
      if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
        if (!el._radioTooltip) {
          el._radioTooltip = new bootstrap.Tooltip(el);
        }
      }
    });
  }
  function updateCount(targetSelector, inputsSelector){
    const n = document.querySelectorAll(inputsSelector + ':checked').length;
    const el = document.querySelector(targetSelector);
    if (el) el.textContent = n;
  }
  function filterList(input, itemsSelector){
    const q = input.value.toLowerCase().trim();
    document.querySelectorAll(itemsSelector).forEach(li=>{
      const txt = li.textContent.toLowerCase();
      li.style.display = txt.includes(q) ? '' : 'none';
    });
  }
  document.addEventListener('DOMContentLoaded', function () {
    initRadioTooltips();
    if (window.jQuery && $.fn.DataTable) {
      var selector = '#checkinsTable';
      $.fn.dataTable.ext.errMode = 'none';
      $(selector).off('error.dt').on('error.dt', function (e, settings, techNote, message) {
        if (window.Lobibox && typeof Lobibox.notify === 'function') {
          Lobibox.notify('error', {
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'top right',
            icon: 'bi bi-x-circle',
            msg: 'Unable to load check-in table. ' + (message || '')
          });
        } else {
          console.error('DataTables error', message);
        }
      });

      if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
      }

      var table = $(selector).DataTable({
        pageLength: 20,
        lengthChange: false,
        order: [[0,'desc'],[1,'desc']],
        buttons: ['copy','excel','pdf','print']
      });
      table.buttons().container().appendTo(selector + '_wrapper .col-md-6:eq(0)');
    }
  });
</script>
@endpush

@push('styles')
<style>
  .radio-panel .table thead th {
    background: #f1f5ff;
    border: 0;
  }
  .radio-panel .table tbody tr:hover {
    background: rgba(37, 99, 235, 0.04);
  }
  .info-icon { font-size: 16px; cursor: help; }
</style>
@endpush
