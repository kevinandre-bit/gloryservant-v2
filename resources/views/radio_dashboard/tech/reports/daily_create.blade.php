{{-- resources/views/radio_dashboard/tech/reports/daily_create.blade.php --}}
@include('layouts/radio_layout')

@php
  // Fake sites (fallback if controller didn’t pass $sites)
  $sites = $sites ?? [
    ['id'=>1,'name'=>'West — Port-au-Prince','freq'=>'103.3','status'=>'on'],
    ['id'=>2,'name'=>'South — Les Cayes','freq'=>'104.7','status'=>'on'],
    ['id'=>3,'name'=>'South-East — Jacmel','freq'=>'95.5','status'=>'on'],
    ['id'=>4,'name'=>'Grand’Anse — Jérémie','freq'=>'93.7','status'=>'off'],
    ['id'=>5,'name'=>'Center — Hinche','freq'=>'90.3','status'=>'on'],
    ['id'=>6,'name'=>'North-West — Port-de-Paix','freq'=>'93.7','status'=>'on'],
    ['id'=>7,'name'=>'Nippes — Miragoâne','freq'=>'91.9','status'=>'on'],
    ['id'=>8,'name'=>'North-East — Fort-Liberté','freq'=>'—','status'=>'off'],
  ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Technicians</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('tech.checkins.index') }}">Check-ins</a></li>
          <li class="breadcrumb-item active" aria-current="page">Daily Report (New)</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('tech.schedule.index') }}" class="btn btn-outline-secondary">My Schedule</a>
        <a href="{{ route('tech.assignments.index') }}" class="btn btn-outline-primary">Assignments</a>
      </div>
    </div>

    <div class="row g-3">
      {{-- LEFT: Quick Site Status Switches --}}
      <div class="col-12 col-xl-5 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between">
              <h5 class="mb-3">TX Sites — On-Air Status</h5>
              <div class="btn-group btn-group-sm">
                <button id="btnAllOn"  class="btn btn-outline-success">All On-Air</button>
                <button id="btnAllOff" class="btn btn-outline-danger">All Off-Air</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:42%">Site</th>
                    <th>Freq</th>
                    <th>Status</th>
                    <th class="text-end">Toggle</th>
                  </tr>
                </thead>
                <tbody id="sitesTableBody">
                  @foreach($sites as $s)
                    <tr data-site-id="{{ $s['id'] }}">
                      <td class="fw-semibold">{{ $s['name'] }}</td>
                      <td>{{ $s['freq'] }}</td>
                      <td>
                        <span class="badge {{ $s['status']==='on' ? 'bg-success' : 'bg-danger' }} status-badge">
                          {{ $s['status']==='on' ? 'On-Air' : 'Off-Air' }}
                        </span>
                      </td>
                      <td class="text-end">
                        <div class="form-check form-switch d-inline-block">
                          <input class="form-check-input site-toggle"
                                 type="checkbox"
                                 {{ $s['status']==='on' ? 'checked' : '' }}>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <hr class="my-3">
            <div class="d-flex align-items-center gap-2">
              <button id="btnSimulateSave" class="btn btn-primary">Save Status (UI only)</button>
              <div class="small" id="saveHint">This is a mock save to help you test UX.</div>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: Daily Technician Report (interface only) --}}
      <div class="col-12 col-xl-7 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">Daily Signal & Broadcast Monitoring Report</h5>

            <form id="dailyTechForm" onsubmit="return false;">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Date</label>
                  <input type="date" class="form-control" value="{{ now()->toDateString() }}">
                </div>
                <div class="col-md-8">
                  <label class="form-label">Site Monitored</label>
                  <select class="form-select" id="formSite">
                    @foreach($sites as $s)
                      <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                    @endforeach
                    <option value="hub">Port-au-Prince (Hub)</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label d-block">Signal Status</label>
                  <div class="btn-group" role="group" aria-label="signal">
                    <input type="radio" class="btn-check" name="signal" id="sig-exc" autocomplete="off" checked>
                    <label class="btn btn-outline-success" for="sig-exc">Excellent</label>

                    <input type="radio" class="btn-check" name="signal" id="sig-good" autocomplete="off">
                    <label class="btn btn-outline-primary" for="sig-good">Good</label>

                    <input type="radio" class="btn-check" name="signal" id="sig-weak" autocomplete="off">
                    <label class="btn btn-outline-warning" for="sig-weak">Weak</label>

                    <input type="radio" class="btn-check" name="signal" id="sig-down" autocomplete="off">
                    <label class="btn btn-outline-danger" for="sig-down">Down</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Uptime (hours)</label>
                  <input type="number" class="form-control" placeholder="e.g., 23.5">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Downtime (hours)</label>
                  <input type="number" class="form-control" placeholder="e.g., 0.5">
                </div>

                <div class="col-12">
                  <label class="form-label">Incidents / Issues</label>
                  <textarea class="form-control" rows="3" placeholder="Brief description…"></textarea>
                </div>

                <div class="col-12">
                  <label class="form-label">Immediate Action Taken</label>
                  <textarea class="form-control" rows="2" placeholder="What you did…"></textarea>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Technician Name</label>
                  <input type="text" class="form-control" value="{{ optional(Auth::user())->name }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Signature (type name)</label>
                  <input type="text" class="form-control" placeholder="Your signature">
                </div>
              </div>

              <hr class="my-3">
              <div class="d-flex flex-wrap gap-2">
                <button id="btnSubmitDaily" class="btn btn-primary">Submit (UI only)</button>
                <button id="btnSaveDraft" class="btn btn-outline-secondary">Save Draft (UI only)</button>
                <button id="btnClear" class="btn btn-outline-danger">Clear</button>
              </div>
            </form>

            {{-- A tiny visual hint --}}
            <div class="mt-3 small">
              This page is interface-only. Hook buttons to your POST routes later.
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

{{-- Optional toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
  <div id="techToast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastText">Saved.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

{{-- Page JS (no backend yet) --}}
<script>
(function(){
  function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}
  ready(function(){
    const tbody = document.getElementById('sitesTableBody');
    const btnAllOn  = document.getElementById('btnAllOn');
    const btnAllOff = document.getElementById('btnAllOff');
    const btnSave   = document.getElementById('btnSimulateSave');
    const toastEl   = document.getElementById('techToast');
    const toastText = document.getElementById('toastText');

    // Toggle single row
    function updateRow(row, isOn){
      const badge = row.querySelector('.status-badge');
      const toggle= row.querySelector('.site-toggle');
      toggle.checked = isOn;
      if(isOn){
        badge.classList.remove('bg-danger'); badge.classList.add('bg-success');
        badge.textContent = 'On-Air';
      } else {
        badge.classList.remove('bg-success'); badge.classList.add('bg-danger');
        badge.textContent = 'Off-Air';
      }
    }

    // Bind switches
    tbody.querySelectorAll('tr').forEach(tr=>{
      const sw = tr.querySelector('.site-toggle');
      sw.addEventListener('change', ()=> updateRow(tr, sw.checked));
    });

    // Bulk
    if(btnAllOn)  btnAllOn.addEventListener('click', ()=> tbody.querySelectorAll('tr').forEach(tr=> updateRow(tr,true)));
    if(btnAllOff) btnAllOff.addEventListener('click', ()=> tbody.querySelectorAll('tr').forEach(tr=> updateRow(tr,false)));

    // Mock save status
    if(btnSave) btnSave.addEventListener('click', ()=>{
      const snapshot = Array.from(tbody.querySelectorAll('tr')).map(tr=>{
        const id = tr.getAttribute('data-site-id');
        const on = tr.querySelector('.site-toggle').checked;
        return { id, status: on ? 'on' : 'off' };
      });
      console.log('MOCK SAVE — site statuses:', snapshot);
      if (window.bootstrap && toastEl){
        toastText.textContent = 'Site statuses saved (mock).';
        new bootstrap.Toast(toastEl).show();
      } else {
        alert('Site statuses saved (mock). Check console for payload.');
      }
    });

    // Daily form buttons (mock)
    const btnSubmit = document.getElementById('btnSubmitDaily');
    const btnDraft  = document.getElementById('btnSaveDraft');
    const btnClear  = document.getElementById('btnClear');
    const form      = document.getElementById('dailyTechForm');

    function toast(msg, ok=true){
      if (window.bootstrap && toastEl){
        toastEl.classList.remove('bg-success','bg-danger');
        toastEl.classList.add(ok?'bg-success':'bg-danger');
        toastText.textContent = msg;
        new bootstrap.Toast(toastEl).show();
      } else {
        alert(msg);
      }
    }

    if(btnSubmit) btnSubmit.addEventListener('click', ()=>{
      // Collect minimal payload preview
      const siteSel = document.getElementById('formSite');
      const siteTxt = siteSel.options[siteSel.selectedIndex].text;
      console.log('MOCK SUBMIT — Daily Report for:', siteTxt);
      toast('Daily report submitted (mock).');
    });

    if(btnDraft) btnDraft.addEventListener('click', ()=>{
      toast('Draft saved (mock).');
    });

    if(btnClear) btnClear.addEventListener('click', ()=>{
      form.reset();
      toast('Form cleared.', true);
    });
  });
})();
</script>

<style>
/* Optional: subtle gradient for success components (your requested green) */
.progress-bar.bg-grd-success, .btn-outline-success:hover, .badge.bg-success {
  background-image: linear-gradient(310deg, #17ad37, #98ec2d) !important;
}
</style>
