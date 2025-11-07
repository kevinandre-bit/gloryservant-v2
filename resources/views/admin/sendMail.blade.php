@extends('layouts.default')

@section('meta')
    <title>Send Notifications | Glory Servant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="page-title">Send Notification</h2>
        </div>
    </div>

    <div class="row">
        <!-- Send Notification Form -->
        <div class="col-md-8 mb-4">
            <div class="bg-secondary p-4 rounded shadow-sm">
                <h4 class="mb-3">Send Notification</h4>

                <form action="{{ route('send.notification') }}" method="POST">
                    @csrf

                    <!-- Target Selection -->
                    <div class="mb-3">
                        <label for="target_type" class="form-label">Send To:</label>
                        <select name="target_type" id="target_type" class="form-select" required>
                            <option value="">Select Target...</option>
                            <option value="user">Individual User</option>
                            <option value="company">Company</option>
                            <option value="department">Department</option>
                        </select>
                    </div>

                    <!-- Individual User Selection -->
                    <div class="mb-3" id="user-select" style="display: none;">
                        <label for="receiver_id" class="form-label">Select User</label>
                        <select name="receiver_id" class="form-select">
                            <option value="">Select User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Company Selection -->
                    <div class="mb-3" id="company-select" style="display: none;">
                        <label for="company_id" class="form-label">Select Company</label>
                        <select name="company_id" class="form-select">
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department Selection -->
                    <div class="mb-3" id="department-select" style="display: none;">
                        <label for="department_id" class="form-label">Select Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">Select Department...</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->department }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Message Input -->
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>

        <!-- Notification Stats Table -->
        <div class="col-12">
            <div class="bg-secondary p-4 rounded shadow-sm">
                <h4 class="mb-4">Sent Notifications</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Notification Code</th>
                                <th>Message</th>
                                <th>Target(s)</th>
                                <th>Read</th>
                                <th>Unread</th>
                                <th>Rate (%)</th>
                                <th>Sent At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->notification_code }}</td>
                                    <td>{{ $notification->message }}</td>
                                    <td>{{ ucfirst($notification->target_type) }} - {{ $notification->target_name }}</td>
                                    <td>{{ $notification->read_count }}</td>
                                    <td>{{ $notification->unread_count }}</td>
                                    <td>{{ $notification->rate }}%</td>
                                    <td>{{ $notification->sent_at }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary me-1">Duplicate</a>
                                        <form action="#" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if($notifications->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No notifications sent yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const targetType = document.querySelector('select[name="target_type"]');
        const userSelect = document.getElementById("user-select");
        const companySelect = document.getElementById("company-select");
        const departmentSelect = document.getElementById("department-select");

        targetType.addEventListener("change", function () {
            userSelect.style.display = this.value === "user" ? "block" : "none";
            companySelect.style.display = this.value === "company" ? "block" : "none";
            departmentSelect.style.display = this.value === "department" ? "block" : "none";
        });
    });
</script>
@endsection
