@extends('layouts.admin')

@section('meta')
  <title>Wellness | Dashboard</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    {{-- DEBUG: members state --}}
    @if($members->isEmpty())
      <div class="alert alert-warning">
        ⚠️ No members found for your groups.
        <div class="small text-secondary mt-1">
          Make sure you’re set as a leader on at least one group, or ask an admin to assign you.
        </div>
      </div>
    @else
      <div class="alert alert-success">✅ Members loaded: {{ $members->count() }}</div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">favorite</i> Wellness Dashboard</h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkinModal">
        <i class="material-icons-outlined me-1">playlist_add_check</i> New Check-in
      </button>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <strong>There was a problem:</strong>
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="row g-3">
      {{-- My Members --}}
      <div class="col-xl-6">
        <div class="card rounded-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0">My Members</h6>
              <input id="memberSearch" class="form-control form-control-sm" placeholder="Search name…" style="max-width: 220px;">
            </div>
            <div id="memberList" class="list-group">
              @foreach($members as $m)
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                  <div>
                    <div class="fw-semibold">{{ $m->firstname }} {{ $m->lastname }}</div>
                    <div class="text-secondary small">{{ $m->group_name }}</div>
                  </div>
                  <button class="btn btn-sm btn-outline-primary"
                          data-bs-toggle="modal"
                          data-bs-target="#checkinModal"
                          data-volunteer="{{ $m->volunteer_id }}"
                          data-group="{{ $m->group_id }}"
                          data-label="{{ $m->firstname.' '.$m->lastname.' — '.$m->group_name }}">
                    Check-in
                  </button>
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- My Open Cases --}}
      <div class="col-xl-6">
        <div class="card rounded-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0">My Open Cases</h6>
              <a href="{{ route('wellness.cases.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>

            @forelse($myCases as $c)
              <a href="{{ route('wellness.cases.show',$c->id) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                <div class="me-3">
                  <div class="fw-semibold">{{ $c->volunteer_name }}</div>
                  <div class="text-secondary small">{{ $c->group_name }}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge {{ $c->severity==='high'?'bg-danger':($c->severity==='medium'?'bg-warning text-secondary':'bg-success') }}">
                    {{ strtoupper($c->severity) }}
                  </span>
                  <span class="badge bg-light text-secondary">{{ strtoupper($c->status) }}</span>
                </div>
              </a>
            @empty
              <div class="text-secondary">No open cases.</div>
            @endforelse
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

{{-- New Check-in Modal --}}
<div class="modal fade" id="checkinModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" method="post" action="{{ route('wellness.checkins.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">New Wellness Check-in</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- Person & Group --}}
        <div class="row g-2 mb-3">
          <div class="col-md-8">
            <label class="form-label">Volunteer</label>
            <select class="form-select" id="volunteerSelect" name="volunteer_id" required>
              <option value="">Loading…</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Group</label>
            <input type="hidden" name="group_id" id="groupInput" required>
            <input type="text" class="form-control" id="groupDisplay" placeholder="Auto" disabled>
          </div>
        </div>

        <div class="row g-3">
          {{-- A — Attendance + Prayer --}}
          <div class="col-md-6">
            <label class="form-label">Attendance & Prayer</label>
            <div class="border rounded-3 p-3">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="1" id="came" name="attended_service">
                <label class="form-check-label" for="came">Came to service this week</label>
              </div>
              <label class="form-label">Prayer Request (optional)</label>
              <textarea class="form-control" name="prayer_request" rows="3" placeholder="Prayer needs…"></textarea>
            </div>
          </div>

          {{-- B — Sliders --}}
          <div class="col-md-6">
            <label class="form-label">Wellness (1–5)</label>
            <div class="border rounded-3 p-3">
              <div class="mb-2">
                <label class="form-label d-flex justify-content-between">
                  <span>Emotional</span><span id="emoVal" class="text-secondary">3</span>
                </label>
                <input type="range" class="form-range" min="1" max="5" value="3" name="emotional" oninput="emoVal.textContent=this.value">
              </div>
              <div class="mb-2">
                <label class="form-label d-flex justify-content-between">
                  <span>Physical</span><span id="phyVal" class="text-secondary">3</span>
                </label>
                <input type="range" class="form-range" min="1" max="5" value="3" name="physical" oninput="phyVal.textContent=this.value">
              </div>
              <div class="">
                <label class="form-label d-flex justify-content-between">
                  <span>Spiritual</span><span id="spiVal" class="text-secondary">3</span>
                </label>
                <input type="range" class="form-range" min="1" max="5" value="3" name="spiritual" oninput="spiVal.textContent=this.value">
              </div>
            </div>
          </div>

          {{-- C — Notes/Concerns --}}
          <div class="col-12">
            <label class="form-label">Notes (optional)</label>
            <textarea class="form-control" name="notes" rows="3" placeholder="Observations, context, etc."></textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Concerns (optional)</label>
            <div class="d-flex flex-wrap gap-2">
              @php
                $concernTags = ['Health','Family','Work','Emotional','Spiritual','Financial','Relationships','Bereavement'];
              @endphp
              @foreach($concernTags as $tag)
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="concerns[]" value="{{ $tag }}" id="c-{{ $tag }}">
                  <label class="form-check-label" for="c-{{ $tag }}">{{ $tag }}</label>
                </div>
              @endforeach
            </div>
          </div>

          {{-- D — Next actions --}}
          <div class="col-12">
            <label class="form-label">Planned Actions (optional)</label>
            <input type="text" class="form-control mb-2" name="actions[]" placeholder="e.g., Call mid-week">
            <input type="text" class="form-control mb-2" name="actions[]" placeholder="e.g., Share scripture & pray">
          </div>

          <div class="col-md-4">
            <label class="form-label">Check-in Date</label>
            <input type="date" class="form-control" name="checked_at" value="{{ now()->toDateString() }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select class="form-select" name="type">
              <option value="in_person">In person</option>
              <option value="call">Call</option>
              <option value="message">Message</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Submit Check-in</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script id="members-data" type="application/json">{!! $memberOptionsJson !!}</script>
<script>
(() => {
  const list    = document.getElementById('memberList');
  const search  = document.getElementById('memberSearch');
  const modal   = document.getElementById('checkinModal');
  const vSelect = document.getElementById('volunteerSelect');
  const gInput  = document.getElementById('groupInput');
  const gDisp   = document.getElementById('groupDisplay');

  // Cache (declare once)
  let membersLoaded = false;
  let membersCache  = [];

  function loadMembers() {
    if (membersLoaded) return Promise.resolve(membersCache);
    try {
      const el  = document.getElementById('members-data');
      const txt = el ? (el.textContent || el.innerText || '') : '';
      membersCache = txt ? JSON.parse(txt) : [];
    } catch (e) {
      console.error('Failed to parse embedded members JSON', e);
      membersCache = [];
    }
    membersLoaded = true;
    return Promise.resolve(membersCache);
  }

  // Pre-populate select at page load
  document.addEventListener('DOMContentLoaded', async () => {
    const data = await loadMembers();
    populateSelect(data);
  });

  function populateSelect(members) {
    vSelect.innerHTML = '<option value="">Select volunteer…</option>';
    for (const m of members) {
      const opt = document.createElement('option');
      opt.value = String(m.id);
      opt.textContent = m.label || ('ID ' + m.id);
      opt.setAttribute('data-group', String(m.group_id || ''));
      vSelect.appendChild(opt);
    }
  }

  function syncGroup() {
    const opt = vSelect.selectedOptions[0];
    const gid = opt?.getAttribute('data-group') || '';
    gInput.value = gid;
    const txt = opt?.textContent || '';
    gDisp.value = txt.includes('—') ? txt.split('—').pop().trim() : '';
  }

  vSelect?.addEventListener('change', syncGroup);

  // Simple client-side search on left list
  if (search && list) {
    search.addEventListener('input', () => {
      const term = search.value.toLowerCase();
      [...list.querySelectorAll('.list-group-item')].forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }

  // Prefill from "Check-in" button
  modal?.addEventListener('show.bs.modal', async (event) => {
    const btn   = event.relatedTarget;
    const preVol= btn?.getAttribute('data-volunteer') || '';
    const preGrp= btn?.getAttribute('data-group') || '';
    const preLbl= btn?.getAttribute('data-label') || '';

    if (!membersLoaded) {
      const data = await loadMembers();
      populateSelect(data);
    }

    vSelect.value = '';
    gInput.value  = '';
    gDisp.value   = '';

    if (preVol) {
      const opt = vSelect.querySelector(`option[value="${CSS.escape(String(preVol))}"]`);
      vSelect.value = opt ? String(preVol) : '';
      syncGroup();
      if (preGrp) gInput.value = preGrp;
      if (preLbl && preLbl.includes('—')) gDisp.value = preLbl.split('—').pop().trim();
    }
  });
})();
</script>
@endsection