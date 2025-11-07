@extends('layouts.default')
@php use App\Classes\permission; @endphp
@section('content')
<div class="col-md-12 ">
    <div class="row">
        <div class="col-md-12">
            <!-- Page title -->
            <h2 class="page-title">{{ __('user Activity') }}
             <a href="{{ url('reports') }}" class="ui basic blue button mini offsettop5 float-right"><i class="ui icon chevron left"></i>{{ __("Return") }}</a></h2>
        </div>    
    </div>
    <!-- Summary Cards -->
    <div class="row mb-4">
    <!-- Hours Logged -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2 text-primary">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h6 class="text-muted">Hours Logged</h6>
                <h3>
                    {{
                        number_format(
                            $logs->whereNotNull('session_duration')->sum('session_duration') / 3600,
                            1
                        )
                    }} hrs
                </h3>
            </div>
        </div>
    </div>

    <!-- Average Time -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2 text-success">
                    <i class="fas fa-stopwatch fa-2x"></i>
                </div>
                <h6 class="text-muted">Avg. Time</h6>
                <h3>
                     {{
                        round(
                            $logs->whereNotNull('session_duration')->avg('session_duration') / 3600,
                            2
                        )
                    }} hrs
                </h3>
            </div>
        </div>
    </div>

    <!-- Weekly Growth -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2 text-warning">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h6 class="text-muted">Weekly Growth</h6>
                <h3>{{ number_format($weeklyGrowth, 1) }}%</h3>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2 text-danger">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h6 class="text-muted">Active Users</h6>
                <h3>{{ $logs->unique('user_id')->count() }}</h3>
            </div>
        </div>
    </div>
</div>

    <!-- Activity Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <span>User Activity Logs</span>
            <span class="badge bg-light text-dark">{{ $logs->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Company</th>
                            <th>Department</th>
                            <th>Session</th>
                            <th>IP</th>
                            <th>Location</th>
                            <th>Page</th>
                            <th>Action</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Duration (s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                        <tr>
                            <td>
                                    <span>{{ $log->username ?? 'Guest' }}</span>
                                </div>
                            </td>
                            <td>{{ $log->company ?? 'N/A' }}</td>
                            <td>{{ $log->department ?? 'N/A' }}</td>
                            <td>{{ $log->session_id }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ $log->city }}, {{ $log->country }}</td>
                            <td>{{ $log->page }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->session_start }}</td>
                            <td>
                                @if($log->session_end)
                                    {{ $log->session_end }}
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>{{ $log->session_duration ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

<script>
function exportToPDF() {
    const content = document.querySelector('.container');
    const options = {
        margin: [10, 10, 10, 10],
        filename: 'user-activity-report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, width: 800 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(options).from(content).save();
}
</script>
<script>
    /**
     * Auto-refresh the entire page or specific sections.
     * @param {number} interval - The interval time in milliseconds.
     */
    function autoRefresh(interval) {
        setInterval(() => {
            window.location.reload();
        }, interval);
    }

    // Example: Refresh every 5 minutes (300000ms)
    autoRefresh(300000);
</script>
