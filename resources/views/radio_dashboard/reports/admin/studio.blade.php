@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">analytics</i>Report Studio
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ route('reports.build', ['type'=>'daily_admin']) }}" class="btn btn-primary">
          <i class="material-icons-outlined">today</i> New Daily Ops
        </a>
        <a href="{{ route('reports.build', ['type'=>'weekly']) }}" class="btn btn-outline-primary">
          <i class="material-icons-outlined">date_range</i> New Weekly Summary
        </a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="card rounded-4">
          <div class="card-body text-center">
            <div class="small">Aired as Scheduled (yesterday)</div>
            <div class="display-6 fw-semibold">{{ $kpis['matchPct'] }}%</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card rounded-4">
          <div class="card-body text-center">
            <div class="small">Network Uptime (24h)</div>
            <div class="display-6 fw-semibold">{{ $kpis['uptimePct'] }}%</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card rounded-4">
          <div class="card-body text-center">
            <div class="small">Critical Alerts</div>
            <div class="display-6 fw-semibold">{{ $kpis['critAlerts'] }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card rounded-4 mt-3">
      <div class="card-body">
        <h6 class="mb-3 d-flex align-items-center gap-2">
          <i class="material-icons-outlined">auto_awesome_mosaic</i>Quick Start
        </h6>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('reports.build', ['type'=>'daily_tech']) }}" class="btn btn-light">
            <i class="material-icons-outlined">handyman</i> Daily — Technician
          </a>
          <a href="{{ route('reports.build', ['type'=>'daily_op']) }}" class="btn btn-light">
            <i class="material-icons-outlined">schedule</i> Daily — Operator
          </a>
          <a href="{{ route('reports.build', ['type'=>'daily_admin']) }}" class="btn btn-light">
            <i class="material-icons-outlined">summarize</i> Daily — Admin
          </a>
          <a href="{{ route('reports.build', ['type'=>'weekly']) }}" class="btn btn-light">
            <i class="material-icons-outlined">insights</i> Weekly Summary
          </a>
        </div>
      </div>
    </div>

    <div class="card rounded-4 mt-3">
      <div class="card-body">
        <h6 class="mb-3 d-flex align-items-center gap-2">
          <i class="material-icons-outlined">history</i>Recent Reports
        </h6>
        <ul class="list-group list-group-flush">
          @foreach($recent as $r)
            <li class="list-group-item d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <i class="material-icons-outlined">description</i>
                <div>
                  <div class="fw-semibold">{{ $r['title'] }}</div>
                  <div class="small">{{ $r['when'] }}</div>
                </div>
              </div>
              <a href="{{ route('reports.preview', ['type'=>$r['type']]) }}" class="btn btn-sm btn-outline-secondary">
                View
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>

  </div>
</main>
