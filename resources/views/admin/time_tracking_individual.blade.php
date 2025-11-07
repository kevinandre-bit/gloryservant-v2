@extends('layouts.admin')

@section('meta')
  <title>Volunteer Time Tracking — Individual View</title>
@endsection

@section('content')
@php
    $filters = $filters ?? [];
    $filters = array_merge([
        'from'      => now()->subDays(6)->toDateString(),
        'to'        => now()->toDateString(),
        'status'    => 'all',
        'search'    => '',
        'person_id' => null,
    ], $filters);

    $statusOptions = $statusOptions ?? [
        'all'         => 'All records',
        'on_time'     => 'On time (<= 08:05)',
        'late'        => 'Late (> 08:05)',
        'early'       => 'Early (< 08:00)',
        'missing_in'  => 'Missing clock-in',
        'missing_out' => 'Missing clock-out',
    ];

    $cards = $cards ?? [
        'today'    => ['minutes' => 0, 'sessions' => 0],
        'week'     => ['minutes' => 0, 'sessions' => 0],
        'month'    => ['minutes' => 0, 'sessions' => 0, 'overtime_minutes' => 0],
        'overtime' => ['minutes' => 0, 'sessions' => 0],
    ];

    $summary = $summary ?? [
        'worked_minutes'        => 0,
        'overtime_minutes'      => 0,
        'late_minutes'          => 0,
        'late_count'            => 0,
        'sessions'              => 0,
        'avg_clock_in_seconds'  => null,
        'avg_clock_out_seconds' => null,
        'open_sessions'         => 0,
    ];

    $attendance     = $attendance ?? [];
    $searchResults  = collect($searchResults ?? []);
    $volunteer      = $volunteer ?? null;
    $nowDisplay     = now();

    $activeSuggestion = null;
    if ($volunteer) {
        $activeSuggestion = trim(($volunteer->name ?? 'Unknown').' · '.($volunteer->idno ?? 'ID'));
    }

    $searchValue = $filters['search'] !== '' ? $filters['search'] : ($activeSuggestion ?? '');

    $suggestionItems = $searchResults->map(function ($r) {
        $label = trim(($r->name ?? 'Unknown').' · '.($r->idno ?? 'ID'));
        return [
            'id'    => $r->id,
            'label' => $label,
        ];
    })->values()->all();

    $formatMinutes = function ($minutes) {
        $minutes = (int) $minutes;
        if ($minutes <= 0) {
            return '0m';
        }
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;
        if ($hours > 0 && $mins > 0) {
            return sprintf('%dh %02dm', $hours, $mins);
        }
        if ($hours > 0) {
            return sprintf('%dh', $hours);
        }
        return sprintf('%dm', $mins);
    };

    $formatMinutesOrDash = function ($minutes) use ($formatMinutes) {
        return (int) $minutes > 0 ? $formatMinutes($minutes) : '—';
    };

    $formatSeconds = function ($seconds) {
        if ($seconds === null) {
            return '—';
        }
        $seconds = (int) round($seconds);
        if ($seconds <= 0) {
            return '—';
        }
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    };

    $statusBadge = function (?string $status) {
        $status = strtolower(trim((string) $status));
        if ($status === '') {
            return 'secondary';
        }
        if (strpos($status, 'late') !== false) {
            return 'warning';
        }
        if (strpos($status, 'early') !== false) {
            return 'info';
        }
        if (strpos($status, 'absent') !== false || strpos($status, 'missing') !== false) {
            return 'danger';
        }
        if (strpos($status, 'present') !== false || strpos($status, 'on time') !== false) {
            return 'success';
        }
        return 'primary';
    };

    $cardDefinitions = [
        'today' => ['title' => 'Total Hours Today', 'icon' => 'alarm'],
        'week'  => ['title' => 'Total Hours This Week', 'icon' => 'date_range'],
        'month' => ['title' => 'Total Hours This Month', 'icon' => 'calendar_month'],
        'overtime' => ['title' => 'Overtime This Month', 'icon' => 'schedule_send'],
    ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <div class="d-flex align-items-center gap-2">
        <i class="material-icons-outlined text-primary">schedule</i>
        <h5 class="mb-0">Volunteer Time Tracking</h5>
      </div>
      @if($volunteer)
        <span class="badge bg-light text-secondary border small px-3 py-2">
          {{ $volunteer->name }} · {{ $volunteer->idno }}
        </span>
      @endif
    </div>

    <div class="card mb-3 shadow-sm">
      <div class="card-body">
        <form method="get"
              action="{{ route('admin.time_tracking.individual') }}"
              class="d-flex flex-wrap align-items-end gap-3">
          <div class="flex-grow-1 position-relative" style="min-width:280px;">
            <label class="form-label small mb-1">Search & select volunteer</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="material-icons-outlined fs-18">search</i>
              </span>
              <input type="text"
                     class="form-control border-start-0"
                     name="search"
                     id="volunteer-search"
                     value="{{ $searchValue }}"
                     autocomplete="off"
                     placeholder="Start typing a name or ID">
              <input type="hidden"
                     name="person_id"
                     id="volunteer-person-id"
                     value="{{ $filters['person_id'] }}">
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
            <div id="volunteer-suggestion-box"
                 class="list-group shadow-sm position-absolute w-100 d-none"
                 style="z-index:10; max-height:260px; overflow-y:auto; top:calc(100% + 4px);">
            </div>
          </div>

          <div class="flex-grow-1" style="min-width:150px;">
            <label class="form-label small mb-1">From</label>
            <input type="date"
                   class="form-control form-control-sm"
                   name="from"
                   value="{{ $filters['from'] }}">
          </div>

          <div class="flex-grow-1" style="min-width:150px;">
            <label class="form-label small mb-1">To</label>
            <input type="date"
                   class="form-control form-control-sm"
                   name="to"
                   value="{{ $filters['to'] }}">
          </div>

          <div class="flex-grow-1" style="min-width:150px;">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
              @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected($filters['status'] === $value)>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="d-flex align-items-end gap-2 flex-grow-0">
            <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
            <a href="{{ route('admin.time_tracking.individual') }}" class="btn btn-link btn-sm text-decoration-none">Reset</a>
          </div>
        </form>
      </div>
    </div>

    @if(!$volunteer)
      <div class="alert alert-info border-0 shadow-sm">
        Search for a volunteer and select them to explore individual time tracking insights.
      </div>
    @else
      <div class="row g-3 mb-3">
        <div class="col-xl-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <div class="text-center mb-3">
                <div class="avatar avatar-xl avatar-rounded mx-auto mb-3" style="width:200px;height:200px;">
                  <img src="{{ $volunteerAvatar ?? '/assets2/images/avatar-default.png' }}"
                       alt="Profile"
                       style="width:200px;height:200px;object-fit:cover;border-radius:50%;">
                </div>
                <h6 class="mb-1">{{ $volunteer->name }}</h6>
                <p class="text-secondary small mb-1">{{ $volunteer->ministry }} • {{ $volunteer->campus }}</p>
                <span class="badge bg-primary text-white small px-2 py-1">ID {{ $volunteer->idno }}</span>
              </div>
              <div class="small text-secondary d-flex flex-column gap-2">
                <div class="d-flex justify-content-between">
                  <span>Date range</span>
                  <span class="fw-semibold">{{ $filters['from'] }} → {{ $filters['to'] }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>Open sessions</span>
                  <span class="fw-semibold">{{ $summary['open_sessions'] }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>Generated</span>
                  <span>{{ $nowDisplay->format('M d, Y h:i A') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

            <div class="col-xl-8">
              <div class="row g-3">
                @foreach($cardDefinitions as $key => $card)
                  @php
                    $data = $cards[$key] ?? ['minutes' => 0, 'sessions' => 0, 'overtime_minutes' => 0];
                    $primaryValue = $formatMinutes($data['minutes'] ?? 0);
                    $footnote = $key === 'overtime'
                      ? (($data['minutes'] ?? 0) > 0 ? 'Logged this month' : 'No overtime logged')
                      : (($data['sessions'] ?? 0).' session'.(($data['sessions'] ?? 0) === 1 ? '' : 's'));
                    $deltaText = $key === 'month'
                      ? 'Overtime '.$formatMinutesOrDash($data['overtime_minutes'] ?? 0)
                      : null;
                    $footnoteClass = $key === 'overtime' ? 'text-warning' : 'text-success';
                  @endphp
                  <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <span class="avatar avatar-sm bg-light text-primary">
                            <i class="material-icons-outlined">{{ $card['icon'] }}</i>
                          </span>
                          @if($deltaText)
                            <span class="badge bg-light text-secondary border small">{{ $deltaText }}</span>
                          @endif
                        </div>
                        <h3 class="mb-0">{{ $primaryValue }}</h3>
                        <p class="text-secondary small mb-1">{{ $card['title'] }}</p>
                        <p class="{{ $footnoteClass }} small mb-0">{{ $footnote }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="row g-3 mt-2 mb-3">
            <div class="col-sm-6 col-lg-3">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                  <p class="text-secondary small mb-1">Total working hours</p>
                  <h4 class="mb-0">{{ $formatMinutes($summary['worked_minutes']) }}</h4>
                  <span class="text-secondary small">{{ $summary['sessions'] }} session{{ $summary['sessions'] === 1 ? '' : 's' }}</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                  <p class="text-secondary small mb-1">Late arrival</p>
                  <h4 class="mb-0">{{ $formatMinutesOrDash($summary['late_minutes']) }}</h4>
                  <span class="text-secondary small">{{ $summary['late_count'] }} late arrival{{ $summary['late_count'] === 1 ? '' : 's' }}</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                  <p class="text-secondary small mb-1">Overtime</p>
                  <h4 class="mb-0">{{ $formatMinutesOrDash($summary['overtime_minutes']) }}</h4>
                  <span class="text-secondary small">{{ $summary['open_sessions'] }} open session{{ $summary['open_sessions'] === 1 ? '' : 's' }}</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                  <p class="text-secondary small mb-1">Average check in/out</p>
                  <h4 class="mb-0">{{ $formatSeconds($summary['avg_clock_in_seconds']) }}</h4>
                  <span class="text-secondary small">Checkout {{ $formatSeconds($summary['avg_clock_out_seconds']) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      

      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
              <h6 class="mb-0">Attendance Records</h6>
              <p class="text-secondary small mb-0">{{ count($attendance) }} record{{ count($attendance) === 1 ? '' : 's' }} in selection</p>
            </div>
            <span class="badge bg-light text-secondary border small">
              Status filter: {{ $statusOptions[$filters['status']] ?? 'All records' }}
            </span>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Check In</th>
                  <th scope="col">Check Out</th>
                  <th scope="col">Total</th>
                  <th scope="col">Late</th>
                  <th scope="col">Overtime</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($attendance as $row)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $row['date_display'] }}</div>
                      <div class="text-secondary small">{{ $row['weekday'] }}</div>
                    </td>
                    <td>
                      @if($row['check_in'])
                        <span class="badge bg-light text-secondary border">{{ $row['check_in'] }}</span>
                      @else
                        <span class="text-secondary">—</span>
                      @endif
                    </td>
                    <td>
                      @if($row['check_out'])
                        <span class="badge bg-light text-secondary border">{{ $row['check_out'] }}</span>
                      @else
                        <span class="text-secondary">{{ $row['open_session'] ? 'Open' : '—' }}</span>
                      @endif
                    </td>
                    <td>{{ $formatMinutes($row['worked_minutes']) }}</td>
                    <td>{{ $formatMinutesOrDash($row['late_minutes']) }}</td>
                    <td>{{ $formatMinutesOrDash($row['overtime_minutes']) }}</td>
                    <td class="small">
                      @php
                        $statusIn = $row['status_in'] ?? null;
                        $statusOut = $row['status_out'] ?? null;
                      @endphp
                      @if($statusIn)
                        <span class="badge bg-{{ $statusBadge($statusIn) }}">{{ $statusIn }}</span>
                      @else
                        <span class="badge bg-secondary">No clock-in</span>
                      @endif
                      @if($statusOut)
                        <span class="badge bg-{{ $statusBadge($statusOut) }}">{{ $statusOut }}</span>
                      @elseif(!$row['open_session'])
                        <span class="badge bg-secondary">No clock-out</span>
                      @endif
                      @if($row['open_session'])
                        <span class="badge bg-warning text-secondary">Open</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-secondary py-4">
                      No attendance entries match the selected filters.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
  </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('volunteer-search');
  const hidden = document.getElementById('volunteer-person-id');
  const box = document.getElementById('volunteer-suggestion-box');
  if (!input || !hidden || !box) {
    return;
  }

  const suggestions = @json($suggestionItems);
  const map = suggestions.reduce((acc, item) => {
    acc[item.label] = item.id;
    return acc;
  }, {});

  const syncHidden = (value) => {
    if (map[value]) {
      hidden.value = map[value];
      input.value = value;
    } else if (value.trim() === '') {
      hidden.value = '';
    }
  };

  const render = (query) => {
    box.innerHTML = '';
    let filtered = suggestions;
    const norm = query.trim().toLowerCase();
    if (norm !== '') {
      filtered = suggestions.filter(item => item.label.toLowerCase().includes(norm));
    }
    filtered.forEach(item => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'list-group-item list-group-item-action py-1 px-2 small';
      btn.textContent = item.label;
      btn.addEventListener('click', () => {
        input.value = item.label;
        hidden.value = item.id;
        box.innerHTML = '';
        box.classList.add('d-none');
      });
      box.appendChild(btn);
    });
    if (box.children.length > 0 && document.activeElement === input) {
      box.classList.remove('d-none');
    } else {
      box.classList.add('d-none');
    }
  };

  render(input.value);

  input.addEventListener('input', () => {
    if (!map[input.value]) {
      hidden.value = '';
    }
    render(input.value);
  });

  input.addEventListener('focus', () => {
    input.value = '';
    hidden.value = '';
    render('');
  });
  input.addEventListener('change', () => syncHidden(input.value));
  input.addEventListener('blur', () => {
    setTimeout(() => {
      box.innerHTML = '';
      box.classList.add('d-none');
      syncHidden(input.value);
    }, 150);
  });

  document.addEventListener('click', (event) => {
    if (!box.contains(event.target) && event.target !== input) {
      box.classList.add('d-none');
    }
  });
});
</script>
@endpush
