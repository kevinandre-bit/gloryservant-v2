{{-- Weekly Summary (Admin → Manager) --}}
@include('layouts/admin')

@php
  $week = $week ?? '2025-W34';
  $program = $program ?? (object)[ 'match_pct'=>96.2, 'ads_top5'=>[
    ['name'=>'SponsorX','pct'=>92],['name'=>'SponsorY','pct'=>98],['name'=>'SponsorZ','pct'=>95],['name'=>'ChurchMall','pct'=>99],['name'=>'BooksCo','pct'=>94],
  ]];
  $tech = $tech ?? (object)[ 'uptime_pct'=>99.1, 'major_incidents'=>[
    ['date'=>'Mon','site'=>'Jacmel','detail'=>'Link flap 4x'],
    ['date'=>'Thu','site'=>'Les Cayes','detail'=>'Power outage 18m'],
  ]];
  $aud = $aud ?? (object)[
    'avg_daily_reach'=>5400,
    'peaks'=>['Tue 07:45','Sun 10:05'],
    'contrib_total'=>125,
    'next_steps'=>78,
    'radio_vital'=>83
  ];
  $compliance = $compliance ?? (object)[ 'hosting'=>'OK','taxes'=>'OK','licenses'=>'Pending: Fort-Liberté (30d)'];
  $budget = $budget ?? ['UPS batteries (Cayes): $800','Spare coax (Jacmel): $250'];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h4 class="mb-0">Weekly Summary — {{ $week }}</h4>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('reports.daily') }}">Daily Reports</a>
        <a class="btn btn-primary" href="#">Export PDF</a>
      </div>
    </div>

    <div class="row g-3">
      {{-- Programming --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Programming</h6>
            <div id="chartWkProg" style="height:220px;"></div>
            <div>% aired as scheduled: <strong>{{ number_format($program->match_pct,1) }}%</strong></div>
            <div class="mt-3">
              <h6 class="mb-2">Ad Performance (Top 5)</h6>
              <div id="chartWkAds" style="height:220px;"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Technical --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Technical</h6>
            <div id="chartWkUptime" style="height:220px;"></div>
            <div>Uptime: <strong>{{ number_format($tech->uptime_pct,1) }}%</strong></div>
            <h6 class="mt-3">Major Incidents</h6>
            <ul class="mb-0">
              @foreach($tech->major_incidents as $i)
                <li><strong>{{ $i['date'] }}</strong> — {{ $i['site'] }} — {{ $i['detail'] }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      {{-- Audience --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Audience</h6>
            <div class="row g-3">
              <div class="col-6">
                <div class="border rounded-3 p-3 h-100">
                  <div>Avg Daily Reach</div>
                  <h3 class="mb-0">{{ number_format($aud->avg_daily_reach) }}</h3>
                </div>
              </div>
              <div class="col-6">
                <div class="border rounded-3 p-3 h-100">
                  <div>Weekly Radio Vital</div>
                  <h3 class="mb-0">{{ $aud->radio_vital }}</h3>
                </div>
              </div>
            </div>
            <div class="row g-3 mt-1">
              <div class="col-6">
                <div class="border rounded-3 p-3 h-100">
                  <div>Contributions</div>
                  <h3 class="mb-0">{{ number_format($aud->contrib_total) }}</h3>
                </div>
              </div>
              <div class="col-6">
                <div class="border rounded-3 p-3 h-100">
                  <div>Next Steps</div>
                  <h3 class="mb-0">{{ number_format($aud->next_steps) }}</h3>
                </div>
              </div>
            </div>
            <div class="mt-3">
              <h6 class="mb-1">Peaks</h6>
              <ul class="mb-0">@foreach($aud->peaks as $p)<li>{{ $p }}</li>@endforeach</ul>
            </div>
          </div>
        </div>
      </div>

      {{-- Compliance + Budget --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Compliance</h6>
            <ul class="mb-3">
              <li>Hosting: <strong>{{ $compliance->hosting }}</strong></li>
              <li>Taxes: <strong>{{ $compliance->taxes }}</strong></li>
              <li>Licenses: <strong>{!! $compliance->licenses !!}</strong></li>
            </ul>

            <h6>Budget Requests</h6>
            <ul class="mb-0">
              @foreach($budget as $b) <li>{{ $b }}</li> @endforeach
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

    // Programming radial
    new ApexCharts(document.querySelector("#chartWkProg"), {
      chart:{ type:'radialBar', height:220 },
      series:[ Number({{ (float)$program->match_pct }}) ],
      labels:['Match %'],
      colors:['#02c27a'],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#98ec2d'], opacityFrom:1, opacityTo:1, stops:[0,100] } },
      plotOptions:{ radialBar:{ hollow:{ size:'70%' }, dataLabels:{ value:{ formatter:v=>Math.round(v)+'%' } } } }
    }).render();

    // Ads Top 5
    const adv = @json(array_column($program->ads_top5,'name'));
    const pct = @json(array_map(fn($x)=>$x['pct'],$program->ads_top5));
    new ApexCharts(document.querySelector("#chartWkAds"), {
      chart:{ type:'bar', height:220, toolbar:{show:false} },
      series:[{ name:'Delivery %', data: pct }],
      xaxis:{ categories: adv },
      colors:['#0d6efd'],
      dataLabels:{ enabled:true, formatter:(v)=> Math.round(Number(v))+'%' }
    }).render();

    // Uptime area
    new ApexCharts(document.querySelector("#chartWkUptime"), {
      chart:{ type:'area', height:220, sparkline:{enabled:true} },
      series:[{ name:'Uptime %', data:[98.9,99.2,99.0,99.4,{{ (float)$tech->uptime_pct }}] }],
      stroke:{ width:2, curve:'smooth' },
      colors:["#02c27a"],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#98ec2d'], opacityFrom:0.8, opacityTo:0.1, stops:[0,100,100,100] } },
      dataLabels:{ enabled:false }
    }).render();
  });
})();
</script>