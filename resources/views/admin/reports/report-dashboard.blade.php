@extends('layouts.admin')
@section('title','Report Dashboard')

@section('content')

<main class="main-wrapper analytics-dashboard">
  <div class="main-content">


    {{-- Header / Breadcrumb --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Analytics</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
              <a href="{{ route('admin.reports.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Team Productivity Dashboard</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <div class="btn-group">
          <button type="button" class="btn btn-primary">Export</button>
          <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
            <a class="dropdown-item" href="javascript:void(0)">Export PDF</a>
            <a class="dropdown-item" href="javascript:void(0)">Export Excel</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="javascript:void(0)">Schedule Email</a>
          </div>
        </div>
      </div>
    </div>

<script id="dashboard-json" type="application/json">
{!! json_encode($vm, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>
    {{-- Filters --}}
    <form id="filterForm" class="row g-2 align-items-end" method="GET" action="{{ route('admin.reports.dashboard') }}">
  <div class="col-12 col-md-3">
    <label class="form-label">Date Range</label>
    <select id="rangeSelect" name="range" class="form-select">
      <option value="WEEK">This Week</option>
      <option value="MONTH">This Month</option>
      <option value="QUARTER">This Quarter</option>
      <option value="YEAR">This Year</option>
      <option value="CUSTOM">Custom…</option>
    </select>
  </div>

  <div class="col-6 col-md-3 d-none" id="customFromWrap">
    <label class="form-label">From</label>
    <input type="date" id="fromDate" name="from" class="form-control">
  </div>

  <div class="col-6 col-md-3 d-none" id="customToWrap">
    <label class="form-label">To</label>
    <input type="date" id="toDate" name="to" class="form-control">
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Team</label>
    <select id="teamSelect" name="team_id" class="form-select"></select>
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Member</label>
    <select id="memberSelect" name="person_id" class="form-select"></select>
  </div>

  <div class="col-12 col-md-3">
    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-sliders me-1"></i> Apply
    </button>
  </div>
</form>


    {{-- KPI Row --}}<br>
    <div class="row g-3">
      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Attendance Rate</div>
                <div class="h3 mb-0 fw-bold" id="kpiAttendance">—%</div>
                <div class="small">
                  <span id="kpiAttendanceDelta" class="badge rounded-pill"></span>
                  <span class="text-secondary">vs prior period</span>
                </div>
              </div>
              <div class="badge-icon rounded-circle bg-primary">
                <i class="bi bi-people-fill fs-5"></i>
              </div>
            </div>
            <div class="mt-3">
              <canvas id="sparkAttendance" height="30"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Avg Productivity</div>
                <div class="h3 mb-0 fw-bold"><span id="kpiScore">—</span> / 100</div>
                <div class="small">
                  <span id="kpiScoreDelta" class="badge rounded-pill"></span>
                  <span class="text-secondary">vs prior period</span>
                </div>
              </div>
              <div class="badge-icon rounded-circle bg-success">
                <i class="bi bi-speedometer2 fs-5"></i>
              </div>
            </div>
            <div class="mt-3">
              <canvas id="sparkScore" height="30"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Active Members</div>
                <div class="h3 mb-0 fw-bold" id="kpiActive">—</div>
                <div class="small text-secondary">across <span id="kpiTeams">—</span> teams</div>
              </div>
              <div class="badge-icon rounded-circle bg-danger">
                <i class="bi bi-person-check-fill fs-5"></i>
              </div>
            </div>
            <div class="mt-3">
              <div class="progress" style="height:8px;">
                <div id="kpiActiveBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
              </div>
              <div class="small text-secondary mt-1">target 150</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-3">
        <div class="card raised h-80">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-secondary text-uppercase small">Overall %</div>
                <div class="h3 mb-0 fw-bold" id="kpiEngagement">—%</div>
                <div class="small">
                  <span id="kpiEngagementDelta" class="badge rounded-pill"></span>
                  <span class="text-secondary">3-week trend</span>
                </div>
              </div>
              <div class="badge-icon rounded-circle bg-warning">
                <i class="bi bi-graph-up-arrow fs-5"></i>
              </div>
            </div>
            <div class="mt-3">
              <canvas id="sparkEngagement" height="30"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-3 mt-1">
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Attendance Trend (12 weeks)</h6>
            <span class="dash-lable bg-primary">Line • Weekly</span>
          </div>
          <div class="card-body">
            <div class="chart-container1">
              <canvas id="chartAttendance"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 d-flex">
        <div class="card w-100 d-flex flex-column">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Productivity Forecast</h6>
            <span class="dash-lable bg-primary">Line • Next 4 weeks</span>
          </div>
          <div class="card-body" style="height: 360px;">
            <div style="height:100%;">
              <canvas id="chartForecast"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Insights & Leaderboards (gapless, tabbed) --}}
      {{-- Smart Insights --}}
      <div class="row g-3 align-items-stretch mt-1">
      {{-- Smart Insights --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="card w-100 d-flex flex-column" style="height: 420px;">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Smart Insights</h6>
          </div>
          <div class="card-body overflow-auto">
            <ul id="insightsList" class="list-unstyled mb-0 vstack gap-3"></ul>
          </div>
        </div>
      </div>

      {{-- Leaderboards (tabs) --}}
      <div class="col-12 col-lg-8 d-flex">
        <div class="card w-100 d-flex flex-column">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
              <h6 class="mb-0">Productivity</h6>
              <span class="text-secondary small d-none d-md-inline">Report</span>
            </div>

            {{-- tabs --}}
            <ul class="nav nav-pills nav-pills-sm" id="lbTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#lbTop" type="button" role="tab">
                  Top Performers
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#lbRisk" type="button" role="tab">
                  Bottom / At-Risk
                </button>
              </li>
            </ul>
          </div>

          <div class="card-body flex-grow-1">
            <div class="tab-content h-100">
              {{-- Top table --}}
              <div class="tab-pane fade show active h-100" id="lbTop" role="tabpanel">
                <div class="table-responsive">
                  <table class="table align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Name</th><th>Team</th><th>Score</th>
                        <th style="width:200px;">Progress</th>
                      </tr>
                    </thead>
                    <tbody id="tblTop"><!-- Filled by JS --></tbody>
                  </table>
                </div>
              </div>

              {{-- Risk table --}}
              <div class="tab-pane fade h-100" id="lbRisk" role="tabpanel">
                <div class="d-flex justify-content-end mb-2">
                  <span class="text-secondary small">Attendance &lt; 60% or Score &lt; 60</span>
                </div>
                <div class="table-responsive">
                  <table class="table align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Name</th><th>Team</th><th>Attendance</th><th>Score</th><th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="tblRisk"><!-- Filled by JS --></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> <!-- /card-body -->
        </div>
      </div>
    </div>


    

    {{-- Members snapshot --}}
    <div class="card mt-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Members Snapshot</h6>
        <div class="text-secondary small">Last 4 weeks — hover to see values</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Member</th><th>Team</th><th>Attendance</th><th>Productivity</th><th>Trend</th>
              </tr>
            </thead>
            <tbody id="tblMembers">
              {{-- Filled by JS --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>


    {{-- Charts Row 2 --}}
    <div class="row g-3 mt-1 align-items-stretch">
      <div class="col-12 col-lg-5 d-flex">
        <div class="card w-100 d-flex flex-column">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Category Mix</h6>
            <span class="dash-lable">Donut • Weight</span>
          </div>
          <div class="card-body position-relative" style="height: 360px;">
            <div style="height:100%;">
              <canvas id="chartCategories"></canvas>
            </div>
            <div class="piechart-legend">
              <div class="fw-semibold">Categories</div>
              <div class="text-secondary small">Attendance • Productivity • Engagement • Training</div>
            </div>
          </div>
        </div>
      </div>

      
    </div>


  </div>
</main>
@endsection

@push('scripts')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  window.PERSON_SHOW_BASE = "{{ rtrim(route('admin.reports.people.show', ['id' => 0]), '0') }}";
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  /* =========================================
     1) Read the VM JSON rendered by Blade
  ========================================= */
  let RAW = {};
  try { RAW = JSON.parse(document.getElementById('dashboard-json').textContent || '{}'); }
  catch(e){ console.error('Dashboard JSON parse error', e); RAW = {}; }

  /* =========================================
     2) Map VM -> chart-friendly shape
  ========================================= */
  const weeks = (RAW.trend && Array.isArray(RAW.trend.labels)) ? RAW.trend.labels : [];

  // teamsCompare (array) -> teams object { "Team A": {score: 88.5}, ... }
  const teamsObj = {};
  (RAW.teamsCompare || []).forEach(t => {
    if (t && t.team) teamsObj[String(t.team)] = { score: (typeof t.score === 'number' ? t.score : null) };
  });

  const dataset = {
    attendancePct: (RAW.trend && Array.isArray(RAW.trend.attendance)) ? RAW.trend.attendance : [],
    avgScore:      Array.isArray(RAW.avgScoreHistory) ? RAW.avgScoreHistory : [],
    teams:         teamsObj,
    categoryMix:   RAW.categoryMix || {},
    members:       Array.isArray(RAW.members) ? RAW.members : [],
    activeMembers: RAW.kpis && Number.isFinite(RAW.kpis.activeMembers) ? RAW.kpis.activeMembers : 0,
    teamCount:     RAW.kpis && Number.isFinite(RAW.kpis.teamCount)     ? RAW.kpis.teamCount     : 0,
    targetActive:  Number.isFinite(RAW.targetActive) ? RAW.targetActive : 150
  };

  /* =========================================
     3) Helpers
  ========================================= */
  const avg = arr => (arr.length ? (arr.reduce((a,b)=>a+(+b||0),0) / arr.length) : 0);
  const pctDelta = (curr, prev) => (prev === 0 ? 0 : ((curr - prev) / prev) * 100);
  const lastOf = (arr, fb=0) => (arr.length ? (+arr[arr.length-1]||0) : fb);

  /* =========================================
     4) KPIs
  ========================================= */
  (function renderKPIs(){
    const currAtt      = lastOf(dataset.attendancePct, 0);
    const prevAttAvg   = avg(dataset.attendancePct.slice(-5,-1));
    const currScore    = lastOf(dataset.avgScore, 0);
    const prevScoreAvg = avg(dataset.avgScore.slice(-5,-1));
    const engagement   = Math.round(currAtt*0.6 + currScore*0.4);

    const elAtt = document.getElementById('kpiAttendance');
    if (elAtt) elAtt.textContent = dataset.attendancePct.length ? (currAtt + '%') : '—%';

    const elDAtt = document.getElementById('kpiAttendanceDelta');
    if (elDAtt){
      const d = pctDelta(currAtt, prevAttAvg);
      elDAtt.textContent = (d>=0?'▲ ':'▼ ') + Math.abs(d).toFixed(1) + '%';
      elDAtt.classList.add(d>=0 ? 'bg-success-subtle' : 'bg-danger-subtle', d>=0 ? 'text-success' : 'text-danger');
    }

    const elSc = document.getElementById('kpiScore');
    if (elSc) elSc.textContent = dataset.avgScore.length ? currScore : '—';

    const elDSc = document.getElementById('kpiScoreDelta');
    if (elDSc){
      const d = pctDelta(currScore, prevScoreAvg);
      elDSc.textContent = (d>=0?'▲ ':'▼ ') + Math.abs(d).toFixed(1) + '%';
      elDSc.classList.add(d>=0 ? 'bg-success-subtle' : 'bg-danger-subtle', d>=0 ? 'text-success' : 'text-danger');
    }

    const elActive = document.getElementById('kpiActive');
    if (elActive) elActive.textContent = dataset.activeMembers || '—';

    const elTeams = document.getElementById('kpiTeams');
    if (elTeams) elTeams.textContent = dataset.teamCount || '—';

    const bar = document.getElementById('kpiActiveBar');
    if (bar){
      const pctTarget = Math.min(100, Math.round((dataset.activeMembers / Math.max(1, dataset.targetActive)) * 100));
      bar.style.width = pctTarget + '%';
    }

    const elEng = document.getElementById('kpiEngagement');
    if (elEng) elEng.textContent = (dataset.attendancePct.length && dataset.avgScore.length) ? (engagement+'%') : '—%';

    const elDEng = document.getElementById('kpiEngagementDelta');
    if (elDEng){
      const d = pctDelta(engagement, Math.round(prevAttAvg*0.6 + prevScoreAvg*0.4));
      elDEng.textContent = (d>=0?'▲ ':'▼ ') + Math.abs(d).toFixed(1) + '%';
      elDEng.classList.add(d>=0 ? 'bg-success-subtle' : 'bg-danger-subtle', d>=0 ? 'text-success' : 'text-danger');
    }
  })();

  /* =========================================
     5) Mini sparklines
  ========================================= */
  function sparkline(elId, data, color) {
    const el = document.getElementById(elId);
    if (!el || !data.length || !window.Chart) return;
    new Chart(el, {
      type: 'line',
      data: { labels: data.map((_,i)=>i+1), datasets:[{ data, borderColor: color, backgroundColor:'rgba(0,0,0,0)', tension:.35, pointRadius:0 }] },
      options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:{enabled:false} }, scales:{ x:{display:false}, y:{display:false} }, elements:{ line:{ borderWidth:2 } } }
    });
  }
  sparkline('sparkAttendance', dataset.attendancePct.slice(-8), '#0d6efd');
  sparkline('sparkScore',      dataset.avgScore.slice(-8),      '#6610f2');
  sparkline('sparkEngagement', dataset.avgScore.map((v,i)=>Math.round((+v||0)*0.4 + (dataset.attendancePct[i]||0)*0.6)).slice(-8), '#20c997');

  /* =========================================
     6) Big charts
  ========================================= */
  const commonOpts = {
    responsive:true, maintainAspectRatio:false, animation:{duration:350},
    plugins:{ legend:{ labels:{ boxWidth:12 } }, tooltip:{ mode:'index', intersect:false } },
    layout:{ padding:{ left:4, right:8, top:8, bottom:4 } }
  };

  // Attendance Trend
  const elAtt = document.getElementById('chartAttendance');
  if (elAtt && weeks.length && dataset.attendancePct.length && window.Chart){
    new Chart(elAtt, {
      type: 'line',
      data: {
        labels: weeks,
        datasets: [{ label:'Attendance %', data: dataset.attendancePct, borderColor:'#0d6efd', backgroundColor:'rgba(13,110,253,.12)', fill:true, tension:.35, pointRadius:3, pointHoverRadius:5 }]
      },
      options: { ...commonOpts, plugins:{ ...commonOpts.plugins, legend:{display:false} }, scales:{ y:{ min:50, max:100, ticks:{ callback:v=>v+'%' } }, x:{ grid:{display:false} } } }
    });
  }

  // Category Mix (Donut)
  const elCat = document.getElementById('chartCategories');
  if (elCat && Object.keys(dataset.categoryMix).length && window.Chart){
    const catLabels = Object.keys(dataset.categoryMix);
    const catVals   = catLabels.map(k => +dataset.categoryMix[k] || 0);
    new Chart(elCat, {
      type: 'doughnut',
      data: { labels: catLabels, datasets:[{ data: catVals, borderWidth:0, backgroundColor:['#0d6efd','#20c997','#6610f2','#fd7e14','#dc3545','#6c757d'] }] },
      options: { ...commonOpts, plugins:{ ...commonOpts.plugins, legend:{ position:'bottom' } }, cutout:'65%' }
    });
  }

  // Productivity Forecast
  const elFc = document.getElementById('chartForecast');
  if (elFc && dataset.avgScore.length && window.Chart){
    const hist = dataset.avgScore.slice();
    const last = lastOf(hist, 75);
    const forecast = [last+1,last+2,last+3,last+4];
    let labels = weeks.slice();
    if (labels.length < hist.length + 4) labels = labels.concat(['W13','W14','W15','W16']);

    new Chart(elFc, {
      type: 'line',
      data: {
        labels,
        datasets: [
          { label:'Actual',   data: hist.concat([null,null,null,null]), borderColor:'#0d6efd', fill:false, tension:.25 },
          { label:'Forecast', data: Array(hist.length).fill(null).concat(forecast), borderColor:'#6f42c1', borderDash:[6,6], fill:false, tension:.25 }
        ]
      },
      options: { ...commonOpts, plugins:{ ...commonOpts.plugins, legend:{ position:'bottom' } }, scales:{ y:{ min: 0, max: 100 } } }
    });
  }

  /* =========================================
     7) Insights
  ========================================= */
  (function buildInsights(){
    const list = document.getElementById('insightsList');
    if (!list) return;

    const add = (iconClass, toneClass, text) => {
      const li = document.createElement('li');
      li.innerHTML =
        '<div class="d-flex gap-2 align-items-start">'
        + '<span class="btn btn-sm btn-circle '+toneClass+' text-white"><i class="'+iconClass+'"></i></span>'
        + '<div>'+text+'</div>'
        + '</div>';
      list.appendChild(li);
    };

    const last2 = dataset.attendancePct.slice(-2);
    if (last2.length === 2){
      const wow = pctDelta(+last2[1]||0, +last2[0]||0);
      add(wow>=0 ? 'bi bi-arrow-up-right' : 'bi bi-arrow-down-right', wow>=0 ? 'btn-success' : 'btn-danger',
          'Attendance ' + (wow>=0?'improved':'declined') + ' ' + Math.abs(wow).toFixed(1) + '% week-over-week.');
    }

    const teamsArr = Object.entries(dataset.teams||{});
    if (teamsArr.length){
      teamsArr.sort((a,b)=>(+b[1].score||0) - (+a[1].score||0));
      add('bi bi-award', 'btn-primary', 'Top team by average score: <strong>'+teamsArr[0][0]+'</strong> ('+(teamsArr[0][1].score ?? '—')+').');
    }

    const atRisk = (dataset.members||[]).filter(m => (m.att ?? 100) < 60 || (m.score ?? 100) < 60).length;
    add('bi bi-exclamation-triangle','btn-warning', atRisk + ' member(s) flagged at risk (attendance < 60% or score < 60).');

    const last3 = dataset.avgScore.slice(-3);
    if (last3.length === 3){
      const momentum = (+last3[2]||0) - (+last3[0]||0);
      add(momentum>=0?'bi bi-graph-up':'bi bi-graph-down', momentum>=0?'btn-success':'btn-danger',
          'Productivity momentum ' + (momentum>=0?'up':'down') + ' ' + Math.abs(momentum) + ' pts over the last 3 weeks.');
    }
  })();

  /* =========================================
     8) Tables (incl. clickable Members)
  ========================================= */
  (function buildTables(){
    const elTop = document.getElementById('tblTop');
    const elRisk = document.getElementById('tblRisk');
    const elMembers = document.getElementById('tblMembers');

    // Top performers
    const top5 = (dataset.members||[])
      .filter(m => m.score != null)
      .sort((a,b)=>(+b.score||0) - (+a.score||0))
      .slice(0,5);

    top5.forEach(m => {
      if (!elTop) return;
      const pct = Math.min(100, +m.score||0);
      elTop.insertAdjacentHTML('beforeend',
        '<tr>'
          + '<td>'+ (m.name||'—') +'</td>'
          + '<td>'+ (m.team||'—') +'</td>'
          + '<td><span class="fw-semibold">'+ (m.score==null?'—':m.score) +'</span></td>'
          + '<td><div class="progress" style="height:8px;"><div class="progress-bar bg-success" style="width:'+pct+'%"></div></div></td>'
        + '</tr>');
    });

    // At risk
    const risk = (dataset.members||[]).filter(m => (m.att ?? 100) < 60 || (m.score ?? 100) < 60);
    risk.forEach(m => {
      if (!elRisk) return;
      const status = ((m.att ?? 100) < 60 && (m.score ?? 100) < 60) ? 'Critical' : 'Watch';
      const badge  = status === 'Critical' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning';
      elRisk.insertAdjacentHTML('beforeend',
        '<tr>'
          + '<td>'+ (m.name||'—') +'</td>'
          + '<td>'+ (m.team||'—') +'</td>'
          + '<td>'+ (m.att==null?'—':(m.att+'%')) +'</td>'
          + '<td>'+ (m.score==null?'—':m.score) +'</td>'
          + '<td><span class="badge '+badge+'">'+status+'</span></td>'
        + '</tr>');
    });

    // Members snapshot (full row clickable)
    (dataset.members||[]).forEach(m => {
      if (!elMembers) return;
      const att = m.att==null?0:(+m.att||0);
      const cls = att>=80 ? 'bg-success' : (att>=60 ? 'bg-warning' : 'bg-danger');
      const base = (window.PERSON_SHOW_BASE || '/admin/reports/people').replace(/\/+$/,'');
      const url  = base + '/' + (m.id ?? '');

      elMembers.insertAdjacentHTML('beforeend',
        '<tr class="member-row" data-href="'+url+'" style="cursor:pointer;">'
          + '<td class="fw-semibold">'
            + '<a href="'+url+'" class="text-decoration-none text-light">'+ (m.name||'—') +'</a>'
          + '</td>'
          + '<td>'+ (m.team||'—') +'</td>'
          + '<td>'
            + '<div class="d-flex align-items-center gap-2">'
              + '<div class="w-100">'
                + '<div class="progress" style="height:8px;">'
                  + '<div class="progress-bar '+cls+'" style="width:'+att+'%"></div>'
                + '</div>'
              + '</div>'
              + '<span class="small">'+(m.att==null?'—':(att+'%'))+'</span>'
            + '</div>'
          + '</td>'
          + '<td>'+ (m.score==null?'—':m.score) +'</td>'
          + '<td><span class="text-secondary">—</span></td>'
        + '</tr>');
    });

    // Delegated click: whole row navigates (but keep normal <a> click)
    document.addEventListener('click', function(e){
  const row = e.target.closest('.member-row[data-href]');
  if (row && e.target.tagName !== 'A') {
    window.location.href = row.getAttribute('data-href');
  }
});
  })();

  /* =========================================
     9) Filter UI show/hide custom dates (UI only)
  ========================================= */
  (function wireFilters(){
    const rangeSel = document.getElementById('rangeSelect');
    const fromWrap = document.getElementById('customFromWrap');
    const toWrap   = document.getElementById('customToWrap');
    if (!rangeSel || !fromWrap || !toWrap) return;
    function sync(){
      const isCustom = (rangeSel.value||'').toUpperCase() === 'CUSTOM';
      fromWrap.classList.toggle('d-none', !isCustom);
      toWrap.classList.toggle('d-none', !isCustom);
    }
    rangeSel.addEventListener('change', sync);
    sync();
  })();
});
</script>
@endpush