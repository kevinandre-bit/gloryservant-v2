@extends('layouts.personal')

@section('meta')
    <title>Notifications | Glory Servant</title>
  
@endsection

@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Notifications</h2>

            <!-- Filter Dropdown -->
            <div class="mb-3">
                <select class="form-select" id="filter" aria-label="Notification Filter">
                    <option value="all" selected>All Notifications</option>
                    <option value="read">Read</option>
                    <option value="unread">Unread</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                </select>
            </div>

            <!-- Notification List Container -->
            <div id="notification-list" class="list-group mb-3">
                <h5 class="mb-2">Notifications</h5>
            </div>

            <!-- Load More Button -->
            <button class="btn btn-primary w-100" id="load-more">Load More</button>
        </div>
    </div>
</div>



@endsection


@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@endsection
