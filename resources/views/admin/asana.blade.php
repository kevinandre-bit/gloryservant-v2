@extends('layouts.default')

@section('meta')
    <title>Asana Dashboard | Glory Servant</title>
    <meta name="description" content="View Asana project tasks.">
@endsection

@section('styles')
    <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
    <style>.datepicker { z-index: 9999 }</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <h2 class="page-title">{{ __('Asana Dashboard') }}</h2>

       <table class="table table-bordered">
    <thead>
        <tr>
            <th>Task Name</th>
            <th>Completed</th>
            <th>Assignee</th>
            <th>Due Date</th>
            <th>Created At</th>
            <th>Last Modified</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
            <tr>
                <td>{{ $task['name'] ?? 'N/A' }}</td>
                <td>
                    @if(isset($task['completed']))
                        {{ $task['completed'] ? '✅' : '❌' }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $task['assignee']['name'] ?? 'Unassigned' }}</td>
                <td>{{ $task['due_on'] ?? 'No due date' }}</td>
                <td>{{ $task['created_at'] ?? 'N/A' }}</td>
                <td>{{ $task['modified_at'] ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


    </div>
</div>
@endsection
