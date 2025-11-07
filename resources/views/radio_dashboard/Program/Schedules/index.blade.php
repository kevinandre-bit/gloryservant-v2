@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Programming</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Batches</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('program.schedules.upload') }}" class="btn btn-primary btn-sm">
          <i class="material-icons-outlined">upload_file</i> Upload Grid
        </a>
        <a href="{{ route('program.schedules.template') }}" class="btn btn-outline-secondary btn-sm">
          <i class="material-icons-outlined">description</i> Download Template
        </a>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Uploaded Program Grids</h5>
          <div class="d-flex gap-2">
            <input class="form-control form-control-sm" placeholder="Search period/file…">
            <button class="btn btn-outline-secondary btn-sm"><i class="material-icons-outlined">search</i></button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Period</th>
                <th>Start</th>
                <th>End</th>
                <th>File</th>
                <th>Status</th>
                <th>Uploaded By</th>
                <th>Uploaded At</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($batches as $b)
                <tr>
                  <td class="fw-semibold">{{ $b['period_label'] }}</td>
                  <td>{{ optional($b['starts_on'])->format('Y-m-d') }}</td>
                  <td>{{ optional($b['ends_on'])->format('Y-m-d') }}</td>
                  <td>{{ $b['original_filename'] }}</td>
                  <td>
                    <span class="badge
                      @if($b['status']==='PENDING') bg-secondary
                      @elseif($b['status']==='IMPORTED') bg-info
                      @elseif($b['status']==='EVALUATED') bg-success
                      @else bg-danger @endif">
                      {{ $b['status'] }}
                    </span>
                  </td>
                  <td>{{ $b['uploader'] }}</td>
                  <td>{{ optional($b['created_at'])->diffForHumans() }}</td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-light" href="{{ route('program.schedules.show', $b['id']) }}">
                      View
                    </a>
                  </td>
                </tr>

                    @if($b['status']==='IMPORTED')
                      <button class="btn btn-sm btn-primary">Evaluate</button>
                    @endif
                    <button class="btn btn-sm btn-outline-secondary">Download</button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4">No batches yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</main>
