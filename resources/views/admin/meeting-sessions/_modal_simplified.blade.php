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
        <div class="row g-3">
          <!-- Basic Info -->
          <div class="col-12">
            <label class="form-label">Meeting Link</label>
            <select name="meeting_link_id" id="event_meeting_link_select" class="form-select" required>
              <option value="">Select meeting link</option>
              @foreach($meetingLinks as $ml)
                <option value="{{ $ml->id }}">{{ $ml->title }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="col-md-8">
            <label class="form-label">Session Title (optional)</label>
            <input type="text" name="title" id="event_title" class="form-control" placeholder="Leave blank to use meeting link title">
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select name="meeting_type" id="event_type" class="form-select" required>
              <option value="meeting">Meeting</option>
              <option value="training">Training</option>
            </select>
          </div>

          <!-- Date & Time -->
          <div class="col-md-4">
            <label class="form-label">Date</label>
            <input type="date" name="meeting_date" id="event_date" class="form-control" required>
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Start Time</label>
            <input type="time" name="start_time" id="event_start_time" class="form-control">
          </div>
          
          <div class="col-md-4">
            <label class="form-label">End Time</label>
            <input type="time" name="end_time" id="event_end_time" class="form-control">
          </div>

          <!-- Video Link -->
          <div class="col-12">
            <label class="form-label">Video Meeting Link (optional)</label>
            <input type="url" name="video_url" id="event_video_url" class="form-control" placeholder="https://zoom.us/j/...">
          </div>

          <!-- Quick Audience Selection -->
          <div class="col-12">
            <label class="form-label">Who can attend?</label>
            <div class="btn-group w-100" role="group">
              <input type="radio" class="btn-check" name="audience_preset" id="audience_all" value="all" checked>
              <label class="btn btn-outline-secondary" for="audience_all">Everyone</label>
              
              <input type="radio" class="btn-check" name="audience_preset" id="audience_custom" value="custom">
              <label class="btn btn-outline-secondary" for="audience_custom">Specific Groups</label>
            </div>
          </div>

          <!-- Custom Audience (Hidden by default) -->
          <div class="col-12 d-none" id="custom_audience_section">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label small">Campus</label>
                    <select name="campus_group[]" class="form-select form-select-sm" multiple>
                      @foreach($campuses as $campus)
                        @php
                          if (is_object($campus)) {
                              $campusVal = $campus->id ?? $campus->campus ?? $campus->name ?? json_encode($campus);
                              $campusLabel = $campus->campus ?? $campus->name ?? (string)($campus->id ?? $campusVal);
                          } else {
                              $campusVal = $campus;
                              $campusLabel = $campus;
                          }
                        @endphp
                        <option value="{{ $campusVal }}">{{ $campusLabel }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Ministry</label>
                      <select name="ministry_group[]" class="form-select form-select-sm" multiple>
                      @foreach($ministries as $ministry)
                        @php
                          if (is_object($ministry)) {
                              $minVal = $ministry->id ?? $ministry->name ?? $ministry->ministry ?? json_encode($ministry);
                              $minLabel = $ministry->name ?? $ministry->ministry ?? (string)($ministry->id ?? $minVal);
                          } else {
                              $minVal = $ministry;
                              $minLabel = $ministry;
                          }
                        @endphp
                        <option value="{{ $minVal }}">{{ $minLabel }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Department</label>
                      <select name="dept_group[]" class="form-select form-select-sm" multiple>
                      @foreach($departments as $dept)
                        @php
                          if (is_object($dept)) {
                              $deptVal = $dept->id ?? $dept->name ?? $dept->department ?? json_encode($dept);
                              $deptLabel = $dept->name ?? $dept->department ?? (string)($dept->id ?? $deptVal);
                          } else {
                              $deptVal = $dept;
                              $deptLabel = $dept;
                          }
                        @endphp
                        <option value="{{ $deptVal }}">{{ $deptLabel }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="col-12">
            <label class="form-label">Notes (optional)</label>
            <textarea name="notes" id="event_notes" class="form-control" rows="2" placeholder="Agenda, special instructions, etc."></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="btnDeleteEvent" class="btn btn-outline-danger d-none me-auto">Delete</a>
        <button type="submit" class="btn btn-primary">Save Session</button>
      </div>
    </form>
  </div>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
  const audienceRadios = document.querySelectorAll('input[name="audience_preset"]');
  const customSection = document.getElementById('custom_audience_section');
  
  audienceRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      if (this.value === 'custom') {
        customSection.classList.remove('d-none');
      } else {
        customSection.classList.add('d-none');
        // Clear custom selections when switching to "Everyone"
        customSection.querySelectorAll('select').forEach(select => {
          select.selectedIndex = -1;
        });
      }
    });
  });
});
</script>