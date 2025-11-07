@extends('layouts.admin_v2')

@section('meta')
  <title>{{ $link->title ?? 'Meeting Attendance' }} | Glory Servant</title>
@endsection

@section('content')
<style nonce="{{ $cspNonce ?? '' }}">
  .attendance-shell {
    min-height: calc(100vh - 72px);
    background: radial-gradient(1200px 600px at 85% 10%, rgba(251,146,60,.15), transparent),
                radial-gradient(900px 500px at 10% 90%, rgba(255,186,8,.12), transparent),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 4rem 0;
  }
  .att-panel {
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,.06);
    backdrop-filter: blur(10px);
    padding: 2.5rem 2.25rem;
    color: #1f2937;
  }
  .att-panel h2 { font-weight: 600; color: #111; }
  .att-panel .form-control,
  .att-panel .form-select {
    background: #fff;
    border: 1px solid rgba(0,0,0,.12);
    color: #111;
    border-radius: 12px;
    padding: 0.75rem 1rem;
  }
  .att-panel .form-control:focus,
  .att-panel .form-select:focus {
    background: #fffaf5;
    border-color: rgba(251,146,60,.8);
    box-shadow: 0 0 0 0.2rem rgba(251,146,60,.25);
  }
  .att-stepper {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  .att-stepper .step {
    flex: 1;
    padding: 0.75rem 1rem;
    border-radius: 16px;
    background: rgba(0,0,0,.03);
    border: 1px solid rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: .9rem;
    color: #4b5563;
  }
  .att-stepper .step.active {
    background: linear-gradient(135deg,#fb923c,#f97316);
    color: #fff;
    font-weight: 600;
    border-color: transparent;
  }
  .att-stepper .step span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 12px;
    background: rgba(0,0,0,.05);
    color: inherit;
    font-weight: 600;
  }
  .att-feedback {
    border-radius: 18px;
    padding: 1rem 1.25rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1.5rem;
  }
  .att-feedback.success {
    background: rgba(34,197,94,.08);
    border: 1px solid rgba(74,222,128,.25);
    color: #166534;
  }
  .att-feedback.duplicate {
    background: rgba(248,113,113,.1);
    border: 1px solid rgba(239,68,68,.25);
    color: #b91c1c;
  }
  .att-feedback .icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(0,0,0,.05);
    font-size: 1.25rem;
  }
  .att-input {
    position: relative;
  }
  .att-input-icon {
    position: absolute;
    top: 50%;
    left: 1rem;
    transform: translateY(-50%);
    color: rgba(17,24,39,.45);
    pointer-events: none;
  }
  .att-input input {
    padding-left: 3rem !important;
  }
  .att-helper { color: rgba(17,24,39,.55); font-size: .85rem; }
  .attendance-suggestions {
  position: absolute;
  right: 0;                 /* right-align to input */
  left: auto;               /* disable left:0 */
  bottom: calc(100% + 8px); /* put it ABOVE the input */
  top: auto;                /* disable top */
  width: min(420px, 100%);  /* tidy width, never overflow input */
  border-radius: 18px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(0,0,0,.08);
  box-shadow: 0 24px 45px rgba(0,0,0,.1);
  z-index: 50;
  max-height: 320px;
  display: flex;
  flex-direction: column;
}
@media (max-width: 575.98px) {
  .attendance-suggestions {
    width: 100%;            /* full width on small screens */
    right: 0;
  }
}
  .suggestion-card {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,.05);
    padding: 0.9rem 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.9rem;
    background: transparent;
    color: #111;
  }
  .suggestion-card:last-child { border-bottom: none; }
  .suggestion-card:hover,
  .suggestion-card.active {
    background: linear-gradient(135deg, rgba(251,146,60,.12), rgba(255,186,8,.12));
  }
  .suggestion-avatar {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: rgba(0,0,0,.05);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #111;
    text-transform: uppercase;
  }
  .suggestion-body strong { display: block; font-size: .95rem; color: #111; }
  .suggestion-meta { font-size: .8rem; color: rgba(17,24,39,.6); }
  .att-alert {
    margin-top: 0.75rem;
    border-radius: 14px;
    padding: 0.75rem 1rem;
    display: flex;
    gap: 0.75rem;
    align-items: center;
    background: rgba(251,146,60,.08);
    border: 1px solid rgba(251,146,60,.25);
    color: #7c2d12;
  }
  .att-alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    background: rgba(0,0,0,.05);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    color: #fb923c;
  }
  .att-alert.duplicate {
    background: rgba(248,113,113,.08);
    border: 1px solid rgba(239,68,68,.25);
    color: #991b1b;
  }
  .att-alert button { color: inherit; text-decoration: underline; }
  .guest-fields {
    margin-top: 1.5rem;
    padding: 1.25rem;
    border-radius: 18px;
    background: rgba(0,0,0,.02);
    border: 1px dashed rgba(0,0,0,.15);
    transition: all .25s ease;
    max-height: 0;
    overflow: hidden;
  }
  .guest-fields.is-visible {
    opacity: 1;
    transform: translateY(0);
    max-height: 600px;
  }
  .guest-fields:not(.is-visible) {
    opacity: 0;
    transform: translateY(-10px);
    pointer-events: none;
  }
  .btn-gradient {
    background: linear-gradient(135deg,#fb923c 0%,#f97316 100%);
    border: none;
    border-radius: 12px;
    padding: 0.85rem 1.2rem;
    font-weight: 600;
    color: #fff;
  }
  .btn-gradient:disabled {
    background: linear-gradient(135deg, rgba(251,146,60,.4), rgba(249,115,22,.4));
    color: rgba(255,255,255,.7);
  }
  .att-secondary-text { color: rgba(17,24,39,.6); font-size: .85rem; }
  .att-info-card {
    background: #fff;
    border-radius: 28px;
    padding: 2.5rem;
    border: 1px solid rgba(0,0,0,.06);
    color: #111;
    height: 100%;
    box-shadow: 0 24px 60px rgba(0,0,0,.08);
  }
  .att-info-card h3 { font-weight: 600; color: #000; }
  .att-info-card .tag {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .8rem;
    padding: .45rem .85rem;
    border-radius: 999px;
    background: rgba(251,146,60,.15);
    border: 1px solid rgba(251,146,60,.35);
    color: #7c2d12;
    text-transform: uppercase;
    letter-spacing: .05em;
  }
  .att-stats {
    margin-top: 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px,1fr));
    gap: 1rem;
  }
  .att-stat-card {
    padding: 1rem 1.25rem;
    border-radius: 18px;
    background: rgba(0,0,0,.02);
    border: 1px solid rgba(0,0,0,.08);
  }
  .att-stat-card span { display: block; font-size: .75rem; color: rgba(17,24,39,.6); }
  .att-stat-card strong { font-size: 1.2rem; color: #000; }
  .att-groups {
    margin-top: 2rem;
    display: grid;
    gap: .75rem;
  }
  .att-group-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .85rem 1rem;
    border-radius: 14px;
    background: rgba(0,0,0,.02);
    border: 1px solid rgba(0,0,0,.08);
  }
  .att-group-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: rgba(251,146,60,.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #c2410c;
    font-size: 1rem;
  }
  @media (max-width: 991.98px) {
    .att-panel { padding: 2rem 1.75rem; }
    .attendance-shell { padding: 2.5rem 0; }
    .att-info-card { padding: 2rem; }
  }
  @media (max-width: 575.98px) {
    .att-panel { border-radius: 20px; padding: 1.75rem 1.25rem; }
    .attendance-suggestions { top: calc(100% + 6px); border-radius: 16px; }
    .suggestion-card { padding: 0.75rem 0.9rem; }
    .att-stepper { flex-direction: column; }
  }
</style>

@php
  $campusGroup   = collect(json_decode($link->campus_group ?? '[]', true))->filter()->values();
  $ministryGroup = collect(json_decode($link->ministry_group ?? '[]', true))->filter()->values();
  $deptGroup     = collect(json_decode($link->dept_group ?? '[]', true))->filter()->values();
@endphp

<div class="attendance-shell">
  <div class="container">
    <div class="row g-4 align-items-stretch justify-content-center">
      <div class="col-12 col-lg-5">
        <div class="att-panel">
          <div class="d-flex align-items-center gap-3 mb-4">
            <img src="{{ asset('assets3/img/logo.png') }}" alt="Logo" style="height:48px;">
            <div>
              <h2 class="mb-0" style="font-size:1.4rem; color:#111;">{{ $link->title ?? 'Meeting Attendance' }}</h2>
            </div>
          </div>

          <div class="att-stepper mb-4">
            <div class="step active"><span>1</span>Verify Email</div>
            <div class="step"><span>2</span>Confirm Details</div>
          </div>

          @if(session('success'))
            <div class="att-feedback success">
              <div class="icon"><i class="bi bi-check2"></i></div>
              <div>
                <h5 class="mb-1 text-white">You&rsquo;re checked in!</h5>
                <p class="mb-0">{{ session('success') }} Thanks for confirming your attendance.</p>
              </div>
            </div>
          @endif

          @if(session('info'))
            <div class="att-feedback duplicate">
              <div class="icon"><i class="bi bi-exclamation-octagon"></i></div>
              <div>
                <h5 class="mb-1 text-white">Already checked in</h5>
                <p class="mb-0">{{ session('info') }} If you think this is an error, please contact your meeting coordinator.</p>
              </div>
            </div>
          @endif

          @if(($link->mode ?? 'auto') === 'auto')
            <form id="attendanceForm" method="POST" action="{{ route('meeting.attendance.resolve', $token) }}" novalidate>
              @csrf
              @auth
                <div class="att-secondary-text mb-3">
                  Hello <strong class="text-white">{{ auth()->user()->name ?? auth()->user()->email }}</strong>. Click the button to confirm your attendance.
                </div>
                <button type="submit" class="btn btn-gradient w-100" id="submitBtn">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSpinner"></span>
                  Mark Me Present
                </button>
              @else
                <div class="att-secondary-text mb-3">This link requires authentication. Please sign in to be marked present.</div>
                <a class="btn btn-gradient w-100" href="{{ route('login') }}">Log In</a>
              @endauth
            </form>
          @endif

          @if(($link->mode ?? 'auto') === 'form')
            <form id="attendanceForm" method="POST" action="{{ route('meeting.attendance.resolve', $token) }}" novalidate>
              @csrf
              <div class="mb-4">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <div class="att-input">
                <i class="att-input-icon bi bi-envelope"></i>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="attEmail" name="email"
                       value="{{ old('email') }}"
                       placeholder="you@example.com" autocomplete="off" required>
                {{-- Suggestions anchored to the input wrapper --}}
                <div id="emailSuggest" class="attendance-suggestions d-none" role="listbox" aria-label="Previous attendees"></div>
              </div>

              <div class="att-helper mt-2">We&rsquo;ll locate your profile instantly. If we can&rsquo;t find you, we&rsquo;ll ask for a few more details.</div>
              @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

              <div id="matchAlert" class="att-alert d-none mt-3">
                <div class="att-alert-icon"><i class="bi bi-check-circle"></i></div>
                <div>
                  <div class="fw-semibold" id="matchName">Profile matched</div>
                  <div class="att-helper mb-1">We locked in your details so you can confirm quickly.</div>
                  <button type="button" class="btn btn-sm btn-link p-0" id="editFields">Not you? Edit details</button>
                </div>
              </div>
            </div>

              <div id="progressiveFields" class="guest-fields">
                <div class="mb-3">
                  <label class="form-label">Your Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control text-uppercase" id="attEmployee" name="employee" value="{{ old('employee') }}" placeholder="FIRST LAST">
                </div>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">Campus</label>
                    <select class="form-select" id="attCampus" name="campus">
                      <option value="">Select campus</option>
                      @foreach($campusOptions as $opt)
                        <option value="{{ $opt->campus }}">{{ $opt->campus }}</option>
                      @endforeach
                    </select>
                    <small class="att-secondary-text">Helps your campus leads follow up.</small>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Ministry</label>
                    <select class="form-select" id="attMinistry" name="ministry">
                      <option value="">Select ministry</option>
                      @foreach($ministryOptions as $opt)
                        <option value="{{ $opt->ministry }}">{{ $opt->ministry }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select class="form-select" id="attDept" name="dept">
                      <option value="">Select department</option>
                      @foreach($deptOptions as $opt)
                        <option value="{{ $opt->department }}">{{ $opt->department }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-gradient w-100" id="submitBtn" disabled>
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSpinner"></span>
                  Confirm Attendance
                </button>
              </div>
            </form>
            <p class="att-secondary-text mt-4 mb-0">Need help? <a href="{{ url('/support') }}" class="link-light text-decoration-none">Contact support</a>.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>


<script id="attendance-config" type="application/json">{"suggestUrl": "{{ route('attendance.suggest') }}", "prefillEmail": "{{ old('email') ? strtolower(old('email')) : '' }}"}</script>
<script src="{{ asset('assets3/js/attendance-dynamic-form.js') }}" defer></script>

<!-- moved to external: attendance-dynamic-form.js -->

  if (!emailInput || !submitBtn) {
    return;
  }

  const fEmp = document.getElementById('attEmployee');
  const fCampus = document.getElementById('attCampus');
  const fMinistry = document.getElementById('attMinistry');
  const fDept = document.getElementById('attDept');

  let suggestions = [];
  let activeIndex = -1;
  let hasExact = false;

  const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

  const escHtml = (text = '') => text.replace(/[&<>"']/g, (m) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[m] || m));

  const hilite = (text, query) => {
    if (!query) return escHtml(text);
    const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&')})`, 'ig');
    return escHtml(text).replace(re, '<mark>$1</mark>');
  };

  const getInitial = (record) => {
    const source = (record.employee || record.email || '').trim();
    return source ? source.charAt(0).toUpperCase() : '?';
  };

  const selectByText = (selectEl, valueText) => {
    if (!selectEl) return;
    const target = (valueText || '').trim().toUpperCase();
    let matched = false;
    Array.from(selectEl.options).forEach((opt) => {
      if (opt.value.trim().toUpperCase() === target) {
        opt.selected = true;
        matched = true;
      }
    });
    if (!matched) {
      selectEl.value = '';
    }
  };

  const autofillProfile = (profile) => {
    emailInput.value = profile.email || emailInput.value;
    if (fEmp && profile.employee) fEmp.value = profile.employee.toUpperCase();
    selectByText(fCampus, profile.campus || '');
    selectByText(fMinistry, profile.ministry || '');
    selectByText(fDept, profile.dept || '');
  };

  const setLocked = (locked) => {
    [fEmp, fCampus, fMinistry, fDept].forEach((el) => {
      if (!el) return;
      if (el.tagName === 'SELECT') {
        el.disabled = locked;
      } else {
        el.readOnly = locked;
      }
      el.classList.toggle('form-control-plaintext', locked && el.tagName !== 'SELECT');
    });
  };

  const setGuestVisible = (show) => {
    if (!guestFields) return;
    guestFields.classList.toggle('is-visible', show);
    guestFields.setAttribute('aria-hidden', show ? 'false' : 'true');
  };
  setGuestVisible(false);

  const hideMatchAlert = () => {
    matchAlert?.classList.add('d-none');
  };

  const showMatchAlert = (displayName) => {
    if (!matchAlert) return;
    if (matchNameEl) {
      matchNameEl.textContent = displayName || 'Profile matched';
    }
    matchAlert.classList.remove('d-none');
  };

  const closeSuggestions = () => {
    if (suggestionBox) {
      suggestionBox.classList.add('d-none');
      suggestionBox.innerHTML = '';
    }
    suggestions = [];
    activeIndex = -1;
  };

  const renderSuggestions = (query) => {
    if (!suggestionBox) return;
    if (!suggestions.length) {
      closeSuggestions();
      return;
    }

    const html = suggestions.map((item, idx) => {
      const metaParts = [item.campus, item.ministry, item.dept].filter(Boolean);
      return `
        <button type="button" class="suggestion-card ${idx === activeIndex ? 'active' : ''}" data-index="${idx}">
          <div class="suggestion-avatar">${escHtml(getInitial(item))}</div>
          <div class="suggestion-body">
            <strong>${hilite(item.employee || item.email, query)}</strong>
            <div class="suggestion-meta">${hilite(item.email, query)}</div>
            ${metaParts.length ? `<div class="suggestion-meta">${metaParts.map(escHtml).join(' • ')}</div>` : ''}
          </div>
        </button>
      `;
    }).join('');

    suggestionBox.innerHTML = html;
    suggestionBox.classList.remove('d-none');
  };

  const setSubmitState = () => {
    const emailOk = isValidEmail(emailInput.value.trim());
    if (hasExact && emailOk) {
      submitBtn.disabled = false;
      return;
    }
    const nameOk = (fEmp?.value || '').trim().length > 1;
    submitBtn.disabled = !(emailOk && nameOk);
  };

  const probeExact = async (specificEmail = null) => {
    const value = (specificEmail || emailInput.value || '').trim().toLowerCase();
    if (!isValidEmail(value)) {
      setSubmitState();
      return;
    }

    try {
      const url = `{{ route('attendance.suggest') }}?email=${encodeURIComponent(value)}`;
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();

      if (data.match && data.profile) {
        hasExact = true;
        setLocked(true);
        setGuestVisible(false);
        autofillProfile(data.profile);
        showMatchAlert(data.profile.employee || data.profile.email);
      } else {
        hasExact = false;
        setLocked(false);
        hideMatchAlert();
        setGuestVisible(true);
      }

      closeSuggestions();
      setSubmitState();
    } catch (error) {
      console.error(error);
    }
  };

  const debounce = (fn, delay) => {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  };

  const querySuggest = debounce(async () => {
    const query = emailInput.value.trim().toLowerCase();
    if (!query) {
      hasExact = false;
      hideMatchAlert();
      setGuestVisible(false);
      setLocked(false);
      closeSuggestions();
      setSubmitState();
      return;
    }

    try {
      const url = `{{ route('attendance.suggest') }}?email=${encodeURIComponent(query)}`;
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();

      if (data.match && data.profile) {
        hasExact = true;
        setLocked(true);
        setGuestVisible(false);
        autofillProfile(data.profile);
        showMatchAlert(data.profile.employee || data.profile.email);
        closeSuggestions();
      } else {
        hasExact = false;
        hideMatchAlert();
        setLocked(false);
        setGuestVisible(false);
        suggestions = Array.isArray(data.suggestions) ? data.suggestions : [];
        activeIndex = -1;
        renderSuggestions(query);
      }

      setSubmitState();
    } catch (error) {
      console.error(error);
    }
  }, 200);

  emailInput.addEventListener('input', querySuggest);
  emailInput.addEventListener('focus', querySuggest);

  emailInput.addEventListener('blur', () => {
    setTimeout(() => {
      if (!suggestionBox || suggestionBox.classList.contains('d-none')) {
        probeExact();
      }
    }, 150);
  });

  emailInput.addEventListener('keydown', (event) => {
    if (!suggestionBox || suggestionBox.classList.contains('d-none')) return;
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      activeIndex = (activeIndex + 1) % suggestions.length;
      renderSuggestions(emailInput.value.trim());
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      activeIndex = (activeIndex - 1 + suggestions.length) % suggestions.length;
      renderSuggestions(emailInput.value.trim());
    } else if (event.key === 'Enter') {
      if (activeIndex > -1) {
        event.preventDefault();
        probeExact(suggestions[activeIndex]?.email || null);
      }
    } else if (event.key === 'Escape') {
      closeSuggestions();
    }
  });

  suggestionBox?.addEventListener('click', (event) => {
    const btn = event.target.closest('[data-index]');
    if (!btn) return;
    const record = suggestions[Number(btn.dataset.index)];
    if (record) {
      probeExact(record.email);
    }
  });

  document.addEventListener('click', (event) => {
    if (!suggestionBox || suggestionBox.classList.contains('d-none')) return;
    if (event.target === emailInput || suggestionBox.contains(event.target)) return;
    closeSuggestions();
  });

  editBtn?.addEventListener('click', () => {
    hideMatchAlert();
    hasExact = false;
    setLocked(false);
    setGuestVisible(true);
    setSubmitState();
  });

  fEmp?.addEventListener('input', setSubmitState);

  // handled by external via prefillEmail
  setSubmitState();
-->


{{-- Minimal UX script moved to external: attendance-dynamic-form.js --}}
@endsection
