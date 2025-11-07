@include('layouts/radio_layout')

@php($d = $data ?? [])
<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Monitoring</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="/dashboard"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Source (Miami)</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <button class="btn btn-outline-secondary" id="btnRefresh">Refresh</button>
      </div>
    </div>

    <div class="row g-3">

      <div class="col-12 col-xl-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">Encoder Status</h5>
            <div class="d-flex flex-column gap-2">
              <div><strong>Status:</strong> <span class="{{ ($d['encoder']['status'] ?? '')==='ONLINE' ? 'text-success' : 'text-danger' }}">{{ $d['encoder']['status'] ?? '—' }}</span></div>
              <div><strong>Bitrate:</strong> {{ $d['encoder']['bitrate'] ?? '—' }} kbps</div>
              <div><strong>Codec:</strong> {{ $d['encoder']['codec'] ?? '—' }}</div>
              <div><strong>Protocol:</strong> {{ $d['encoder']['uplink'] ?? '—' }}</div>
              <div><strong>Latency:</strong> {{ $d['encoder']['latency'] ?? '—' }} ms</div>
              <div><strong>Uptime (24h):</strong> <span id="uptime24">{{ $d['encoder']['uptime24'] ?? '—' }}%</span></div>
              <div><strong>Last Update:</strong> {{ $d['lastUpdatedAt'] ?? '—' }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-8 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
              <h5 class="mb-0">Stream Health — Last 24h</h5>
            </div>
            <div id="chart-src-uptime" class="mt-2" style="height:220px;"></div>
          </div>
        </div>
      </div>

      <div class="col-12 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">Recent Disconnects</h5>
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr><th>Start Time</th><th>Duration</th></tr>
                </thead>
                <tbody>
                  @forelse(($d['disconnects'] ?? []) as $ev)
                    <tr>
                      <td>{{ $ev['started_at'] }}</td>
                      <td>{{ $ev['duration'] }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="2">No disconnects 🎉</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function(){
  const PALETTE = {
    primary:'#0d6efd',
    success:'#17ad37',
    successTo:'#98ec2d',
  };
  function grad(to){return{type:'gradient',gradient:{shade:'dark',gradientToColors:[to],shadeIntensity:1,type:'vertical',opacityFrom:0.8,opacityTo:0.1,stops:[0,100,100,100]}};}
  function greenGrad(){return{type:'gradient',gradient:{shade:'dark',gradientToColors:[PALETTE.successTo],shadeIntensity:1,type:'diagonal1',opacityFrom:0.9,opacityTo:0.9,stops:[0,100]}};}

  const d = @json($d);
  const series24 = (d.series24h || []).map(Number);
  if (typeof ApexCharts !== 'undefined') {
    new ApexCharts(document.querySelector('#chart-src-uptime'), {
      chart: { type:'area', height:220, sparkline:{enabled:false}, toolbar:{show:false} },
      series: [{ name:'Uptime %', data: series24 }],
      dataLabels:{ enabled:false },
      stroke:{ width:2, curve:'smooth' },
      fill: grad(PALETTE.primary),
      colors:[PALETTE.primary],
      tooltip:{ y:{ formatter:(v)=> (Math.round(v*10)/10)+'%' } },
      xaxis:{ categories: Array(series24.length).fill('').map((_,i)=> (24-series24.length+i)+'h') }
    }).render();
  }

  document.getElementById('btnRefresh')?.addEventListener('click', ()=> location.reload());
})();
</script>