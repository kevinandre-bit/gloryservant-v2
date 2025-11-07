@extends('layouts.admin')
    
    
    @section('meta')
        <title>Campuses | Glory Servant</title>
        <meta name="description" content="Workday campuses, view campuses, and export or download campuses.">
    @endsection

    @section('content')
    @include('admin.modals.modal-import-campus')

    <main class="main-wrapper">
  <div class="main-content">

    {{-- Breadcrumb / header --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Campuses</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Campus</li>
          </ol>
        </nav>
      </div>

      <div class="ms-auto">
        <div class="btn-group">
          <button class="btn btn-primary px-3 me-2" data-bs-toggle="modal" data-bs-target="#ImportCampusModal">
            <i class="material-icons-outlined">upload</i> Import
          </button>
          <a href="{{ url('export/fields/campus') }}" class="btn btn-outline-secondary px-3">
            <i class="material-icons-outlined">download</i> Export
          </a>
        </div>
      </div>
    </div>
    <!-- end breadcrumb -->

    <div class="row">
      {{-- Left: form --}}
      <div class="col-md-4">
        <div class="card rounded-4">
          <div class="card-body">

            <h6 class="mb-3 text-uppercase">Add campus</h6>

            @if ($errors->any())
              <div class="alert alert-danger">
                <strong>There were some errors with your submission</strong>
                <ul class="mb-0 mt-2">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form id="add_campus_form" action="{{ url('fields/campus/add') }}" method="post" accept-charset="utf-8">
              @csrf

              <div class="mb-3">
                <label class="form-label">Campus Name <span class="text-muted small">e.g. "Main Office"</span></label>
                <input name="campus"
                       value="{{ old('campus') }}"
                       type="text"
                       class="form-control text-uppercase"
                       placeholder="Enter campus name"
                       required>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-grd-info">
                  <i class="material-icons-outlined">check</i> Save
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>

      {{-- Right: list/table --}}
      <div class="col-md-8">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="mb-3 text-uppercase">Campus list</h6>

            <div class="table-responsive">
              <table id="campusTable" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Campus</th>
                    <th style="width:120px" class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @isset($data)
                    @forelse($data as $campus)
                      <tr>
                        <td>{{ $campus->campus }}</td>
                        <td class="text-end">
                          <a href="{{ url('fields/campus/delete/'.$campus->id) }}"
                             class="btn btn-outline-danger btn-sm"
                             onclick="return confirm('Delete this campus?');"
                             title="Delete">
                            <i class="material-icons-outlined">delete_outline</i>
                          </a>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="2" class="text-center text-muted">No campuses found</td>
                      </tr>
                    @endforelse
                  @endisset
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="ImportCampusModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form action="{{ url('fields/campus/import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <div class="modal-header border-bottom-0 py-2">
            <h5 class="modal-title"><i class="material-icons-outlined">upload_file</i> Import campuses</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted">Upload a CSV file with one campus per row (column name: campus).</p>
            <div class="mb-3">
              <input type="file" name="file" accept=".csv" class="form-control" required>
            </div>
            <div class="small text-muted">Example CSV header: <code>campus</code></div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Import</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Keep campus input uppercase as the user types
    const campusInput = document.querySelector('#add_campus_form input[name="campus"]');
    if (campusInput) {
      campusInput.addEventListener('input', e => e.target.value = e.target.value.toUpperCase());
    }

    // Initialize DataTable for campus table if DataTables available
    if (window.jQuery && $.fn.DataTable && ! $.fn.DataTable.isDataTable('#campusTable')) {
      $('#campusTable').DataTable({
        pageLength: 25,
        responsive: true,
        lengthChange: true,
        ordering: true,
        language: { searchPlaceholder: "Search campuses..." }
      });
    }
  });
</script>
@endpush

    @endsection

    @section('scripts')
    <script type="text/javascript">
    $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: true,ordering: true});
    function validateFile() {
        var f = document.getElementById("csvfile").value;
        var d = f.lastIndexOf(".") + 1;
        var ext = f.substr(d, f.length).toLowerCase();
        if (ext == "csv") { } else {
            document.getElementById("csvfile").value="";
            $.notify({
            icon: 'ui icon times',
            message: "Please upload only CSV file format."},
            {type: 'danger',timer: 400});
        }
    }
    </script>

    @endsection


  