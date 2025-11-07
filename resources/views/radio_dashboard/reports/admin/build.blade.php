@include('layouts/radio_layout')

@php
  $d = $adminDefaults ?? [];
  $tech = $d['tech'] ?? [];
  $inc  = $d['incidents'] ?? [];
  $ov   = $d['overall'] ?? [];
  $todo = $d['todo'] ?? [];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Report Studio</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('reports.studio') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active">Build — Admin Daily Summary</li>
        </ol>
      </div>
      <div class="ms-auto">
        <span class="badge bg-light text-dark">{{ $title }}</span>
      </div>
    </div>

    @if($type !== 'daily_admin')
      <div class="alert alert-info">
        This page is optimized for <strong>Daily Operations (Admin)</strong>. Switch type to <code>daily_admin</code>.
      </div>
    @endif

    {{-- Single form that posts to preview (GET for now so it’s easy to inspect) --}}
    <form method="get" action="{{ route('reports.preview',['type'=>'daily_admin']) }}" id="builderForm">
      <input type="hidden" name="type" value="daily_admin">

      <div class="row g-3">

        {{-- Header --}}
        <div class="col-12 col-xl-4">
          <div class="card rounded-4"><div class="card-body p-4">
            <h5 class="mb-2 d-flex align-items-center gap-2">
              <i class="material-icons-outlined">event</i> Header
            </h5>
            <label class="form-label">Date</label>
            <input name="from" type="date" class="form-control" value="{{ $d['date'] ?? $date }}">
            <input type="hidden" name="to" value="{{ $d['date'] ?? $date }}">
            <label class="form-label mt-3">Prepared by</label>
            <input name="prepared_by" type="text" class="form-control" value="{{ $d['prepared_by'] ?? 'Admin – Jane Doe' }}">
          </div></div>
        </div>

        {{-- Technicians Today (attendance + notes) --}}
        <div class="col-12 col-xl-8">
          <div class="card rounded-4"><div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
              <h5 class="mb-2 d-flex align-items-center gap-2">
                <i class="material-icons-outlined">badge</i> Technicians (Today) — Attendance
              </h5>
              <button type="button" class="btn btn-sm btn-light" id="addTechRow">
                <i class="material-icons-outlined">add</i> Add Tech
              </button>
            </div>

            <div class="table-responsive">
              <table class="table align-middle mb-0" id="techTable">
                <thead class="table-light">
                  <tr>
                    <th>Technician</th><th>Shift</th><th>Present</th><th>Notes</th><th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tech as $i => $t)
                    <tr>
                      <td><input class="form-control" name="tech[{{$i}}][name]" value="{{ $t['name'] }}"></td>
                      <td><input class="form-control" name="tech[{{$i}}][shift]" value="{{ $t['shift'] }}"></td>
                      <td class="text-center">
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" name="tech[{{$i}}][present]" {{ !empty($t['present']) ? 'checked' : '' }}>
                        </div>
                      </td>
                      <td><input class="form-control" name="tech[{{$i}}][notes]" value="{{ $t['notes'] }}"></td>
                      <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary remove-row">
                          <i class="material-icons-outlined">close</i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div></div>
        </div>

        {{-- Incident Queue --}}
        <div class="col-12">
          <div class="card rounded-4"><div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
              <h5 class="mb-2 d-flex align-items-center gap-2">
                <i class="material-icons-outlined">report</i> Technician Incident Reports
              </h5>
              <button type="button" class="btn btn-sm btn-light" id="addIncidentRow">
                <i class="material-icons-outlined">add</i> Add Incident
              </button>
            </div>

            <div class="table-responsive">
              <table class="table align-middle mb-0" id="incidentTable">
                <thead class="table-light">
                  <tr>
                    <th>Station</th><th>Time</th><th>Issue</th><th>Downtime</th><th>Resolution</th><th>Status</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($inc as $i => $r)
                    <tr>
                      <td><input class="form-control" name="incidents[{{$i}}][station]" value="{{ $r['station'] }}"></td>
                      <td><input class="form-control" name="incidents[{{$i}}][time]" value="{{ $r['time'] }}"></td>
                      <td><input class="form-control" name="incidents[{{$i}}][issue]" value="{{ $r['issue'] }}"></td>
                      <td><input class="form-control" name="incidents[{{$i}}][downtime]" value="{{ $r['downtime'] }}"></td>
                      <td><input class="form-control" name="incidents[{{$i}}][resolution]" value="{{ $r['resolution'] }}"></td>
                      <td style="min-width:110px;">
                        <select class="form-select" name="incidents[{{$i}}][status]">
                          @php $st = $r['status'] ?? 'open'; @endphp
                          <option value="open"      {{ $st==='open' ? 'selected':'' }}>Open</option>
                          <option value="completed" {{ $st==='completed' ? 'selected':'' }}>Completed</option>
                          <option value="forwarded" {{ $st==='forwarded' ? 'selected':'' }}>Forwarded</option>
                        </select>
                      </td>
                      <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-success mark-complete">Complete</button>
                        <button type="button" class="btn btn-sm btn-outline-primary mark-forward">Forward</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary remove-row">
                          <i class="material-icons-outlined">close</i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div></div>
        </div>

        {{-- Overall Broadcast Status --}}
        <div class="col-12 col-xl-6">
          <div class="card rounded-4"><div class="card-body p-4">
            <h5 class="mb-2 d-flex align-items-center gap-2">
              <i class="material-icons-outlined">signal_cellular_alt</i> Overall Broadcast Status
            </h5>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Stations Monitored</label>
                <input type="number" name="stations_monitored" class="form-control" value="{{ $ov['stations_monitored'] ?? 10 }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Overall Uptime (%)</label>
                <input type="number" step="0.1" name="uptime_pct" class="form-control" value="{{ $ov['uptime_pct'] ?? 98.9 }}">
              </div>
              <div class="col-12">
                <label class="form-label">Total Downtime</label>
                <input type="text" name="total_downtime" class="form-control" value="{{ $ov['total_downtime'] ?? '2h 20m' }}">
              </div>
              <div class="col-12">
                <label class="form-label">Remark (optional)</label>
                <input type="text" name="overall_remark" class="form-control" value="{{ $ov['remark'] ?? '' }}">
              </div>
            </div>
          </div></div>
        </div>

        {{-- Observations & To-Do --}}
        <div class="col-12 col-xl-6">
          <div class="card rounded-4"><div class="card-body p-4">
            <h5 class="mb-2 d-flex align-items-center gap-2">
              <i class="material-icons-outlined">fact_check</i> Observations & Follow-up To-Do
            </h5>
            <label class="form-label">Admin Observations</label>
            <textarea name="observations" rows="6" class="form-control">{{ $d['observations'] ?? '' }}</textarea>

            <div class="d-flex align-items-center justify-content-between mt-3">
              <label class="form-label mb-0">To-Do Items (forward to manager as actions)</label>
              <button type="button" class="btn btn-sm btn-light" id="addTodoRow">
                <i class="material-icons-outlined">add_task</i> Add To-Do
              </button>
            </div>

            <div id="todoList" class="mt-2">
              @foreach($todo as $i => $t)
                <div class="d-flex align-items-center gap-2 mb-2 todo-row">
                  <div class="form-check m-0">
                    <input class="form-check-input" type="checkbox" name="todo[{{$i}}][done]" {{ !empty($t['done'])?'checked':'' }}>
                  </div>
                  <input class="form-control" name="todo[{{$i}}][text]" value="{{ $t['text'] ?? '' }}" placeholder="Follow-up item">
                  <button type="button" class="btn btn-sm btn-outline-secondary remove-todo">
                    <i class="material-icons-outlined">close</i>
                  </button>
                </div>
              @endforeach
            </div>

            {{-- This hidden textarea will be filled from todo[] as a bullet list before submit --}}
            <textarea name="manager_actions" id="managerActions" class="d-none"></textarea>
          </div></div>
        </div>

        <div class="col-12">
          <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit" id="btnPreview">
              <i class="material-icons-outlined">visibility</i> Preview Daily Summary
            </button>
            <a class="btn btn-light" href="{{ route('reports.studio') }}">Back to Studio</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</main>

<script>
(function(){
  function onReady(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}

  onReady(function(){
    const techTable   = document.getElementById('techTable').querySelector('tbody');
    const addTech     = document.getElementById('addTechRow');
    const incTable    = document.getElementById('incidentTable').querySelector('tbody');
    const addIncident = document.getElementById('addIncidentRow');
    const todoList    = document.getElementById('todoList');
    const addTodo     = document.getElementById('addTodoRow');
    const form        = document.getElementById('builderForm');
    const managerTA   = document.getElementById('managerActions');

    // Add/Remove rows util
    function removeRowBtnHandler(e){
      const tr = e.target.closest('tr');
      if(tr) tr.remove();
    }

    // TECH: add row
    addTech?.addEventListener('click', function(){
      const idx = techTable.querySelectorAll('tr').length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input class="form-control" name="tech[${idx}][name]"  placeholder="Technician"></td>
        <td><input class="form-control" name="tech[${idx}][shift]" placeholder="08:00 – 16:00"></td>
        <td class="text-center">
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" name="tech[${idx}][present]" checked>
          </div>
        </td>
        <td><input class="form-control" name="tech[${idx}][notes]" placeholder="Notes"></td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-outline-secondary remove-row">
            <i class="material-icons-outlined">close</i>
          </button>
        </td>`;
      techTable.appendChild(tr);
    });

    // INC: add row
    addIncident?.addEventListener('click', function(){
      const idx = incTable.querySelectorAll('tr').length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input class="form-control" name="incidents[${idx}][station]"    placeholder="Station"></td>
        <td><input class="form-control" name="incidents[${idx}][time]"       placeholder="Time"></td>
        <td><input class="form-control" name="incidents[${idx}][issue]"      placeholder="Issue"></td>
        <td><input class="form-control" name="incidents[${idx}][downtime]"   placeholder="Downtime"></td>
        <td><input class="form-control" name="incidents[${idx}][resolution]" placeholder="Resolution"></td>
        <td>
          <select class="form-select" name="incidents[${idx}][status]">
            <option value="open" selected>Open</option>
            <option value="completed">Completed</option>
            <option value="forwarded">Forwarded</option>
          </select>
        </td>
        <td class="text-nowrap">
          <button type="button" class="btn btn-sm btn-outline-success mark-complete">Complete</button>
          <button type="button" class="btn btn-sm btn-outline-primary mark-forward">Forward</button>
          <button type="button" class="btn btn-sm btn-outline-secondary remove-row">
            <i class="material-icons-outlined">close</i>
          </button>
        </td>`;
      incTable.appendChild(tr);
    });

    // Delegate remove + status buttons on incident table
    incTable.addEventListener('click', function(e){
      if(e.target.closest('.remove-row')){
        e.preventDefault();
        const tr = e.target.closest('tr'); if (tr) tr.remove();
      }
      if(e.target.closest('.mark-complete')){
        e.preventDefault();
        const tr = e.target.closest('tr'); if (!tr) return;
        const sel = tr.querySelector('select[name*="[status]"]');
        if (sel) sel.value = 'completed';
      }
      if(e.target.closest('.mark-forward')){
        e.preventDefault();
        const tr = e.target.closest('tr'); if (!tr) return;
        const sel = tr.querySelector('select[name*="[status]"]');
        if (sel) sel.value = 'forwarded';
      }
    });

    // Remove row in tech table
    techTable.addEventListener('click', function(e){
      if(e.target.closest('.remove-row')){
        e.preventDefault();
        const tr = e.target.closest('tr'); if (tr) tr.remove();
      }
    });

    // TODO: add row
    addTodo?.addEventListener('click', function(){
      const idx = todoList.querySelectorAll('.todo-row').length;
      const row = document.createElement('div');
      row.className = 'd-flex align-items-center gap-2 mb-2 todo-row';
      row.innerHTML = `
        <div class="form-check m-0">
          <input class="form-check-input" type="checkbox" name="todo[${idx}][done]">
        </div>
        <input class="form-control" name="todo[${idx}][text]" placeholder="Follow-up item">
        <button type="button" class="btn btn-sm btn-outline-secondary remove-todo">
          <i class="material-icons-outlined">close</i>
        </button>`;
      todoList.appendChild(row);
    });

    // TODO: remove row
    todoList.addEventListener('click', function(e){
      if(e.target.closest('.remove-todo')){
        e.preventDefault();
        const row = e.target.closest('.todo-row'); if (row) row.remove();
      }
    });

    // Before Preview: compile todo[] into manager_actions (one per line)
    form.addEventListener('submit', function(){
      const items = Array.from(todoList.querySelectorAll('input[name*="[text]"]'))
        .map(inp => inp.value.trim())
        .filter(Boolean);
      managerTA.value = items.join('\n');
    });
  });
})();
</script>