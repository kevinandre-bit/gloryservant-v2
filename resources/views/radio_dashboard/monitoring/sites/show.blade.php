{{-- Site Detail --}}
@include('layouts/radio_layout')

@php
  // Safe defaults so the page renders even without a controller payload
  $site = $site ?? [
    'id'         => 1,
    'department' => 'Ouest',
    'capital'    => 'Port-au-Prince',
    'frequency'  => '103.3',
    'freq_status'=> 'Acquired',
    'online'     => true,
    'rf'         => 81,
    'snr'        => 27,
    'uptime'     => 99.5,
    'power'      => 'Stable',
    'last_maint' => '2025-08-21',
  ];

  // Last 7 days uptime (%) and SNR samples (fake if not provided)
  $metrics7d = $metrics7d ?? [
    ['date'=>'2025-08-17','uptime'=>99.1,'snr'=>25],
    ['date'=>'2025-08-18','uptime'=>98.7,'snr'=>24],
    ['date'=>'2025-08-19','uptime'=>99.4,'snr'=>26],
    ['date'=>'2025-08-20','uptime'=>97.9,'snr'=>23],
    ['date'=>'2025-08-21','uptime'=>99.6,'snr'=>27],
    ['date'=>'2025-08-22','uptime'=>99.2,'snr'=>26],
    ['date'=>'2025-08-23','uptime'=>99.0,'snr'=>25],
  ];

  // Recent incidents (optional)
  $incidents = $incidents ?? [
    ['when'=>'2025-08-22 13:12','title'=>'Short RF dip','severity'=>'warn','note'=>'Brief SNR drop, auto-recovered'],
    ['when'=>'2025-08-20 02:44','title'=>'Power outage','severity'=>'crit','note'=>'Diesel generator engaged 27m'],
  ];

  $freqBadge = $site['freq_status'] === 'Acquired' ? 'bg-success' : 'bg-warning text-dark';
  $onlineBadge = $site['online'] ? 'bg-success' : 'bg-danger';
  $powerBadge = $site['power'] === 'Stable' ? 'bg-success' : ($site['power'] === 'Flaky' ? 'bg-warning text-dark' : 'bg-danger');
@endphp

<main class="main-wrapper">
  <div class="main-content">

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Monitoring</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="/dashboard"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('monitoring.sites.index') }}">TX Sites</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $site['department'] }}</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('maintenance.tasks.index') }}?site_id={{ $site['id'] }}">Create Maintenance Ticket</a>
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.hub') }}">Back to Hub</a>
      </div>
    </div>

    <div class="row g-3">

      {{-- Site header card --}}
      <div class="col-12">
        <div class="card rounded-4">
          <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <h4 class="mb-1">{{ $site['department'] }} — {{ $site['capital'] }}</h4>
              <div>Frequency: <strong>{{ $site['frequency'] ?: '—' }} MHz</strong>
                <span class="badge {{ $freqBadge }} ms-2">{{ $site['freq_status'] }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div>Status:
                <span class="badge {{ $onlineBadge }} ms-1">{{ $site['online'] ? 'ONLINE' : 'OFFLINE' }}</span>
              </div>
              <div>Power:
                <span class="badge {{ $powerBadge }} ms-1">{{ $site['power'] }}</span>
              </div>
              <div>Last Maintenance: <strong>{{ $site['last_maint'] }}</strong></div>
            </div>
          </div>
        </div>
      </div>

      {{-- KPIs --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4 text-center">
            <h6 class="mb-1">RF Strength</h6>
            <h2 class="mb-0">{{ $site['rf'] }}%</h2>
            <div id="rfSpark" class="mt-3" style="height:80px;"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4 text-center">
            <h6 class="mb-1">SNR</h6>
            <h2 class="mb-0">{{ $site['snr'] }} dB</h2>
            <div id="snrSpark" class="mt-3" style="height:80px;"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4 text-center">
            <h6 class="mb-1">Uptime (7 days)</h6>
            <h2 class="mb-0">{{ $site['uptime'] }}%</h2>
            <div id="uptimeSpark" class="mt-3" style="height:80px;"></div>
          </div>
        </div>
      </div>

      {{-- Charts --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">Uptime — Last 7 days</h5>
            <div id="chartUptime7d" style="height:220px;"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">SNR — Last 7 days</h5>
            <div id="chartSnr7d" style="height:220px;"></div>
          </div>
        </div>
      </div>

      {{-- Incidents table (NO unbalanced Blade directives) --}}
      <div class="col-12 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between mb-2">
              <h5 class="mb-0">Recent Incidents</h5>
              <a class="btn btn-sm btn-light" href="{{ route('maintenance.tasks.index') }}?site_id={{ $site['id'] }}">Open Maintenance</a>
            </div>
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr>
                    <th>When</th>
                    <th>Title</th>
                    <th>Severity</th>
                    <th>Note</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($incidents))
                    @foreach($incidents as $row)
                      @php
                        $sev = $row['severity'] ?? 'info';
                        $sevClass = $sev === 'crit' ? 'bg-danger' : ($sev === 'warn' ? 'bg-warning text-dark' : 'bg-secondary');
                      @endphp
                      <tr>
                        <td>{{ $row['when'] ?? '—' }}</td>
                        <td>{{ $row['title'] ?? '—' }}</td>
                        <td><span class="badge {{ $sevClass }}">{{ strtoupper($sev) }}</span></td>
                        <td>{{ $row['note'] ?? '' }}</td>
                      </tr>
                    @endforeach
                  @else
                    <tr><td colspan="4" class="text-center">No incidents.</td></tr>
                  @endif
                </tbody>
              </table>
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

    const metrics = @json($metrics7d);
    const dates   = metrics.map(m => m.date);
    const ups     = metrics.map(m => Number(m.uptime));
    const snrs    = metrics.map(m => Number(m.snr));

    // Spark: RF
    new ApexCharts(document.querySelector('#rfSpark'), {
      chart:{ type:'area', height:80, sparkline:{enabled:true} },
      series:[{ data:[60,65,70,68,72,78,{{ (int)$site['rf'] }}] }],
      stroke:{ width:2, curve:'smooth' },
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#17ad37','#98ec2d'], shadeIntensity:1, type:'vertical', opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100]} },
      colors:['#17ad37'],
    }).render();

    // Spark: SNR
    new ApexCharts(document.querySelector('#snrSpark'), {
      chart:{ type:'area', height:80, sparkline:{enabled:true} },
      series:[{ data:[20,22,23,24,25,26,{{ (int)$site['snr'] }}] }],
      stroke:{ width:2, curve:'smooth' },
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#0d6efd'], shadeIntensity:1, type:'vertical', opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100]} },
      colors:['#0d6efd'],
    }).render();

    // Spark: Uptime
    new ApexCharts(document.querySelector('#uptimeSpark'), {
      chart:{ type:'bar', height:80, sparkline:{enabled:true} },
      series:[{ data:[97.8,98.9,99.4,98.2,99.6,99.2,{{ (float)$site['uptime'] }}] }],
      plotOptions:{ bar:{ columnWidth:'45%', endingShape:'rounded' } },
      colors:['#fd7e14'],
    }).render();

    // Uptime 7d
    new ApexCharts(document.querySelector('#chartUptime7d'), {
      chart:{ type:'bar', height:220, toolbar:{show:false} },
      series:[{ name:'Uptime %', data: ups }],
      xaxis:{ categories: dates },
      plotOptions:{ bar:{ columnWidth:'45%', endingShape:'rounded' } },
      dataLabels:{ enabled:true, formatter:(v)=> `${Math.round(v)}%` },
      colors:['#02c27a'],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#17ad37','#98ec2d'], shadeIntensity:1, type:'vertical', opacityFrom:1, opacityTo:1, stops:[0,100] } },
    }).render();

    // SNR 7d
    new ApexCharts(document.querySelector('#chartSnr7d'), {
      chart:{ type:'area', height:220, toolbar:{show:false} },
      series:[{ name:'SNR (dB)', data: snrs }],
      xaxis:{ categories: dates },
      stroke:{ width:2, curve:'smooth' },
      dataLabels:{ enabled:false },
      colors:['#6f42c1'],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#6f42c1'], shadeIntensity:1, type:'vertical', opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100] } },
    }).render();
  });
})();
</script>