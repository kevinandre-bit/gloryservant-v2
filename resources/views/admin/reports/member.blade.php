{{-- resources/views/admin/reports/member.blade.php --}}
@extends('layouts.admin')
@section('title', 'Member Report')

@section('content')
<main class="main-wrapper analytics-dashboard">
  <div class="main-content">

    {{-- Breadcrumb / Header --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Analytics</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
              <a href="{{ route('admin.reports.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.dashboard') }}">Team Productivity Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $vm['person']['name'] }}</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">← Back</a>
      </div>
    </div>

    {{-- Filter Bar (Team / Member / Preset Range) --}}
    <form class="card mb-3" method="GET" action="{{ route('admin.reports.people.show', ['id'=>$vm['person']['id']]) }}">
      <div class="card-body row g-2 align-items-end">

        <div class="col-12 col-lg-3">
          <label class="form-label">Team</label>
          <select id="fltTeam" class="form-select">
            <option value="">All teams</option>
            @foreach($vm['teamsList'] as $t)
              <option value="{{ $t->id }}" {{ ($vm['person']['team_id'] ?? null) == $t->id ? 'selected' : '' }}>
                {{ $t->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-lg-4">
          <label class="form-label">Member</label>
          <select id="fltMember" name="id" class="form-select"><!-- filled by JS --></select>
        </div>

        <div class="col-12 col-lg-3">
          <label class="form-label">Range</label>
          @php $r = $vm['filters']['range'] ?? 'D7'; @endphp
          <select id="fltRange" name="range" class="form-select">
            <option value="D7"  {{ $r==='D7'  ? 'selected' : '' }}>Last 7 days</option>
            <option value="W2"  {{ $r==='W2'  ? 'selected' : '' }}>Last 2 Weeks</option>
            <option value="M1"  {{ $r==='M1'  ? 'selected' : '' }}>Last Month</option>
            <option value="M3"  {{ $r==='M3'  ? 'selected' : '' }}>Last 3 Months</option>
            <option value="YTD" {{ $r==='YTD' ? 'selected' : '' }}>This Year</option>
          </select>
        </div>

        <div class="col-12 col-lg-2 d-grid">
          <button type="submit" class="btn btn-primary">Apply</button>
        </div>
      </div>
    </form>

    {{-- Header Card --}}
    <div class="card mb-3">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="h5 mb-1">{{ $vm['person']['name'] }}</div>
          <div class="text-secondary">Team: <strong>{{ $vm['person']['team'] }}</strong></div>
        </div>
        <div class="text-secondary small">
          Range: {{ $vm['filters']['start'] }} — {{ $vm['filters']['end'] }}
        </div>
      </div>
    </div>

    {{-- KPI Row --}}
    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card raised h-100"><div class="card-body">
          <div class="text-secondary text-uppercase small">Attendance</div>
          <div class="h3 fw-bold">{{ $vm['kpis']['attendance'] !== null ? $vm['kpis']['attendance'].'%' : '—%' }}</div>
        </div></div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card raised h-100"><div class="card-body">
          <div class="text-secondary text-uppercase small">Productivity</div>
          <div class="h3 fw-bold">{{ $vm['kpis']['productivity'] !== null ? $vm['kpis']['productivity'].'%' : '—%' }}</div>
        </div></div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card raised h-100"><div class="card-body">
          <div class="text-secondary text-uppercase small">Composite</div>
          <div class="h3 fw-bold">{{ $vm['kpis']['composite'] !== null ? $vm['kpis']['composite'].'%' : '—%' }}</div>
        </div></div>
      </div>
    </div>

    {{-- Trend & Radar --}}
    <div class="row g-3 mt-1">
      <div class="col-12 col-lg-8">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">12-Week Trends</h6>
            <span class="dash-lable bg-primary">Line • Weekly</span>
          </div>
          <div class="card-body" style="height:360px;">
            <canvas id="trendLine"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Category Radar</h6>
            <span class="dash-lable">Radar</span>
          </div>
          <div class="card-body" style="height:360px;">
            <canvas id="radarChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card mt-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Recent Activity</h6>
        <span class="text-secondary small">Last 15 records in range</span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr><th>Date</th><th>Metric</th><th>Value</th></tr>
            </thead>
            <tbody>
              @forelse($vm['recent'] as $r)
                <tr>
                  <td>{{ $r['date'] }}</td>
                  <td>{{ $r['metric'] }}</td>
                  <td>{{ $r['value'] }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-secondary">No activity in this period.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>
@endsection

@push('scripts')
<script>
  // route template we can swap __ID__ into
  window.PERSON_URL_TMPL = "{{ route('admin.reports.people.show', ['id' => '__ID__']) }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Embed VM for JS --}}
<script id="member-json" type="application/json">
{!! json_encode($vm, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const RAW = JSON.parse(document.getElementById('member-json').textContent || '{}');

  // ===== Fill Member select (by team) =====
  const people = Array.isArray(RAW.peopleList) ? RAW.peopleList : [];
  const selTeam   = document.getElementById('fltTeam');
  const selMember = document.getElementById('fltMember');
  const selRange  = document.getElementById('fltRange');

  const byTeam = {};
  people.forEach(p => { (byTeam[p.team_id ?? 0] ||= []).push(p); });

  function nameOf(p){ return ((p.first_name||'') + ' ' + (p.last_name||'')).trim() || ('ID#'+p.id); }

  function populateMembers(teamId, preselectId){
    selMember.innerHTML = '';
    const list = (teamId ? (byTeam[+teamId]||[]) : people).slice();
    list.sort((a,b) => (a.last_name||'').localeCompare(b.last_name||'') || (a.first_name||'').localeCompare(b.first_name||''));
    list.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.id;
      opt.textContent = nameOf(p);
      if (+p.id === +preselectId) opt.selected = true;
      selMember.appendChild(opt);
    });
    if (!selMember.value && list.length) selMember.value = list[0].id;
  }

  const currentId     = RAW.person?.id ?? null;
  const currentTeamId = RAW.person?.team_id ?? '';
  if (selTeam)  selTeam.value = currentTeamId || '';
  populateMembers(selTeam?.value || '', currentId);

  // team change -> rebuild members
  selTeam?.addEventListener('change', () => {
    populateMembers(selTeam.value || '', null);
  });

  // member change -> navigate (preserve range)
  selMember?.addEventListener('change', () => {
    const base = "{{ route('admin.reports.people.show', ['id' => '__ID__']) }}";
    const url  = base.replace('__ID__', encodeURIComponent(selMember.value));
    const params = new URLSearchParams();
    if (selRange?.value) params.set('range', selRange.value);
    window.location.assign(url + (params.toString() ? ('?'+params.toString()) : ''));
  });

  // ===== Charts =====
  const labels = Array.isArray(RAW.trend?.labels) ? RAW.trend.labels : [];
  const att    = Array.isArray(RAW.trend?.attendance) ? RAW.trend.attendance : [];
  const prod   = Array.isArray(RAW.trend?.productivity) ? RAW.trend.productivity : [];

  // Trend line
  const elTrend = document.getElementById('trendLine');
  if (elTrend && labels.length){
    new Chart(elTrend, {
      type: 'line',
      data: {
        labels,
        datasets: [
          { label:'Attendance %',   data: att,  borderColor:'#0d6efd', backgroundColor:'rgba(13,110,253,.12)', fill:true, tension:.35, pointRadius:3 },
          { label:'Productivity %', data: prod, borderColor:'#20c997', backgroundColor:'rgba(32,201,151,.10)', fill:true, tension:.35, pointRadius:3 }
        ]
      },
      options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ position:'bottom' } },
        scales:{
          y:{ min:0, max:100, ticks:{ callback:(v)=>v+'%' }},
          x:{ grid:{ display:false } }
        }
      }
    });
  }

  // Radar
  const rLabs = Array.isArray(RAW.categories?.labels) ? RAW.categories.labels : [];
  const rVals = Array.isArray(RAW.categories?.scores) ? RAW.categories.scores : [];
  const elRadar = document.getElementById('radarChart');
  if (elRadar && rLabs.length){
    new Chart(elRadar, {
      type: 'radar',
      data: { labels: rLabs, datasets: [{ label:'Category Score', data: rVals, borderColor:'#6f42c1', backgroundColor:'rgba(111,66,193,.15)', pointRadius:3 }] },
      options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ r:{ suggestedMin:0, suggestedMax:100 } } }
    });
  }
});
</script>
@endpush