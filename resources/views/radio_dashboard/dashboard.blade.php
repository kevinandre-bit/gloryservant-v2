@extends('layouts.radio_layout')

@section('content')

<main class="main-wrapper radio-dashboard-skin">
  <div class="main-content radio-dashboard-content">
    @php
      $total          = $sites->count();
      $onAir          = $sites->filter(fn($s) => strcasecmp($s->ui_status ?? '', 'on_air') === 0)->count();
      $onAirPct       = $total ? round($onAir * 100 / max($total, 1)) : 0;
      $lastCheckHuman = $lastCheckAt ? \Carbon\Carbon::parse($lastCheckAt)->diffForHumans() : '—';
    @endphp

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
      <div class="breadcrumb-title pe-3">Radio Operations</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"></li>
          <li class="breadcrumb-item active" aria-current="page">Radio Dashboard</li>
        </ol>
      </div>
    </div>

    <div class="row g-3 mb-4 radio-metrics">
      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Stations On Air</span>
            <span class="radio-metric-icon icon-primary">
              <i class="material-icons-outlined">radio</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $onAir }}</div>
          <div class="radio-metric-subtext">{{ $total }} total • {{ $onAirPct }}%</div>
          <span class="radio-chip-accent mt-2 align-self-start">{{ __('Live Now') }}</span>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Check-ins Today</span>
            <span class="radio-metric-icon icon-info">
              <i class="material-icons-outlined">checklist</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $checkinsToday }}</div>
          <div class="radio-metric-subtext">Yesterday {{ $checkinsYesterday }}</div>
          <span class="radio-chip-accent mt-2 align-self-start">{{ __('Ops Activity') }}</span>
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
          <div class="radio-metric-subtext">{{ $topIssue ? ucfirst($topIssue) : 'No critical issue' }}</div>
          <span class="radio-chip-accent mt-2 align-self-start">{{ __('Hot Spots') }}</span>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <div class="card radio-metric-card h-100">
          <div class="radio-metric-top">
            <span class="radio-metric-label">Last Check-in</span>
            <span class="radio-metric-icon icon-success">
              <i class="material-icons-outlined">update</i>
            </span>
          </div>
          <div class="radio-metric-value">{{ $lastCheckHuman }}</div>
          <div class="radio-metric-subtext">{{ $lastCheckStation ?? '—' }}</div>
          <span class="radio-chip-accent mt-2 align-self-start">{{ __('Fresh Update') }}</span>
        </div>
      </div>
    </div>

    <div class="row g-4 align-items-start">
      <div class="col-12 col-xxl-8">
        <div class="card radio-panel h-100">
          <div class="card-body p-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between px-4 pt-4 pb-3">
              <div>
                <h5 class="card-title mb-1 d-flex align-items-center gap-3">
                  <span>{{ __('Sites Snapshot') }}</span>
                  <span class="radio-chip-accent">{{ __('Live feed') }}</span>
                </h5>
                <p class="text-muted small mb-0">Live overview of station health and last updates.</p>
              </div>
            </div>

            @php
              $shekinahLive = $sites->filter(fn($s) =>
                  str_starts_with(strtolower($s->name ?? ''), 'radio shekinah') &&
                  strcasecmp($s->ui_status ?? '', 'on_air') === 0
              )->count();

              $teknoLive = $sites->filter(fn($s) =>
                  str_starts_with(strtolower($s->name ?? ''), 'radio tekno') &&
                  strcasecmp($s->ui_status ?? '', 'on_air') === 0
              )->count();

              $totalLive = $onAir;
            @endphp

            <div class="px-4 pb-3">
              <div class="radio-filter btn-group" role="group" aria-label="Radio groups">
                <button type="button" class="btn active" data-filter="all">
                  All
                  <span class="badge">{{ $totalLive }}</span>
                </button>
                <button type="button" class="btn" data-filter="shekinah">
                  Shekinah
                  <span class="badge">{{ $shekinahLive }}</span>
                </button>
                <button type="button" class="btn" data-filter="tekno">
                  Tekno
                  <span class="badge">{{ $teknoLive }}</span>
                </button>
              </div>
            </div>

            <div class="table-responsive" id="sitesGrid">
              <table class="table align-middle mb-0 radio-table">
                <thead>
                  <tr>
                    <th scope="col" class="text-uppercase small fw-semibold">#</th>
                    <th scope="col" class="text-uppercase small fw-semibold">Site</th>
                    <th scope="col" class="text-uppercase small fw-semibold">Frequency</th>
                    <th scope="col" class="text-uppercase small fw-semibold">Status</th>
                    <th scope="col" class="text-uppercase small fw-semibold">Last Check</th>
                    <th scope="col" class="text-uppercase small fw-semibold text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($sites as $site)
                    @php
                      $rawStatus = (string) (
                          ($site->last_status ?? null)
                          ?? ($site->ui_status ?? null)
                          ?? (($site->on_air ?? 0) ? 'on_air' : null)
                          ?? 'unspecified'
                      );

                      $statusKey = strtolower(trim(preg_replace('/[\s\-]+/', '_', $rawStatus)));
                      $aliases = [
                        'online' => 'on_air',
                        'on' => 'on_air',
                        'onair' => 'on_air',
                        'link_down' => 'link',
                        'linkdown' => 'link',
                        'no_freq' => 'no_frequency',
                        'nofrequency' => 'no_frequency',
                        'unspecified_issue' => 'unspecified',
                        'unknown' => 'unspecified',
                        'computer' => 'computer_issue',
                        'comp_issue' => 'computer_issue',
                        'pc_issue' => 'computer_issue',
                        'payment' => 'payment_issue',
                        'billing' => 'payment_issue',
                        'finance' => 'payment_issue',
                      ];
                      if (isset($aliases[$statusKey])) {
                        $statusKey = $aliases[$statusKey];
                      }

                      $validStatuses = [
                        'on_air','offline','issue','power','link','internet',
                        'no_frequency','unspecified','computer_issue','payment_issue'
                      ];
                      if (! in_array($statusKey, $validStatuses, true)) {
                        $statusKey = 'unspecified';
                      }

                      $statusLabelMap = [
                        'on_air' => 'On Air',
                        'offline' => 'Offline',
                        'issue' => 'Issue',
                        'power' => 'Power',
                        'link' => 'Link Down',
                        'internet' => 'Internet',
                        'no_frequency' => 'No Frequency',
                        'unspecified' => 'Unspecified',
                        'computer_issue' => 'Computer Issue',
                        'payment_issue' => 'Payment Issue',
                      ];

                      $statusLabel = $statusLabelMap[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
                      $isOnAir = (int) ($site->on_air ?? 0) === 1;
                    @endphp
                    @php
                      $nameLower = strtolower($site->name ?? '');
                      $groupKey = str_starts_with($nameLower, 'radio tekno') ? 'tekno'
                                : (str_starts_with($nameLower, 'radio shekinah') ? 'shekinah' : 'other');
                    @endphp
                    <tr data-group="{{ $groupKey }}">
                      <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                      <td class="fw-semibold text-dark">{{ $site->name }}</td>
                      <td class="text-muted">{{ $site->frequency ?? '—' }}</td>
                      <td>
                        <span class="radio-status-chip radio-status-{{ $statusKey }}">
                          {{ $statusLabel }}
                        </span>
                      </td>
                      <td class="text-muted">
                        {{ $site->last_check ? \Carbon\Carbon::parse($site->last_check)->diffForHumans(null, true) : '—' }}
                      </td>
                      <td class="text-end">
                        <div class="d-inline-flex gap-2">
                          @if($isOnAir)
                            <form method="post" action="{{ route('tech.checkins.station.quick', $site->id) }}">
                              @csrf
                              <button type="submit" class="btn btn-radio-primary btn-sm">
                                Quick Update
                              </button>
                            </form>
                          @else
                            <form method="post" action="{{ route('tech.checkins.station.toggle', $site->id) }}">
                              @csrf
                              <button type="submit" class="btn btn-radio-outline btn-sm">
                                Mark On
                              </button>
                            </form>
                            <form method="post" action="{{ route('tech.checkins.station.quick', $site->id) }}">
                              @csrf
                              <button type="submit" class="btn btn-radio-primary btn-sm">
                                Quick Update
                              </button>
                            </form>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-4 text-muted">
                        No stations found. Import data to populate the dashboard.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xxl-4">
        <div class="d-flex flex-column gap-4">
          <div class="card radio-panel">
            <div class="card-body">
              <h6 class="card-title mb-3 d-flex align-items-center gap-2">
                <i class="material-icons-outlined text-danger">bolt</i>
                Quick Actions
              </h6>
              <div class="d-grid gap-2">
                <button class="btn btn-radio-danger" data-bs-toggle="modal" data-bs-target="#modalReportIncident">
                  <i class="material-icons-outlined me-2">warning</i> Report Incident
                </button>
                <button class="btn btn-radio-primary" data-bs-toggle="modal" data-bs-target="#modalCheckIn">
                  <i class="material-icons-outlined me-2">check_circle</i> Check In
                </button>
              </div>
            </div>
          </div>

          <div class="card radio-panel">
            <div class="card-body">
              <h6 class="card-title mb-3 d-flex align-items-center gap-2">
                <i class="material-icons-outlined text-primary">list</i>
                Activity
              </h6>
              <ul class="list-unstyled mb-0 radio-activity">
                @forelse($activity as $event)
                  @php
                    $activityStatus = strtolower(str_replace(' ', '_', $event->status ?? 'unspecified'));
                    if (! in_array($activityStatus, ['on_air','offline','power','link','internet','no_frequency','computer_issue','payment_issue','unspecified'], true)) {
                      $activityStatus = 'unspecified';
                    }
                    $activityLabel = ucfirst(str_replace('_', ' ', $activityStatus));
                  @endphp
                  <li class="d-flex align-items-center justify-content-between gap-3 mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <span class="radio-status-chip radio-status-{{ $activityStatus }}">{{ $activityLabel }}</span>
                      <span class="fw-semibold text-dark">{{ $event->station_name }}</span>
                    </div>
                    <span class="text-muted small">
                      {{ \Carbon\Carbon::parse($event->created_at)->diffForHumans(null, true) }}
                    </span>
                  </li>
                @empty
                  <li class="text-muted">No recent activity.</li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade checkin-modal" id="modalCheckIn" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header border-0">
            <div>
              <h6 class="modal-title d-flex align-items-center gap-2">
                <i class="material-icons-outlined text-success">checklist</i>
                {{ __('Log Check-in') }}
              </h6>
              <span class="radio-chip-accent d-inline-flex mt-2">{{ __('Ops Update') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <p class="text-secondary small mb-3">
              Log the latest station state so the ops team has real-time visibility.
            </p>
            <form method="post" action="{{ route('tech.checkins.store') }}" id="checkinForm" class="d-flex flex-column gap-3">
              @csrf

              <div class="checkin-station-card">
                <label class="form-label text-uppercase small mb-2">{{ __('Radio Station') }}</label>
                <div class="checkin-meta">
                  <select class="form-select @error('station_id') is-invalid @enderror" name="station_id" required>
                    <option value="">{{ __('Select station…') }}</option>
                    @foreach($sites as $s)
                      <option value="{{ $s->id }}">
                        {{ $s->name }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-control bg-white border-0 shadow-none">
                    <div class="small text-secondary text-uppercase">{{ __('Timestamp') }}</div>
                    <div class="fw-semibold">{{ now()->format('Y-m-d H:i') }}</div>
                  </div>
                </div>
                @error('station_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div>
                <label class="form-label text-uppercase small mb-2">{{ __('Status') }}</label>
                @php
                  $statuses = [
                    ['val'=>'on_air',         'label'=>'On Air',          'icon'=>'check_circle'],
                    ['val'=>'link',           'label'=>'Link Down',       'icon'=>'lan'],
                    ['val'=>'power',          'label'=>'Power',           'icon'=>'power_off'],
                    ['val'=>'internet',       'label'=>'Internet',        'icon'=>'wifi_off'],
                    ['val'=>'computer_issue', 'label'=>'Computer Issue',  'icon'=>'computer'],
                    ['val'=>'payment_issue',  'label'=>'Payment Issue',   'icon'=>'credit_card'],
                    ['val'=>'no_frequency',   'label'=>'No Frequency',    'icon'=>'cell_tower'],
                    ['val'=>'unspecified',    'label'=>'Unspecified',     'icon'=>'report_problem'],
                  ];
                @endphp
                <div class="row g-2 status-grid">
                  @foreach($statuses as $st)
                    <div class="col-6 col-md-3">
                      <input type="radio" class="btn-check" name="status" id="st-{{ $st['val'] }}" value="{{ $st['val'] }}" @checked(old('status') === $st['val'])>
                      <label class="btn w-100 d-flex flex-column align-items-center gap-2" for="st-{{ $st['val'] }}">
                        <i class="material-icons-outlined">{{ $st['icon'] }}</i>
                        <span>{{ $st['label'] }}</span>
                      </label>
                    </div>
                  @endforeach
                </div>
                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>

              <div class="checkin-meta">
                <div>
                  <label class="form-label text-uppercase small mb-2">{{ __('Responsibility') }}</label>
                  <div class="d-flex flex-wrap gap-2">
                    <input type="radio" class="btn-check" name="responsibility" id="resp-shekinah" value="shekinah" @checked(old('responsibility')==='shekinah') required>
                    <label class="btn btn-outline-primary d-flex align-items-center gap-2" for="resp-shekinah">
                      <i class="material-icons-outlined">diversity_3</i> {{ __('Shekinah Team') }}
                    </label>

                    <input type="radio" class="btn-check" name="responsibility" id="resp-landlord" value="landlord" @checked(old('responsibility')==='landlord')>
                    <label class="btn btn-outline-primary d-flex align-items-center gap-2" for="resp-landlord">
                      <i class="material-icons-outlined">home_work</i> {{ __('Landlord') }}
                    </label>
                  </div>
                  @error('responsibility') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
              </div>

              <div>
                <label class="form-label text-uppercase small mb-2">{{ __('Note') }}</label>
                <textarea class="form-control @error('note') is-invalid @enderror" name="note" rows="4" placeholder="{{ __('What did you observe? Include next actions or important details.') }}">{{ old('note') }}</textarea>
                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="d-flex justify-content-between align-items-center pt-2">
                <span class="text-secondary small">{{ __('Submission records current location and time automatically.') }}</span>
                <button class="btn btn-radio-primary" type="submit">
                  <i class="material-icons-outlined me-1">send</i> {{ __('Submit update') }}
                </button>
              </div>
            </form>
          </div>

          <div class="modal-footer border-0">
            <button type="button" class="btn btn-radio-outline" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

@push('scripts')
<script>
  (function () {
    const grid = document.getElementById('sitesGrid');
    const buttons = document.querySelectorAll('.radio-filter .btn');
    if (!grid || !buttons.length) return;

    function applyFilter(key) {
      grid.querySelectorAll('tbody tr').forEach(row => {
        const group = row.getAttribute('data-group') || 'other';
        row.style.display = (key === 'all' || group === key) ? '' : 'none';
      });
    }

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyFilter(btn.getAttribute('data-filter'));
      });
    });

    applyFilter('all');
  })();
</script>
@endpush

@endsection
