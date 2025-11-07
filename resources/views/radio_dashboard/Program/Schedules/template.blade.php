@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Programming</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Download Template</li>
        </ol>
      </div>
      <div class="ms-auto">
        {{-- Link to same route for now (UI-only) --}}
        <a href="{{ route('program.schedules.template') }}" class="btn btn-primary btn-sm">
          <i class="material-icons-outlined">download</i> Download Template
        </a>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-4">
        <h5 class="mb-3">Program Grid Template</h5>
        <p>Fill the <code>ProgramGrid</code> sheet with the following columns.</p>

        <div class="table-responsive mt-3">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Date</th><th>Start</th><th>End</th><th>ShowName</th><th>ContentType</th><th>AdCampaign</th><th>FileRef</th><th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2025-08-25</td><td>06:00</td><td>07:00</td><td>Morning Worship</td><td>WORSHIP</td><td></td><td>morning_worship_0600.mp3</td><td></td>
              </tr>
              <tr>
                <td>2025-08-25</td><td>07:00</td><td>07:05</td><td>Ad Break</td><td>AD</td><td>SponsorX-30s</td><td>ad_sponsorx_30s.mp3</td><td></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <a href="{{ route('program.schedules.upload') }}" class="btn btn-outline-primary btn-sm">
            <i class="material-icons-outlined">upload_file</i> Go to Upload
          </a>
        </div>
      </div>
    </div>

  </div>
</main>
