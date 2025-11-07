@extends('layouts.default')

@section('meta')
    <title>Meeting Attendance | Glory Servant</title>
    <meta name="description" content="View attendance for all meetings.">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <h2 class="page-title">Meeting Attendance <a href="{{ url('reports') }}" class="ui basic blue button mini offsettop5 float-right"><i class="ui icon chevron left"></i>{{ __("Return") }}</a></h2>
    </div>

    <div class="row">
        <div class="box box-success">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover" id="attendance-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Meeting</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $attendance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $attendance->employee }}</td>
                            <td>{{ $attendance->meeting_type }}</td>
                            <td>{{ $attendance->meeting_type}}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#attendance-table').DataTable({
            responsive: true,
            pageLength: 20,
            lengthChange: false,
            order: [[4, 'desc']] // Order by meeting date
        });
    });
</script>
@endsection
