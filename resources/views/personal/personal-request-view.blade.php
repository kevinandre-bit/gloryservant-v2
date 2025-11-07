@extends('layouts.personal')

@section('content')
<div class="ui container-fluid col-sm-12 col-md-12 col-lg-12">
    <h2 class="ui header">My Requests</h2>

    @if(session('success'))
        <div class="ui positive message">
            {{ session('success') }}
        </div>
    @endif

    <table class="ui celled table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Message</th>
                <th>Status</th>
                <th>Admin Response</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->type }}</td>
                    <td>{!! nl2br(e($request->message)) !!}</td>
                    <td>
                        <div class="ui label 
                            {{ $request->status === 'approved' ? 'green' : 
                               ($request->status === 'rejected' ? 'red' : 
                               ($request->status === 'resolved' ? 'blue' : 'grey')) }}">
                            {{ ucfirst($request->status) }}
                        </div>
                    </td>
                    <td>{!! nl2br(e($request->admin_response ?? '—')) !!}</td>
                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
