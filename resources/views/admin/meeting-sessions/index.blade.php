@extends('layouts.admin')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div class="d-flex align-items-start gap-2">
        <i class="material-icons-outlined text-primary">calendar_month</i>
        <div>
          <h4 class="mb-1">Meeting Sessions</h4>
          <p class="text-muted mb-0">Schedule and manage meeting sessions</p>
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#meetingEventModal">
          <i class="material-icons-outlined fs-18">add</i>
          Add Session
        </button>
        <a class="btn btn-outline-secondary d-flex align-items-center gap-2" href="{{ url('meeting-links') }}">
          <i class="material-icons-outlined fs-18">link</i>
          Meeting Links
        </a>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div id="calendar"></div>
      </div>
    </div>

    @include('admin.meeting-sessions._modal_simplified')
  </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
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
        eventClick: function(info) {
            // Edit existing event
            const eventId = info.event.id;
            fetch(`{{ url('meeting-events') }}/${eventId}/json`)
                .then(response => response.json())
                .then(data => {
                    // Populate form with event data
                    form.reset();
                    form.action = `{{ url('meeting-events') }}/${eventId}`;
                    document.getElementById('meetingEventMethodField').value = 'PUT';
                    document.getElementById('event_id_field').value = data.id;
                    document.getElementById('event_title').value = data.title || '';
                    document.getElementById('event_date').value = data.meeting_date || '';
                    document.getElementById('event_start_time').value = data.start_time ? data.start_time.substring(0, 5) : '';
                    document.getElementById('event_end_time').value = data.end_time ? data.end_time.substring(0, 5) : '';
                    
                    modal.show();
                })
                .catch(err => console.error('Failed to fetch event:', err));
        },
        select: function(info) {
            // Create new event
            form.reset();
            form.action = '{{ route("meeting-events.store") }}';
            document.getElementById('meetingEventMethodField').value = 'POST';
            document.getElementById('event_id_field').value = '';
            document.getElementById('event_date').value = info.startStr;
            
            modal.show();
        },
        eventDidMount: function(info) {
            // Style events based on type
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

});
</script>