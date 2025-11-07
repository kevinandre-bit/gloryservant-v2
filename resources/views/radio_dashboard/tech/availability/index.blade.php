{{-- resources/views/radio_dashboard/tech/availability/index.blade.php --}}
@include('layouts/radio_layout')

@php
  $myRules = [
    ['id'=>1,'kind'=>'available','days'=>['Mon','Tue','Wed','Thu','Fri'],'time_start'=>'08:00','time_end'=>'16:00','note'=>'Regular office hours'],
    ['id'=>2,'kind'=>'unavailable','days'=>['Sun'],'time_start'=>'00:00','time_end'=>'23:59','note'=>'Church service'],
  ];

  // Simple team coverage heatmap (7 days x 6 blocks [4-hour chunks])
  $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
  $blocks = ['00–04','04–08','08–12','12–16','16–20','20–24'];
  $coverage = [];
  foreach ($blocks as $b) {
    $row = [];
    foreach ($days as $d) { $row[] = rand(0,5); } // 0..5 available techs (mock)
    $coverage[] = $row;
  }
@endphp

<style>
  .heat { display:grid; grid-template-columns: 100px repeat(7, 1fr); gap:6px; }
  .heat .cell, .heat .head { padding:.4rem; border-radius:.4rem; text-align:center; }
  .heat .head { background:#f3f5f9; font-weight:600; }
  .heat .label { text-align:right; padding-right:.4rem; font-weight:600; }
  .h-0{ background:#f0f0f0; }
  .h-1{ background:#e6f7ea; }
  .h-2{ background-image: linear-gradient(310deg, #17ad37, #98ec2d); color:#fff; opacity:.65; }
  .h-3{ background-image: linear-gradient(310deg, #17ad37, #98ec2d); color:#fff; opacity:.8; }
  .h-4{ background-image: linear-gradient(310deg, #17ad37, #98ec2d); color:#fff; opacity:.9; }
  .h-5{ background-image: linear-gradient(310deg, #17ad37, #98ec2d); color:#fff; }
  .rule-chip{ padding:.15rem .5rem; border-radius:.6rem; background:#eef3ff; }
</style>

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">schedule</i> Availability</h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRule"><i class="material-icons-outlined me-1">add</i> Add Rule</button>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-5">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="mb-3">My Availability</h6>
            <form>
              <div class="mb-2">
                <label class="form-label">Kind</label>
                <select class="form-select"><option>available</option><option>unavailable</option></select>
              </div>
              <div class="mb-2">
                <label class="form-label">Days</label>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($days as $d)
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="day-{{ $d }}" checked>
                      <label class="form-check-label" for="day-{{ $d }}">{{ $d }}</label>
                    </div>
                  @endforeach
                </div>
              </div>
              <div class="row">
                <div class="col-md-6"><label class="form-label">Start</label><input type="time" class="form-control" value="08:00"></div>
                <div class="col-md-6"><label class="form-label">End</label><input type="time" class="form-control" value="16:00"></div>
              </div>
              <div class="mt-2"><label class="form-label">Note</label><input class="form-control" placeholder="Optional note"></div>
              <div class="d-flex justify-content-end mt-3"><button class="btn btn-primary" type="button">Save (Mock)</button></div>
            </form>

            <hr class="my-4">

            <h6 class="mb-2">My Rules</h6>
            @forelse($myRules as $r)
              <div class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2">
                <div>
                  <span class="rule-chip">{{ $r['kind'] }}</span>
                  <span class="ms-2">{{ implode(',', $r['days']) }}</span>
                  <span class="ms-2">{{ $r['time_start'] }}–{{ $r['time_end'] }}</span>
                  <span class="ms-2">{{ $r['note'] }}</span>
                </div>
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-secondary">Edit</button>
                  <button class="btn btn-outline-danger">Delete</button>
                </div>
              </div>
            @empty
              <div class="text-muted">No rules yet.</div>
            @endforelse

          </div>
        </div>
      </div>

      <div class="col-12 col-xl-7">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="mb-3">Team Coverage Heatmap</h6>
            <div class="heat">
              <div></div>
              @foreach($days as $d)<div class="head">{{ $d }}</div>@endforeach

              @foreach($blocks as $i => $blk)
                <div class="label">{{ $blk }}</div>
                @foreach($coverage[$i] as $val)
                  @php
                    $cls = 'h-'.max(0,min(5,$val));
                  @endphp
                  <div class="cell {{ $cls }}">{{ $val }}</div>
                @endforeach
              @endforeach
            </div>
            <div class="mt-3 small">
              <strong>Legend:</strong> 0=gray (no coverage), 1–5 = green gradient (more available techs)
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- New Rule Modal -->
<div class="modal fade" id="newRule" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Add Availability Rule</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form>
          <div class="mb-2">
            <label class="form-label">Kind</label>
            <select class="form-select"><option>available</option><option>unavailable</option></select>
          </div>
          <div class="mb-2">
            <label class="form-label">Days</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach($days as $d)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="m-{{ $d }}" checked>
                  <label class="form-check-label" for="m-{{ $d }}">{{ $d }}</label>
                </div>
              @endforeach
            </div>
          </div>
          <div class="row">
            <div class="col-md-6"><label class="form-label">Start</label><input type="time" class="form-control" value="08:00"></div>
            <div class="col-md-6"><label class="form-label">End</label><input type="time" class="form-control" value="16:00"></div>
          </div>
          <div class="mt-2"><label class="form-label">Note</label><input class="form-control" placeholder="Optional note"></div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" data-bs-dismiss="modal">Save (Mock)</button></div>
    </div>
  </div>
</div>
