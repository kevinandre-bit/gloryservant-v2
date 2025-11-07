@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-4">Attendance Links</h2>
        <ul>
            @foreach($meetings as $meeting)
                <li>
                    <strong>{{ $meeting->name }}:</strong>
                    <a href="{{ route('attendance.checkin', $meeting->slug) }}" target="_blank" rel="noopener noreferrer">
                        {{ route('attendance.checkin', $meeting->slug) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
