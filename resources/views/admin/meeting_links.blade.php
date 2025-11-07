@extends('layouts.default')

@section('meta')
    <title>Meeting Links | Glory Servant</title>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <h2 class="page-title">Meeting Links</h2>
    </div>

    <div class="row">
        <div class="box box-success">
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $meeting)
                        <tr>
                            <td>{{ $meeting->name }}</td>
                            <td>{{ $meeting->slug }}</td>
                            <td>{{ $meeting->meeting_code }}</td>
                            <td>{{ $meeting->description }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary copy-btn" data-link="{{ url('attendance/' . $meeting->meeting_code) }}" data-alert-id="alert-{{ $meeting->id }}">
                                    Copy Link
                                </button>
                                <div id="alert-{{ $meeting->id }}" class="alert alert-info mb-0 py-1 px-2 d-none fade" style="font-size: 0.9rem;">
                                    Copied!
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', function () {
            const link = this.getAttribute('data-link');
            const alertId = this.getAttribute('data-alert-id');
            const alertElement = document.getElementById(alertId);

            navigator.clipboard.writeText(link).then(() => {
                // Show the alert
                alertElement.classList.remove('d-none');
                alertElement.classList.add('show');

                // Hide after 2.5 seconds
                setTimeout(() => {
                    alertElement.classList.remove('show');
                    alertElement.classList.add('d-none');
                }, 2500);
            });
        });
    });
</script>

@endsection
