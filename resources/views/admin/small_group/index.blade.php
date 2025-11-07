@extends('layouts.admin')

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">groups</i> Small Groups</h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#groupModal">
        <i class="material-icons-outlined me-1">group_add</i> New Group
      </button>
    </div>

    

    <div class="row g-3">
      {{-- groups list --}}
      <div class="col-xl-4">
        <div class="card"><div class="card-body">
          <h6>Groups</h6>
          <div class="list-group">
            @foreach($groups as $g)
              <a href="{{ route('small.index',['group_id'=>$g->id]) }}"
                 class="list-group-item list-group-item-action {{ (int)$activeId===$g->id ? 'active' : '' }}">
                 <div>{{ $g->name }}</div>
                 <small>Leader: {{ $g->leader_name ?: '—' }}</small>
                 <span class="badge bg-secondary">{{ $g->members_count }}</span>
              </a>
            @endforeach
          </div>
        </div></div>
      </div>

      {{-- active group --}}
      <div class="col-xl-8">
        @if($activeGroup)
        <div class="card"><div class="card-body">
          <h6 class="mb-3">{{ $activeGroup->name }}</h6>

          {{-- Leader --}}
          <form method="post" action="{{ route('small.leader',$activeGroup->id) }}" class="mb-3">
            @csrf
            <label class="form-label">Leader</label>
            <select name="leader_id" class="form-select">
              <option value="">— None —</option>
              @foreach($people as $p)
                <option value="{{ $p->id }}" {{ $activeGroup->leader_id==$p->id?'selected':'' }}>
                  {{ $p->firstname }} {{ $p->lastname }}
                  @if(!empty($p->campus)) — {{ $p->campus }} @endif
                </option>
              @endforeach
            </select>
            <button class="btn btn-outline-primary mt-2">Save Leader</button>
          </form>

                    {{-- ===== Members block ===== --}}
<div class="row g-3 align-items-start">

  {{-- LEFT: sync members (form wraps only this column) --}}
  <div class="col-md-6">
    <form id="syncForm" method="post" action="{{ route('small.sync', $activeGroup->id) }}">
      @csrf

      {{-- Controls --}}
      <div class="d-flex align-items-center gap-2 mb-2">
        <input id="peopleSearch" type="search" class="form-control" placeholder="Search name, campus, ministry" style="max-width: 320px;">
        <select id="perPage" class="form-select" style="width: 120px;">
          <option value="10">10 / page</option>
          <option value="20" selected>20 / page</option>
          <option value="50">50 / page</option>
        </select>
        <span id="selectedCount" class="ms-auto small text-muted">0 selected</span>
      </div>

      {{-- People list --}}
      <div id="peopleList" class="border rounded-3" style="max-height:420px;overflow:auto"></div>

      {{-- Pager --}}
      <div class="d-flex justify-content-between align-items-center mt-2">
        <button class="btn btn-sm btn-outline-secondary" id="prevPage" type="button">Previous</button>
        <span id="rangeInfo" class="small text-muted">0–0 of 0</span>
        <button class="btn btn-sm btn-outline-secondary" id="nextPage" type="button">Next</button>
      </div>

      {{-- Hidden sink stays INSIDE the form --}}
      <div id="selectedSink"></div>

      <button class="btn btn-primary mt-3" type="submit">Save Members</button>
    </form>
  </div>

  {{-- RIGHT: current members (no nesting) --}}
  <div class="col-md-6">
    <ul class="list-group" id="currentMembers">
      @foreach($members as $m)
        <li class="list-group-item d-flex align-items-center justify-content-between">
          <div class="me-2">
            <div class="fw-semibold">{{ $m->firstname }} {{ $m->lastname }}</div>
            @if(!empty($m->email))<div class="small text-muted">{{ $m->email }}</div>@endif
            @if(!empty($m->campus) || !empty($m->ministry))
              <div class="small text-muted">
                {{ $m->campus ? 'Campus: '.$m->campus : '' }} {{ $m->ministry ? '— Ministry: '.$m->ministry : '' }}
              </div>
            @endif
          </div>
          <div class="d-flex align-items-center gap-2">
            @if($m->is_leader)
              <span class="badge bg-success">Leader</span>
            @endif
            <form method="post"
                  action="{{ route('small.remove', ['group'=>$activeGroup->id, 'person'=>$m->volunteer_id]) }}"
                  onsubmit="return confirm('Remove this member from the group?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
            </form>
          </div>
        </li>
      @endforeach
    </ul>
  </div>

</div>
        </div></div>
        @else
          <div class="alert alert-info">Select a group on the left to manage.</div>
        @endif
      </div>
    </div>
  </div>
</main>
<script>
                (() => {
                  // ---- Data from backend (must be set by the controller) ----
                  const PEOPLE = Array.isArray(@json($peopleArray ?? [])) ? @json($peopleArray ?? []) : [];
                  const PRESELECTED = new Set(Array.isArray(@json($memberIds ?? [])) ? @json($memberIds ?? []) : []);

                  // ---- Grab nodes (they MUST exist now) ----
                  const $search     = document.getElementById('peopleSearch');
                  const $perPage    = document.getElementById('perPage');
                  const $list       = document.getElementById('peopleList');
                  const $prev       = document.getElementById('prevPage');
                  const $next       = document.getElementById('nextPage');
                  const $rangeInfo  = document.getElementById('rangeInfo');
                  const $selCount   = document.getElementById('selectedCount');
                  const $sink       = document.getElementById('selectedSink');

                  // ---- Quick diagnostics (will show on screen if something critical is missing) ----
                  function panic(msg){
                    console.error(msg);
                    if ($list) $list.innerHTML = `<div class="p-3 text-danger">${msg}</div>`;
                  }
                  if (!$list) return panic('peopleList not found. Ensure the Members UI IDs are present.');
                  if (!$prev || !$next || !$rangeInfo || !$sink) return panic('Pagination or hidden sink elements are missing.');
                  console.log('People loaded:', PEOPLE.length, 'Preselected:', PRESELECTED.size);

                  // ---- State ----
                  const state = {
                    term: '',
                    page: 1,
                    perPage: (function(){ const v = parseInt($perPage?.value,10); return Number.isFinite(v)&&v>0?v:20; })(),
                    selected: new Set([...PRESELECTED]),
                  };

                  // ---- Helpers ----
                  const norm = s => String(s ?? '').toLowerCase().trim();

                  function filtered(){
                    if (!state.term) return PEOPLE;
                    const t = norm(state.term);
                    return PEOPLE.filter(p =>
                      norm(p.name).includes(t) ||
                      norm(p.campus).includes(t) ||
                      norm(p.ministry).includes(t)
                    );
                  }

                  function pageSlice(rows){
                    const start = (state.page - 1) * state.perPage;
                    return rows.slice(start, start + state.perPage);
                  }

                  function syncHidden(){
                    $sink.innerHTML = '';
                    state.selected.forEach(id => {
                      const input = document.createElement('input');
                      input.type = 'hidden';
                      input.name = 'volunteer_ids[]';
                      input.value = id;
                      $sink.appendChild(input);
                    });
                  }

                  function updateSelectedCount(){
                    if ($selCount) $selCount.textContent = `${state.selected.size} selected`;
                  }

                  function render(){
                    const rows  = filtered();
                    const total = rows.length;
                    const maxPg = Math.max(1, Math.ceil(total / state.perPage));
                    if (state.page > maxPg) state.page = maxPg;

                    const slice = pageSlice(rows);
                    $list.innerHTML = '';

                    if (!total) {
                      $list.innerHTML = `<div class="p-3">No people found.</div>`;
                    } else if (!slice.length) {
                      $list.innerHTML = `<div class="p-3">No results on this page.</div>`;
                    } else {
                      slice.forEach(p => {
                        const id = String(p.id);
                        const campus   = p.campus ? `Campus: ${p.campus}` : '';
                        const ministry = p.ministry ? `Ministry: ${p.ministry}` : '';
                        const sub = [campus, ministry].filter(Boolean).join(' — ');
                        const row = document.createElement('div');
                        row.className = 'form-check border-bottom py-2 px-5 d-flex align-items-start gap-2';
                        row.innerHTML = `
                          <input class="form-check-input mt-1" type="checkbox" id="p-${id}" value="${id}">
                          <label class="form-check-label w-100" for="p-${id}">
                            <div class="fw-semibold">${p.name || '(no name)'}</div>
                            ${sub ? `<div class="text-muted small">${sub}</div>` : ''}
                          </label>
                        `;

                        // Whole row toggles the checkbox (except when clicking the checkbox itself)
                        row.addEventListener('click', (e) => {
                          if (e.target.matches('input[type="checkbox"]')) return;
                          const cb = row.querySelector('input[type="checkbox"]');
                          cb.checked = !cb.checked;
                          cb.dispatchEvent(new Event('change', { bubbles: true }));
                        });

                        const cb = row.querySelector('input[type="checkbox"]');
                        cb.checked = state.selected.has(Number(id));
                        cb.addEventListener('change', (e) => {
                          const pid = Number(e.target.value);
                          if (e.target.checked) state.selected.add(pid);
                          else state.selected.delete(pid);
                          syncHidden(); updateSelectedCount();
                        });

                        $list.appendChild(row);
                      });
                    }

                    const start = total ? ((state.page - 1) * state.perPage + 1) : 0;
                    const end   = Math.min(state.page * state.perPage, total);
                    $rangeInfo.textContent = `${start}–${end} of ${total}`;
                    $prev.disabled = state.page <= 1;
                    $next.disabled = state.page >= maxPg;

                    updateSelectedCount();
                    syncHidden();
                  }

                  // ---- Events ----
                  let debounceId = null;
                  function debounce(fn, delay){ return function(...args){ clearTimeout(debounceId); debounceId = setTimeout(()=>fn.apply(this,args), delay); }; }

                  $search?.addEventListener('input', debounce(e => {
                    state.term = norm(e.target.value);
                    state.page = 1;
                    render();
                  }, 120));

                  $perPage?.addEventListener('change', e => {
                    const v = parseInt(e.target.value, 10);
                    state.perPage = Number.isFinite(v) && v > 0 ? v : 20;
                    state.page = 1;
                    render();
                  });

                  $prev.addEventListener('click', () => { if (state.page > 1) { state.page--; render(); }});
                  $next.addEventListener('click', () => { state.page++; render(); });

                  // ---- First paint ----
                  render();
                })();
              </script>
{{-- modal new group --}}
<div class="modal fade" id="groupModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="post" action="{{ route('small.store') }}">
      @csrf
      <div class="modal-header"><h5>Create Group</h5></div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control mb-2" placeholder="Group Name" required>
        <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
        <select name="leader_id" class="form-select">
          <option value="">— Leader (optional) —</option>
          @foreach($people as $p)
            <option value="{{ $p->id }}">
              {{ $p->firstname }} {{ $p->lastname }}@if(!empty($p->campus)) — {{ $p->campus }}@endif
            </option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div></div>
</div>
@endsection

