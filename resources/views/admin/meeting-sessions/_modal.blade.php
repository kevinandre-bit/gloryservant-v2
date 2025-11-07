<div class="modal fade" id="meetingEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="post" id="meetingEventForm" action="{{ route('meeting-events.store') }}">
      @csrf
      <input type="hidden" name="_method" id="meetingEventMethodField" value="">
      <input type="hidden" name="id" id="event_id_field" value="">

      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center gap-2" id="meetingEventModalTitle">
          <i class="material-icons-outlined">event</i>
          Schedule Session
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="bs-stepper">
          <div class="bs-stepper-header" role="tablist">
            <div class="step" data-target="#step-details">
              <button type="button" class="step-trigger" role="tab" id="step-details-trigger" aria-controls="step-details">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Session Details</span>
              </button>
            </div>
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#step-recurrence">
              <button type="button" class="step-trigger" role="tab" id="step-recurrence-trigger" aria-controls="step-recurrence">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Recurrence</span>
              </button>
            </div>
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#step-audience">
              <button type="button" class="step-trigger" role="tab" id="step-audience-trigger" aria-controls="step-audience">
                <span class="bs-stepper-circle">3</span>
                <span class="bs-stepper-label">Audience & Notes</span>
              </button>
            </div>
          </div>
          <div class="bs-stepper-content">
            <div id="step-details" class="content" role="tabpanel" aria-labelledby="step-details-trigger">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Meeting link</label>
                  <select id="event_meeting_link_select" name="meeting_link_id" class="form-select" required>
                    <option value="">Select meeting link</option>
                    @foreach($meetingLinks as $ml)
                      <option value="{{ $ml->id }}">{{ $ml->title }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Session title</label>
                  <input type="text" name="title" id="event_title" class="form-control" placeholder="Defaults to meeting link title">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Session type</label>
                  <select name="meeting_type" id="event_type" class="form-select" required>
                    <option value="meeting">Meeting</option>
                    <option value="training">Training</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Meeting date</label>
                  <input type="date" name="meeting_date" id="event_date" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Start time</label>
                  <input type="time" name="start_time" id="event_start_time" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">End time</label>
                  <input type="time" name="end_time" id="event_end_time" class="form-control">
                </div>
              </div>
              <div class="mt-3 text-end">
                <button type="button" class="btn btn-primary stepper-next">Next</button>
              </div>
            </div>
            <div id="step-recurrence" class="content" role="tabpanel" aria-labelledby="step-recurrence-trigger">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Frequency</label>
                  <select name="frequency" id="event_frequency" class="form-select">
                    <option value="once" selected>Once</option>
                    <option value="weekly">Weekly</option>
                    <option value="biweekly">Biweekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="custom">Custom</option>
                  </select>
                </div>
                <div class="col-md-8">
                  <label class="form-label">Frequency details (optional)</label>
                  <input type="text" name="frequency_meta[note]" id="event_frequency_note" class="form-control" placeholder="e.g. every second Tuesday">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Session expires</label>
                  <input type="date" name="expires_at" id="event_expires_at" class="form-control">
                </div>
              </div>
              <div class="mt-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary stepper-previous">Previous</button>
                <button type="button" class="btn btn-primary stepper-next">Next</button>
              </div>
            </div>
            <div id="step-audience" class="content" role="tabpanel" aria-labelledby="step-audience-trigger">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Video meeting link</label>
                        <input type="url" name="video_url" id="event_video_url" class="form-control" placeholder="https://">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="event_notes" class="form-control" rows="2" placeholder="Optional details or agenda"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="accordion" id="eventAudienceAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventCampusCollapse">
                                    Campus Filters
                                    </button>
                                </h2>
                                <div id="eventCampusCollapse" class="accordion-collapse collapse" data-bs-parent="#eventAudienceAccordion">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-end gap-2 mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventCampusBox" data-action="all">Select all</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventCampusBox" data-action="none">Clear</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventCampusBox" data-action="invert">Invert</button>
                                        </div>
                                        <div class="row g-2" id="eventCampusBox">
                                            @foreach ($campuses as $campus)
                                                @php
                                                $label = data_get($campus, 'label')
                                                        ?? data_get($campus, 'name')
                                                        ?? data_get($campus, 'campus_name')
                                                        ?? data_get($campus, 'campus')
                                                        ?? $campus;
                                                $value   = data_get($campus, 'id', $label);
                                                $inputId = 'event-campus-'.Illuminate\Support\Str::slug((string)$label);
                                                @endphp
                                                <div class="col-sm-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="campus_group[]" id="{{ $inputId }}" value="{{ $value }}" data-label="{{ $label }}">
                                                    <label class="form-check-label" for="{{ $inputId }}">{{ $label }}</label>
                                                </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventMinistryCollapse">
                                    Ministry Filters
                                    </button>
                                </h2>
                                <div id="eventMinistryCollapse" class="accordion-collapse collapse" data-bs-parent="#eventAudienceAccordion">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-end gap-2 mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventMinistryBox" data-action="all">Select all</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventMinistryBox" data-action="none">Clear</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventMinistryBox" data-action="invert">Invert</button>
                                        </div>
                                        <div class="row g-2" id="eventMinistryBox">
                                            @foreach ($ministries as $ministry)
                                                @php
                                                $label = data_get($ministry, 'name')
                                                        ?? data_get($ministry, 'title')
                                                        ?? data_get($ministry, 'ministry_name')
                                                        ?? data_get($ministry, 'label')
                                                        ?? $ministry;
                                                $value   = data_get($ministry, 'id', $label);
                                                $inputId = 'event-ministry-'.Illuminate\Support\Str::slug((string)$label);
                                                @endphp
                                                <div class="col-sm-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="ministry_group[]" id="{{ $inputId }}" value="{{ $value }}" data-label="{{ $label }}">
                                                    <label class="form-check-label" for="{{ $inputId }}">{{ $label }}</label>
                                                </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventDeptCollapse">
                                    Department Filters
                                    </button>
                                </h2>
                                <div id="eventDeptCollapse" class="accordion-collapse collapse" data-bs-parent="#eventAudienceAccordion">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-end gap-2 mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventDeptBox" data-action="all">Select all</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventDeptBox" data-action="none">Clear</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary audience-action" data-target="#eventDeptBox" data-action="invert">Invert</button>
                                        </div>
                                        <div class="row g-2" id="eventDeptBox">
                                            @foreach ($departments as $dept)
                                                @php
                                                $label = data_get($dept, 'label')
                                                        ?? data_get($dept, 'name')
                                                        ?? data_get($dept, 'department_name')
                                                        ?? data_get($dept, 'title')
                                                        ?? $dept;
                                                $value   = data_get($dept, 'id', $label);
                                                $inputId = 'event-dept-'.Illuminate\Support\Str::slug((string)$label);
                                                @endphp
                                                <div class="col-sm-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="dept_group[]" id="{{ $inputId }}" value="{{ $value }}" data-label="{{ $label }}">
                                                    <label class="form-check-label" for="{{ $inputId }}">{{ $label }}</label>
                                                </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                -</div>
                            </div>
                        </div>
                          @push('scripts')
                          <script nonce="{{ request()->attributes->get('cspNonce') }}">
                          document.addEventListener('DOMContentLoaded', function() {
                            var modalEl = document.getElementById('meetingEventModal');
                            if (modalEl) {
                              modalEl.addEventListener('shown.bs.modal', function() {
                                var modalStepperEl = modalEl.querySelector('.bs-stepper');
                                if (modalStepperEl && window.Stepper) {
                                  window.meetingSessionStepper = new Stepper(modalStepperEl);
                                }
                              });
                            }
                          });
                          </script>
                          @endpush
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-secondary stepper-previous">Previous</button>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" id="btnDeleteEvent" class="btn btn-outline-danger d-none" onclick="return confirm('Are you sure you want to delete this session?');">Delete</a>
                        <button type="submit" class="btn btn-primary">Save session</button>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>