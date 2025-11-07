@php use App\Classes\permission; @endphp
@include('layouts/admin')

<main class="main-wrapper">
  <div class="main-content">
    <!-- breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Volunteers</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item active" aria-current="page">
            <i class="fadeIn animated bx bx-calendar-week"></i> Schedule
          </li>
        </ol>
      </div>
      <div class="ms-auto">
        <div class="btn-group">
          <div class="btn-group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddScheduleModal">
              Add Schedule
            </button>
          </div>
        </div>
      </div>
    </div>
    <hr>

    <div class="card mb-3 shadow-sm border-0">
      <div class="card-body">
        <form method="GET" action="{{ url('schedules') }}" class="row g-3 align-items-end">
          <div class="col-12 col-md-4 col-lg-3">
            <label class="form-label text-muted text-uppercase small mb-1">Campus</label>
            <select name="campus" class="form-select form-select-sm">
              <option value="">All campuses</option>
              @foreach(($campusOptions ?? []) as $campus)
                <option value="{{ $campus }}" @selected(($filters['campus'] ?? '') === $campus)>{{ $campus }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <label class="form-label text-muted text-uppercase small mb-1">Ministry</label>
            <select name="ministry" class="form-select form-select-sm">
              <option value="">All ministries</option>
              @foreach(($ministryOptions ?? []) as $ministry)
                <option value="{{ $ministry }}" @selected(($filters['ministry'] ?? '') === $ministry)>{{ $ministry }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <label class="form-label text-muted text-uppercase small mb-1">Department</label>
            <select name="department" class="form-select form-select-sm">
              <option value="">All departments</option>
              @foreach(($departmentOptions ?? []) as $department)
                <option value="{{ $department }}" @selected(($filters['department'] ?? '') === $department)>{{ $department }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm px-4 mt-auto">Apply Filters</button>
            <a href="{{ url('schedules') }}" class="btn btn-outline-secondary btn-sm mt-auto">Reset</a>
          </div>
        </form>
      </div>
    </div>

    @if(!empty($weeklyCounts))
      <div class="row g-2 mb-3 flex-nowrap overflow-auto">
        @foreach($weeklyCounts as $day => $count)
          <div class="col-12 col-md-6 col-lg-4 col-xl-3 col-xxl-2" style="min-width: 150px;">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body py-3 text-center">
                <span class="text-muted text-uppercase small">{{ $day }}</span>
                <h4 class="fw-semibold mb-0">{{ $count }}</h4>
                <span class="text-muted small">Scheduled</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Time (Start–Off)</th>
                <th>Hours</th>
                <th>Rest Days</th>
                <th>From (Date)</th>
                <th>To (Date)</th>
                <th>Status</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>
            @isset($schedules)
              @foreach ($schedules as $sched)
                @php
                  // Support either column naming
                  $in  = $sched->intime  ?? $sched->timein  ?? null;
                  $out = $sched->outime  ?? $sched->timeout ?? null;

                  $fmt12 = fn($t)=> $t ? date('h:i A', strtotime($t)) : '—';
                  $fmt24 = fn($t)=> $t ? date('H:i',   strtotime($t)) : '—';
                @endphp
                <tr>
                  <td>{{ $sched->employee }}</td>
                  <td>
                    @if((int)($tf ?? 1) === 1)
                      {{ $fmt12($in) }} - {{ $fmt12($out) }}
                    @else
                      {{ $fmt24($in) }} - {{ $fmt24($out) }}
                    @endif
                  </td>
                  <td>{{ $sched->hours }} hr</td>
                  <td>{{ $sched->restday }}</td>
                  <td>{{ $sched->datefrom ? \Carbon\Carbon::parse($sched->datefrom)->format('D, M d, Y') : '—' }}</td>
                  <td>{{ $sched->dateto   ? \Carbon\Carbon::parse($sched->dateto)->format('D, M d, Y')   : '—' }}</td>
                  <td>
                    @if($sched->archive == '0')
                      <span class="green">{{ __('Present') }}</span>
                    @else
                      <span class="teal">{{ __('Archived') }}</span>
                    @endif
                  </td>
                  <td>
                    @if($sched->archive == '0')
                      <a 
                        href="javascript:void(0)" 
                        class="ui circular basic icon button tiny text-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#FormModal"
                        data-id="{{ Crypt::encryptString($sched->id) }}"
                        data-idno="{{ $sched->idno ?? '' }}"
                        data-name="{{ $sched->employee }}"
                        data-intime="{{ $sched->intime ?? '' }}"
                        data-outime="{{ $sched->outime ?? '' }}"
                        data-datefrom="{{ \Illuminate\Support\Str::of($sched->datefrom ?? '')->substr(0,10) }}"
                        data-dateto="{{ \Illuminate\Support\Str::of($sched->dateto ?? '')->substr(0,10) }}"
                        data-restday="{{ $sched->restday ?? '' }}"
                      >
                        <i class="bx bx-edit"></i>
                      </a>

                      <a href="{{ url('/schedules/delete/'.$sched->id) }}" class="btn gap-2">
                        <i class="fadeIn animated bx bx-trash"></i>
                      </a>
                      <a href="{{ url('/schedules/archive/'.$sched->id) }}" class="btn gap-2">
                        <i class="fadeIn animated bx bx-archive"></i>
                      </a>
                    @endif
                  </td>
                </tr>
              @endforeach
            @endisset
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="FormModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-bottom-0 py-2 bg-dark">
            <h5 class=" text-secondary">Edit volunteer Schedule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            {{-- IMPORTANT: Post to schedules/update to match your controller --}}
            <form id="attendanceForm" class="row g-3" method="POST" action="{{ url('schedules/update') }}">
              @csrf

              <input type="hidden" name="id" id="modalId">
              <input type="hidden" name="idno" id="modalIdno">

              <div class="col-md-12">
                <label class="form-label">Volunteer</label>
                <input type="text" class="form-control" name="name" id="modalName" readonly>
              </div>

              <!-- Use type="text" so Flatpickr fully controls the field -->
              <div class="col-md-6">
                <label class="form-label">Start time</label>
                <input type="text" class="form-control" name="intime" id="modalTimeIn" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Off time</label>
                <input type="text" class="form-control" name="outime" id="modalTimeOut" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">From</label>
                <input type="text" class="form-control" name="datefrom" id="modalDateFrom" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">To</label>
                <input type="text" class="form-control" name="dateto" id="modalDateTo" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Hours (auto)</label>
                <input type="text" class="form-control" name="hours" id="modalHours" readonly>
                <div class="form-text">= (Off − Start) × (7 − rest days)</div>
              </div>

              <div class="col-12">
                <label class="form-label d-block">Rest Days</label>
                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="restday[]" value="{{ $day }}" id="modalRest{{ $day }}">
                    <label class="form-check-label" for="modalRest{{ $day }}">{{ __($day) }}</label>
                  </div>
                @endforeach
              </div>

              <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
    <!-- /Edit Modal -->

<!-- Add Schedule Modal -->
<div class="modal fade" id="AddScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom-0 py-2 bg-dark">
        <h5 class="modal-title text-secondary ">Add New Schedule</h5>
        <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="addScheduleForm" class="row g-3" method="POST" action="{{ route('schedules.add') }}">
          @csrf

          {{-- Volunteer --}}
          <div class="col-md-12">
            <label class="form-label">Volunteer</label>
            <select class="form-select" name="id" id="addVolunteer" required>
              <option value="" selected disabled>Choose a volunteer…</option>
              @foreach($employee as $p)
                <option value="{{ $p->id }}">
                  {{ $p->lastname }}, {{ $p->firstname }}
                </option>
              @endforeach
            </select>
            {{-- Optional: show derived name read-only --}}
            <input type="text" style="display:none;" class="form-control mt-2 "  id="addVolunteerName" name="employee" readonly placeholder="Volunteer name (auto)">
          </div>

          <div class="col-md-6">
            <label class="form-label">Start time</label>
            <input type="text" class="form-control" name="intime" id="addTimeIn" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Off time</label>
            <input type="text" class="form-control" name="outime" id="addTimeOut" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">From</label>
            <input type="text" class="form-control" name="datefrom" id="addDateFrom" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">To</label>
            <input type="text" class="form-control" name="dateto" id="addDateTo" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Hours (auto)</label>
            <input type="text" class="form-control" name="hours" id="addHours" readonly>
            <div class="form-text">= (Off − Start) × (7 − rest days)</div>
          </div>

          <div class="col-12">
            <label class="form-label d-block">Rest Days</label>
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="restday[]" value="{{ $day }}" id="addRest{{ $day }}">
                <label class="form-check-label" for="addRest{{ $day }}">{{ __($day) }}</label>
              </div>
            @endforeach
          </div>

          {{-- This will be added dynamically if user confirms conflict --}}
          {{-- <input type="hidden" name="force" value="1"> --}}

          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- /Add Schedule Modal -->

<!-- Conflict Confirmation Modal -->
<div class="modal fade" id="ConflictModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-primary-subtle border-0 rounded-top-4">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bx bx-error-circle"></i>
          Potential Schedule Conflict
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <p class="mb-2">This schedule overlaps an existing <strong>active</strong> schedule for this volunteer.</p>

        <!-- populated dynamically with a list of conflicts, if provided -->
        <div id="ConflictList" class="small text-secondary"></div>

        <hr class="my-3">
        <p class="mb-0">
          If you continue, the <strong>new schedule will take priority</strong>.<br>
          The overlapping schedule(s) will be <strong>archived</strong> and set <strong>inactive</strong>.
        </p>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="btnConfirmConflict" class="btn btn-success">
          Continue &amp; Replace
        </button>
      </div>
    </div>
  </div>
</div>
  </div>
</main>

<footer class="page-footer">
  <p class="mb-0">Copyright © 2025. All right reserved.</p>
</footer>

{{-- DataTables (keep if you use it) --}}

<script>
  $(function(){
    var table = $('#example2').DataTable({
      pageLength: 30,
      lengthChange: false,
      buttons: ['copy','excel','pdf','print']
    });
    table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
  });
</script>

<!-- Load once, above this script -->
<!-- Flatpickr once -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ------- helpers you already have -------
  function diffHours24(startStr, endStr) {
    if (!startStr || !endStr) return 0;
    const [sh, sm] = startStr.split(':').map(Number);
    const [eh, em] = endStr.split(':').map(Number);
    let diff = (eh*60+em) - (sh*60+sm);
    if (diff < 0) diff += 24*60;
    return diff / 60;
  }
  function setupTimePicker(sel) {
    return flatpickr(sel, {
      enableTime: true, noCalendar: true,
      dateFormat: 'H:i',        // submit value
      altInput: true, altFormat:'h:i K', // display
      time_24hr: true, allowInput: true
    });
  }
  function setupDatePicker(sel) {
    return flatpickr(sel, {
      dateFormat: 'Y-m-d',
      altInput: true, altFormat: 'F j, Y',
      allowInput: true
    });
  }

  // ------- EDIT MODAL -------
  const editModal = document.getElementById('FormModal');
  if (!editModal) return;

  const $E = (s)=>editModal.querySelector(s);
  const fpTimeIn  = setupTimePicker('#modalTimeIn');
  const fpTimeOut = setupTimePicker('#modalTimeOut');
  const fpFrom    = setupDatePicker('#modalDateFrom');
  const fpTo      = setupDatePicker('#modalDateTo');

  function recalcEditHours(){
    const start = $E('#modalTimeIn').value;
    const end   = $E('#modalTimeOut').value;
    const daily = diffHours24(start, end);
    const restChecked = editModal.querySelectorAll('input[name="restday[]"]:checked').length;
    const workDays = Math.max(0, 7 - restChecked);
    $E('#modalHours').value = Number.isFinite(daily*workDays) ? (daily*workDays).toFixed(2) : '';
  }

  // FILL-IN HANDLER — this is what was missing
  editModal.addEventListener('show.bs.modal', (ev)=>{
    const btn = ev.relatedTarget;
    if (!btn) return;

    // 1) Basic fields
    $E('#modalId').value   = btn.dataset.id   || '';
    $E('#modalIdno').value = btn.dataset.idno || '';
    $E('#modalName').value = btn.dataset.name || '';
    $E('#modalHours').value = '';

    // 2) Clear pickers then set via Flatpickr API
    fpFrom.clear(); fpTo.clear(); fpTimeIn.clear(); fpTimeOut.clear();

    // Dates: prefer YYYY-MM-DD in data-*
    if (btn.dataset.datefrom) fpFrom.setDate(btn.dataset.datefrom, true, 'Y-m-d');
    if (btn.dataset.dateto)   fpTo.setDate(btn.dataset.dateto,   true, 'Y-m-d');

    // Times: accepts 'HH:mm' or 'h:i K'
    if (btn.dataset.intime) fpTimeIn.setDate(btn.dataset.intime, true);
    if (btn.dataset.outime) fpTimeOut.setDate(btn.dataset.outime, true);

    // 3) Rest days: uncheck all then check incoming
    editModal.querySelectorAll('input[name="restday[]"]').forEach(cb => cb.checked = false);
    (btn.dataset.restday || '')
      .split(',')
      .map(s => s.trim())
      .filter(Boolean)
      .forEach(day => {
        const cb = editModal.querySelector('#modalRest' + day);
        if (cb) cb.checked = true;
      });
  });

  // Recalculate hours after it’s visible (ensures alt inputs exist)
  editModal.addEventListener('shown.bs.modal', recalcEditHours);

  // Live recalc on change
  fpTimeIn.config.onChange.push(recalcEditHours);
  fpTimeOut.config.onChange.push(recalcEditHours);
  editModal.querySelectorAll('input[name="restday[]"]').forEach(cb=>{
    cb.addEventListener('change', recalcEditHours);
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ============ Common helpers ============
  function diffHours24(startStr, endStr) {
    if (!startStr || !endStr) return 0;
    const [sh, sm] = startStr.split(':').map(Number);
    const [eh, em] = endStr.split(':').map(Number);
    let diff = (eh*60+em) - (sh*60+sm);
    if (diff < 0) diff += 24*60; // overnight
    return diff / 60;
  }
  function countChecked(container, name) {
    return container.querySelectorAll(`input[name="${name}"]:checked`).length;
  }
  function setupTimePicker(sel) {
    return flatpickr(sel, {
      enableTime: true, noCalendar: true,
      dateFormat: 'H:i',             // value for backend
      altInput: true, altFormat: 'h:i K', // user-friendly
      time_24hr: true, allowInput: true
    });
  }
  function setupDatePicker(sel) {
    return flatpickr(sel, {
      dateFormat: 'Y-m-d',
      altInput: true, altFormat: 'F j, Y',
      allowInput: true
    });
  }

  // ============ ADD MODAL ============
  const addModal = document.getElementById('AddScheduleModal');
  if (addModal) {
    const $A = (s)=>addModal.querySelector(s);
    const aTimeIn  = setupTimePicker('#addTimeIn');
    const aTimeOut = setupTimePicker('#addTimeOut');
    const aFrom    = setupDatePicker('#addDateFrom');
    const aTo      = setupDatePicker('#addDateTo');

    function recalcAddHours(){
      const daily = diffHours24($A('#addTimeIn').value, $A('#addTimeOut').value);
      const work  = Math.max(0, 7 - countChecked(addModal,'restday[]'));
      $A('#addHours').value = (Number.isFinite(daily*work) ? (daily*work).toFixed(2) : '');
    }

    // derive read-only volunteer name when selected
    const volunteerSel = $A('#addVolunteer');
    const volunteerName = $A('#addVolunteerName');
    volunteerSel.addEventListener('change', ()=>{
      const opt = volunteerSel.options[volunteerSel.selectedIndex];
      volunteerName.value = opt ? opt.text : '';
    });

    // recalc on time/rest changes
    aTimeIn.config.onChange.push(recalcAddHours);
    aTimeOut.config.onChange.push(recalcAddHours);
    addModal.querySelectorAll('input[name="restday[]"]').forEach(cb=>cb.addEventListener('change', recalcAddHours));

    addModal.addEventListener('shown.bs.modal', ()=>{
      // clear fields each time it's opened
      document.getElementById('addScheduleForm')?.reset?.();
      aTimeIn.clear(); aTimeOut.clear(); aFrom.clear(); aTo.clear();
      $A('#addHours').value = '';
      addModal.querySelectorAll('input[name="restday[]"]').forEach(cb => cb.checked = false);
    });

    // ---- Conflict pre-check with Bootstrap modal ----
    const addForm = document.getElementById('addScheduleForm');
    const conflictModalEl = document.getElementById('ConflictModal');
    const conflictModal   = new bootstrap.Modal(conflictModalEl);
    const conflictListEl  = conflictModalEl.querySelector('#ConflictList');
    const btnConfirm      = conflictModalEl.querySelector('#btnConfirmConflict');
    let pendingSubmit = false;

    addForm.addEventListener('submit', async (e)=>{
      // If already confirmed once, let it go
      if (addForm.querySelector('input[name="force"][value="1"]')) return;

      e.preventDefault();

      const id       = $A('#addVolunteer').value;
      const datefrom = $A('#addDateFrom').value;
      const dateto   = $A('#addDateTo').value;

      recalcAddHours();

      if (!id || !datefrom || !dateto) {
        alert('Please pick a volunteer and date range.');
        return;
      }

      try {
        const resp = await fetch(`{{ route('schedules.check') }}`, {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ id, datefrom, dateto })
        });
        const data = await resp.json();

        if (data?.conflict) {
          // build list
          conflictListEl.innerHTML = '';
          if (Array.isArray(data.conflicts) && data.conflicts.length) {
            const ul = document.createElement('ul');
            ul.className = 'mb-0';
            data.conflicts.forEach(c => {
              const from = (c.datefrom ?? '').toString().slice(0,10);
              const to   = (c.dateto ?? '').toString().slice(0,10);
              const li = document.createElement('li');
              li.textContent = `${from} → ${to}`;
              ul.appendChild(li);
            });
            conflictListEl.appendChild(ul);
          } else {
            conflictListEl.textContent = 'One or more active schedules overlap this date range.';
          }

          pendingSubmit = true;
          conflictModal.show();
          return;
        }

        // no conflict → submit now
        addForm.submit();

      } catch (err) {
        console.error(err);
        alert('Could not verify conflicts. Please try again.');
      }
    });

    // confirm -> add force flag + submit
    btnConfirm.addEventListener('click', ()=>{
      if (!pendingSubmit) return;
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'force';
      input.value = '1';
      addForm.appendChild(input);

      btnConfirm.disabled = true;
      addForm.submit();
    });

    // reset on close
    conflictModalEl.addEventListener('hidden.bs.modal', ()=>{
      pendingSubmit = false;
      btnConfirm.disabled = false;
      conflictListEl.innerHTML = '';
    });
  }
});
</script>
