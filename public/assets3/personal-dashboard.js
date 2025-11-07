(function(){
  // Dismiss semantic UI messages
  if (window.jQuery) {
    jQuery(function(){
      jQuery('.message .close').on('click', function(){
        var $msg = jQuery(this).closest('.message');
        if ($msg.transition) { $msg.transition('fade'); } else { $msg.hide(); }
      });
    });
  }

  // QR scanner (requires #launchScanner, #scanner-container, #video, #canvas)
  function initScanner(){
    var launchBtn = document.getElementById('launchScanner');
    if (!launchBtn) return;
    launchBtn.addEventListener('click', function(){ startScan(); });
  }

  async function startScan(){
    var container = document.getElementById('scanner-container');
    var video     = document.getElementById('video');
    var canvas    = document.getElementById('canvas');
    if (!container || !video || !canvas) return;
    var ctx       = canvas.getContext('2d');

    container.classList.remove('d-none');
    container.classList.add('d-block');
    let stream;
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
      video.srcObject = stream;
      await video.play();
    } catch (err) {
      console.error('Camera error:', err);
      alert('Unable to access camera.');
      return;
    }

    function scan(){
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        var sx = video.videoWidth * 0.25;
        var sy = video.videoHeight * 0.25;
        var sSizeW = video.videoWidth * 0.5;
        var sSizeH = video.videoHeight * 0.5;
        ctx.drawImage(video, sx, sy, sSizeW, sSizeH, 0, 0, canvas.width, canvas.height);
        try {
          if (typeof jsQR !== 'undefined') {
            var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            var code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code) {
              stream.getTracks().forEach(function(t){ t.stop(); });
              container.style.display = 'none';
              window.location.href = '/scan?token=' + encodeURIComponent(code.data);
              return;
            }
          }
        } catch (e) {}
      }
      requestAnimationFrame(scan);
    }
    requestAnimationFrame(scan);
  }

  document.addEventListener('DOMContentLoaded', function(){
    initScanner();

    // Live clock on personal dashboard, if present
    var clockEl = document.getElementById('dashboardClock');
    if (clockEl) {
      var tf = parseInt(clockEl.getAttribute('data-tf') || '1', 10); // 1=12h, else 24h
      function fmt(n){ return (n<10? '0':'')+n; }
      function tick(){
        var d = new Date();
        var h = d.getHours();
        if (tf === 1) {
          var ampm = h>=12 ? ' PM' : ' AM';
          h = h % 12; h = h ? h : 12;
          clockEl.textContent = h + ':' + fmt(d.getMinutes()) + ':' + fmt(d.getSeconds()) + ampm;
        } else {
          clockEl.textContent = fmt(h) + ':' + fmt(d.getMinutes()) + ':' + fmt(d.getSeconds());
        }
      }
      tick();
      setInterval(tick, 1000);
    }

    // Dashboard clock in/out
    var btn = document.getElementById('dashClockBtn');
    if (btn) {
      btn.addEventListener('click', function(){
        var type = btn.getAttribute('data-type') || 'timein';
        submitDashboardClock(type);
      });
    }

    // Initial async loads (if backend endpoints exist)
    try { fetchSummary(); } catch(e){}
    try { fetchSchedule(); } catch(e){}
    try { fetchMeetings(); } catch(e){}
    try { fetchLeaves(); } catch(e){}
    try { fetchNotifications(); setInterval(fetchNotifications, 120000); } catch(e){}
  });
  
  // --- Dashboard clock-in/out helpers ---
  function getBaseUrl(){
    var el = document.getElementById('_url');
    return el ? el.value : '';
  }
  function getIdno(){
    var cfg = document.getElementById('personal-config');
    return cfg ? (cfg.getAttribute('data-idno') || '').toUpperCase() : '';
  }
  function csrf(){
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function notify(type, message){
    if (window.$ && $.notify) {
      $.notify({ icon: 'ui icon info', message: message }, { type: type, timer: 400 });
    } else {
      console[type === 'danger' ? 'error' : 'log'](message);
    }
  }

  function acquirePositionWithPrompt(){
    return new Promise(function(resolve, reject){
      if (!('geolocation' in navigator)) {
        if (window.Swal) Swal.fire('Geolocation Not Supported','Please use a compatible browser.','error');
        return reject(new Error('geolocation_unsupported'));
      }
      function requestPosition(){
        navigator.geolocation.getCurrentPosition(function(pos){ resolve(pos); }, function(err){
          if (err && (err.code === 1 || err.code === err.PERMISSION_DENIED)) {
            if (window.Swal) {
              Swal.fire({
                icon: 'warning', title: 'Location Blocked',
                html: 'Location is blocked for this site.<br>Enable it in your browser\'s Site Settings, then try again.',
                showCancelButton: true, confirmButtonText: 'Try Again', cancelButtonText: 'Cancel'
              }).then(function(r){ if (r.isConfirmed) requestPosition(); else reject(err); });
            } else {
              reject(err);
            }
          } else {
            if (window.Swal) Swal.fire('Location Unavailable','Unable to get your location. Please try again.','info');
            reject(err || new Error('geolocation_unavailable'));
          }
        }, { enableHighAccuracy: false, timeout: 15000, maximumAge: 0 });
      }
      if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'geolocation' }).then(function(status){
          if (status.state === 'granted') requestPosition();
          else if (status.state === 'prompt') {
            if (window.Swal) {
              Swal.fire({
                icon: 'info', title: 'Allow Location?', text: 'We use your location to verify on‑site clock‑ins.',
                showCancelButton: true, confirmButtonText: 'Allow Location', cancelButtonText: 'Not Now'
              }).then(function(r){ if (r.isConfirmed) requestPosition(); else reject(new Error('user_cancelled')); });
            } else { requestPosition(); }
          } else { // denied
            if (window.Swal) {
              Swal.fire({
                icon: 'warning', title: 'Location Blocked',
                html: 'Open Site Settings and enable Location for this domain, then press Try Again.',
                showCancelButton: true, confirmButtonText: 'Try Again', cancelButtonText: 'Cancel'
              }).then(function(r){ if (r.isConfirmed) requestPosition(); else reject(new Error('permission_denied')); });
            } else { reject(new Error('permission_denied')); }
          }
        }).catch(function(){ requestPosition(); });
      } else {
        requestPosition();
      }
    });
  }

  function getPositionIfGranted(){
    return new Promise(function(resolve){
      if(!('geolocation' in navigator)) return resolve(null);
      if(!(navigator.permissions && navigator.permissions.query)) return resolve(null);
      navigator.permissions.query({ name: 'geolocation' }).then(function(status){
        if(status.state === 'granted'){
          navigator.geolocation.getCurrentPosition(function(pos){ resolve(pos); }, function(){ resolve(null); }, { maximumAge: 30000 });
        } else { resolve(null); }
      }).catch(function(){ resolve(null); });
    });
  }

  function submitDashboardClock(type){
    var idno = getIdno();
    if (!idno) { notify('danger', 'Missing ID number for your account.'); return; }
    var url = getBaseUrl();
    var postUrl = url + '/attendance/addWebApp';
    var comment = '';

    function doSubmit(pos){
      var payload = {
        idno: idno,
        type: type,
        clockin_comment: comment
      };
      if (pos && pos.coords){ payload.latitude = pos.coords.latitude; payload.longitude = pos.coords.longitude; }

      var xhr = new XMLHttpRequest();
      xhr.open('POST', postUrl, true);
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.setRequestHeader('X-CSRF-TOKEN', csrf());
      xhr.onreadystatechange = function(){
        if (xhr.readyState === 4){
          var res = {};
          try { res = JSON.parse(xhr.responseText || '{}'); } catch(e){}
          if (xhr.status >= 200 && xhr.status < 300 && !(res && res.error)){
            var btn = document.getElementById('dashClockBtn');
            var statusEl = document.getElementById('dashStatus');
            var timeEl   = document.getElementById('dashStatusTime');
            if (btn) {
              if (type === 'timein') { btn.setAttribute('data-type','timeout'); btn.textContent = 'Clock Out'; }
              else { btn.setAttribute('data-type','timein'); btn.textContent = 'Clock In'; }
            }
            if (statusEl) {
              var label = (type==='timein' ? 'Clocked In' : 'Clocked Out') + ' ';
              if (statusEl.firstChild && statusEl.firstChild.nodeType === 3) {
                statusEl.firstChild.nodeValue = label;
              } else {
                statusEl.insertBefore(document.createTextNode(label), statusEl.firstChild);
              }
            }
            if (timeEl) { timeEl.textContent = (type==='timein' ? ('@ ' + (res.time || '')) : ''); }
            notify('success', (type==='timein' ? 'Clocked in' : 'Clocked out') + ' successfully.');
            try { if (typeof fetchSummary === 'function') fetchSummary(); } catch(e){}
          } else {
            var msg = (res && res.error) ? res.error : (xhr.responseText || 'Clock-in failed.');
            notify('danger', msg);
          }
        }
      };
      xhr.send(JSON.stringify(payload));
    }

    if (type === 'timeout') {
      getPositionIfGranted().then(function(pos){ doSubmit(pos); });
    } else {
      acquirePositionWithPrompt().then(function(pos){ doSubmit(pos); })
        .catch(function(){ /* cancelled or blocked */ });
    }
  }

  // ---- Async data helpers (prepared for JSON endpoints) ----
  function cfg(){ return document.getElementById('personal-config'); }
  function fetchJSON(url, opts){
    return fetch(url, Object.assign({ method:'GET', credentials:'same-origin', headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } }, opts||{}))
      .then(function(r){ if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); });
  }

  function fetchSummary(){
    var c = cfg(); if (!c) return; var url = c.getAttribute('data-summary'); if (!url) return;
    fetchJSON(url).then(updateKpis).catch(function(){});
  }
  function updateKpis(data){
    var el;
    if ((el=document.getElementById('kpi-hours-today-value')) && data.today){ el.textContent = data.today.worked+' / '+data.today.expected; }
    if ((el=document.getElementById('kpi-hours-today-trend')) && data.today){ el.textContent = data.today.trend || ''; }
    if ((el=document.getElementById('kpi-hours-week-value')) && data.week){ el.textContent = data.week.worked+' / '+data.week.expected; }
    if ((el=document.getElementById('kpi-hours-week-trend')) && data.week){ el.textContent = data.week.trend || ''; }
    if ((el=document.getElementById('kpi-overtime-value')) && data.overtime){ el.textContent = data.overtime.hours+'h'; }
    if ((el=document.getElementById('kpi-overtime-trend')) && data.overtime){ el.textContent = data.overtime.trend || ''; }
    if ((el=document.getElementById('kpi-lates-value')) && data.lates){ el.textContent = data.lates.count+' this week'; }
    if ((el=document.getElementById('kpi-lates-trend')) && data.lates){ el.textContent = data.lates.trend || ''; }

    if (data.today){
      var pct = Math.min(100, Math.round((parseFloat(data.today.worked||0) / Math.max(1, parseFloat(data.today.expected||1))) * 100));
      var ring = document.getElementById('progressRing'); var val = document.getElementById('progressRingValue');
      if (ring) ring.style.setProperty('--pg', pct+'%'); if (val) val.textContent = parseFloat(data.today.worked||0).toFixed(2)+'h';
    }

    // Populate Last Time Late / Overall if provided in summary
    if (data.lastLate){
      var wrap=document.getElementById('last-late'); var empty=document.getElementById('last-late-empty');
      if (wrap && empty){ empty.classList.add('d-none'); wrap.classList.remove('d-none'); }
      var m = data.lastLate;
      setText('last-late-date', m.date || '');
      setText('last-late-scheduled', m.scheduled || '');
      setText('last-late-actual', m.actual || '');
      setText('last-late-mins', (m.minutesLate!=null? m.minutesLate+'m' : ''));
    }
    if (data.lastSession){
      var wrap2=document.getElementById('last-overall'); var empty2=document.getElementById('last-overall-empty');
      if (wrap2 && empty2){ empty2.classList.add('d-none'); wrap2.classList.remove('d-none'); }
      var s = data.lastSession;
      setText('last-overall-date', s.date || '');
      setText('last-overall-times', (s.timeIn||'') + (s.timeOut? (' - '+s.timeOut) : ''));
      setText('last-overall-length', s.length || '');
    }
  }

  function setText(id, text){ var el=document.getElementById(id); if (el) el.textContent = text; }

  function fetchSchedule(){
    var c = cfg(); if (!c) return; var url = c.getAttribute('data-schedule'); if (!url) return;
    fetchJSON(url).then(function(data){
      var list = document.getElementById('schedule-list'); var empty = document.getElementById('schedule-empty'); if (!list||!empty) return;
      list.innerHTML='';
      if (data.blocks && data.blocks.length){
        empty.style.display='none';
        data.blocks.forEach(function(b){
          var li=document.createElement('li'); var dot=document.createElement('div'); dot.className='dot'; if (b.color) dot.style.background=b.color;
          var t=document.createElement('div'); t.className='text'; t.textContent = b.start+' - '+b.end+(b.dept?' · '+b.dept:'')+(b.campus?' · '+b.campus:'');
          li.appendChild(dot); li.appendChild(t); list.appendChild(li);
        });
      } else { empty.style.display='block'; }

      // Also populate four cards grid if present
      var grid = document.getElementById('schedule-cards');
      if (grid){
        grid.innerHTML='';
        var blocks = (data.blocks||[]).slice(0,4);
        // pad to 4 with placeholders
        while (blocks.length < 4) blocks.push(null);
        blocks.forEach(function(b, idx){
          var col = document.createElement('div'); col.className='col-12 col-sm-6';
          var card = document.createElement('div'); card.className='sched-chip'+(idx===0&&b?' active':'');
          if (b){
            var t=document.createElement('div'); t.className='time'; t.textContent=(b.start+' – '+b.end+' •');
            var m=document.createElement('div'); m.className='meta'; m.textContent=((b.title||b.dept||'')+(b.campus?' • '+b.campus:''));
            card.appendChild(t); card.appendChild(m);
          } else {
            var t2=document.createElement('div'); t2.className='time'; t2.textContent='—';
            var m2=document.createElement('div'); m2.className='meta'; m2.textContent='No schedule assigned';
            card.appendChild(t2); card.appendChild(m2);
          }
          col.appendChild(card); grid.appendChild(col);
        });
      }

      // Fill the dedicated Today’s Schedule and Upcoming Shift cards
      var todayList = document.getElementById('today-schedule-list');
      var todayEmpty = document.getElementById('today-schedule-empty');
      if (todayList && todayEmpty){
        todayList.innerHTML='';
        var todays = (data.todayBlocks || data.blocks || []).slice(0,3);
        if (todays.length){
          todayEmpty.classList.add('d-none');
          todays.forEach(function(b){
            var li=document.createElement('li');
            li.innerHTML = '<div class="fw-semibold">'+(b.start||'')+' – '+(b.end||'')+'</div>'+
                           '<div class="small text-muted">'+((b.title||b.dept||'')+(b.campus?' • '+b.campus:''))+'</div>';
            todayList.appendChild(li);
          });
        } else {
          todayEmpty.classList.remove('d-none');
        }
      }

      var upWrap = document.getElementById('upcoming-shift');
      var upEmpty = document.getElementById('upcoming-shift-empty');
      if (upWrap && upEmpty){
        var nxt = data.next || null;
        if (!nxt && (data.blocks||[]).length){ nxt = data.blocks[0]; }
        if (nxt){
          upEmpty.classList.add('d-none'); upWrap.classList.remove('d-none');
          setText('upcoming-shift-time', (nxt.start||'')+' – '+(nxt.end||''));
          setText('upcoming-shift-where', ((nxt.title||nxt.dept||'')+(nxt.campus?' • '+nxt.campus:'')));
          setText('upcoming-shift-countdown', data.nextStartsIn || nxt.startsIn || '');
        } else {
          upEmpty.classList.remove('d-none'); upWrap.classList.add('d-none');
        }
      }
    }).catch(function(){});
  }

  function fetchMeetings(){
    var c = cfg(); if (!c) return; var url = c.getAttribute('data-meetings'); if (!url) return;
    fetchJSON(url).then(function(data){
      var nextEl = document.getElementById('next-meeting'); var nextLink=document.getElementById('next-meeting-link');
      if (data.next){ nextEl.textContent = data.next.title+' — '+data.next.time+(data.next.tags?(' · '+data.next.tags.join(', ')):''); if (data.next.link && nextLink) nextLink.href=data.next.link; }
      var up = document.getElementById('upcoming-meetings'); if (up && data.upcoming){ up.innerHTML=''; data.upcoming.forEach(function(m){ var li=document.createElement('li'); var d=document.createElement('div'); d.className='dot'; var t=document.createElement('div'); t.className='text'; t.textContent=m.time+' — '+m.title; li.appendChild(d); li.appendChild(t); up.appendChild(li); }); }
      // Team members
      var teamList = document.getElementById('team-members');
      if (teamList){
        teamList.innerHTML='';
        if (Array.isArray(data.team) && data.team.length){
          data.team.forEach(function(member){
          var li=document.createElement('li');
          li.className='team-member-card';
          var avatar=document.createElement('img'); avatar.src = member.avatar || '/assets/images/faces/default_user4.jpg'; avatar.alt=member.name || 'Member';
          var info=document.createElement('div'); info.className='team-member-info';
          var name=document.createElement('div'); name.className='team-member-name'; name.textContent=member.name || '';
          var role=document.createElement('div'); role.className='team-member-role'; role.textContent=member.role || '';
          info.appendChild(name); info.appendChild(role);
          var actions=document.createElement('div'); actions.className='team-member-actions';
          if (member.email){ var mail=document.createElement('a'); mail.href='mailto:'+member.email; mail.innerHTML='<i class="far fa-envelope"></i>'; actions.appendChild(mail); }
          if (member.phone){ var phone=document.createElement('a'); phone.href='tel:'+member.phone; phone.innerHTML='<i class="fas fa-phone"></i>'; actions.appendChild(phone); }
          if (member.chat){ var chat=document.createElement('a'); chat.href=member.chat; chat.innerHTML='<i class="far fa-comment-dots"></i>'; actions.appendChild(chat); }
          li.appendChild(avatar); li.appendChild(info); li.appendChild(actions); teamList.appendChild(li);
        });
        } else {
          var empty=document.createElement('li');
          empty.className='team-member-card';
          empty.innerHTML = '<div class="team-member-info"><div class="team-member-name text-muted">No team members assigned.</div></div>';
          teamList.appendChild(empty);
        }
      }

      var upTimeline = document.getElementById('upcoming-meetings');
      if (upTimeline){
        upTimeline.innerHTML='';
        var meetings = Array.isArray(data.upcoming) ? data.upcoming : [];
        if (meetings.length){
          meetings.forEach(function(m){
          var item=document.createElement('li'); item.className='meeting-timeline-item';
          var dot=document.createElement('div'); dot.className='meeting-dot'; if (m.category){ dot.classList.add(m.category); }
          var content=document.createElement('div'); content.className='meeting-content';
          var title=document.createElement('div'); title.className='meeting-title'; title.textContent = m.title || '';
          var meta=document.createElement('div'); meta.className='meeting-meta'; meta.textContent = (m.time||'') + (m.location? (' • '+m.location) : '') + (m.startsIn ? (' • ' + m.startsIn) : '');
          content.appendChild(title); content.appendChild(meta);
          item.appendChild(dot); item.appendChild(content);
          upTimeline.appendChild(item);
        });
        } else {
          var empty=document.createElement('li'); empty.className='meeting-timeline-item';
          var dot=document.createElement('div'); dot.className='meeting-dot'; dot.style.opacity='0.2';
          var content=document.createElement('div'); content.className='meeting-content';
          content.innerHTML = '<div class="meeting-meta">No upcoming meetings.</div>';
          empty.appendChild(dot); empty.appendChild(content); upTimeline.appendChild(empty);
        }
      }

      var ann = document.getElementById('recent-announcements');
      if (ann){
        ann.innerHTML='';
        var posts = Array.isArray(data.announcements) ? data.announcements.slice(0,3) : [];
        if (posts.length){
          posts.forEach(function(a){
          var li=document.createElement('li'); li.className='announcement-item';
          li.innerHTML = '<div class="announcement-title">'+(a.title||'')+'</div>'+
                         '<div class="announcement-preview">'+(a.preview||'')+'</div>';
          ann.appendChild(li);
        });
        } else {
          var empty=document.createElement('li'); empty.className='announcement-item'; empty.innerHTML='<div class="announcement-preview">No announcements yet.</div>'; ann.appendChild(empty);
        }
      }
    }).catch(function(){});
  }

  function fetchLeaves(){
    var c = cfg(); if (!c) return; var url = c.getAttribute('data-leaves'); if (!url) return;
    fetchJSON(url).then(function(data){
      var bal=document.getElementById('leave-balance'); if (bal && data.balance){ bal.textContent = data.balance.used+' of '+data.balance.total+' days used'; }
      var list=document.getElementById('pending-leaves'); if (list){ list.innerHTML=''; (data.pending||[]).forEach(function(lv){ var li=document.createElement('li'); li.className='mb-2'; li.innerHTML='<div class="d-flex justify-content-between"><span>'+(lv.type||'Leave')+'</span><span class="badge bg-warning text-dark">'+(lv.status||'Pending')+'</span></div><div class="small text-muted">'+(lv.range||'')+'</div>'; list.appendChild(li); }); if (!list.children.length) list.innerHTML='<li class="text-muted small">No pending leave requests.</li>'; }
    }).catch(function(){});
  }

  function fetchNotifications(){
    var c = cfg(); if (!c) return; var url = c.getAttribute('data-notifications-api'); if (!url) return;
    fetchJSON(url).then(function(data){
      var feed=document.getElementById('notif-feed'); var badge=document.getElementById('notif-unread'); if (badge && typeof data.unread==='number') badge.textContent=data.unread;
      if (feed){ feed.innerHTML=''; (data.items||[]).forEach(function(n){ var li=document.createElement('li'); li.className='mb-2'; li.innerHTML='<div class="d-flex justify-content-between"><span>'+(n.title||'')+'</span>'+(n.time?'<span class="small text-muted">'+n.time+'</span>':'')+'</div>'+(n.preview?'<div class="small text-muted">'+n.preview+'</div>':''); feed.appendChild(li); }); }
    }).catch(function(){});
  }
})();
