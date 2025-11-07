@extends('layouts.admin')

@section('meta')
  <title>Wellness Cases</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">assignment</i> Wellness Cases</h5>
      <form class="d-flex" method="get">
        <input class="form-control me-2" type="search" name="q" value="{{ request('q') }}" placeholder="Search…">
        <button class="btn btn-outline-secondary">Search</button>
      </form>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card rounded-4">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Volunteer</th>
                <th>Group</th>
                <th>Status</th>
                <th>Severity</th>
                <th>Opened</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            @foreach($cases as $c)
              <tr>
                <td>#{{ $c->id }}</td>
                <td>{{ $c->volunteer_name }}</td>
                <td>{{ $c->group_name }}</td>
                <td><span class="badge bg-light ">{{ strtoupper($c->status) }}</span></td>
                <td>
                  <span class="badge {{ $c->severity==='high'?'bg-danger':($c->severity==='medium'?'bg-warning text-dark':'bg-success') }}">
                    {{ strtoupper($c->severity) }}
                  </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($c->created_at)->format('Y-m-d') }}</td>
                <td class="text-end">
                  <a href="{{ route('wellness.cases.show',$c->id) }}" class="btn btn-sm btn-primary">Open</a>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>

        {{ $cases->links() }}
      </div>
    </div>
  </div>
</main>
@endsection