@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>⚠️ Already Checked In</h2>
    <p>You already checked in for {{ $meeting->name }} today.</p>
</div>
@endsection
