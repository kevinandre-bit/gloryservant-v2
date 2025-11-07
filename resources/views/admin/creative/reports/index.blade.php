@extends('layouts.crw_layout')

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Creative Workload</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.creative.index') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Reports</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Task Reports</h6>
          </div>
          <div class="card-body">
            <p class="text-secondary mb-3">Export detailed task data including assignments, status, and timelines.</p>
            
            <form action="{{ route('admin.creative.reports.tasks') }}" method="GET" class="mb-3">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Start Date</label>
                  <input type="date" name="start_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">End Date</label>
                  <input type="date" name="end_date" class="form-control form-control-sm">
                </div>
              </div>
              <button type="submit" class="btn btn-success btn-sm mt-2">
                <i class="bi bi-download me-1"></i>Export CSV
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Contribution Reports</h6>
          </div>
          <div class="card-body">
            <p class="text-secondary mb-3">Export volunteer contributions, points earned, and activity history.</p>
            
            <form action="{{ route('admin.creative.reports.contributions') }}" method="GET" class="mb-3">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Start Date</label>
                  <input type="date" name="start_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">End Date</label>
                  <input type="date" name="end_date" class="form-control form-control-sm">
                </div>
              </div>
              <button type="submit" class="btn btn-success btn-sm mt-2">
                <i class="bi bi-download me-1"></i>Export CSV
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>
@endsection