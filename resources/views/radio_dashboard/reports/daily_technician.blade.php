{{-- Daily Technician Report — Signal, Incidents, Actions --}}
@include('layouts/radio_layout')

@php
  $date = $date ?? now()->format('Y-m-d');
  $sites = $sites ?? [
    ['name'=>'Ouest / Port-au-Prince','snr'=>26,'status'=>'UP','outages'=>0,'notes'=>'OK'],
    ['name'=>'Sud / Les Cayes','snr'=>22,'status'=>'UP','outages'=>1,'notes'=>'Brief power dip'],
    ['name'=>'Sud-Est / Jacmel','snr'=>17,'status'=>'DEGRADED','outages'=>0,'notes'=>'Low SNR'],
  ];
  $incidents = $incidents ?? [
    ['time'=>'08:41','site'=>'Jacmel','type'=>'Signal','detail'=>'SNR dropped to 16 dB','action'=>'Antenna check scheduled','status'=>'Open'],
    ['time'=>'13:05','site'=>'Les Cayes','type'=>'Power','detail'=>'UPS transfer for 2m','action'=>'Verified generator','status'=>'Closed'],
  ];
  $actions = $actions ?? [
    ['site'=>'Pap Hub','task'=>'Link reprobe','owner'=>'Tech-3','status'=>'Done'],
    ['site'=>'Jacmel','task'=>'Inspect coax','owner'=>'Tech-1','status'=>'Planned'],
  ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h4 class="mb-0">Daily Technician Report</h4>
      <div class="d-flex gap-2">
        <input type="date" class="form-control" style="width:180px" value="{{ $date }}">
        <a class="btn btn-outline-primary" href="{{ route('reports.daily') }}">Back</a>
        <a class="btn btn-primary" href="#">Export PDF</a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-4 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Signal Quality Snapshot</h6>
            <div id="chartTechSnr" style="height:210px;"></div>
            <div>Below threshold (&lt;18 dB): 
              <strong>
                {{ collect($sites)->where('snr','<',18)->count() }}
              </strong>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-8 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Sites Status</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr><th>Site</th><th>SNR (dB)</th><th>Status</th><th>Outages</th><th>Notes</th></tr>
                </thead>
                <tbody>
                  @foreach($sites as $s)
                    @php
                      $badge = $s['status']==='UP' ? 'bg-success' : ($s['status']==='DEGRADED' ? 'bg-warning text-dark' : 'bg-danger');
                    @endphp
                    <tr>
                      <td>{{ $s['name'] }}</td>
                      <td>{{ $s['snr'] }}</td>
                      <td><span class="badge {{ $badge }}">{{ $s['status'] }}</span></td>
                      <td>{{ $s['outages'] }}</td>
                      <td>{{ $s['notes'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <h6>Incidents</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr><th>Time</th><th>Site</th><th>Type</th><th>Detail</th><th>Action</th><th>Status</th></tr>
                </thead>
                <tbody>
                  @foreach($incidents as $i)
                    @php $b = $i['status']==='Open' ? 'bg-danger' : 'bg-success'; @endphp
                    <tr>
                      <td>{{ $i['time'] }}</td><td>{{ $i['site'] }}</td><td>{{ $i['type'] }}</td>
                      <td>{{ $i['detail'] }}</td><td>{{ $i['action'] }}</td>
                      <td><span class="badge {{ $b }}">{{ $i['status'] }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <h6 class="mt-4">Actions Taken / Planned</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr><th>Site</th><th>Task</th><th>Owner</th><th>Status</th></tr>
                </thead>
                <tbody>
                  @foreach($actions as $a)
                    @php $b = $a['status']==='Done' ? 'bg-success' : 'bg-warning text-dark'; @endphp
                    <tr>
                      <td>{{ $a['site'] }}</td><td>{{ $a['task'] }}</td><td>{{ $a['owner'] }}</td>
                      <td><span class="badge {{ $b }}">{{ $a['status'] }}</span></td>
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
</main>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function(){
  function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}
  ready(function(){
    if(typeof ApexCharts==='undefined') return;

    // SNR bar (sites)
    const names = @json(array_column($sites,'name'));
    const snrs  = @json(array_map(fn($s)=>$s['snr'],$sites));
    new ApexCharts(document.querySelector("#chartTechSnr"), {
      chart:{ type:'bar', height:210, toolbar:{show:false} },
      series:[{ name:'SNR', data: snrs }],
      xaxis:{ categories:names },
      plotOptions:{ bar:{ horizontal:true } },
      colors:["#02c27a"],
      fill:{ type:'gradient', gradient:{ shade:'dark', gradientToColors:['#98ec2d'], opacityFrom:0.9, opacityTo:0.3, stops:[0,100] } },
      dataLabels:{ enabled:true }
    }).render();
  });
})();
</script>