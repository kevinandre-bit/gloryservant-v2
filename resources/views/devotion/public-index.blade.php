@extends('layouts.admin_v2')

@section('content')
<main class="container-fluid">
  <div class="ROW" style="margin: 2% 2% 2% 2%;">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <h6 class="mb-0 text-uppercase">Devotions</h6>
        {{-- Logo on the right, same position as old button --}}
        <img src="{{ asset('assets2/images/logo1.png') }}" alt="Logo" class="img-fluid" style="height:60px;">
      </div>

          {{-- Optional quick filters above table --}}
          {{-- Filters --}}
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
      @foreach(($departmentOptions ?? []) as $dept)
        <option value="{{ $dept }}" {{ request('department')===$dept ? 'selected' : '' }}>{{ $dept }}</option>
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
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
  </div>

  <div class="col-6 col-md-1">
    <label class="form-label">To</label>
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Person</label>
    <select name="person" class="form-select person-select">
      <option value="">All People</option>
      @foreach(($peopleOptions ?? []) as $opt)
        <option value="{{ $opt->id }}" {{ (string)request('person') === (string)$opt->id ? 'selected' : '' }}>
          {{ $opt->name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Inline button, fills remaining 1 col --}}
  <div class="col-12 col-md-1 d-grid">
    <button class="btn btn-outline-primary w-100 h-100">
      <i class="bi bi-funnel"></i>
      <span class="d-none d-lg-inline ms-1">Filter</span>
    </button>
  </div>
</form>

          <hr>
      {{-- Weekly Devotion Summary --}}
      <div class="row row-cols-1 row-cols-xl-3 mb-4">
        <div class="col">
  <div class="card rounded-4">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3 mb-2">
        <h2 id="totalDevotionsSummary" class="mb-0">{{ $summary['total'] ?? 0 }}</h2>
        <span id="devotionGrowthBadge"
              class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
          {{ $summary['growth'] ?? '0%' }}
        </span>
      </div>
      <p class="mb-0">Total Devotions Posted</p>
      <div class="mt-4">
        <p class="mb-2 d-flex align-items-center justify-content-between">
          <span id="leftToGoalText">{{ $summary['leftToGoal'] ?? 0 }} left to Goal</span>
          <span id="progressPercent">{{ $summary['progress'] ?? '0%' }}</span>
        </p>
        <div class="progress w-100" style="height:6px;">
          <div id="devotionProgressBar" class="progress-bar bg-grd-purple"
               style="width: {{ $summary['progress'] ?? '0%' }}"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col">
  <div class="card rounded-4">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3 mb-2">
        <h2 id="noDevotionCount" class="mb-0">{{ $summary['noDevotion'] ?? 0 }}</h2>
        <span id="noDevotionBadge" class="dash-lable bg-danger text-danger bg-opacity-10">
          {{ $summary['noDevotionPercent'] ?? '0%' }}
        </span>
      </div>
      <p class="mb-0">
        @if(($summary['noDevotion'] ?? 0) > 0)
          People With No Devotions Yet
        @else
          🎉 Everyone Posted At Least Once
        @endif
      </p>
      <div class="mt-4">
        <p class="mb-2 d-flex align-items-center justify-content-between">
          <span id="noDevotionLeftToGoalText">{{ $summary['noDevotionLeft'] ?? 0 }} left to Goal</span>
          <span id="noDevotionProgressPercent">{{ $summary['noDevotionPercent'] ?? '0%' }}</span>
        </p>
        <div class="progress w-100" style="height:6px;">
          <div id="noDevotionProgressBar" class="progress-bar bg-grd-danger"
               style="width: {{ $summary['noDevotionPercent'] ?? '0%' }}"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col">
  <div class="card rounded-4">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3 mb-2">
        <h2 id="sixPlusCount" class="mb-0">{{ $summary['sixPlus'] ?? 0 }}</h2>
        <span id="sixPlusBadge" class="dash-lable bg-success text-success bg-opacity-10">
          {{ $summary['sixPlusPercent'] ?? '0%' }}
        </span>
      </div>
      <p class="mb-0">
        @if(($summary['sixPlus'] ?? 0) > 0)
          People Reached the Weekly Goal (6+ Devotions)
        @else
          No One Has Reached the Weekly Goal Yet
        @endif
      </p>
      <div class="mt-4">
        <p class="mb-2 d-flex align-items-center justify-content-between">
          <span id="sixPlusLeftToGoalText">{{ $summary['sixPlusLeft'] ?? 0 }} left to Goal</span>
          <span id="sixPlusProgressPercent">{{ $summary['sixPlusPercent'] ?? '0%' }}</span>
        </p>
        <div class="progress w-100" style="height:6px;">
          <div id="sixPlusProgressBar" class="progress-bar bg-grd-success"
               style="width: {{ $summary['sixPlusPercent'] ?? '0%' }}"></div>
        </div>
      </div>
    </div>
  </div>
</div>
      </div>
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                  <thead>
        <tr>
          <th>Date</th>
          <th>Volunteer</th>
          <th>Campus</th>
          <th>Department</th>
          <th>Ministry</th>
          <th>Devotion Text</th>
          <th>Status</th>
          <th>Submitted</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($devotions as $d)
          <tr>
            <td>{{ \Illuminate\Support\Carbon::parse($d->devotion_date)->format('Y-m-d') }}</td>
            <td>{{ trim(($d->lastname ?? '').', '.($d->firstname ?? '').' '.($d->mi ?? '')) }}</td>
            <td>{{ $d->campus_name ?? '—' }}</td>
            <td>{{ $d->department ?? '—' }}</td>
      <td>{{ $d->ministry ?? '—' }}</td>
            <td title="{{ $d->devotion_text }}">
              <div class="text-truncate" style="max-width:520px;">{{ $d->devotion_text }}</div>
            </td>
            <td>{{ $d->status }}</td>
            <td>{{ \Illuminate\Support\Carbon::parse($d->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th>Date</th>
          <th>Volunteer</th>
          <th>Campus</th>
          <th>Department</th>
          <th>Ministry</th>
          <th>Devotion Text</th>
          <th>Status</th>
          <th>Submitted</th>
        </tr>
      </tfoot>
                </table>
              </div>
            </div>
          </div>
  </div>
</main>

{{-- Toast errors via Lobibox (optional, matches your pattern) --}}
@if ($errors->any())
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      @foreach ($errors->all() as $msg)
        Lobibox.notify('error', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top center',
          icon: 'bi bi-x-circle',
          msg: @json($msg)
        });
      @endforeach
    });
  </script>
@endif
@endsection

@push('scripts')
  {{-- DataTables init for #example2. Make sure DataTables JS/CSS are included in your layout --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery && $.fn.DataTable) {
        $('#example2').DataTable({
          pageLength: 25,
          order: [[0, 'desc']], // sort by Date desc
          columnDefs: [
            { targets: [3], orderable: false } // Devotion Text not great for sorting
          ]
        });
      } else {
        console.warn('DataTables not found. Ensure JS/CSS are loaded.');
      }
    });
  </script>
@endpush