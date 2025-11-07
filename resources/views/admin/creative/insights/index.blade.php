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
            <li class="breadcrumb-item active" aria-current="page">Advanced Insights</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <select id="timeRange" class="form-select" onchange="updateMetrics()">
          <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
          <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
          <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
        </select>
      </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-success">{{ $metrics['completion_rate'] }}%</div>
            <div class="text-muted small">Completion Rate</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-info">{{ $metrics['avg_completion_time'] }}h</div>
            <div class="text-muted small">Avg Completion Time</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-warning">{{ $metrics['efficiency_score'] }}%</div>
            <div class="text-muted small">Efficiency Score</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-primary">{{ $insights['capacity_forecast']['capacity_utilization'] }}%</div>
            <div class="text-muted small">Capacity Utilization</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <!-- Workload Trend -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Workload Trend & Predictions</h6>
          </div>
          <div class="card-body">
            <canvas id="trendChart" height="100"></canvas>
          </div>
        </div>
      </div>

      <!-- Top Performers -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Top Performers</h6>
          </div>
          <div class="card-body">
            @forelse($metrics['top_performers'] as $performer)
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <div class="fw-semibold">{{ $performer->firstname }} {{ $performer->lastname }}</div>
                <small class="text-muted">{{ $performer->completed_tasks }} tasks • {{ round($performer->avg_hours, 1) }}h avg</small>
              </div>
              <span class="badge bg-success">{{ $performer->completed_tasks }}</span>
            </div>
            @empty
            <p class="text-muted">No data available</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-3">
      <!-- Burnout Risk -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Burnout Risk Analysis</h6>
            <span class="badge bg-danger">AI Powered</span>
          </div>
          <div class="card-body">
            @forelse($insights['burnout_risk'] as $person)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <div class="fw-semibold">{{ $person['name'] }}</div>
                <small class="text-muted">{{ $person['task_count'] }} tasks • {{ $person['completion_rate'] }}% completion</small>
              </div>
              <div class="text-end">
                @php
                  $riskColor = $person['risk_score'] > 70 ? 'danger' : ($person['risk_score'] > 40 ? 'warning' : 'success');
                @endphp
                <span class="badge bg-{{ $riskColor }}">{{ $person['risk_score'] }}%</span>
                <div class="progress mt-1" style="height: 4px; width: 80px;">
                  <div class="progress-bar bg-{{ $riskColor }}" style="width: {{ $person['risk_score'] }}%"></div>
                </div>
              </div>
            </div>
            @empty
            <p class="text-muted">No high-risk volunteers detected</p>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Deadline Predictions -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Deadline Risk Predictions</h6>
            <span class="badge bg-info">Predictive</span>
          </div>
          <div class="card-body">
            @forelse($insights['deadline_predictions'] as $prediction)
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <div class="fw-semibold">{{ Str::limit($prediction['title'], 25) }}</div>
                <small class="text-muted">{{ $prediction['hours_remaining'] }}h remaining</small>
              </div>
              @php
                $riskColors = ['low' => 'success', 'medium' => 'warning', 'high' => 'danger'];
                $riskColor = $riskColors[$prediction['risk_level']];
              @endphp
              <span class="badge bg-{{ $riskColor }}">{{ ucfirst($prediction['risk_level']) }}</span>
            </div>
            @empty
            <p class="text-muted">No upcoming deadlines</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <!-- Bottlenecks -->
    <div class="row g-3 mt-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Process Bottlenecks</h6>
          </div>
          <div class="card-body">
            <div class="row">
              @forelse($metrics['bottlenecks'] as $bottleneck)
              <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                  <div class="h4 text-warning">{{ $bottleneck->count }}</div>
                  <div class="text-muted">{{ ucwords(str_replace('_', ' ', $bottleneck->status)) }}</div>
                  <small class="text-muted">{{ round($bottleneck->avg_age_hours, 1) }}h avg age</small>
                </div>
              </div>
              @empty
              <div class="col-12">
                <p class="text-muted text-center">No bottlenecks detected</p>
              </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Workload Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendData = @json($metrics['workload_trend']);

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: trendData.map(item => new Date(item.date).toLocaleDateString()),
        datasets: [{
            label: 'Tasks Created',
            data: trendData.map(item => item.count),
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
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function updateMetrics() {
    const days = document.getElementById('timeRange').value;
    window.location.href = `{{ route('admin.creative.insights.index') }}?days=${days}`;
}
</script>
@endsection