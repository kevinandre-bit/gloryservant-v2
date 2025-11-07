{{-- Technician Daily Report (UI-only) --}}
@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Technician Daily Report</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Daily (UI-only)</li>
        </ol>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-8">
        <div class="card rounded-4">
          <div class="card-body p-4">

            <form id="dailyReportForm" method="post" action="{{ route('tech.reports.daily.preview') }}">
              @csrf

              {{-- Top info --}}
              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label">Date</label>
                  <input type="date" name="date" class="form-control" value="{{ $defaults['date'] ?? now()->toDateString() }}" required>
                </div>
                <div class="col-6">
                  <label class="form-label">Site</label>
                  <select name="site_id" class="form-select" required>
                    <option value="">Select site…</option>
                    @foreach($sites as $s)
                      @php $isAssigned = in_array($s['id'], $assignedSiteIds ?? []); @endphp
                      <option value="{{ $s['id'] }}">
                        {{ $s['name'] }} {{ $isAssigned ? '• (Assigned)' : '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <hr class="my-4">

              {{-- Accordion --}}
              <div class="accordion" id="reportAccordion">

                {{-- A) Signal & Broadcast --}}
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingA">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA" aria-expanded="true" aria-controls="collapseA">
                      Signal & Broadcast Monitoring
                    </button>
                  </h2>
                  <div id="collapseA" class="accordion-collapse collapse show" aria-labelledby="headingA" data-bs-parent="#reportAccordion">
                    <div class="accordion-body">
                      <div class="row g-3">
                        <div class="col-12">
                          <label class="form-label d-block">Signal status</label>
                          @php $sig = $defaults['signal_status'] ?? 'Good'; @endphp
                          <div class="d-flex gap-3 flex-wrap">
                            @foreach(['Excellent','Good','Weak','Down'] as $opt)
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="signal_status" value="{{ $opt }}" {{ $sig===$opt ? 'checked' : '' }} required>
                                <span class="form-check-label">{{ $opt }}</span>
                              </label>
                            @endforeach
                          </div>
                        </div>
                        <div class="col-6">
                          <label class="form-label">Uptime (hours)</label>
                          <input type="number" step="0.1" min="0" max="24" name="uptime_hours" class="form-control" value="{{ $defaults['uptime_hours'] ?? '' }}">
                        </div>
                        <div class="col-6">
                          <label class="form-label">Downtime (hours)</label>
                          <input type="number" step="0.1" min="0" max="24" name="downtime_hours" class="form-control" value="{{ $defaults['downtime_hours'] ?? '' }}">
                        </div>
                        <div class="col-12">
                          <label class="form-label">Notes / Observations</label>
                          <textarea name="signal_notes" class="form-control" rows="2" placeholder="Any interference, audio issues, dropouts…"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- B) Equipment Health --}}
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingB">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB" aria-expanded="false" aria-controls="collapseB">
                      Equipment Health
                    </button>
                  </h2>
                  <div id="collapseB" class="accordion-collapse collapse" aria-labelledby="headingB" data-bs-parent="#reportAccordion">
                    <div class="accordion-body">
                      <div class="row g-3">
                        <div class="col-12 col-md-4">
                          <label class="form-label">Transmitter</label>
                          <select name="transmitter_status" id="transmitter_status" class="form-select">
                            @php $tx = $defaults['transmitter'] ?? 'OK'; @endphp
                            <option {{ $tx==='OK' ? 'selected':'' }}>OK</option>
                            <option {{ $tx==='Needs Service' ? 'selected':'' }}>Needs Service</option>
                          </select>
                        </div>
                        <div class="col-12 col-md-4">
                          <label class="form-label">Antenna</label>
                          <select name="antenna_status" id="antenna_status" class="form-select">
                            @php $ant = $defaults['antenna'] ?? 'OK'; @endphp
                            <option {{ $ant==='OK' ? 'selected':'' }}>OK</option>
                            <option {{ $ant==='Needs Service' ? 'selected':'' }}>Needs Service</option>
                          </select>
                        </div>
                        <div class="col-12 col-md-4">
                          <label class="form-label">UPS / Power</label>
                          <select name="ups_status" id="ups_status" class="form-select">
                            @php $ups = $defaults['ups'] ?? 'OK'; @endphp
                            <option {{ $ups==='OK' ? 'selected':'' }}>OK</option>
                            <option {{ $ups==='Needs Service' ? 'selected':'' }}>Needs Service</option>
                          </select>
                        </div>

                        <div class="col-12 d-none" id="faults_block">
                          <label class="form-label">Observed faults</label>
                          <textarea name="faults_text" class="form-control" rows="2" placeholder="Describe the fault(s) found…"></textarea>
                        </div>

                        <div class="col-12">
                          <label class="form-label">Preventive maintenance performed</label>
                          <textarea name="preventive_actions" class="form-control" rows="2" placeholder="Dust cleaning, connector tighten, meter checks…"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- C) Routine Maintenance (only if scheduled today) --}}
                @if($scheduledToday)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingC">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseC" aria-expanded="false" aria-controls="collapseC">
                      Routine Maintenance (Scheduled Today)
                    </button>
                  </h2>
                  <div id="collapseC" class="accordion-collapse collapse" aria-labelledby="headingC" data-bs-parent="#reportAccordion">
                    <div class="accordion-body">
                      <div class="row g-3">
                        <div class="col-12">
                          <label class="form-label d-block">Maintenance type</label>
                          <div class="d-flex gap-3 flex-wrap">
                            @foreach(['Monthly','Quarterly','Yearly'] as $m)
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="maintenance_type" value="{{ $m }}">
                                <span class="form-check-label">{{ $m }}</span>
                              </label>
                            @endforeach
                          </div>
                        </div>

                        <div class="col-12">
                          <label class="form-label d-block">Tasks completed</label>
                          <div class="row">
                            <div class="col-6 col-md-3">
                              <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="transmitter_check" value="1">
                                <span class="form-check-label">Transmitter check</span>
                              </label>
                            </div>
                            <div class="col-6 col-md-3">
                              <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="antenna_inspection" value="1">
                                <span class="form-check-label">Antenna inspection</span>
                              </label>
                            </div>
                            <div class="col-6 col-md-3">
                              <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="cabling_check" value="1">
                                <span class="form-check-label">Cabling</span>
                              </label>
                            </div>
                            <div class="col-6 col-md-3">
                              <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="power_check" value="1">
                                <span class="form-check-label">Power system</span>
                              </label>
                            </div>
                          </div>
                        </div>

                        <div class="col-12">
                          <label class="form-label">Replacements made</label>
                          <input type="text" name="replacements_made" class="form-control" placeholder="Parts replaced…">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif

                {{-- D) Incident (optional) --}}
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingD">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseD" aria-expanded="false" aria-controls="collapseD">
                      Incident (optional)
                    </button>
                  </h2>
                  <div id="collapseD" class="accordion-collapse collapse" aria-labelledby="headingD" data-bs-parent="#reportAccordion">
                    <div class="accordion-body">
                      <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleIncident">
                        <label class="form-check-label" for="toggleIncident">Add Incident</label>
                      </div>

                      <div id="incidentBlock" class="row g-3 d-none">
                        <div class="col-12 col-md-6">
                          <label class="form-label">Occurred at</label>
                          <input type="datetime-local" name="incident_occurred_at" class="form-control">
                        </div>
                        <div class="col-12">
                          <label class="form-label">Issue description</label>
                          <textarea name="incident_description" class="form-control" rows="2" placeholder="Describe the issue…"></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                          <label class="form-label">Temporary action taken</label>
                          <input type="text" name="incident_temp_action" class="form-control" placeholder="Bypass, restart, swap…">
                        </div>
                        <div class="col-12 col-md-6">
                          <label class="form-label">Parts / support required</label>
                          <input type="text" name="incident_parts" class="form-control" placeholder="Cables, PSU, engineer…">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>{{-- /accordion --}}

              <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" id="btnDraft" class="btn btn-outline-secondary">Save as Draft (UI)</button>
              </div>

            </form>

          </div>
        </div>
      </div>

      {{-- Helper / Preview area --}}
      <div class="col-12 col-xl-4">
        <div class="card rounded-4">
          <div class="card-body p-4">
            <h6 class="mb-3">Tips</h6>
            <ul class="mb-0">
              <li>Date and Site are required.</li>
              <li>“Needs Service” will ask for faults description.</li>
              <li>Toggle “Add Incident” to include an incident inline.</li>
              <li>This page uses fake data—no database yet.</li>
            </ul>
            <hr>
            <div class="d-grid gap-2">
              <a href="{{ route('dashboard.index') }}" class="btn btn-light">Back to Dashboard</a>
            </div>
          </div>
        </div>
      </div>

    </div>{{-- /row --}}
  </div>
</main>

{{-- Optional: ApexCharts CDN (only if you want to add tiny success charts later) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
(function(){
  function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);}

  ready(function(){
    // Show fault textarea if any equipment = "Needs Service"
    const selects = ['transmitter_status','antenna_status','ups_status'].map(id=>document.getElementById(id)).filter(Boolean);
    const faults  = document.getElementById('faults_block');

    function checkFaults(){
      const needs = selects.some(s => s && s.value === 'Needs Service');
      if(faults) faults.classList.toggle('d-none', !needs);
    }
    selects.forEach(s => s && s.addEventListener('change', checkFaults));
    checkFaults();

    // Toggle Incident block
    const incToggle = document.getElementById('toggleIncident');
    const incBlock  = document.getElementById('incidentBlock');
    if(incToggle && incBlock){
      incToggle.addEventListener('change', () => {
        incBlock.classList.toggle('d-none', !incToggle.checked);
      });
    }

    // Save as draft (UI only)
    const btnDraft = document.getElementById('btnDraft');
    if(btnDraft){
      btnDraft.addEventListener('click', function(){
        alert('Draft saved locally (UI only). In production, this would store to DB.');
      });
    }

    // Optional: inline client-side validation (minimal)
    const form = document.getElementById('dailyReportForm');
    form.addEventListener('submit', function(e){
      // If incident toggled, ensure description provided
      if(incToggle && incToggle.checked){
        const desc = form.querySelector('[name="incident_description"]');
        if(!desc.value.trim()){
          e.preventDefault();
          alert('Please fill the incident description.');
        }
      }
    });
  });
})();
</script>
