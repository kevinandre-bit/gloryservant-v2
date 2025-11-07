{{-- HUB (PAP) — Ingest & Distribution --}}
@include('layouts/radio_layout')

@php
  // ---------- Safe defaults (if controller doesn't pass data) ----------
  $hub = $hub ?? (object)[
    'name' => 'Port-au-Prince Hub',
    'ingest' => (object)[
      'from_source_status' => 'ONLINE', // ONLINE | DEGRADED | OFFLINE
      'bitrate_kbps' => 192,
      'uptime_24h' => 99.2,
      'last_disconnect' => (object)['started_at' => '2025-08-22 03:14', 'duration' => '2m 11s'],
      'series24h' => [98.5,99.3,99.1,99.2,98.7,99.4,99.2],
    ],
    // per-link distribution status to 10 TX sites
    'links' => [
      ['id'=>1,'site'=>'Ouest / Port-au-Prince','ok'=>true,'lat_ms'=>18,'loss_pct'=>0.1],
      ['id'=>2,'site'=>'Sud / Aux Cayes','ok'=>true,'lat_ms'=>26,'loss_pct'=>0.3],
      ['id'=>3,'site'=>'Sud-Est / Jacmel','ok'=>false,'lat_ms'=>0,'loss_pct'=>100],
      ['id'=>4,'site'=>"Grand'Anse / Jérémie",'ok'=>true,'lat_ms'=>32,'loss_pct'=>0.6],
      ['id'=>5,'site'=>'Centre / Hinche','ok'=>true,'lat_ms'=>20,'loss_pct'=>0.2],
      ['id'=>6,'site'=>'Nord-Ouest / Port-de-Paix','ok'=>true,'lat_ms'=>28,'loss_pct'=>0.5],
      ['id'=>7,'site'=>'Nippes / Miragoâne','ok'=>true,'lat_ms'=>24,'loss_pct'=>0.2],
      ['id'=>8,'site'=>"Nord-Est / Fort-Liberté",'ok'=>true,'lat_ms'=>23,'loss_pct'=>0.2],
      ['id'=>9,'site'=>'Nord / Cap-Haïtien','ok'=>true,'lat_ms'=>21,'loss_pct'=>0.1],
      ['id'=>10,'site'=>'Artibonite / Gonaïves','ok'=>true,'lat_ms'=>22,'loss_pct'=>0.2],
    ],
    // schedule vs aired parity summary (yesterday)
    'parity' => (object)[
      'expected_min' => 1440,
      'aired_min' => 1370,
      'match_pct' => 95.1,
      'top_mismatches' => [
        ['show'=>'Morning Worship','detail'=>'Late start 2m'],
        ['show'=>'Ad SponsorX','detail'=>'Missed 1 spot'],
        ['show'=>'Noon Sermon','detail'=>'Early end 1m'],
      ],
    ],
  ];

  $greenStart = '#17ad37'; $greenEnd = '#98ec2d';
  $ing = $hub->ingest;
  $links = $hub->links;
  $par  = $hub->parity;

  $linksOk = collect($links)->where('ok', true)->count();
  $linksTotal = count($links);
@endphp

<main class="main-wrapper">
  <div class="main-content">

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Monitoring</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="/dashboard"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active">Hub (PAP)</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('monitoring.source') }}">View Source</a>
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.sites.index') }}">All TX Sites</a>
        <a id="btnReprobe" class="btn btn-primary" href="{{ route('monitoring.hub') }}?action=reprobe">Reprobe Links</a>
      </div>
    </div>

    <div class="row g-3">

      {{-- Ingest from SOURCE --}}
      <div class="col-12 col-xxl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between">
              <h5 class="mb-3">Ingest from SOURCE</h5>
              @php
                $cls = $ing->from_source_status === 'ONLINE' ? 'bg-success' : ($ing->from_source_status === 'DEGRADED' ? 'bg-warning text-dark' : 'bg-danger');
              @endphp
              <span class="badge {{ $cls }}">{{ $ing->from_source_status }}</span>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                  <div>Bitrate</div>
                  <h4 class="mb-1">{{ $ing->bitrate_kbps }} kbps</h4>
                  <div>Uptime (24h)</div>
                  <h4 id="ingUptime" class="mb-3">{{ number_format($ing->uptime_24h,1) }}%</h4>
                  <div id="chartIngestUptime" style="height:120px;"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                  <div>Last disconnect</div>
                  @if(!empty($ing->last_disconnect))
                    <h6 class="mb-1">{{ $ing->last_disconnect->started_at }}</h6>
                    <div>Duration: <strong>{{ $ing->last_disconnect->duration }}</strong></div>
                  @else
                    <h6 class="mb-1">—</h6>
                    <div>Duration: <strong>—</strong></div>
                  @endif
                  <div class="mt-3" id="chartIngestSpark" style="height:80px;"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- Distribution to TX Sites --}}
      <div class="col-12 col-xxl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between">
              <h5 class="mb-3">Distribution Health</h5>
              <div>Links OK: <strong id="linksOkText">{{ $linksOk }}/{{ $linksTotal }}</strong></div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <div id="chartDistribution" style="height:200px;"></div>
              </div>
              <div class="col-md-6">
                <div class="table-responsive" style="max-height:200px; overflow:auto;">
                  <table class="table table-sm align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Site</th>
                        <th>Status</th>
                        <th>Latency</th>
                        <th>Loss</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($links as $lnk)
                        @php
                          $b = $lnk['ok'] ? 'bg-success' : 'bg-danger';
                        @endphp
                        <tr>
                          <td>{{ $lnk['site'] }}</td>
                          <td><span class="badge {{ $b }}">{{ $lnk['ok'] ? 'OK' : 'ISSUE' }}</span></td>
                          <td>{{ $lnk['lat_ms'] }} ms</td>
                          <td>{{ $lnk['loss_pct'] }}%</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- Parity: Schedule vs Aired (yesterday) --}}
      <div class="col-12 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between">
              <h5 class="mb-3">Schedule vs Aired — Yesterday</h5>
              @php
                $badgeCls = $par->match_pct < 90 ? 'bg-danger' : ($par->match_pct < 95 ? 'bg-warning text-dark' : 'bg-success');
              @endphp
              <span class="badge {{ $badgeCls }}" id="parityBadge">{{ number_format($par->match_pct,1) }}%</span>
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <div id="chartParity" style="height:220px;"></div>
                <div class="mt-2">Expected: <strong>{{ $par->expected_min }}</strong> min • Aired: <strong>{{ $par->aired_min }}</strong> min</div>
              </div>
              <div class="col-md-8">
                <div class="border rounded-3 p-3 h-100">
                  <h6 class="mb-2">Top mismatches</h6>
                  <ul class="mb-0">
                    @forelse($par->top_mismatches as $mm)
                      <li><strong>{{ $mm['show'] }}</strong> — {{ $mm['detail'] }}</li>
                    @empty
                      <li>No mismatches.</li>
                    @endforelse
                  </ul>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>

  </div>
</main>

{{-- ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function(){
  function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}
  ready(function(){
    if(typeof ApexCharts==='undefined'){ console.error('ApexCharts not loaded'); return; }

    // ===== Ingest charts =====
    const ingestSeries = @json($hub->ingest->series24h);
    new ApexCharts(document.querySelector('#chartIngestUptime'), {
      chart: { type:'area', height:120, toolbar:{show:false} },
      series: [{ name:'Uptime %', data: ingestSeries }],
      stroke: { width:2, curve:'smooth' },
      dataLabels: { enabled:false },
      colors: ['#0d6efd'],
      fill: { type:'gradient', gradient:{
        shade:'dark', gradientToColors:['#0d6efd'], shadeIntensity:1,
        type:'vertical', opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100]
      }},
      tooltip: { y:{ formatter:v=> (Math.round(v*10)/10) + '%' } }
    }).render();

    new ApexCharts(document.querySelector('#chartIngestSpark'), {
      chart: { type:'area', height:80, sparkline:{enabled:true} },
      series: [{ data: ingestSeries }],
      stroke: { width:2, curve:'smooth' },
      colors: ['#6f42c1'],
      fill: { type:'gradient', gradient:{
        shade:'dark', gradientToColors:['#6f42c1'], shadeIntensity:1,
        type:'vertical', opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100]
      }},
      tooltip: { enabled:false }
    }).render();

    // ===== Distribution donut =====
    const linksOk = {{ $linksOk }};
    const linksTotal = {{ $linksTotal }};
    new ApexCharts(document.querySelector('#chartDistribution'), {
      chart: { type:'donut', height:200 },
      series: [linksOk, Math.max(linksTotal - linksOk, 0)],
      labels: ['OK','Issues'],
      colors: ['#17ad37','#fd7e14'],
      fill: { type:'gradient', gradient:{
        shade:'dark', type:'vertical', shadeIntensity:1,
        gradientToColors: ['#98ec2d','#fd7e14'], opacityFrom:1, opacityTo:1, stops:[0,100]
      }},
      dataLabels: { enabled:true }
    }).render();

    // ===== Parity donut =====
    const expected = {{ (int)$par->expected_min }};
    const aired    = {{ (int)$par->aired_min }};
    const missed   = Math.max(expected - aired, 0);
    new ApexCharts(document.querySelector('#chartParity'), {
      chart: { type:'donut', height:220 },
      series: [aired, missed],
      labels: ['Aired','Missed'],
      colors: ['#02c27a','#fc185a'],
      fill: { type:'gradient', gradient:{
        shade:'dark', type:'vertical', shadeIntensity:1,
        gradientToColors: ['{{ $greenEnd }}','#fc185a'], opacityFrom:1, opacityTo:1, stops:[0,100]
      }},
      dataLabels: { enabled:true, formatter:(v)=> `${Math.round(v)}%` }
    }).render();
  });
})();
</script>