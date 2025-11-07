@extends('layouts.crw_layout')

@section('meta')
    <title>Creative Requests</title>
@endsection

@section('content')
<main class="main-wrapper">
    <div class="main-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Creative Workload</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Requests</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('admin.creative.requests.create-graphic') }}" class="btn btn-success">
                        <i class="material-icons-outlined">image</i> New Graphic Request
                    </a>
                    <a href="{{ route('admin.creative.requests.create-video') }}" class="btn btn-primary">
                        <i class="material-icons-outlined">videocam</i> New Video Request
                    </a>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Requester</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Tasks</th>
                                <th>Due Date</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.creative.requests.show', $request->id) }}">
                                            {{ Str::limit($request->title, 40) }}
                                        </a>
                                    </td>
                                    <td>{{ $request->firstname }} {{ $request->lastname }}</td>
                                    <td><span class="badge bg-light-primary text-primary">{{ ucfirst($request->request_type) }}</span></td>
                                    <td><span class="badge bg-light-warning text-warning">{{ ucfirst($request->priority) }}</span></td>
                                    <td><span class="badge bg-light-info text-info">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $request->tasks_count ?? 0 }}</span>
                                    </td>
                                    @php
                                        // created_at / desired_due_at may be strings from DB joins; ensure we format safely
                                        $due = $request->desired_due_at ?? null;
                                        try {
                                            $dueLabel = $due ? \Carbon\Carbon::parse($due)->format('M d, Y') : 'N/A';
                                        } catch (\Throwable $e) {
                                            $dueLabel = is_string($due) ? $due : 'N/A';
                                        }

                                        $created = $request->created_at ?? null;
                                        try {
                                            $createdLabel = $created ? \Carbon\Carbon::parse($created)->format('M d, Y') : '';
                                        } catch (\Throwable $e) {
                                            $createdLabel = is_string($created) ? $created : '';
                                        }
                                    @endphp
                                    <td>{{ $dueLabel }}</td>
                                    <td>{{ $createdLabel }}</td>
                                    <td>
                                        <a href="{{ route('admin.creative.requests.show', $request->id) }}" class="btn btn-sm btn-light">
                                            <i class="material-icons-outlined">visibility</i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No creative requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>

    </div>
</main>
@endsection