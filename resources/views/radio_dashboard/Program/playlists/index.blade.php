@extends('layouts.radio_layout')
@php use App\Classes\permission; @endphp

@section('meta')
  <title>{{ __('Playlists') }} | Glory Servant</title>
@endsection

@section('content')
<main class="main-wrapper radio-dashboard-skin">
  <div class="main-content radio-dashboard-content">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <h4 class="mb-0 radio-title-accent">{{ __('Playlists') }}</h4>
        <span class="radio-chip-accent">{{ $playlists->total() }} {{ __('total') }}</span>
      </div>
      <a href="{{ route('program.playlists.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
        <i class="bi bi-plus-lg me-1"></i>{{ __('New Playlist') }}
      </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-3 rounded-3 border-0 shadow-sm">
      <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label for="q" class="form-label small mb-1">{{ __('Search') }}</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-0 text-secondary">
                <i class="bi bi-search"></i>
              </span>
              <input id="q" type="search" name="q" value="{{ request('q') }}" class="form-control border-0"
                     placeholder="{{ __('Name / owner…') }}">
              @if(request('q'))
                <a href="{{ route('program.playlists.index') }}" class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-x-lg"></i>
                </a>
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label small mb-1">{{ __('Status') }}</label>
            <select id="status" name="status" class="form-select form-select-sm">
              <option value="">{{ __('All') }}</option>
              @foreach(['draft','approved','exported'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label for="owner" class="form-label small mb-1">{{ __('Owner') }}</label>
            <input id="owner" type="text" name="owner" value="{{ request('owner') }}" class="form-control form-control-sm"
                   placeholder="{{ __('Name or email') }}">
          </div>
          <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary btn-sm w-100">
              <i class="bi bi-funnel me-1"></i>{{ __('Filter') }}
            </button>
            @if(request()->hasAny(['q','status','owner']))
              <a class="btn btn-light btn-sm w-100" href="{{ route('program.playlists.index') }}">
                <i class="bi bi-backspace me-1"></i>{{ __('Reset') }}
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Table --}}
    <div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="w-30">{{ __('Name') }}</th>
            <th class="w-15 text-center">{{ __('Total') }}</th>
            <th class="w-15 text-center">{{ __('Status') }}</th>
            <th class="w-20 text-center">{{ __('Owner') }}</th>
            <th class="w-15 text-center">{{ __('Updated') }}</th>
            <th class="w-15 text-center">{{ __('YouTube Peak') }}</th>
            <th class="w-20 text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($playlists as $p)
            @php
              $peak = (int) ($p->youtube_peak ?? 0);

              // Format like 6.5K, 10K, etc.
              $formatK = function(int $n): string {
                  if ($n >= 1000) {
                      $k = $n / 1000;
                      // show one decimal under 10K, otherwise integer K
                      return $k < 10 ? number_format($k, 1).'K' : number_format(floor($k)).'K';
                  }
                  return (string) $n;
              };

              // Trend label based on ranges
              if ($peak >= 10000) {
                  $trendLabel = '10K+';
                  $trendClass = 'bg-success';
              } elseif ($peak >= 9000) {
                  $trendLabel = '9–10K';
                  $trendClass = 'bg-success';
              } elseif ($peak >= 8000) {
                  $trendLabel = '8–9K';
                  $trendClass = 'bg-primary';
              } elseif ($peak >= 7000) {
                  $trendLabel = '7–8K';
                  $trendClass = 'bg-info';
              } elseif ($peak >= 6000) {
                  $trendLabel = '6–7K';
                  $trendClass = 'bg-warning';
              } elseif ($peak >= 5000) {
                  $trendLabel = '5–6K';
                  $trendClass = 'bg-warning';
              } else {
                  $trendLabel = 'Under 5K';
                  $trendClass = 'bg-secondary';
              }
            @endphp

            <tr>
              <td>
                <div class="fw-semibold">{{ $p->name }}</div>
                <div class="text-secondary small">#{{ $p->id }}</div>
              </td>

              <td class="text-center">
                <span class="badge rounded-pill bg-light text-secondary">
                  {{ \App\Support\Time::formatHms($p->total_seconds) }}
                </span>
              </td>

              <td class="text-center">
                <span class="badge 
                  @if($p->status==='draft') bg-secondary 
                  @elseif($p->status==='approved') bg-success 
                  @elseif($p->status==='exported') bg-info 
                  @endif">
                  {{ ucfirst($p->status) }}
                </span>
              </td>

              <td class="text-center">{{ optional($p->user)->name ?? '—' }}</td>

              <td class="text-center text-secondary small">{{ $p->updated_at->diffForHumans() }}</td>
              <td class="text-center">
                <div class="fw-semibold">{{ number_format($p->youtube_peak) }}</div>
                <div class="{{ $p->trend_color_class }} small">
                  {{ $p->trend_label }}
                </div>
              </td>

              <td class="text-end">
                <a href="{{ route('program.playlists.show', $p) }}" class="btn btn-sm btn-light">
                  <i class="material-icons-outlined align-middle">visibility</i> {{ __('View') }}
                </a>
                <form method="POST" action="{{ route('program.playlists.duplicate', $p) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary">
                    <i class="material-icons-outlined align-middle">content_copy</i>
                    {{ __('Save as new') }}
                  </button>
                </form>
                <a href="{{ route('program.playlists.edit', $p) }}" class="btn btn-sm btn-outline-primary">
                  <i class="material-icons-outlined align-middle">edit</i> {{ __('Edit') }}
                </a>
                <form action="{{ route('program.playlists.destroy', $p) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('{{ __('Delete this playlist?') }}')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="material-icons-outlined align-middle">delete</i> {{ __('Delete') }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-secondary py-4">{{ __('No playlists yet.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($playlists->hasPages())
    <div class="card-footer border-0 bg-transparent pt-3">
      {!! $playlists->withQueryString()->onEachSide(1)->links('vendor.pagination.radio') !!}
    </div>
  @endif
</div>

  </div>
</main>
@endsection
