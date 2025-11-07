document.addEventListener('DOMContentLoaded', function () {
  var cfg = document.getElementById('personal-config');
  var csrf = document.querySelector('meta[name="csrf-token"]');
  var csrfToken = csrf ? csrf.getAttribute('content') : '';

  function route(name) { return cfg ? cfg.getAttribute('data-' + name) : ''; }
  function data(name) { return cfg ? cfg.getAttribute('data-' + name) : null; }

  // Flash notifications (Bootstrap Notify if available)
  try {
    var success = data('success');
    var info    = data('info');
    var error   = data('error');
    var errors  = [];
    try { errors = JSON.parse(data('errors') || '[]'); } catch(e) {}
    if (window.jQuery && jQuery.notify) {
      if (success) jQuery(function(){ jQuery.notify({icon:'ti-check', message: success}, {type:'success', timer:600});});
      if (error)   jQuery(function(){ jQuery.notify({icon:'ti-close', message: error}, {type:'danger',  timer:600});});
      if (Array.isArray(errors)) {
        errors.forEach(function(msg){ jQuery(function(){ jQuery.notify({icon:'ti-close', message: msg}, {type:'danger', timer:1200});});});
      }
    }
  } catch(e) {}

  // Notifications dropdown + mark all as read (if elements exist)
  try {
    var list = document.getElementById('notification-items');
    var countBadge = document.getElementById('notification-count');
    var noNotif = document.getElementById('no-notifications');
    var dropdown = document.getElementById('notificationDropdown');
    var markAll = document.getElementById('markAllAsReadBtn');

    function loadNotifications() {
      var url = route('fetch');
      if (!url || !list) return;
      fetch(url, {credentials:'same-origin'})
        .then(function(res){ return res.ok ? res.json() : []; })
        .then(function(data){
          list.innerHTML = '';
          if (!data || !data.length) {
            if (noNotif) noNotif.style.display = 'block';
            if (countBadge) countBadge.textContent = '0';
            return;
          }
          if (noNotif) noNotif.style.display = 'none';
          if (countBadge) countBadge.textContent = data.length;
          data.forEach(function(notif){
            var item = document.createElement('div');
            item.className = 'item';
            item.innerHTML = '<div class="header">'+(notif.title||'')+'</div>' +
                             '<div class="description small">'+(notif.message||'')+'</div>' +
                             '<div class="meta text-muted notif-meta">'+(new Date(notif.created_at)).toLocaleString()+'</div>';
            list.appendChild(item);
          });
        })
        .catch(function(){});
    }
    if (dropdown) dropdown.addEventListener('click', loadNotifications);
    if (markAll) markAll.addEventListener('click', function(){
      var url = route('mark-all');
      if (!url) return;
      fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN': csrfToken, 'Content-Type':'application/json'}})
        .then(function(){ if (countBadge) countBadge.textContent = '0'; loadNotifications(); })
        .catch(function(){});
    });
  } catch(e) {}

  // Permission denied modal (if flash present)
  try {
    if (data('denied') === '1') {
      var modal = document.getElementById('deniedModal');
      if (modal && window.bootstrap) new bootstrap.Modal(modal).show();
    }
  } catch(e) {}

  // Session activity tracking (inactivity end) + initial page view
  try {
    var sessionId = localStorage.getItem('session_id') || Date.now().toString();
    localStorage.setItem('session_id', sessionId);
    var inactivityTimer, sessionEnded = false;

    function trackUserAction(action, isInactive) {
      fetch('/track-action', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ session_id: sessionId, page: location.pathname, action: action, is_inactive: !!isInactive })
      }).catch(function(){});
    }
    function endSession(){ if (!sessionEnded){ sessionEnded = true; trackUserAction('Session Ended', true);} }
    function reset(){ if (sessionEnded) return; clearTimeout(inactivityTimer); inactivityTimer = setTimeout(endSession, 300000); }
    trackUserAction('Page View');
    ['mousemove','keydown','click'].forEach(function(ev){ window.addEventListener(ev, reset); });
    reset();
  } catch(e) {}

  // Session ping redirect to login when 401
  try {
    var pingUrl = route('session-ping');
    var loginUrl = route('login');
    function ping(){ if (!pingUrl) return; fetch(pingUrl, {credentials:'include'}).then(function(r){ if (r.status===401 && loginUrl) location.href = loginUrl;}).catch(function(){}); }
    setInterval(ping, 60000);
  } catch(e) {}
});

