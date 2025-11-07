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
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <a href="{{ route('admin.creative.requests.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Total Requests</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['total_requests'] }}</div>
                <div class="small text-secondary">all time</div>
              </div>
              <div class="badge-icon rounded-circle bg-primary">
                <i class="bi bi-file-text fs-5"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Pending</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['pending_requests'] }}</div>
                <div class="small text-secondary">awaiting assignment</div>
              </div>
              <div class="badge-icon rounded-circle bg-warning">
                <i class="bi bi-clock fs-5"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Active Tasks</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['active_tasks'] }}</div>
                <div class="small text-secondary">in progress</div>
              </div>
              <div class="badge-icon rounded-circle bg-info">
                <i class="bi bi-list-check fs-5"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Completed Today</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['completed_today'] }}</div>
                <div class="small text-secondary">{{ now()->format('M d, Y') }}</div>
              </div>
              <div class="badge-icon rounded-circle bg-success">
                <i class="bi bi-check-lg fs-5"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-3">
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Workload Over Time</h6>
            <span class="text-secondary small">Tasks created in the last 30 days</span>
          </div>
          <div class="card-body">
            <canvas id="workloadChart" height="100"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Task Status Distribution</h6>
          </div>
          <div class="card-body">
            <canvas id="statusChart" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-3">
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Request Types</h6>
          </div>
          <div class="card-body">
            <canvas id="requestTypeChart" height="150"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Top Contributors</h6>
            <span class="text-secondary small">Ranked by total points earned</span>
          </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:60px;">Rank</th>
                <th>Volunteer</th>
                <th style="width:120px;" class="text-end">Points</th>
              </tr>
            </thead>
            <tbody>
              @forelse($leaderboard as $index => $person)
              <tr>
                <td>
                  <div class="d-flex align-items-center justify-content-center">
                    @if($index === 0)
                      <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i> 1</span>
                    @elseif($index === 1)
                      <span class="badge bg-secondary"><i class="bi bi-award-fill"></i> 2</span>
                    @elseif($index === 2)
                      <span class="badge bg-danger"><i class="bi bi-award-fill"></i> 3</span>
                    @else
                      <span class="text-secondary">{{ $index + 1 }}</span>
                    @endif
                  </div>
                </td>
                <td class="fw-semibold">{{ $person->firstname }} {{ $person->lastname }}</td>
                <td class="text-end"><span class="badge bg-primary-subtle text-primary">{{ number_format($person->total_points) }} pts</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center text-secondary py-4">No contributors yet</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
        </div>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Workload Over Time Chart
const workloadCtx = document.getElementById('workloadChart').getContext('2d');
const workloadData = @json($workloadData);
const workloadLabels = workloadData.map(item => new Date(item.date).toLocaleDateString());
const workloadCounts = workloadData.map(item => item.count);

new Chart(workloadCtx, {
    type: 'line',
    data: {
        labels: workloadLabels,
        datasets: [{
            label: 'Tasks Created',
            data: workloadCounts,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Task Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = @json($tasksByStatus);
const statusLabels = statusData.map(item => item.status.replace('_', ' ').toUpperCase());
const statusCounts = statusData.map(item => item.count);

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusCounts,
            backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545', '#6f42c1']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Request Types Chart
const requestTypeCtx = document.getElementById('requestTypeChart').getContext('2d');
const requestTypeData = @json($requestsByType);
const requestTypeLabels = requestTypeData.map(item => item.request_type.replace('_', ' ').toUpperCase());
const requestTypeCounts = requestTypeData.map(item => item.count);

new Chart(requestTypeCtx, {
    type: 'bar',
    data: {
        labels: requestTypeLabels,
        datasets: [{
            label: 'Requests',
            data: requestTypeCounts,
            backgroundColor: '#198754'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endsection
