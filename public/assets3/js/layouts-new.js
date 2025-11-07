// Shared layout boot for layouts/new_layout.blade.php (CSP safe)
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    try {
      // PerfectScrollbar
      var listEl = document.querySelector('.user-list');
      if (listEl && window.PerfectScrollbar) new PerfectScrollbar(listEl);

      // Feather icons
      if (typeof feather !== 'undefined' && feather.replace) feather.replace();

      // Optional Stepper
      var stepperEl = document.querySelector('.bs-stepper');
      if (stepperEl && window.Stepper) new Stepper(stepperEl);

      // Flash notifications
      var flash = document.getElementById('flash-data');
      if (flash && window.Lobibox && typeof Lobibox.notify === 'function') {
        var success = flash.getAttribute('data-success');
        var error   = flash.getAttribute('data-error');
        if (success) Lobibox.notify('success', { pauseDelayOnHover:true, continueDelayOnInactiveTab:false, position:'top right', icon:'bi bi-check2-circle', msg: success });
        if (error)   Lobibox.notify('error',   { pauseDelayOnHover:true, continueDelayOnInactiveTab:false, position:'top right', icon:'bi bi-x-circle',     msg: error   });
      }

      // jQuery ajaxError(403) -> denied modal
      if (window.jQuery) {
        jQuery(document).ajaxError(function(event, xhr){
          if (xhr && xhr.status === 403) {
            var el = document.getElementById('deniedModal');
            if (el && window.bootstrap) new bootstrap.Modal(el).show();
          }
        });
      }

      // Flatpickr helpers (if loaded)
      if (window.flatpickr) {
        var q = function(s){ return document.querySelectorAll(s) };
        q('.datepicker').forEach(function(el){ flatpickr(el); });
        q('.time-picker').forEach(function(el){ flatpickr(el, { enableTime:true, noCalendar:true, dateFormat:'G:i K', time_24hr:false }); });
        q('.date-time').forEach(function(el){ flatpickr(el, { enableTime:true, dateFormat:'Y-m-d H:i' }); });
        q('.date-format').forEach(function(el){ flatpickr(el, { altInput:true, altFormat:'F j, Y', dateFormat:'Y-m-d' }); });
        q('.date-range').forEach(function(el){ flatpickr(el, { mode:'range', altInput:true, altFormat:'F j, Y', dateFormat:'Y-m-d' }); });
        q('.date-inline').forEach(function(el){ flatpickr(el, { inline:true, altInput:true, altFormat:'F j, Y', dateFormat:'Y-m-d' }); });
      }

      // Session ping (read from meta) - background only, no navigation
      var pingMeta = document.querySelector('meta[name="session-ping-url"]');
      var loginMeta= document.querySelector('meta[name="login-url"]');
      var pingUrl   = pingMeta ? pingMeta.getAttribute('content') : null;
      var loginUrl  = loginMeta? loginMeta.getAttribute('content'): null;
      if (pingUrl) {
        setInterval(function(){
          try {
            fetch(pingUrl, { 
              credentials:'include',
              method: 'GET',
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(r){ 
              if (r.status === 401 && loginUrl && !window.location.pathname.includes('/login')) {
                window.location.href = loginUrl; 
              }
            }).catch(function(err){
              console.debug('Session ping failed:', err);
            });
          } catch(e) {
            console.debug('Session ping error:', e);
          }
        }, 60000);
      }

      // Show denied modal on load when flagged
      var deniedFlag = document.getElementById('denied-flag');
      if (deniedFlag && deniedFlag.getAttribute('data-show') === '1') {
        var dm = document.getElementById('deniedModal');
        if (dm && window.bootstrap) new bootstrap.Modal(dm).show();
      }
    } catch(e) {
      if (window.console && console.warn) console.warn('layouts boot error', e);
    }
  });
})();

