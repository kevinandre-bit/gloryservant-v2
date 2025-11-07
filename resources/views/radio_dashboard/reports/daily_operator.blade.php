{{-- Daily Operator Report — Aired vs Scheduled, Deviations, Interventions --}}
@include('layouts/radio_layout')

@php
  $date = $date ?? now()->format('Y-m-d');
  $kpi = $kpi ?? (object)[ 'match_pct'=>94.6, 'ads_expected'=>120, 'ads_aired'=>112 ];
  $deviations = $deviations ?? [
    ['time'=>'06:00','show'=>'Morning Worship','kind'=>'LATE_START','detail'=>'+2m'],
    ['time'=>'12:00','show'=>'Noon Sermon','kind'=>'EARLY_END','detail'=>'-1m'],
    ['time'=>'16:30','show'=>'Ad SponsorX','kind'=>'AD_MISSED','detail'=>'1 spot'],
  ];
  $manuals = $manuals ?? [
    ['time'=>'08:12','action'=>'Manual segue','reason'=>'Dead air risk'],
    ['time'=>'18:03','action'=>'Fade out','reason'=>'Overtime on live feed'],
  ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h4 class="mb-0">Daily Operator Report</h4>
      <div class="d-flex gap-2">
        <input type="date" class="form-control" style="width:180px" value="{{ $date }}">
        <a class="btn btn-outline-primary" href="{{ route('playout.logs.index') }}">Playout Logs</a>
        <a class="btn btn-primary" href="#">Export PDF</a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>% Aired as Scheduled</h6>
            <div id="chartOpMatch" style="height:220px;"></div>
            <div>Match: <strong>{{ number_format($kpi->match_pct,1) }}%</strong></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-8 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Ads Delivery (Yesterday)</h6>
            <div id="chartOpAds" style="height:220px;"></div>
            <div>Expected: <strong>{{ $kpi->ads_expected }}</strong> • Aired: <strong>{{ $kpi->ads_aired }}</strong></div>
          </div>
        </div>
      </div>

      <div class="col-12 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Deviations</h6>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead class="table-light">
                  <tr><th>Time</th><th>Show</th><th>Kind</th><th>Detail</th></tr>
                </thead>
                <tbody>
                  @foreach($deviations as $d)
                    @php
                      $badge = in_array($d['kind'],['AD_MISSED','MISSED']) ? 'bg-danger' :
                               (in_array($d['kind'],['LATE_START','EARLY_END','PARTIAL']) ? 'bg-warning text-dark' : 'bg-secondary');
                    @endphp
                    <tr>
                      <td>{{ $d['time'] }}</td><td>{{ $d['show'] }}</td>
                      <td><span class="badge {{ $badge }}">{{ $d['kind'] }}</span></td>
                      <td>{{ $d['detail'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <h6 class="mt-4">Manual Interventions</h6>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead class="table-light">
                  <tr><th>Time</th><th>Action</th><th>Reason</th></tr>
                </thead>
                <tbody>
                  @foreach($manuals as $m)
                    <tr><td>{{ $m['time'] }}</td><td>{{ $m['action'] }}</td><td>{{ $m['reason'] }}</td></tr>
                  @endforeach
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
  function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}
  ready(function(){
    if(typeof ApexCharts==='undefined') return;

    // Match radial
    new ApexCharts(document.querySelector("#chartOpMatch"), {
      chart:{ type:'radialBar', height:220 },
      series:[ Number({{ (float)$kpi->match_pct }}) ],
      labels:['Match %'],
      plotOptions:{ radialBar:{ hollow:{ size:'70%' }, dataLabels:{ value:{ formatter:v=>Math.round(v)+'%' } } } },
      colors:['#02c27a'],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#98ec2d'], opacityFrom:1, opacityTo:1, stops:[0,100] } }
    }).render();

    // Ads column (Expected vs Aired)
    new ApexCharts(document.querySelector("#chartOpAds"), {
      chart:{ type:'bar', height:220, toolbar:{show:false} },
      series:[{ name:'Count', data:[ {{ (int)$kpi->ads_expected }}, {{ (int)$kpi->ads_aired }} ]}],
      xaxis:{ categories:['Expected','Aired'] },
      colors:['#0d6efd','#fc185a'],
      dataLabels:{ enabled:true }
    }).render();
  });
})();
</script>