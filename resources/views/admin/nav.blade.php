@php use App\Classes\permission; @endphp
<style nonce="{{ $cspNonce }}"> 
  .meeting-item.is-expired {
    opacity:.55;
  }
  .meeting-item.is-expired .btn {
    pointer-events:none;
    opacity:.7;
  }
</style> 
<header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4">
      <div class="btn-toggle">
        <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
      </div>
      <div class="search-bar flex-grow-1">
        <div class="position-relative">
          <input class="form-control rounded-5 px-5 search-control d-lg-block d-none" type="text" placeholder="Search">
          <span class="material-icons-outlined position-absolute d-lg-block d-none ms-3 translate-middle-y start-0 top-50">search</span>
          <span class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 search-close">close</span>
          <div class="search-popup p-3">
            <div class="card rounded-4 overflow-hidden">
              <div class="card-header d-lg-none">
                <div class="position-relative">
                  <input class="form-control rounded-5 px-5 mobile-search-control" type="text" placeholder="Search">
                  <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50">search</span>
                  <span class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 mobile-search-close">close</span>
                 </div>
              </div>
              <div class="card-body search-content">
                <p class="search-title">Recent Searches</p>
                <div class="d-flex align-items-start flex-wrap gap-2 kewords-wrapper">
                  <a href="javascript:;" class="kewords"><span>Angular Template</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                  <a href="javascript:;" class="kewords"><span>Dashboard</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                  <a href="javascript:;" class="kewords"><span>Admin Template</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                  <a href="javascript:;" class="kewords"><span>Bootstrap 5 Admin</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                  <a href="javascript:;" class="kewords"><span>Html eCommerce</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                  <a href="javascript:;" class="kewords"><span>Sass</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                  <a href="javascript:;" class="kewords"><span>laravel 9</span><i
                      class="material-icons-outlined fs-6">search</i></a>
                </div>
                <hr>
                <p class="search-title">Tutorials</p>
                <div class="search-list d-flex flex-column gap-2">
                  <div class="search-list-item d-flex align-items-center gap-3">
                    <div class="list-icon">
                      <i class="material-icons-outlined fs-5">play_circle</i>
                    </div>
                    <div class="">
                      <h5 class="mb-0 search-list-title ">Wordpress Tutorials</h5>
                    </div>
                  </div>
                  <div class="search-list-item d-flex align-items-center gap-3">
                    <div class="list-icon">
                      <i class="material-icons-outlined fs-5">shopping_basket</i>
                    </div>
                    <div class="">
                      <h5 class="mb-0 search-list-title">eCommerce Website Tutorials</h5>
                    </div>
                  </div>
  
                  <div class="search-list-item d-flex align-items-center gap-3">
                    <div class="list-icon">
                      <i class="material-icons-outlined fs-5">laptop</i>
                    </div>
                    <div class="">
                      <h5 class="mb-0 search-list-title">Responsive Design</h5>
                    </div>
                  </div>
                </div>
  
                <hr>
                <p class="search-title">Members</p>
  
                <div class="search-list d-flex flex-column gap-2">
                  <div class="search-list-item d-flex align-items-center gap-3">
                    <div class="memmber-img">
                      <img src="/assets2/images/avatars/01.png" width="32" height="32" class="rounded-circle" alt="">
                    </div>
                    <div class="">
                      <h5 class="mb-0 search-list-title ">Andrew Stark</h5>
                    </div>
                  </div>
  
                  <div class="search-list-item d-flex align-items-center gap-3">
                    <div class="memmber-img">
                      <img src="/assets2/images/avatars/02.png" width="32" height="32" class="rounded-circle" alt="">
                    </div>
                    <div class="">
                      <h5 class="mb-0 search-list-title ">Snetro Jhonia</h5>
                    </div>
                  </div>
  
                  <div class="search-list-item d-flex align-items-center gap-3">
                    <div class="memmber-img">
                      <img src="/assets2/images/avatars/03.png" width="32" height="32" class="rounded-circle" alt="">
                    </div>
                    <div class="">
                      <h5 class="mb-0 search-list-title">Michle Clark</h5>
                    </div>
                  </div>
  
                </div>
              </div>
              <div class="card-footer text-center bg-transparent">
                <a href="javascript:;" class="btn w-100">See All Search Results</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <ul class="navbar-nav gap-1 nav-right-links align-items-center">
        <li class="nav-item d-lg-none mobile-search-btn">
          <a class="nav-link" href="javascript:;"><i class="material-icons-outlined">search</i></a>
        </li>
        @if(permission::permitted('meeting-links') === 'success')
        <!-- Meeting Links -->
        <li class="nav-item dropdown">
          <a href="{{ route('meetings.index') }}">
            <i class="fadeIn animated bx bx-link text-light" style="font-size:1.3em;"></i>
          </a>
        </li>
        @endif

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-bs-auto-close="outside"
            data-bs-toggle="dropdown" href="javascript:;"><i class="material-icons-outlined">apps</i></a>
          <div class="dropdown-menu dropdown-menu-end dropdown-apps shadow-lg p-3">
            <div class="border rounded-4 overflow-hidden">
              <div class="row row-cols-3 g-0 border-bottom">
                 @if(permission::permitted('campus') === 'success')
                <div class="col border-end">
                  <a href="{{ url('campus') }}" style="color: white;">
                    <div class="app-wrapper d-flex flex-column gap-2 text-center">
                    <div class="app-icon">
                      <div class="font-22"><i class="fadeIn animated bx bx-buildings"></i></div>
                    </div>
                    <div class="app-name">
                      <p class="mb-0">Campus</p>
                    </div>
                  </div>
                  </a>
                </div>
                @endif<!--end row-->
                @if(permission::permitted('campus') === 'success')
                <div class="col border-end">
                  <a href="{{ url('department') }}" style="color: white;">
                    <div class="app-wrapper d-flex flex-column gap-2 text-center">
                    <div class="app-icon">
                      <div class="font-22"><i class="fadeIn animated bx bx-vector"></i></div>
                    </div>
                    <div class="app-name">
                      <p class="mb-0">Dept</p>
                    </div>
                  </div>
                  </a>
                </div>
                @endif<!--end row-->
                @if(permission::permitted('ministries') === 'success')
                <div class="col">
                  <a href="{{ url('ministry') }}" style="color: white;">
                    <div class="app-wrapper d-flex flex-column gap-2 text-center">
                    <div class="app-icon">
                      <div class="font-22"><i class="fadeIn animated bx bx-shape-polygon"></i></div>
                    </div>
                    <div class="app-name">
                      <p class="mb-0">Ministry</p>
                    </div>
                    </div>
                  </a>
                </div>
                @endif<!--end row-->

            </div>
          </div>
        </li>
                @php
          // Safety defaults si jamais le composer n'a rien injecté
          $navUnreadCount   = $navUnreadCount   ?? 0;  
          $navNotifications = collect($navNotifications ?? [])->take(10);
        @endphp

        <style nonce="{{ $cspNonce }}">
          .badge-notify{position:absolute;top:-2px;right:-2px;display:inline-block;min-width:18px;height:18px;
            line-height:18px;padding:0 6px;border-radius:9px;background:#dc3545;color:#fff;font-size:11px;text-align:center}
          .notif-avatar{width:40px;height:40px} /* un peu plus petit pour fit */
          .dropdown-notify .notify-title{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px}
          .dropdown-notify .notify-time{font-size:11px;opacity:.6}
        </style>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
             data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0)">
            <i class="material-icons-outlined">notifications</i>
            @if($navUnreadCount > 0)
              <span class="badge-notify">{{ $navUnreadCount }}</span>
            @endif
          </a>

          <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow" style="min-width:280px">
            <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
              <h5 class="notiy-title mb-0">Notifications</h5>
              @if (Route::has('notifications.markAllRead'))
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0 p-0">
                  @csrf
                  <button class="btn btn-sm btn-link text-decoration-none" type="submit" title="Tout marquer comme lu">
                    <span class="material-icons-outlined fs-6">done_all</span>
                  </button>
                </form>
              @endif
            </div>

            <div class="notify-list" style="max-height: 320px; overflow:auto;">
              @forelse($navNotifications as $n)
                @php
                  $title = $n->title ?? ($n->type ?? 'Notification');
                  $icon  = $n->icon  ?? null;
                  $url   = !empty($n->url) ? url($n->url) : 'javascript:void(0)';
                  $isRead = (isset($n->is_read) && (int)$n->is_read === 1);
                  try { $when = \Illuminate\Support\Carbon::parse($n->created_at)->diffForHumans(); }
                  catch (\Throwable $e) { $when = ''; }
                  $initials = strtoupper(substr(trim($n->type ?? 'NT'),0,2));
                @endphp

                <div>
                  <a class="dropdown-item border-bottom py-2 {{ $isRead ? '' : 'bg-light' }}"
                     href="{{ $url }}">
                    <div class="d-flex align-items-center gap-3 position-relative">
                      <div>
                        @if($icon)
                          <div class="user-wrapper bg-primary text-primary bg-opacity-10 d-inline-flex
                                      align-items-center justify-content-center rounded-circle notif-avatar">
                            <span class="material-icons-outlined">{{ $icon }}</span>
                          </div>
                        @else
                          <div class="user-wrapper bg-secondary text-secondary bg-opacity-10 d-inline-flex
                                      align-items-center justify-content-center rounded-circle notif-avatar">
                            <span>{{ $initials }}</span>
                          </div>
                        @endif
                      </div>

                      <div class="flex-grow-1">
                        <h5 class="notify-title mb-0">{{ $title }}</h5>
                        @if($when !== '')
                          <p class="mb-0 notify-time">{{ $when }}</p>
                        @endif
                      </div>
                    </div>
                  </a>
                </div>
              @empty
                <div class="px-3 py-2 text-center text-light">Aucune notification</div>
              @endforelse
            </div>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
            <img src="{{ $userAvatar ?? asset('images/avatar-default.png') }}"
               class="rounded-circle p-1 border"
               width="45" height="45"
               alt="{{ Auth::user()->name }}">
          </a>
          <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
            <a class="dropdown-item  gap-2 py-2" href="javascript:;">
              <div class="text-center">
                 <img src="{{ $userAvatar ?? asset('images/avatar-default.png') }}"
                   class="rounded-circle p-1 shadow mb-3"
                   width="90" height="90"
                   alt="{{ Auth::user()->name }}">
            </a>
                <h5 class="user-name mb-0 fw-bold">{{ Auth::user()->name }}!</h5>
              </div>
            </a>
            <hr class="dropdown-divider">
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
              class="material-icons-outlined">person_outline</i>Profile</a>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
              class="material-icons-outlined">shield</i>update account</a>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
              class="material-icons-outlined">lock</i>Change password</a>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('personal/dashboard') }}"><i class="lni lni-exit"></i>Personal Account</a>
            <hr class="dropdown-divider">
            @if(permission::permitted('dashboard') === 'success')
             <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('dashboard') }}"><i class="lni lni-cog"></i>System Dashboard</a>
             @endif
             @if(permission::permitted('radio-dashboard') === 'success')
             <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('/radio/dashboard') }}"><i class="lni lni-rss-feed"></i>Radio Dashboard</a>
             @endif
            <form action="{{ route('logout') }}" method="POST" class="m-0">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border:none; background:none; width:100%; text-align:left; cursor:pointer;">
                <i class="material-icons-outlined">power_settings_new</i>Logout
              </button>
            </form>
          </div>
        </li>
      </ul>

    </nav>
  </header>
      @php
        // Normalize to arrays of strings so Blade never receives an object
        $campuses = collect($campuses ?? [])
            ->map(function ($c) {
                if (is_string($c)) return trim($c);
                if (is_array($c))  return (string)($c['campus'] ?? $c['name'] ?? '');
                if (is_object($c)) return (string)($c->campus ?? $c->name ?? '');
                return (string)$c;
            })->filter()->unique()->values()->all();

        $ministries = collect($ministries ?? [])
            ->map(function ($m) {
                if (is_string($m)) return trim($m);
                if (is_array($m))  return (string)($m['ministry'] ?? $m['name'] ?? '');
                if (is_object($m)) return (string)($m->ministry ?? $m->name ?? '');
                return (string)$m;
            })->filter()->unique()->values()->all();

        $departments = collect($departments ?? [])
            ->map(function ($d) {
                if (is_string($d)) return trim($d);
                if (is_array($d))  return (string)($d['department'] ?? $d['name'] ?? '');
                if (is_object($d)) return (string)($d->department ?? $d->name ?? '');
                return (string)$d;
            })->filter()->unique()->values()->all();
      @endphp
      

{{-- QR PREVIEW MODAL --}}
<div class="modal fade" id="qrPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title d-flex align-items-center gap-2">
          <i class="material-icons-outlined">qr_code_2</i>
          <span id="qrTitle">QR</span>
        </h6>
        <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
          <i class="material-icons-outlined">close</i>
        </a>
      </div>
      <div class="modal-body pt-2">
        <div class="small  mb-2" style="color: #b3b7bb;">
          <span id="qrMeta"></span>
        </div>
        <div class="text-center">
          <img id="qrImg" src="" alt="QR Code" class="w-100" style="max-width:420px;">
        </div>
        <div class="mt-3">
          <label class="form-label small">Link</label>
          <div class="d-flex gap-2">
            <input id="qrLink" class="form-control form-control-sm" readonly>
            <button class="btn btn-sm btn-outline-secondary qr-copy-btn" type="button" data-qr-target="#qrLink">
              <i class="material-icons-outlined" style="font-size:16px;">content_copy</i>
            </button>
            <a id="qrDownload" class="btn btn-sm btn-outline-secondary" download>
              <i class="material-icons-outlined" style="font-size:16px;">download</i>
            </a>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-primary" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

{{-- STYLES --}}
<style nonce="{{ $cspNonce }}">
  .dropdown-meetings{width:520px; max-height:70vh; overflow:auto; border-radius:12px;}
  .meeting-item:hover{background:rgba(0,0,0,0.03);}
  .meeting-desc{
    font-size:.8rem; color:#6c757d;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
  }
  .badge-soft{
    background:#f4f6f8; color:#475569; border-radius:999px; padding:.15rem .5rem; font-weight:500;
  }
  .badge-min{background:#f3f0ff; color:#5641d9;}
  .badge-dept{background:#eefaf3; color:#0f7b3e;}
  .btn-xs{--bs-btn-padding-y:.15rem; --bs-btn-padding-x:.35rem; --bs-btn-font-size:.75rem;}
  .copy-toast{
    position:absolute; right:8px; top:8px;
    background:#e7f1ff; color:#0b5ed7; border-radius:8px; padding:.1rem .4rem; font-size:.7rem;
  }
</style>

{{-- SCRIPTS --}}
<script nonce="{{ $cspNonce }}">
// Mode selector functionality
(function(){
  const modeSel   = document.getElementById('modeSelect');
  const group     = document.getElementById('requireAuthGroup');
  const chk       = document.getElementById('chkRequireAuth');
  const hidden    = document.getElementById('requireAuthHidden');

  if (!modeSel || !group || !chk || !hidden) return;

  function applyModeUI() {
    const isForm = modeSel.value === 'form';
    group.style.display = isForm ? 'none' : '';
    chk.disabled = isForm;
    if (isForm) {
      hidden.value = '0';
    } else {
      hidden.value = chk.checked ? '1' : '0';
    }
  }

  chk.addEventListener('change', () => {
    if (!chk.disabled) hidden.value = chk.checked ? '1' : '0';
  });

  modeSel.addEventListener('change', applyModeUI);
  applyModeUI();
})();

// Meetings list filtering
(function(){
  const list      = document.getElementById('meetingsList');
  if (!list) return;
  const items     = Array.from(list.querySelectorAll('.meeting-item'));
  const selCampus = document.getElementById('fltCampus');
  const selMin    = document.getElementById('fltMinistry');
  const selDept   = document.getElementById('fltDept');
  const lblCount  = document.getElementById('fltCount');
  const btnReset  = document.getElementById('fltReset');

  const parse = (el, attr) => {
    try { return JSON.parse(el.getAttribute(attr) || '[]'); }
    catch(e){ return []; }
  };

  function matches(el, campus, min, dept){
    const camps = parse(el, 'data-camp');
    const mins  = parse(el, 'data-mins');
    const depts = parse(el, 'data-dept');

    const norm = s => String(s || '').trim().toLowerCase();
    const has  = (arr, val) => !val || arr.some(a => norm(a) === norm(val));

    return has(camps, campus) && has(mins, min) && has(depts, dept);
  }

  function apply(){
    const c = selCampus ? selCampus.value : '';
    const m = selMin    ? selMin.value    : '';
    const d = selDept   ? selDept.value   : '';
    let shown = 0;

    items.forEach(el => {
      const ok = matches(el, c, m, d);
      el.classList.toggle('d-none', !ok);
      const hr = el.nextElementSibling;
      if (hr && hr.tagName === 'HR') {
        hr.classList.toggle('d-none', !ok);
      }
      if (ok) shown++;
    });

    if (lblCount) {
      const total = items.length;
      lblCount.textContent = (c||m||d) ? `Showing ${shown} of ${total}` : `${total} total`;
    }
  }

  function reset(){
    if (selCampus) selCampus.value = '';
    if (selMin)    selMin.value    = '';
    if (selDept)   selDept.value   = '';
    apply();
  }

  selCampus && selCampus.addEventListener('change', apply);
  selMin    && selMin.addEventListener('change', apply);
  selDept   && selDept.addEventListener('change', apply);
  btnReset  && btnReset.addEventListener('click', reset);

  apply();
})();

// Bulk checkbox helpers
function bulkCheck(containerSelector, checked) {
  document.querySelectorAll(`${containerSelector} input[type="checkbox"]`).forEach(cb => cb.checked = !!checked);
}
function bulkInvert(containerSelector) {
  document.querySelectorAll(`${containerSelector} input[type="checkbox"]`).forEach(cb => cb.checked = !cb.checked);
}

// Edit modal functionality
const editModal = document.getElementById('editMeetingModal');
if (editModal) {
  editModal.addEventListener('show.bs.modal', (ev) => {
    const btn = ev.relatedTarget;
    if (!btn) return;

    const id          = btn.getAttribute('data-id');
    const title       = btn.getAttribute('data-title') || '';
    const description = btn.getAttribute('data-description') || '';
    const expires     = btn.getAttribute('data-expires') || '';
    const camp        = JSON.parse(btn.getAttribute('data-camp') || '[]');
    const mins        = JSON.parse(btn.getAttribute('data-mins') || '[]');
    const dept        = JSON.parse(btn.getAttribute('data-dept') || '[]');

    const form = editModal.querySelector('#editMeetingForm');
    if (form) form.action = `{{ url('meeting-links') }}/${id}`;

    const titleEl = editModal.querySelector('#edit_title');
    const descEl = editModal.querySelector('#edit_description');
    const expiresEl = editModal.querySelector('#edit_expires');
    
    if (titleEl) titleEl.value = title;
    if (descEl) descEl.value = description;

    let ymd = '';
    if (expires) {
      const d = new Date(expires);
      if (!isNaN(d.getTime())) {
        const mm = String(d.getMonth()+1).padStart(2,'0');
        const dd = String(d.getDate()).padStart(2,'0');
        ymd = `${d.getFullYear()}-${mm}-${dd}`;
      }
    }
    if (expiresEl) expiresEl.value = ymd;

    bulkCheck('#editCampusBox', false);
    bulkCheck('#editMinistryBox', false);
    bulkCheck('#editDeptBox', false);

    function checkList(container, values) {
      const set = new Set(values || []);
      const containerEl = editModal.querySelector(container);
      if (containerEl) {
        containerEl.querySelectorAll('input[type="checkbox"]').forEach(cb => {
          if (set.has(cb.value)) cb.checked = true;
        });
      }
    }
    checkList('#editCampusBox', camp);
    checkList('#editMinistryBox', mins);
    checkList('#editDeptBox', dept);
  });
}

// Copy buttons functionality
document.querySelectorAll('.copy-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const link = btn.dataset.link || btn.getAttribute('data-link');
    const alertId = btn.dataset.alertId || btn.getAttribute('data-alert-id');
    if (link) {
      navigator.clipboard.writeText(link).then(() => {
        const el = document.getElementById(alertId);
        if (el) {
          el.classList.remove('d-none');
          el.style.opacity = 1;
          setTimeout(()=>{ el.style.opacity = 0; }, 1300);
          setTimeout(()=>{ el.classList.add('d-none'); el.style.opacity=1; }, 1700);
        }
      });
    }
  });
});

// QR modal functionality
const qrModal = document.getElementById('qrPreviewModal');
if (qrModal) {
  qrModal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    if (!btn) return;
    if (btn.getAttribute('data-expired') === '1') {
      event.preventDefault();
      return;
    }

    const title   = btn.getAttribute('data-title') || 'QR';
    const url     = btn.getAttribute('data-url')   || '';
    const qr      = btn.getAttribute('data-qr')    || '';
    const expires = btn.getAttribute('data-expires') || '';
    const camp    = JSON.parse(btn.getAttribute('data-camp') || '[]');
    const mins    = JSON.parse(btn.getAttribute('data-mins') || '[]');
    const dept    = JSON.parse(btn.getAttribute('data-dept') || '[]');

    const qrTitleEl = qrModal.querySelector('#qrTitle');
    const qrImgEl = qrModal.querySelector('#qrImg');
    const qrLinkEl = qrModal.querySelector('#qrLink');
    const qrMetaEl = qrModal.querySelector('#qrMeta');
    const dlEl = qrModal.querySelector('#qrDownload');

    if (qrTitleEl) qrTitleEl.textContent = title;
    if (qrImgEl) qrImgEl.src = qr;
    if (qrLinkEl) qrLinkEl.value = url;
    if (dlEl) {
      dlEl.href = qr;
      dlEl.download = (title || 'qr').replace(/\s+/g,'_') + '.svg';
    }

    const parts = [];
    parts.push(expires ? `Expires: ${new Date(expires).toLocaleDateString()}` : 'No expiry');
    if (camp.length) parts.push(`${camp.length} campus`);
    if (mins.length) parts.push(`${mins.length} ministry`);
    if (dept.length) parts.push(`${dept.length} dept`);
    if (qrMetaEl) qrMetaEl.textContent = parts.join(' • ');
  });
}

// QR copy button handler
document.querySelectorAll('.qr-copy-btn').forEach(btn => {
  btn.addEventListener('click', function(){
    try {
      const target = this.getAttribute('data-qr-target');
      const el = document.querySelector(target);
      if (el) {
        navigator.clipboard.writeText(el.value || el.textContent || '');
      }
    } catch (e) {
      console.error('QR copy failed', e);
    }
  });
});
</script>