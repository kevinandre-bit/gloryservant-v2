// Auth login page behavior: password toggle and basic inactivity tracker
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var wrapper = document.getElementById('show_hide_password');
    if (wrapper) {
      var input = wrapper.querySelector('input[type="password"], input#password');
      var btn   = wrapper.querySelector('.password-toggle');
      var icon  = wrapper.querySelector('i');
      if (btn && input) {
        btn.addEventListener('click', function(){
          var isText = input.getAttribute('type') === 'text';
          input.setAttribute('type', isText ? 'password' : 'text');
          if (icon) {
            icon.classList.toggle('bi-eye-fill', !isText);
            icon.classList.toggle('bi-eye-slash-fill', isText);
          }
          btn.setAttribute('aria-label', isText ? 'Show password' : 'Hide password');
        });
      }
    }

    // Optional: lightweight inactivity tracker (no sensitive data)
    try {
      var key = 'session_id';
      var sid = localStorage.getItem(key);
      if (!sid) { sid = String(Date.now()); localStorage.setItem(key, sid); }

      var csrfEl = document.querySelector('meta[name="csrf-token"]');
      var csrf   = csrfEl ? csrfEl.getAttribute('content') : '';

      var ended = false, timer;
      function track(action, inactive){
        var payload = { session_id: sid, page: location.pathname, action: action, is_inactive: !!inactive };
        fetch('/track-action', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
          body: JSON.stringify(payload),
          credentials: 'same-origin'
        }).catch(function(){ /* ignore */ });
      }
      function endSession(){ if (!ended){ ended = true; track('Session Ended', true); } }
      function reset(){ if (ended) return; clearTimeout(timer); timer = setTimeout(endSession, 300000); }
      ['mousemove','keydown','click','touchstart'].forEach(function(evt){ window.addEventListener(evt, reset, { passive: true }); });
      track('Page View');
      reset();
    } catch(e) { /* ignore tracking errors */ }
  });
})();

