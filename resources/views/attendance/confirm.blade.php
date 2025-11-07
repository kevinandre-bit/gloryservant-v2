@extends('layouts.personal')

@section('content')
<div class="container text-center py-5">
    <h1 class="{{ ($ok ?? false) ? 'text-success' : 'text-danger' }}">
        {{ ($ok ?? false) ? 'Meeting Attendance' : 'Cannot Check In' }}
    </h1>

    <p>
        @if (!empty($message))
            {{ $message }}
        @else
            You can check in for <strong>{{ $meeting }}</strong>.
        @endif
    </p>

    @if (!empty($clockSuccess))
        <p class="mt-2">
            Marked at {{ $clockSuccess['date'] }} {{ $clockSuccess['time'] }}
        </p>
    @endif

    {{-- In auto() mode, attendance is already marked by the controller --}}
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">Back to Home</a>
</div>
@endsection