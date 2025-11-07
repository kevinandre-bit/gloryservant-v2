{{-- resources/views/radio_dashboard/tech/schedule/index.blade.php --}}
@include('layouts/radio_layout')

@php
  // Mock data (replace later with real collections)
  $sites = [
    ['id'=>1,'name'=>'Port-au-Prince (Hub)','freq'=>'—'],
    ['id'=>2,'name'=>'Les Cayes','freq'=>'104.7'],
    ['id'=>3,'name'=>'Jacmel','freq'=>'95.5'],
    ['id'=>4,'name'=>'Jérémie','freq'=>'93.7'],
  ];
  $techs = [
    ['id'=>1,'name'=>'Marie Pierre','avatar'=>'https://i.pravatar.cc/80?img=5'],
    ['id'=>2,'name'=>'Jonas Paul','avatar'=>'https://i.pravatar.cc/80?img=12'],
    ['id'=>3,'name'=>'Eliot Noel','avatar'=>'https://i.pravatar.cc/80?img=23'],
  ];

  // Week mock shifts
  $shifts = [
    ['id'=>11,'tech_id'=>1,'tech_name'=>'Marie Pierre','site_id'=>2,'site_name'=>'Les Cayes','role'=>'primary','starts_at'=>'2025-08-25 08:00','ends_at'=>'2025-08-25 16:00','color'=>'#0866ff'],
    ['id'=>12,'tech_id'=>2,'tech_name'=>'Jonas Paul','site_id'=>1,'site_name'=>'Port-au-Prince (Hub)','role'=>'backup','starts_at'=>'2025-08-25 12:00','ends_at'=>'2025-08-25 20:00','color'=>'#6f42c1'],
    ['id'=>13,'tech_id'=>3,'tech_name'=>'Eliot Noel','site_id'=>3,'site_name'=>'Jacmel','role'=>'primary','starts_at'=>'2025-08-26 09:00','ends_at'=>'2025-08-26 17:00','color'=>'#0866ff'],
    ['id'=>14,'tech_id'=>2,'tech_name'=>'Jonas Paul','site_id'=>4,'site_name'=>'Jérémie','role'=>'primary','starts_at'=>'2025-08-27 10:00','ends_at'=>'2025-08-27 18:00','color'=>'#0866ff'],
  ];

  // Group by date for a simple week grid
  $byDay = collect($shifts)->groupBy(function($s){ return \Illuminate\Support\Carbon::parse($s['starts_at'])->toDateString(); });
  $weekDays = [];
  $start = \Illuminate\Support\Carbon::parse('2025-08-25'); // Monday mock
  for ($i=0;$i<7;$i++) { $weekDays[] = $start->copy()->addDays($i); }
@endphp

<style>
  .chip-role-primary{ background-image: linear-gradient(310deg, #0866ff, #0d6efd); color:#fff; padding:.15rem .5rem; border-radius:999px; font-size:.75rem;}
  .chip-role-backup{ background-image: linear-gradient(310deg, #6f42c1, #b07bff); color:#fff; padding:.15rem .5rem; border-radius:999px; font-size:.75rem;}
  .legend-dot{ display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:.3rem; }
  .bg-ok{ background-image: linear-gradient(310deg, #17ad37, #98ec2d) !important; color:#fff; }
  .schedule-card{ border:1px solid #eee; border-left:4px solid var(--edge,#0866ff); border-radius:.6rem; padding:.6rem .7rem; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.04); }
  .sched-grid{ display:grid; grid-template-columns: repeat(7, minmax(220px,1fr)); gap:12px; }
  @media (max-width: 1200px){ .sched-grid{ grid-template-columns:repeat(2,1fr);} }
  @media (max-width: 576px){ .sched-grid{ grid-template-columns:1fr; } }
</style>

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">calendar_month</i> Technician Schedule</h5>
      <div class="d-flex gap-2">
        <button class="btn btn-light"><i class="material-icons-outlined">chevron_left</i></button>
        <button class="btn btn-light">This Week</button>
        <button class="btn btn-light"><i class="material-icons-outlined">chevron_right</i></button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShiftModal">
          <i class="material-icons-outlined me-1">add</i> Add Shift
        </button>
      </div>
    </div>

    <div class="d-flex align-items-center gap-4 mb-2">
      <div><span class="legend-dot" style="background:#0866ff"></span> Technicien</div>
      <div><span class="legend-dot" style="background:#6f42c1"></span> Backup</div>
      <div class="chip-role-primary">Hub/Site Coverage</div>
    </div>

    <div class="card rounded-4">
      <div class="card-body">
        <div class="sched-grid">
          @foreach($weekDays as $day)
            @php
              $key = $day->toDateString();
              $list = $byDay->get($key, collect());
            @endphp
            <div>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <strong>{{ $day->format('D, M j') }}</strong>
                <span class="badge bg-light text-dark">{{ $list->count() }} shift{{ $list->count()===1?'':'s' }}</span>
              </div>

              @forelse($list as $s)
                <div class="schedule-card mb-2" style="--edge: {{ $s['role']==='primary'?'#0866ff':'#6f42c1' }}">
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="fw-semibold">{{ \Illuminate\Support\Carbon::parse($s['starts_at'])->format('H:i') }}–{{ \Illuminate\Support\Carbon::parse($s['ends_at'])->format('H:i') }}</div>
                    <span class="{{ $s['role']==='primary'?'chip-role-primary':'chip-role-backup' }}">{{ ucfirst($s['role']) }}</span>
                  </div>
                  <div class="mt-1">
                    <i class="material-icons-outlined me-1">radio</i>{{ $s['site_name'] }}
                  </div>
                  <div class="mt-1 d-flex align-items-center gap-2">
                    <img src="{{ $techs[$s['tech_id']-1]['avatar'] ?? 'https://i.pravatar.cc/80' }}" class="rounded-circle" width="24" height="24">
                    <span>{{ $s['tech_name'] }}</span>
                  </div>
                </div>
              @empty
                <div class="text-muted">No shifts</div>
              @endforelse
            </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Add Shift Modal -->
<div class="modal fade" id="addShiftModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add Shift</h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Technician</label>
              <select class="form-select">
                @foreach($techs as $t)<option value="{{ $t['id'] }}">{{ $t['name'] }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Site</label>
              <select class="form-select">
                @foreach($sites as $s)<option value="{{ $s['id'] }}">{{ $s['name'] }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select class="form-select">
                <option value="primary">Primary</option>
                <option value="backup">Backup</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" value="2025-08-25">
            </div>
            <div class="col-md-6">
              <label class="form-label">Start</label>
              <input type="time" class="form-control" value="08:00">
            </div>
            <div class="col-md-6">
              <label class="form-label">End</label>
              <input type="time" class="form-control" value="16:00">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" data-bs-dismiss="modal">Save (Mock)</button></div>
    </div>
  </div>
</div>
