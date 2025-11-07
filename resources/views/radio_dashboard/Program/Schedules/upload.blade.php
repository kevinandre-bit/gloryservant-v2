@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Programming</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('program.schedules.index') }}">Batches</a></li>
          <li class="breadcrumb-item active" aria-current="page">Upload Grid</li>
        </ol>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-8">
        <div class="card rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">Upload Program Grid (Excel)</h5>

            {{-- UI-only form (no backend yet) --}}
            <form>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Period Label</label>
                  <input type="text" class="form-control" placeholder="2025-W35">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Starts On</label>
                  <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Ends On</label>
                  <input type="date" class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-label">Excel File (.xlsx / .xls)</label>
                  <input type="file" class="form-control" accept=".xlsx,.xls">
                  <div class="mt-1">
                    Sheet: <code>ProgramGrid</code> — Columns: Date, Start, End, ShowName, ContentType, AdCampaign?, FileRef?, Notes
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-primary">Upload</button>
                <a class="btn btn-outline-secondary" href="{{ route('program.schedules.template') }}">Download Template</a>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="card rounded-4">
          <div class="card-body p-4">
            <h6>Tips</h6>
            <ul class="mb-0">
              <li>Use local time (Port-au-Prince).</li>
              <li>No overlaps: each row must have Start &lt; End.</li>
              <li>Ads rows should include <code>AdCampaign</code>.</li>
              <li>File name pattern: <code>program_grid_YYYY-Www.xlsx</code>.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>
