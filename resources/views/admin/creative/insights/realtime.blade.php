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
            <li class="breadcrumb-item"><a href="{{ route('admin.creative.insights.index') }}">Insights</a></li>
            <li class="breadcrumb-item active" aria-current="page">Real-time Monitor</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-success" id="statusIndicator">Live</span>
          <small class="text-muted">Last updated: <span id="lastUpdate">--</span></small>
        </div>
      </div>
    </div>

    <!-- Real-time Metrics -->
    <div class="row g-3 mb-4">
      <div class="col-md-2">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-primary" id="activeTasks">--</div>
            <div class="text-muted small">Active Tasks</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-success" id="completedToday">--</div>
            <div class="text-muted small">Completed Today</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-warning" id="overdueTasks">--</div>
            <div class="text-muted small">Overdue</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-info" id="activeVolunteers">--</div>
            <div class="text-muted small">Active Volunteers</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-secondary" id="avgResponseTime">--</div>
            <div class="text-muted small">Avg Response (h)</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-center">
          <div class="card-body">
            <div class="display-6 fw-bold text-danger" id="alertCount">--</div>
            <div class="text-muted small">Alerts</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <!-- Live Activity Feed -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Live Activity Feed</h6>
            <span class="badge bg-primary pulse">Live</span>
          </div>
          <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <div id="activityFeed">
              <div class="text-center text-muted py-4">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <div class="mt-2">Loading activities...</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Alerts -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Performance Alerts</h6>
          </div>
          <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <div id="alertsFeed">
              <div class="text-center text-muted py-4">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <div class="mt-2">Analyzing performance...</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Real-time Charts -->
    <div class="row g-3 mt-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Real-time Task Flow</h6>
          </div>
          <div class="card-body">
            <canvas id="realtimeChart" height="80"></canvas>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<style>
.pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

.activity-item {
  border-left: 3px solid #dee2e6;
  padding-left: 15px;
  margin-bottom: 15px;
  position: relative;
}

.activity-item::before {
  content: '';
  position: absolute;
  left: -6px;
  top: 5px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #6c757d;
}

.activity-item.completed::before { background: #198754; }
.activity-item.started::before { background: #0dcaf0; }
.activity-item.assigned::before { background: #ffc107; }
.activity-item.overdue::before { background: #dc3545; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let realtimeChart;
let refreshInterval;

// Initialize real-time chart
function initRealtimeChart() {
    const ctx = document.getElementById('realtimeChart').getContext('2d');
    realtimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Tasks Created',
                data: [],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Tasks Completed',
                data: [],
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
}

// Fetch real-time data
async function fetchRealtimeData() {
    try {
        const response = await fetch('{{ route("admin.creative.insights.api") }}?realtime=1');
        const data = await response.json();
        
        updateMetrics(data.metrics);
        updateActivityFeed(data.activities);
        updateAlerts(data.alerts);
        updateChart(data.chartData);
        
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
        document.getElementById('statusIndicator').className = 'badge bg-success';
        document.getElementById('statusIndicator').textContent = 'Live';
        
    } catch (error) {
        console.error('Failed to fetch real-time data:', error);
        document.getElementById('statusIndicator').className = 'badge bg-danger';
        document.getElementById('statusIndicator').textContent = 'Error';
    }
}

function updateMetrics(metrics) {
    document.getElementById('activeTasks').textContent = metrics.active_tasks || '--';
    document.getElementById('completedToday').textContent = metrics.completed_today || '--';
    document.getElementById('overdueTasks').textContent = metrics.overdue_tasks || '--';
    document.getElementById('activeVolunteers').textContent = metrics.active_volunteers || '--';
    document.getElementById('avgResponseTime').textContent = metrics.avg_response_time || '--';
    document.getElementById('alertCount').textContent = metrics.alert_count || '--';
}

function updateActivityFeed(activities) {
    const feed = document.getElementById('activityFeed');
    if (!activities || activities.length === 0) {
        feed.innerHTML = '<p class="text-muted text-center">No recent activities</p>';
        return;
    }
    
    feed.innerHTML = activities.map(activity => `
        <div class="activity-item ${activity.type}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-semibold">${activity.title}</div>
                    <small class="text-muted">${activity.description}</small>
                </div>
                <small class="text-muted">${activity.time_ago}</small>
            </div>
        </div>
    `).join('');
}

function updateAlerts(alerts) {
    const alertsFeed = document.getElementById('alertsFeed');
    if (!alerts || alerts.length === 0) {
        alertsFeed.innerHTML = '<p class="text-muted text-center">No active alerts</p>';
        return;
    }
    
    alertsFeed.innerHTML = alerts.map(alert => `
        <div class="alert alert-${alert.severity} alert-dismissible fade show" role="alert">
            <strong>${alert.title}</strong> ${alert.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `).join('');
}

function updateChart(chartData) {
    if (!chartData || !realtimeChart) return;
    
    realtimeChart.data.labels = chartData.labels;
    realtimeChart.data.datasets[0].data = chartData.created;
    realtimeChart.data.datasets[1].data = chartData.completed;
    realtimeChart.update('none');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initRealtimeChart();
    fetchRealtimeData();
    
    // Refresh every 30 seconds
    refreshInterval = setInterval(fetchRealtimeData, 30000);
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endsection