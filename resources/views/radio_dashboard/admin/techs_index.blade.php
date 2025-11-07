@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Technicians</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active">Directory</li>
        </ol>
      </div>
    </div>

    @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

    <div class="card rounded-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Registered Technicians (Radio Operators)</h5>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Campus</th>
                <th>Department</th>
                <th>Schedule</th>
                <th class="text-end"></th>
              </tr>
            </thead>
            <tbody>
              <tbody>
                @forelse($technicians as $t)
                  <tr>
                    <td class="fw-semibold">{{ $t->name }}</td>
                    <td>{{ $t->email }}</td>
                    <td>{{ $t->phone }}</td>
                    <td>{{ $t->campus }}</td>
                    <td>{{ $t->department }}</td>
                    <td class="text-truncate" style="max-width: 320px;">
                      {{ $t->schedule_summary ?: '—' }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">No technicians found.</td>
                  </tr>
                @endforelse
              </tbody>
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</main>