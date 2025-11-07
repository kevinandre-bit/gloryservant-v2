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
            <li class="breadcrumb-item active" aria-current="page">System Test</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">System Status Check</h5>
      </div>
      <div class="card-body">
        @php
          $tests = [
            'Database Tables' => function() {
              $requestCount = \App\Classes\table::creativeRequests()->count();
              $taskCount = \App\Classes\table::creativeTasks()->count();
              $assignmentCount = \DB::table('tbl_creative_task_assignments')->count();
              $peopleCount = \DB::table('tbl_people')->count();
              return "Requests: {$requestCount}, Tasks: {$taskCount}, Assignments: {$assignmentCount}, People: {$peopleCount}";
            },
            'Analytics Service' => function() {
              $service = new \App\Services\AnalyticsService();
              $metrics = $service->getPerformanceMetrics(7);
              return "Completion Rate: {$metrics['completion_rate']}%, Efficiency: {$metrics['efficiency_score']}%";
            },
            'Reports Service' => function() {
              $service = new \App\Services\ReportService();
              $tasks = $service->generateTaskReport(now()->subDays(30), now());
              return "Task report generated with " . $tasks->count() . " records";
            },
            'Points Service' => function() {
              $service = new \App\Services\PointsService();
              $total = \DB::table('tbl_creative_points_ledger')->sum('points');
              return "Total points awarded: {$total}";
            },
            'Badge Service' => function() {
              $service = new \App\Services\BadgeService();
              $badges = \DB::table('tbl_creative_badges')->count();
              return "Total badges: {$badges}";
            },
            'Controllers' => function() {
              $controllers = [
                'CreativeAnalyticsController',
                'CreativeRequestController', 
                'CreativeTaskController',
                'CreativeReportsController',
                'CreativeInsightsController'
              ];
              foreach($controllers as $controller) {
                if (!class_exists("\App\Http\Controllers\Admin\{$controller}")) {
                  throw new \Exception("Missing controller: {$controller}");
                }
              }
              return count($controllers) . " controllers found";
            },
            'Routes' => function() {
              $routes = [
                'admin.creative.index',
                'admin.creative.requests.index',
                'admin.creative.reports.index',
                'admin.creative.insights.index',
                'admin.creative.insights.realtime'
              ];
              foreach($routes as $route) {
                if (!\Route::has($route)) {
                  throw new \Exception("Missing route: {$route}");
                }
              }
              return count($routes) . " routes registered";
            },
            'Database Columns' => function() {
              $columns = \DB::select('DESCRIBE tbl_creative_tasks');
              $hasCompletedAt = collect($columns)->pluck('Field')->contains('completed_at');
              return $hasCompletedAt ? "All required columns present" : "Missing completed_at column";
            }
          ];
          
          foreach($tests as $testName => $testFunction) {
            try {
              $result = $testFunction();
              echo "<div class='alert alert-success mb-2'>";
              echo "<strong>✅ {$testName}:</strong> {$result}";
              echo "</div>";
            } catch (\Exception $e) {
              echo "<div class='alert alert-danger mb-2'>";
              echo "<strong>❌ {$testName}:</strong> " . $e->getMessage();
              echo "</div>";
            }
          }
        @endphp

        <div class="mt-3">
          <h6>Feature Tests:</h6>
          <div class="row g-2">
            @php
              $links = [
                ['Dashboard', 'admin.creative.index', 'Basic analytics and overview'],
                ['Requests', 'admin.creative.requests.index', 'Creative request management'],
                ['Reports', 'admin.creative.reports.index', 'CSV export functionality'],
                ['Advanced Insights', 'admin.creative.insights.index', 'AI-powered analytics'],
                ['Real-time Monitor', 'admin.creative.insights.realtime', 'Live dashboard'],
                ['Personal Dashboard', 'personal.creative.dashboard', 'Volunteer view']
              ];
            @endphp
            @foreach($links as [$name, $route, $desc])
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body p-3">
                    <h6 class="mb-1">{{ $name }}</h6>
                    <small class="text-muted d-block mb-2">{{ $desc }}</small>
                    @if(Route::has($route))
                      <a href="{{ route($route) }}" class="btn btn-sm btn-primary">Test {{ $name }}</a>
                      <span class="badge bg-success ms-2">Route OK</span>
                    @else
                      <button class="btn btn-sm btn-secondary" disabled>Missing Route</button>
                      <span class="badge bg-danger ms-2">Route Missing</span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        
        <div class="mt-3">
          <h6>Quick Actions:</h6>
          <div class="btn-group" role="group">
            <button onclick="clearCache()" class="btn btn-warning btn-sm">Clear Cache</button>
            <button onclick="testAPI()" class="btn btn-info btn-sm">Test API</button>
            <button onclick="createSampleData()" class="btn btn-success btn-sm">Create Sample Data</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
function clearCache() {
  fetch('/admin/creative/test?action=clear-cache')
    .then(r => r.text())
    .then(data => {
      alert('Cache cleared! Refresh page to see results.');
      location.reload();
    });
}

function testAPI() {
  fetch('{{ route("admin.creative.insights.api") }}?days=7')
    .then(r => r.json())
    .then(data => {
      console.log('API Response:', data);
      alert('API test successful! Check console for details.');
    })
    .catch(e => {
      console.error('API Error:', e);
      alert('API test failed: ' + e.message);
    });
}

function createSampleData() {
  if(confirm('Create sample data for testing?')) {
    fetch('/admin/creative/test?action=sample-data', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}) 
      .then(r => r.text())
      .then(data => {
        alert('Sample data created! Refresh to see results.');
        location.reload();
      });
  }
}
</script>
@endsection