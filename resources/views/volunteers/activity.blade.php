@extends('layouts.admin_v2')

@section('content')
<main class="container-fluid" style="margin-top:2%;">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
    <h6 class="mb-0 text-uppercase">Volunteer Activity</h6>
    <div>
      <a href="{{ route('volunteers.edit', $vol->id) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-pencil-square me-1"></i> Edit Volunteer
      </a>
      <a href="{{ route('volunteers.index') }}" class="btn btn-sm btn-light">Back to List</a>
    </div>
  </div>
  <hr>

  {{-- Volunteer Header --}}
  <div class="card mb-3">
    <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
      <div>
        <h5 class="mb-1">{{ trim(($vol->lastname ?? '').', '.($vol->firstname ?? '').' '.($vol->mi ?? '')) }}</h5>
        <div class=" small font-weight-light">
          @if(!empty($vol->email)) <span class="me-3"><i class="bi bi-envelope"></i> {{ $vol->email }}</span>@endif
          @if(!empty($vol->campus)) <span class="me-3"><i class="bi bi-building"></i> {{ $vol->campus }}</span>@endif
          <span><i class="bi bi-hash"></i> ID: {{ $vol->id }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters (explicit action keeps the {id} in URL) --}}
  <form method="GET" action="{{ route('volunteers.activity', $vol->id) }}" class="row g-2 mb-3">
    <div class="col-6 col-md-2">
      <label class="form-label">From</label>
      <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label">To</label>
      <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
    </div>
    <div class="col-12 col-md-3">
      <label class="form-label">Type</label>
      @php $t = $filters['type'] ?? 'all'; @endphp
      <select name="type" class="form-select">
        <option value="all"      {{ $t==='all'?'selected':'' }}>All</option>
        <option value="clock"    {{ $t==='clock'?'selected':'' }}>Clock (IN/OUT)</option>
        <option value="devotion" {{ $t==='devotion'?'selected':'' }}>Devotions</option>
        <option value="meeting"  {{ $t==='meeting'?'selected':'' }}>Meetings</option>
      </select>
    </div>
    <div class="col-12 col-md-3">
      <label class="form-label">Search</label>
      <input type="text" name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="Search notes/title/content…">
    </div>
    <div class="col-12 col-md-2 d-grid align-self-end">
      <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter me-1"></i> Filter</button>
    </div>
  </form>

  {{-- Optional debug banner (append ?debug=1) --}}
  @if(request()->boolean('debug') && isset($debug))
    <div class="alert alert-warning">
      <strong>Debug</strong> — Found:
      Clock = {{ $debug['clock'] ?? 0 }},
      Devotion = {{ $debug['devotion'] ?? 0 }},
      Meeting = {{ $debug['meeting'] ?? 0 }}.
      Filters:
      type={{ $filters['type'] ?? 'all' }},
      from={{ $filters['from'] ?? '' }},
      to={{ $filters['to'] ?? '' }},
      q="{{ $filters['q'] ?? '' }}"
    </div>
  @endif

  {{-- Activity Timeline --}}
  <div class="card">
    <div class="card-body">
      <div class="container py-2">
        <h5 class="font-weight-light text-center py-3">Volunteer Activity Timeline</h5>

        @forelse ($p->items as $i => $a)
          @php
            // WHEN
            $dt = $a->happened_at ? \Carbon\Carbon::parse($a->happened_at) : null;
            $when = $dt ? $dt->format('M j, Y • g:i A') : '—';

            // Prefix for narrative
            $prefix = (function($dt2){
              if (empty($dt2)) return '—';
              try {
                $c = \Carbon\Carbon::parse($dt2);
                return $c->format('l, F j').' ('.$c->format('H:i').')';
              } catch (\Throwable $e) { return '—'; }
            })($a->happened_at ?? null);

            $kind    = strtolower((string)($a->kind ?? ''));
            $title   = trim((string)($a->title ?? ''));
            $details = trim((string)($a->details ?? ''));

            // Helpers
            $toHumanDuration = function(string $short) {
              if (preg_match('/^(\d+)h\s*(\d+)m$/', $short, $m)) {
                $h=(int)$m[1]; $m2=(int)$m[2];
                $hs = $h===1?'hour':'hours';
                $ms = $m2===1?'minute':'minutes';
                if ($h && $m2) return "{$h} {$hs} and {$m2} {$ms}";
                if ($h) return "{$h} {$hs}";
                return "{$m2} {$ms}";
              }
              return $short;
            };
            $isUnusuallyLong = function(string $text) {
              return preg_match('/^(\d+)\s*hour/i', $text, $mm) && (int)$mm[1] >= 12;
            };
            $mapIn = [
              'late in' => 'later than the expected time',
              'late'    => 'later than the expected time',
              'in time' => 'on time',
              'on time' => 'on time',
              'ontime'  => 'on time',
            ];
            $mapOut = [
              'on time'   => 'on time',
              'ontime'    => 'on time',
              'early out' => 'earlier than scheduled',
              'early'     => 'earlier than scheduled',
            ];

            // Narrative ($line)
            $line = '';
            if ($kind === 'meeting') {
              $meetingType = ''; $meetingName = $title; $code = '';
              if (preg_match('/\[(.*?)\]/', $title, $m)) { $code = $m[1]; }
              if (preg_match('/\((.*?)\)/', $title, $m)) { $meetingType = trim($m[1]); }
              if (strpos($title, ' (') !== false) { $meetingName = \Illuminate\Support\Str::before($title, ' ('); }
              elseif (strpos($title, ' [') !== false) { $meetingName = \Illuminate\Support\Str::before($title, ' ['); }
              $desc = $meetingType ?: $meetingName ?: 'meeting';
              $desc = \Illuminate\Support\Str::lower($desc);
              $line = "{$prefix}: The volunteer attended a {$desc}.";
              if ($code) $line .= " (Meeting ID: {$code}).";
            }
            elseif ($kind === 'devotion') {
              $status = '';
              if (preg_match('/\(([^)]+)\)\s*$/', $title, $m)) { $status = $m[1]; }
              $main = $status ? "A devotion entry was recorded ({$status})." : "A devotion entry was recorded.";
              $excerpt = $details ? ' '.\Illuminate\Support\Str::limit($details, 160) : '';
              $line = "{$prefix}: {$main}{$excerpt}";
              if (!\Illuminate\Support\Str::endsWith($line, '.')) $line .= '.';
            }
            else { // CLOCK
              $inTime=$inStatus=$outTime=$outStatus=$shortDur='';
              if (preg_match('/IN\s+([0-9:]+\s*(AM|PM))/i', $title, $m))       $inTime   = strtoupper($m[1]);
              if (preg_match('/IN.*?\(([^)]+)\)/i',           $title, $m))     $inStatus = $m[1];
              if (preg_match('/OUT\s+([0-9:]+\s*(AM|PM))/i',  $title, $m))     $outTime  = strtoupper($m[1]);
              if (preg_match('/OUT.*?\(([^)]+)\)/i',          $title, $m))     $outStatus= $m[1];
              if (preg_match('/•\s*(.+)$/',                   $title, $m))     $shortDur = trim($m[1]);

              $overnight = '';
              try {
                if ($inTime && $outTime) {
                  $inC  = \Carbon\Carbon::createFromFormat('g:i A', $inTime);
                  $outC = \Carbon\Carbon::createFromFormat('g:i A', $outTime);
                  if ($outC->lessThan($inC)) $overnight = ' the following day';
                }
              } catch (\Throwable $e) {}

              $inPhrase  = $mapIn[strtolower($inStatus)]   ?? '';
              $outPhrase = $mapOut[strtolower($outStatus)] ?? '';

              if ($inTime && $outTime) {
                $line = "{$prefix}: The volunteer arrived at {$inTime}";
                if ($inPhrase)  $line .= ", which was {$inPhrase}";
                $verbOut = $overnight ? 'clocked out' : 'left';
                $line .= ", and {$verbOut} at {$outTime}{$overnight}";
                if ($outPhrase) {
                  $outPhraseText = strtolower($outPhrase) === 'on time' ? 'right on schedule' : $outPhrase;
                  $line .= ", {$outPhraseText}";
                }
                if ($shortDur) {
                  $human = $toHumanDuration($shortDur);
                  if ($isUnusuallyLong($human)) {
                    $line .= ". This was an unusually long shift of {$human}.";
                  } else {
                    $line .= ". The total time served was {$human}.";
                  }
                } else {
                  $line .= '.';
                }
              } elseif ($inTime) {
                $line = "{$prefix}: The volunteer arrived at {$inTime}";
                if ($inPhrase) $line .= ", which was {$inPhrase}";
                $line .= '.';
              } elseif ($outTime) {
                $verbOut = $overnight ? 'clocked out' : 'left';
                $line = "{$prefix}: The volunteer {$verbOut} at {$outTime}";
                if ($outPhrase) {
                  $outPhraseText = strtolower($outPhrase) === 'on time' ? 'right on schedule' : $outPhrase;
                  $line .= ", {$outPhraseText}";
                }
                $line .= '.';
              } else {
                $line = "{$prefix}: ".($title ?: 'Activity').'.';
              }

              if ($details) {
                $line .= ' '.\Illuminate\Support\Str::limit($details, 200);
                if (!\Illuminate\Support\Str::endsWith($line, '.')) $line .= '.';
              }
            }

            // Timeline cosmetics
            $leftSide   = $i % 2 === 0;
            $badgeClass = $kind==='meeting' ? 'bg-success' : ($kind==='devotion' ? 'bg-info text-dark' : 'bg-secondary');
          @endphp

          <div class="row g-0">
            {{-- Spacer side --}}
            <div class="col-sm {{ $leftSide ? '' : 'order-sm-2' }}"></div>

            {{-- Center dot with vertical line --}}
            <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
              <div class="row h-50">
                <div class="col border-end">&nbsp;</div>
                <div class="col">&nbsp;</div>
              </div>
              <h5 class="m-2">
                <span class="badge rounded-pill {{ $badgeClass }}">&nbsp;</span>
              </h5>
              <div class="row h-50">
                <div class="col border-end">&nbsp;</div>
                <div class="col">&nbsp;</div>
              </div>
            </div>

            {{-- Content card --}}
            <div class="col-sm py-2 {{ $leftSide ? '' : 'order-sm-1' }}">
              <div class="card radius-15 shadow-sm">
                <div class="card-body">
                  <div class="float-end small" style="color:#0d6efd;">{{ $when }}</div>
                  <h5 class="card-title">
                    {{ ucfirst($kind) }} — {{ $title ?: ucfirst($kind) }}
                  </h5>
                  <p class="card-text">{!! nl2br(e($line)) !!}</p>
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center ">No activity found for the selected filters.</p>
        @endforelse

        {{-- Pagination under timeline --}}
        @if(($p->lastPage ?? 1) > 1)
          <nav class="mt-3">
            <ul class="pagination justify-content-end mb-0">
              @for($i=1; $i<=($p->lastPage ?? 1); $i++)
                <li class="page-item {{ $i===($p->current ?? 1) ? 'active' : '' }}">
                  <a class="page-link" href="{{ request()->fullUrlWithQuery(['page'=>$i]) }}">{{ $i }}</a>
                </li>
              @endfor
            </ul>
          </nav>
        @endif
      </div>
    </div>
  </div>
</main>
<!--start switcher-->
  <button class="btn btn-grd btn-grd-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
  </button>
  
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Theme Customizer</h5>
        <p class="mb-0">Customize your theme</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Blue</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>
        </div><!--end row-->

      </div>
    </div>
  </div>
  <!--start switcher-->
@endsection