<!--start header-->
@include('layouts/admin')
<!--end top header-->
@php
  // Pull only what's needed by the UI
  $policySlim = collect(config('report_policies.categories', []))
      ->map(fn($p) => [
          'value_type'     => $p['value_type'] ?? null,
          'allowed_status' => $p['allowed_status'] ?? null,
          'allow_percent'  => $p['allow_percent']  ?? false,   // for status_or_percent
          'allow_number'   => $p['allow_number']   ?? false,   // for status_or_percent
      ])->all();
@endphp

<script>
  window.categoryPolicies = @json($policySlim);
</script>
<!--start main wrapper-->
<main class="main-wrapper">
  <div class="main-content">

    <div class="card mb-3">
      <div class="card-body">
        <h1 class="h4 text-center m-0">REPORT ENTRY</h1>
      </div>
    </div>

    @if(session('ok'))
      <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="row g-3">
      <!-- CENTER: selected team members -->
      <div class="col-lg-9">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="fw-bold">
              @if($team) Team: <span class="text-primary">{{ $team->name }}</span> @else Select a team @endif
            </div>
          </div>
          <div class="card-body p-0">
            @if(isset($team) && $members->count())
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th>Volunteer</th>
                      <th>IDNO</th>
                      <th style="width:140px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($members as $m)
                      <tr>
                        <td>{{ $m->last_name }}, {{ $m->first_name }}</td>
                        <td>{{ $m->idno }}</td>
                        <td>
                          <button class="btn btn-sm btn-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#eventModal"
                                  data-person="{{ $m->id }}"
                                  data-name="{{ $m->last_name }}, {{ $m->first_name }}">
                            Make report
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @elseif(isset($team))
              <div class="p-3 text-secondary">No active members in this team.</div>
            @else
              <div class="p-3 text-secondary">Pick a team from the list.</div>
            @endif
          </div>
        </div>
      </div>

      <!-- RIGHT: team list -->
      <div class="col-lg-3">
        <div class="card">
          <div class="card-header fw-bold text-center">Teams</div>
          <div class="list-group list-group-flush">
            @foreach($teams as $t)
              <a href="{{ route('admin.reports.entry.team', $t->id) }}"
                 class="list-group-item list-group-item-action {{ isset($team) && $team->id==$t->id ? 'active' : '' }}">
                 {{ $t->name }}
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Pass assignments (person → metrics[]) --}}
{{-- Pass person → metrics[] (already shaped in controller) --}}
<script>
  window.personMetrics = @json($assignments ?? []);

  // Build { "<setId>": [{code,label,score}, ...] } from tbl_report_status_set_items
  window.statusSetOptions = (function () {
    const items = @json($statusSetItems ?? collect());
    const map = {};
    for (const it of items) {
      const sid = String(it.status_set_id);
      (map[sid] ||= []).push({
        code:  String(it.code ?? ''),
        label: String(it.label ?? it.code ?? ''),
        score: Number(it.score ?? 0),
      });
    }
    return map;
  })();
</script>

<!-- MODAL: Daily report entry -->
<!-- MODAL: Daily report entry (bulk for one volunteer) -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <form method="post" action="{{ route('admin.reports.entry.events.store') }}" id="eventForm">
        @csrf
        <input type="hidden" name="person_id" id="person_id_modal">
        <input type="hidden" name="bulk" value="1">

        <div class="modal-header">
          <h5 class="modal-title">
            Daily Report — <span id="volunteer_name_label" class="text-primary"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          {{-- top row: date/source/note --}}
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <label class="form-label">Date</label>
              <input type="date" name="occurred_on" id="occurred_on_modal" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Source</label>
              <input name="source" class="form-control" placeholder="manual">
            </div>
            <div class="col-md-6">
              <label class="form-label">Note (applies to all items, optional)</label>
              <input name="note" class="form-control" placeholder="e.g., Shift notes, show details…">
            </div>
          </div>

          {{-- CATEGORY TABS --}}
          <ul class="nav nav-tabs" id="metricTabs" role="tablist"></ul>

          {{-- TAB PANES (one per category) --}}
          <div class="tab-content border border-top-0 rounded-bottom p-0" id="metricTabContent">
            <!-- injected by JS -->
          </div>

          <div class="mt-2 text-secondary small d-flex gap-3 flex-wrap">
            <span id="filledCount">0 item(s) filled.</span>
            <span id="filledCountPerTab" class="text-muted"></span>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save All</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  // from your page
  const assignRaw       = window.personMetrics ?? {};
  const assignMap       = normalizeAssignMap(assignRaw);
  const setOptionsMap   = window.statusSetOptions || {}; // { "<setId>": [{code,label,score}, ...] }

  const modal           = document.getElementById('eventModal');
  const personInput     = document.getElementById('person_id_modal');
  const nameLabel       = document.getElementById('volunteer_name_label');
  const dateInput       = document.getElementById('occurred_on_modal');

  const tabsEl          = document.getElementById('metricTabs');
  const panesEl         = document.getElementById('metricTabContent');

  const filledEl        = document.getElementById('filledCount');
  const filledPerTabEl  = document.getElementById('filledCountPerTab');

  // init date (today)
  if (dateInput) dateInput.value = new Date().toISOString().slice(0,10);

  // ---------- helpers ----------
  function normalizeAssignMap(src){
    if (!Array.isArray(src) && src && typeof src === 'object') return src;
    if (Array.isArray(src)) {
      const out = {};
      for (const r of src) {
        if (!r || r.person_id == null) continue;
        const k = String(r.person_id);
        (out[k] ||= []).push({
          metric_id:     Number(r.metric_id),
          metric_name:   r.metric_name ?? '',
          category_name: r.category_name ?? '',
          value_mode:    r.value_mode ?? '',
          status_set_id: r.status_set_id ?? null,
        });
      }
      return out;
    }
    return {};
  }

  function slugify(s){
    return String(s || 'Uncategorized')
      .toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'') || 'uncategorized';
  }

  function toDisplayNumber(val, decimals = 0){
    if (val === null || val === undefined || val === '') return '';
    const num = Number(val);
    if (!Number.isFinite(num)) return '';
    return decimals === 0 ? String(Math.round(num)) : String(parseFloat(num.toFixed(decimals)));
  }

  function valueCellHTML(m){
    const isSet = String(m.value_mode).toLowerCase() === 'status_set';
    if (isSet && m.status_set_id && setOptionsMap[String(m.status_set_id)]) {
      const opts = ['<option value="">— choose —</option>']
        .concat(setOptionsMap[String(m.status_set_id)].map(o =>
          `<option value="${o.code}" data-score="${o.score}">${o.label}</option>`))
        .join('');
      return `
        <select class="form-select form-select-sm metric-status">
          ${opts}
        </select>
        <input type="hidden" name="metrics[${m.metric_id}][status]" class="metric-status-hidden">
      `;
    }
    return `<span class="text-secondary">—</span>
            <input type="hidden" name="metrics[${m.metric_id}][status]" class="metric-status-hidden" value="">`;
  }

  function scoreCellHTML(m){
    const isSet = String(m.value_mode).toLowerCase() === 'status_set';
    return `<input type="number" min="0" max="100" step="1" inputmode="numeric"
                   class="form-control form-control-sm metric-score"
                   name="metrics[${m.metric_id}][numeric_value]"
                   placeholder="0–100" ${isSet ? 'readonly' : ''}>`;
  }

  function buildRowHTML(m){
    return `
      <tr data-metric-id="${m.metric_id}">
        <td>
          <div class="fw-semibold">${m.metric_name}</div>
          <div class="text-secondary small">${m.category_name || '—'}</div>
          <input type="hidden" name="metrics[${m.metric_id}][metric_id]" value="${m.metric_id}">
        </td>
        <td>${valueCellHTML(m)}</td>
        <td>${scoreCellHTML(m)}</td>
        <td>
          <input type="text" class="form-control form-control-sm"
                 name="metrics[${m.metric_id}][note]" placeholder="optional">
        </td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-outline-secondary clear-row">Clear</button>
        </td>
      </tr>
    `;
  }

  function wireRow(tr){
    const sel        = tr.querySelector('.metric-status');
    const statusHid  = tr.querySelector('.metric-status-hidden');
    const scoreInput = tr.querySelector('.metric-score');

    if (sel) {
      sel.addEventListener('change', () => {
        const opt   = sel.selectedOptions[0];
        const score = opt ? opt.getAttribute('data-score') : '';
        if (statusHid)  statusHid.value  = sel.value || '';
        if (scoreInput) scoreInput.value = score ? toDisplayNumber(score, 0) : '';
        updateFilled();
      });
    }

    tr.querySelector('.clear-row')?.addEventListener('click', () => {
      if (sel) sel.value = '';
      if (statusHid) statusHid.value = '';
      if (scoreInput) scoreInput.value = '';
      const note = tr.querySelector('input[name$="[note]"]'); if (note) note.value = '';
      updateFilled();
    });

    scoreInput?.addEventListener('input', updateFilled);
  }

  // Build tabs + tables for a category → returns tbody element
  function ensureCategoryPane(catName, isActive=false){
    const slug = slugify(catName);
    const tabId = `tab-${slug}`;
    const paneId = `pane-${slug}`;
    // tab
    if (!document.getElementById(tabId)){
      const li = document.createElement('li');
      li.className = 'nav-item';
      li.role = 'presentation';
      li.innerHTML = `
        <button class="nav-link ${isActive?'active':''}" id="${tabId}" data-bs-toggle="tab"
                data-bs-target="#${paneId}" type="button" role="tab" aria-controls="${paneId}"
                aria-selected="${isActive?'true':'false'}">
          ${catName}
        </button>`;
      tabsEl.appendChild(li);
    }
    // pane
    if (!document.getElementById(paneId)){
      const div = document.createElement('div');
      div.className = `tab-pane fade ${isActive?'show active':''}`;
      div.id = paneId; div.role = 'tabpanel'; div.ariaLabel = `Metrics in ${catName}`;
      div.innerHTML = `
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:32%">Metric</th>
                <th style="width:30%">Value</th>
                <th style="width:16%">Score (0–100)</th>
                <th style="width:16%">Per-item note</th>
                <th style="width:6%"  class="text-end">Clear</th>
              </tr>
            </thead>
            <tbody id="tbody-${slug}"></tbody>
          </table>
        </div>`;
      panesEl.appendChild(div);
    }
    return document.getElementById(`tbody-${slug}`);
  }

  // Group metrics by category and render each group into its tab
  function renderByCategory(list){
    tabsEl.innerHTML  = '';
    panesEl.innerHTML = '';

    if (!Array.isArray(list) || list.length === 0){
      // Make a dummy tab/pane with empty message
      ensureCategoryPane('No Metrics', true).innerHTML =
        `<tr><td colspan="5" class="text-secondary">No metrics assigned to this person.</td></tr>`;
      updateFilled();
      return;
    }

    // group
    const groups = {};
    for (const m of list){
      const cat = m.category_name || 'Uncategorized';
      (groups[cat] ||= []).push(m);
    }

    // render in stable order (Attendance, Productivity first if present)
    const order = Object.keys(groups).sort((a,b) => {
      const p = (s) => (s.toLowerCase().startsWith('attendance') ? 0
                      : s.toLowerCase().startsWith('productivity') ? 1
                      : 2);
      const pa = p(a), pb = p(b);
      return pa === pb ? a.localeCompare(b) : (pa - pb);
    });

    order.forEach((catName, i) => {
      const tbody = ensureCategoryPane(catName, i===0);
      tbody.innerHTML = '';
      groups[catName].forEach(m => {
        tbody.insertAdjacentHTML('beforeend', buildRowHTML(m));
      });
    });

    // wire after all DOM is in
    panesEl.querySelectorAll('tbody tr').forEach(wireRow);

    updateFilled();
  }

  // recompute completed counts
  function updateFilled(){
    let total = 0;
    const perTab = [];
    panesEl.querySelectorAll('.tab-pane').forEach(pane => {
      let n = 0;
      pane.querySelectorAll('tbody tr').forEach(tr => {
        const num = tr.querySelector('.metric-score')?.value?.trim() || '';
        const sel = tr.querySelector('.metric-status')?.value?.trim() || '';
        if (num || sel) n++;
      });
      total += n;
      // read tab label
      const tabButton = tabsEl.querySelector(`[data-bs-target="#${pane.id}"]`);
      const label = tabButton ? tabButton.textContent.trim() : 'Tab';
      perTab.push(`${label}: ${n}`);
    });

    if (filledEl) filledEl.textContent = `${total} item(s) filled.`;
    if (filledPerTabEl) filledPerTabEl.textContent = perTab.join(' • ');
  }

  // Prefill existing day values into ALL tab bodies
  async function prefillFor(personId, dateStr){
    try {
      const url = `{{ route('admin.reports.entry.events.day') }}?person_id=${encodeURIComponent(personId)}&date=${encodeURIComponent(dateStr)}`;
      const res = await fetch(url, {headers:{'Accept':'application/json'}});
      if (!res.ok) throw new Error('fetch failed');
      const map = await res.json(); // { metric_id: {status, numeric_value} }

      panesEl.querySelectorAll('tbody tr').forEach(tr => {
        const mid = Number(tr.getAttribute('data-metric-id'));
        const found = map[mid];
        if (!found) return;

        // score
        const scoreInput = tr.querySelector('.metric-score');
        if (scoreInput) scoreInput.value = toDisplayNumber(found.numeric_value, 0);

        // status
        const sel = tr.querySelector('.metric-status');
        const hid = tr.querySelector('.metric-status-hidden');
        if (sel) {
          sel.value = found.status ?? '';
          sel.dispatchEvent(new Event('change', {bubbles:true})); // mirrors score for status_set
        }
        if (hid) hid.value = found.status ?? '';
      });

      updateFilled();
    } catch (e) {
      console.warn('prefill error', e);
    }
  }

  // ---- modal lifecycle ----
  modal.addEventListener('show.bs.modal', function(ev){
    const trigger  = ev.relatedTarget;
    const personId = String(trigger?.getAttribute('data-person') || '');
    const personNm = trigger?.getAttribute('data-name') || '';
    personInput.value     = personId;
    nameLabel.textContent = personNm;

    const list = (assignMap[personId] || []).slice(); // copy
    renderByCategory(list);

    const d = dateInput?.value || new Date().toISOString().slice(0,10);
    prefillFor(personId, d);
  });

  dateInput?.addEventListener('change', () => {
    const pid = personInput.value;
    if (pid) prefillFor(pid, dateInput.value);
  });

})();
</script>
  </div>
</main>