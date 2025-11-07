(function(){
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      if (!calendarEl || !window.FullCalendar) return;

      var stepperEl = document.querySelector('.bs-stepper');
      var stepper = new Stepper(stepperEl);

      var nextButtons = document.querySelectorAll('.stepper-next');
      nextButtons.forEach(function(btn) {
          btn.addEventListener('click', function() {
              stepper.next();
          });
      });

      var prevButtons = document.querySelectorAll('.stepper-previous');
      prevButtons.forEach(function(btn) {
          btn.addEventListener('click', function() {
              stepper.previous();
          });
      });

      var eventsUrl = calendarEl.getAttribute('data-events-url');
      var eventJsonBase = calendarEl.getAttribute('data-event-json-url-base');
      var storeRoute = calendarEl.getAttribute('data-store-route');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: eventsUrl,
        eventDidMount: function(info) {
          var audience = info.event.extendedProps.audience || '';
          var type     = info.event.extendedProps.meeting_type || '';
          var desc = (type ? ('Type: ' + type + '\n') : '') + (audience ? ('Audience: ' + audience) : '');
          if (desc) info.el.setAttribute('title', desc);
        },
        eventClick: function(info) {
          info.jsEvent.preventDefault();
          var eventId = info.event.id;
          if (!eventId) return;

          stepper.to(1);

          fetch(eventJsonBase + '/' + encodeURIComponent(eventId) + '/json')
            .then(function(r){ return r.json(); })
            .then(function(payload){
              var modalEl = document.getElementById('meetingEventModal');
              var form    = document.getElementById('meetingEventForm');
              if (!form) return;

              form.action = eventJsonBase + '/' + payload.id;
              document.getElementById('meetingEventMethodField').value = 'PUT';
              document.getElementById('event_id_field').value = payload.id;

              var mlSelect = document.getElementById('event_meeting_link_select');
              if (mlSelect) mlSelect.value = payload.meeting_link_id || '';

              document.getElementById('event_title').value        = payload.title || '';
              document.getElementById('event_date').value         = payload.meeting_date || '';
              document.getElementById('event_start_time').value   = payload.start_time ? payload.start_time.substring(0,5) : '';
              document.getElementById('event_end_time').value     = payload.end_time ? payload.end_time.substring(0,5) : '';
              document.getElementById('event_type').value         = payload.meeting_type || 'meeting';
              document.getElementById('event_frequency').value    = payload.frequency || 'once';
              document.getElementById('event_expires_at').value   = payload.expires_at ? payload.expires_at.substring(0,10) : '';
              document.getElementById('event_video_url').value    = payload.video_url || '';
              document.getElementById('event_notes').value        = payload.notes || '';

              try {
                var meta = JSON.parse(payload.frequency_meta || 'null');
                document.getElementById('event_frequency_note').value = (meta && meta.note) ? meta.note : '';
              } catch(e) {
                document.getElementById('event_frequency_note').value = '';
              }

              var safeJson = function(v){ try { return JSON.parse(v); } catch(e) { return []; } };
              var camp = safeJson(payload.campus_group || '[]');
              var mins = safeJson(payload.ministry_group || '[]');
              var dept = safeJson(payload.dept_group || '[]');

              var applyAudienceSelection = function(selector, values){
                var set = new Set((values || []).map(String));
                document.querySelectorAll(selector + ' input[type="checkbox"]').forEach(function(cb){
                  cb.checked = set.has(String(cb.value));
                });
              };
              applyAudienceSelection('#eventCampusBox', camp);
              applyAudienceSelection('#eventMinistryBox', mins);
              applyAudienceSelection('#eventDeptBox', dept);

              window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            })
            .catch(function(){ alert('Unable to load event data'); });
        }
      });

      calendar.render();

      var addBtn = document.getElementById('addSessionBtn');
      if (addBtn) {
        addBtn.addEventListener('click', function() {
          var modalEl = document.getElementById('meetingEventModal');
          var form    = document.getElementById('meetingEventForm');
          if (!form || !modalEl) return;

          stepper.to(1);

          form.reset();
          document.getElementById('meetingEventMethodField').value = '';
          document.getElementById('event_id_field').value = '';

          form.setAttribute('action', storeRoute);
          form.setAttribute('method', 'POST');

          var spoof = form.querySelector('input[name="_method"]');
          if (spoof) spoof.value = '';

          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
      }

      document.addEventListener('click', function(e){
        var btn = e.target.closest('.audience-action');
        if (!btn) return;

        var action = btn.dataset.action;
        var target = btn.dataset.target;
        var box = document.querySelector(target);
        if (!box) return;

        var checks = box.querySelectorAll('input[type="checkbox"]');
        if (action === 'all') {
          checks.forEach(function(cb){ cb.checked = true; });
        } else if (action === 'none') {
          checks.forEach(function(cb){ cb.checked = false; });
        } else if (action === 'invert') {
          checks.forEach(function(cb){ cb.checked = !cb.checked; });
        }
      });
    });
  })();
