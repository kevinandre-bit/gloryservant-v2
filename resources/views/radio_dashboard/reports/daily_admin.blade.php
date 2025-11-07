{{-- Daily Operations Report (Admin) — Consolidated 1-pager --}}
@include('layouts/radio_layout')

@php
  $date = $date ?? now()->format('Y-m-d');
  $prog = $prog ?? (object)[ 'match_pct'=>95.8, 'ads_warn'=>['SponsorX'=>92] ];
  $tech = $tech ?? (object)[ 'network_uptime'=>99.3, 'incidents'=>2 ];
  $actions = $actions ?? ['Requested report from Jacmel POC','Confirmed grid validation for W35'];
  $pending = $pending ?? ['Replace coax at Jacmel','Schedule UPS battery test (Cayes)'];
  $reco = $reco ?? ['Move live segment hook earlier (drop at 35\')','Increase app promo in Morning Worship'];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h4 class="mb-0">Daily Operations Report</h4>
      <div class="d-flex gap-2">
        <input type="date" class="form-control" style="width:180px" value="{{ $date }}">
        <a class="btn btn-primary" href="#">Export PDF</a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Programming Status</h6>
            <div id="chartAdmProg" style="height:220px;"></div>
            <div>Match: <strong>{{ number_format($prog->match_pct,1) }}%</strong></div>
            @if(!empty($prog->ads_warn))
              <div class="mt-2">
                @foreach($prog->ads_warn as $adv=>$pct)
                  <div class="alert alert-warning py-1 px-2 mb-1"><strong>{{ $adv }}</strong> delivery {{ $pct }}% (WARN)</div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Technical Status</h6>
            <div id="chartAdmTech" style="height:220px;"></div>
            <div>Network uptime: <strong>{{ number_format($tech->network_uptime,1) }}%</strong> • Incidents: <strong>{{ $tech->incidents }}</strong></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Actions Taken</h6>
            <ul class="mb-0">
              @foreach($actions as $a) <li>{{ $a }}</li> @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Pending Items</h6>
            <ul class="mb-0">
              @foreach($pending as $p) <li>{{ $p }}</li> @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Recommendations</h6>
            <ul class="mb-0">
              @foreach($reco as $r) <li>{!! $r !!}</li> @endforeach
            </ul>
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

    new ApexCharts(document.querySelector("#chartAdmProg"), {
      chart:{ type:'radialBar', height:220 },
      series:[ Number({{ (float)$prog->match_pct }}) ],
      labels:['Match %'],
      colors:['#02c27a'],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#98ec2d'], opacityFrom:1, opacityTo:1, stops:[0,100] } },
      plotOptions:{ radialBar:{ hollow:{ size:'70%' }, dataLabels:{ value:{ formatter:v=>Math.round(v)+'%' } } } }
    }).render();

    new ApexCharts(document.querySelector("#chartAdmTech"), {
      chart:{ type:'area', height:220, sparkline:{enabled:true} },
      series:[{ name:'Uptime %', data:[98.8,99.1,99.0,99.4,99.3,{{ (float)$tech->network_uptime }}] }],
      stroke:{ width:2, curve:'smooth' },
      colors:["#0d6efd"],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#0d6efd'], opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100] } },
      dataLabels:{ enabled:false }
    }).render();
  });
})();
</script>