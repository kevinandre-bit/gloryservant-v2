@extends('layouts.radio_layout')

@section('meta')
  <title>{{ __('View Playlist') }} | Glory Servant</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    {{-- Breadcrumbs --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">{{ __('Programming') }}</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item">
            <a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('program.playlists.index') }}">{{ __('Playlists') }}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('View') }}</li>
        </ol>
      </div> 

      <div class="ms-auto d-flex gap-2">
        {{-- Adjust start time on the fly (GET) --}}
        <form method="GET" class="d-flex align-items-center gap-2">
          <div class="input-group input-group-sm">
            <span class="input-group-text">{{ __('Start') }}</span>
            <input type="datetime-local" name="start_at" value="{{ $startAt }}" class="form-control">
            <button class="btn btn-primary btn-sm">
              <i class="material-icons-outlined">schedule</i> {{ __('Apply') }}
            </button>
          </div>
        </form>
        <a href="{{ route('program.playlists.export.xlsx', $playlist) }}" class="btn btn-secondary btn-sm">
              <i class="material-icons-outlined">file_download</i> XLSX
            </a>
      
            <a href="{{ route('program.playlists.export.csv', $playlist) }}" class="btn btn-outline-secondary btn-sm">
              <i class="material-icons-outlined">download</i> CSV
            </a>

        {{-- Go to Edit --}}
        <a href="{{ route('program.playlists.edit', $playlist) }}" class="btn btn-outline-secondary btn-sm">
          <i class="material-icons-outlined">edit</i> {{ __('Edit') }}
        </a>

        <a href="{{ route('program.playlists.index') }}" class="btn btn-light btn-sm d-flex align-items-center">
              <i class="material-icons-outlined me-1">arrow_back</i>{{ __('Back') }}
            </a>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">
            {{ $playlist->name }}
            <small class="text-secondary">— #{{ $playlist->id }}</small>
          </h5>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark">
              <i class="material-icons-outlined align-middle">timer</i>
              <span class="align-middle">{{ $totalHms }}</span>
            </span>
            <span class="badge bg-primary">
              <i class="material-icons-outlined align-middle">schedule</i>
              <span class="align-middle">{{ $startDisplay }} → {{ $endDisplay }}</span>
            </span>
          </div>
        </div>

        {{-- Items table --}}
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:30%">{{ __('Title / Path') }}</th>
                <th style="width:30%">{{ __('Name') }}</th>
                <th>{{ __('Performer') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Theme') }}</th>
                <th class="text-center">{{ __('Year') }}</th>
                <th class="text-center">{{ __('Duration') }}</th>
                <th class="text-center">{{ __('Start → End') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($playlist->items as $it)
                @php
                  $t = $it->track;
                  $sched = $scheduleById->get($it->id);
                  $d = (int)($t->duration_seconds ?? 0);
                @endphp
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $t->title }}</div>
                  </td>
                  <td>
                    <div class="text-secondary small">{{ $t->display_path }}</div></td>

                    <td>{{ $t->performer ?: '—' }}</td>
                    <td>{{ $t->category  ?: '—' }}</td>
                    <td>{{ $t->theme     ?: '—' }}</td>
                    <td class="text-center">{{ $t->year ?: '—' }}</td>
                  <td class="text-center">{{ \App\Support\Time::formatHms($d) }}</td>
                  <td class="text-center">
                    {{ $sched ? $sched['start']->format('H:i') : '—' }}
                    →
                    {{ $sched ? $sched['end']->format('H:i') : '—' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-secondary">{{ __('No items in this playlist yet.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</main>
@endsection