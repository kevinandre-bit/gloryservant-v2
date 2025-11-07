@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Report Preview</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('reports.studio') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active">{{ $meta['title'] }}</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">Print</button>
        <a class="btn btn-primary btn-sm" href="{{ route('reports.build',['type'=>$meta['type'],'from'=>$meta['period']['from']]) }}">Adjust</a>
      </div>
    </div>

    {{-- ADMIN DAILY SUMMARY LAYOUT --}}
    @if(($meta['type'] ?? '') === 'daily_admin' && isset($admin))
      <div class="card rounded-4 mb-3">
        <div class="card-body p-4">
          <h4 class="mb-1">Admin Daily Summary – Broadcast Operations</h4>
          <div>Date: <strong>{{ \Illuminate\Support\Carbon::parse($admin['date'])->format('F j, Y') }}</strong></div>
          <div>Prepared by: <strong>{{ $meta['prepared_by'] }}</strong></div>
        </div>
      </div>

      <div class="row g-3">
        {{-- 1. Technician Schedule --}}
        <div class="col-12">
          <div class="card rounded-4">
            <div class="card-body p-4">
              <h5 class="mb-3">1) Technician Schedule (Today)</h5>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr><th>Technician</th><th>Shift Hours</th><th>Status</th><th>Notes</th></tr>
                  </thead>
                  <tbody>
                    @foreach($admin['tech'] as $t)
                      <tr>
                        <td>{{ $t['name'] ?? '—' }}</td>
                        <td>{{ $t['shift'] ?? '—' }}</td>
                        <td>{{ $t['status'] ?? '—' }}</td>
                        <td>{{ $t['notes'] ?? '—' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- 2. Overall Broadcast Status --}}
        <div class="col-12 col-xl-6">
          <div class="card rounded-4">
            <div class="card-body p-4">
              <h5 class="mb-3">2) Overall Broadcast Status</h5>
              <div class="row g-3">
                <div class="col-6">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="fw-semibold">Stations Monitored</div>
                    <div class="fs-4">{{ (int)$admin['overall']['stations_monitored'] }}</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="fw-semibold">Overall Uptime</div>
                    <div class="fs-4">{{ number_format((float)$admin['overall']['uptime_pct'],1) }}%</div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="fw-semibold">Total Downtime</div>
                    <div class="fs-5">{{ $admin['overall']['total_downtime'] }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- 3. Key Incidents --}}
        <div class="col-12 col-xl-6">
          <div class="card rounded-4">
            <div class="card-body p-4">
              <h5 class="mb-3">3) Key Incidents</h5>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Station</th><th>Time</th><th>Issue</th><th>Downtime</th><th>Resolution</th><th>Follow-up</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($admin['incidents'] as $i)
                      <tr>
                        <td>{{ $i['station'] ?? '—' }}</td>
                        <td>{{ $i['time'] ?? '—' }}</td>
                        <td>{{ $i['issue'] ?? '—' }}</td>
                        <td>{{ $i['downtime'] ?? '—' }}</td>
                        <td>{{ $i['resolution'] ?? '—' }}</td>
                        <td>{{ $i['followup'] ?? '—' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- 4. Technician Performance --}}
        <div class="col-12">
          <div class="card rounded-4">
            <div class="card-body p-4">
              <h5 class="mb-3">4) Technician Performance</h5>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr><th>Technician</th><th>Tasks Completed</th><th>Notes</th></tr>
                  </thead>
                  <tbody>
                    @foreach($admin['performance'] as $p)
                      <tr>
                        <td>{{ $p['tech'] ?? '—' }}</td>
                        <td>{{ $p['tasks'] ?? '—' }}</td>
                        <td>{{ $p['notes'] ?? '—' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- 5. Admin Observations --}}
        <div class="col-12 col-xl-6">
          <div class="card rounded-4">
            <div class="card-body p-4">
              <h5 class="mb-3">5) Admin Observations</h5>
              <pre class="mb-0" style="white-space:pre-wrap">{{ $admin['observations'] }}</pre>
            </div>
          </div>
        </div>

        {{-- 6. Manager Follow-Up --}}
        <div class="col-12 col-xl-6">
          <div class="card rounded-4">
            <div class="card-body p-4">
              <h5 class="mb-3">6) Manager Follow-Up (Action Required)</h5>
              @php
                $lines = array_filter(array_map('trim', explode("\n", (string)$admin['manager_actions'])));
              @endphp
              @if(count($lines))
                <ul class="mb-0">
                  @foreach($lines as $line)
                    <li>{{ $line }}</li>
                  @endforeach
                </ul>
              @else
                <div>—</div>
              @endif
            </div>
          </div>
        </div>
      </div>
    @else
      {{-- (Optional) fallback for other types retained from earlier system --}}
      <div class="card rounded-4"><div class="card-body p-4">
        <h5 class="mb-2">{{ $meta['title'] }}</h5>
        <div>{{ $meta['period']['from'] }} → {{ $meta['period']['to'] }}</div>
        <div class="mt-2">This type uses the older generic preview. Switch to “Daily Operations” for the Admin Daily Summary layout.</div>
      </div></div>
    @endif

  </div>
</main>