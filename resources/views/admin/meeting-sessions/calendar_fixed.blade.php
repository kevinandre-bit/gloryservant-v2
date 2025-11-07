@extends('layouts.admin')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <style nonce="{{ $cspNonce }}">
    .fc-event {
      border-radius: 6px !important;
      font-size: 12px !important;
      padding: 2px 6px !important;
      margin: 1px 0 !important;
    }
    .fc-event-title {
      font-weight: 500 !important;
    }
    .fc-daygrid-event {
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
    }
    .fc-toolbar-title {
      font-size: 1.5rem !important;
      font-weight: 600 !important;
    }
    .fc-button {
      border-radius: 6px !important;
      font-size: 14px !important;
    }
    .fc-today {
      background-color: rgba(13, 110, 253, 0.05) !important;
    }
    #calendar {
      min-height: 600px;
    }
  </style>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <!-- Alerts -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h4 class="mb-1">Meeting Sessions Calendar</h4>
        <p class="text-muted mb-0">Click dates to schedule sessions, click events to edit</p>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ url('meeting-links') }}">
          <i class="material-icons-outlined me-1">arrow_back</i>
          Back to Links
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#meetingEventModal">
          <i class="material-icons-outlined me-1">add</i>
          Add Session
        </button>
      </div>
    </div>

    <!-- Calendar -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div id="calendar"></div>
      </div>
    </div>

    <!-- Simplified Modal -->
    @include('admin.meeting-sessions._modal_simplified')
  </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const modalEl = document.getElementById('meetingEventModal');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('meetingEventForm');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: '{{ url("meeting-sessions/events") }}',
        editable: true,
        selectable: true,
        eventDisplay: 'block',
        dayMaxEvents: 3,
        
        // Click existing event to edit
        eventClick: function(info) {
            const eventId = info.event.id;
            
            // Set up modal for editing
            form.action = `{{ url('meeting-events') }}/${eventId}`;
            document.getElementById('meetingEventMethodField').value = 'PUT';
            document.getElementById('event_id_field').value = eventId;
            document.getElementById('meetingEventModalTitle').innerHTML = 
                '<i class="material-icons-outlined">edit</i> Edit Session';
            document.getElementById('btnDeleteEvent').classList.remove('d-none');
            document.getElementById('btnDeleteEvent').href = `{{ url('meeting-events') }}/${eventId}`;
            
            // Fetch and populate event data
            fetch(`{{ url('meeting-events') }}/${eventId}/json`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('event_meeting_link_select').value = data.meeting_link_id || '';
                    document.getElementById('event_title').value = data.title || '';
                    document.getElementById('event_type').value = data.meeting_type || 'meeting';
                    document.getElementById('event_date').value = data.meeting_date || '';
                    document.getElementById('event_start_time').value = data.start_time ? data.start_time.substring(0, 5) : '';
                    document.getElementById('event_end_time').value = data.end_time ? data.end_time.substring(0, 5) : '';
                    document.getElementById('event_video_url').value = data.video_url || '';
                    document.getElementById('event_notes').value = data.notes || '';
                    
                    // Handle audience selection
                    const hasCustomAudience = data.campus_group?.length || data.ministry_group?.length || data.dept_group?.length;
                    if (hasCustomAudience) {
                        document.getElementById('audience_custom').checked = true;
                        document.getElementById('custom_audience_section').classList.remove('d-none');
                        
                        // Set selected values
                        if (data.campus_group) {
                            const campusSelect = document.querySelector('select[name="campus_group[]"]');
                            Array.from(campusSelect.options).forEach(option => {
                                option.selected = data.campus_group.includes(option.value);
                            });
                        }
                        if (data.ministry_group) {
                            const ministrySelect = document.querySelector('select[name="ministry_group[]"]');
                            Array.from(ministrySelect.options).forEach(option => {
                                option.selected = data.ministry_group.includes(option.value);
                            });
                        }
                        if (data.dept_group) {
                            const deptSelect = document.querySelector('select[name="dept_group[]"]');
                            Array.from(deptSelect.options).forEach(option => {
                                option.selected = data.dept_group.includes(option.value);
                            });
                        }
                    } else {
                        document.getElementById('audience_all').checked = true;
                        document.getElementById('custom_audience_section').classList.add('d-none');
                    }
                    
                    modal.show();
                })
                .catch(err => {
                    console.error('Failed to fetch event:', err);
                    alert('Failed to load event data');
                });
        },
        
        // Click empty date to create new session
        select: function(info) {
            form.reset();
            form.action = '{{ route("meeting-events.store") }}';
            document.getElementById('meetingEventMethodField').value = 'POST';
            document.getElementById('event_id_field').value = '';
            document.getElementById('meetingEventModalTitle').innerHTML = 
                '<i class="material-icons-outlined">event</i> Schedule Session';
            document.getElementById('btnDeleteEvent').classList.add('d-none');
            document.getElementById('event_date').value = info.startStr;
            document.getElementById('audience_all').checked = true;
            document.getElementById('custom_audience_section').classList.add('d-none');
            
            modal.show();
        },
        
        // Style events by type
        eventDidMount: function(info) {
            const type = info.event.extendedProps.meeting_type;
            if (type === 'training') {
                info.el.style.backgroundColor = '#28a745';
                info.el.style.borderColor = '#28a745';
            } else {
                info.el.style.backgroundColor = '#007bff';
                info.el.style.borderColor = '#007bff';
            }
        }
    });

    calendar.render();

    // Add session button
    document.querySelector('[data-bs-target="#meetingEventModal"]').addEventListener('click', function() {
        form.reset();
        form.action = '{{ route("meeting-events.store") }}';
        document.getElementById('meetingEventMethodField').value = 'POST';
        document.getElementById('event_id_field').value = '';
        document.getElementById('meetingEventModalTitle').innerHTML = 
            '<i class="material-icons-outlined">event</i> Schedule Session';
        document.getElementById('btnDeleteEvent').classList.add('d-none');
        document.getElementById('audience_all').checked = true;
        document.getElementById('custom_audience_section').classList.add('d-none');
    });
});
</script>
@endsection