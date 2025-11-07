@extends('layouts.admin_v2') {{-- change if your layout is different --}}

@section('content')
<main class="">
  <div class="container-fluid" style="margin-top: 2%;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
      <h6 class="mb-0 text-uppercase">Volunteers</h6>
    </div>
    <hr>

    {{-- quick search/filter --}}
    <form method="GET" class="row g-2 mb-3">
      <div class="col-12 col-md-6">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          class="form-control"
          placeholder="Search name, ID or campus…">
      </div>
      <div class="col-12 col-md-4">
        <select name="campus" class="form-select">
          <option value="">All Campuses</option>
          @isset($campuses)
            @foreach($campuses as $camp)
              @php $name = is_object($camp) ? ($camp->campus ?? '') : (string)$camp; @endphp
              <option value="{{ $name }}" {{ request('campus') == $name ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          @endisset
        </select>
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i> Filter</button>
      </div>
    </form>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <th>ID No</th>
              <th>Volunteer</th>
              <th>Email</th>
              <th>Employment Status</th>
              <th>Campus</th>
              <th>Edit</th>
              <th>Activity</th>
            </thead>
            <tbody>
              @foreach ($volunteers ?? [] as $v)
                <tr>
                  <td>{{ $v->idno ?? '—' }}</td>
                  <td>{{ trim(($v->lastname ?? '').', '.($v->firstname ?? '').' '.($v->mi ?? '')) }}</td>
                  <td>{{ $v->email ?? '—' }}</td>
                  <td>{{ $v->employmentstatus ?? '—' }}</td>
                  <td>{{ $v->campus ?? '—' }}</td>
                  <td class="text-center">
                    <a href="{{ route('volunteers.edit', $v->id) }}" class="btn btn-sm btn-primary">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('volunteers.activity', $v->id) }}" class="btn btn-sm btn-secondary">
                      <i class="bi bi-clock-history"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th>ID No</th>
                <th>Volunteer</th>
                <th>Email</th>
                <th>Employment Status</th>
                <th>Campus</th>
                <th>Edit</th>
                <th>Activity</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && $.fn.DataTable) {
      $('#example2').DataTable({ pageLength: 25, order: [[1,'asc']] });
    }
  });
</script>
@endpush