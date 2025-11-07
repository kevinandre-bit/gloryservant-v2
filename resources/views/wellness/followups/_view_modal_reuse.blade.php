{{-- resources/views/wellness/followups/_view_modal_reuse.blade.php --}}
<div class="modal fade" id="followupViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title">
          <i class="material-icons-outlined me-2 text-primary">visibility</i>
          Follow-up Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- LOADING / ERROR --}}
        <div id="fv-loading" class="text-muted mb-2">Loading…</div>
        <div id="fv-error" class="alert alert-danger d-none py-2 px-3">Could not load this follow-up.</div>

        <div id="fv-content" class="d-none">
          {{-- Top summary --}}
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <div class="text-muted small">Volunteer</div>
              <div id="fv-volunteer" class="fw-semibold">—</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small">Group</div>
              <div id="fv-group">—</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small">Meeting Date</div>
              <div id="fv-date">—</div>
            </div>
          </div>

          {{-- Contact info --}}
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <div class="text-muted small">Campus</div>
              <div id="fv-campus">—</div>
            </div>
            <div class="col-md-3">
              <div class="text-muted small">Ministry</div>
              <div id="fv-ministry">—</div>
            </div>
            <div class="col-md-3">
              <div class="text-muted small">Phone</div>
              <div id="fv-phone">—</div>
            </div>
            <div class="col-md-3">
              <div class="text-muted small">Email</div>
              <div id="fv-email">—</div>
            </div>
          </div>

          {{-- Case state --}}
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <div class="text-muted small">Status</div>
              <div><span id="fv-status" class="badge bg-light text-secondary">—</span></div>
            </div>
            <div class="col-md-3">
              <div class="text-muted small">Severity</div>
              <div><span id="fv-sev" class="badge bg-secondary">—</span></div>
            </div>
            <div class="col-md-3">
              <div class="text-muted small">Next Due</div>
              <div id="fv-due">—</div>
            </div>
            <div class="col-md-3">
              <div class="text-muted small">Overdue</div>
              <div id="fv-overdue">—</div>
            </div>
          </div>

          {{-- Report content --}}
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Reason(s)</label>
              <div id="fv-reasons" class="border rounded-3 p-2 bg-light">—</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Conversation</label>
              <div id="fv-conversation" class="border rounded-3 p-2 bg-light">—</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Response</label>
              <div id="fv-response" class="border rounded-3 p-2 bg-light">—</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Next Step(s)</label>
              <div id="fv-steps" class="border rounded-3 p-2 bg-light">—</div>
            </div>
            <div class="col-12">
              <label class="form-label">Leader’s Notes</label>
              <div id="fv-notes" class="border rounded-3 p-2">—</div>
            </div>
            <div class="col-12">
              <label class="form-label">Attachment</label>
              <div>
                <a id="fv-attach" href="#" target="_blank" class="d-none">Download</a>
                <span id="fv-attach-none">—</span>
              </div>
            </div>
          </div>
        </div> {{-- /fv-content --}}
      </div>

      <div class="modal-footer bg-light">
        <small class="text-muted me-auto" id="fv-meta">—</small>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function() {
  const modalEl = document.getElementById('followupViewModal');
  if (!modalEl) return;

  const baseUrl = `{{ url('/wellness/followups') }}`;

  function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = (val === null || val === undefined || val === '') ? '—' : String(val);
  }
  function setBadge(id, val, getClass) {
    const el = document.getElementById(id);
    if (!el) return;
    const text = (val || '').toString().toUpperCase() || '—';
    el.textContent = text;
    if (getClass) el.className = 'badge ' + getClass(val);
  }
  function joinArr(x, sep=', ') { return Array.isArray(x) ? (x.filter(Boolean).join(sep) || '—') : (x || '—'); }
  function fmtDate(iso){ if(!iso) return '—'; try{ return new Date(iso).toLocaleDateString(); }catch(e){ return (iso+'').substring(0,10);} }
  function sevClass(v){ v=(v||'').toLowerCase(); if(v==='high') return 'bg-danger'; if(v==='low') return 'bg-success'; return 'bg-warning text-dark'; }

  modalEl.addEventListener('show.bs.modal', async (ev) => {
    const btn = ev.relatedTarget;
    const id = btn?.getAttribute('data-id');
    if (!id) return;

    const loading = document.getElementById('fv-loading');
    const content = document.getElementById('fv-content');
    const errorBox = document.getElementById('fv-error');

    loading.classList.remove('d-none');
    errorBox.classList.add('d-none');
    content.classList.add('d-none');

    try {
      const res = await fetch(`${baseUrl}/${id}`, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
      if (!res.ok) {
        // Read a small snippet of the body to help diagnose.
        let snippet = '';
        try { snippet = (await res.text()).slice(0, 180); } catch(_) {}
        const msg = `HTTP ${res.status}${snippet ? ` — ${snippet}` : ''}`;
        throw new Error(msg);
      }
      const d = await res.json();

      // Populate
      setText('fv-volunteer', d.volunteer_name);
      setText('fv-group', d.group_name);
      setText('fv-date', fmtDate(d.meeting_date));

      setText('fv-campus', d.campus);
      setText('fv-ministry', d.ministry);
      setText('fv-phone', d.phone);
      setText('fv-email', d.email);

      setBadge('fv-status', d.status, ()=>'bg-light text-secondary');
      setBadge('fv-sev', d.severity, sevClass);
      setText('fv-due', fmtDate(d.followup_due_on));

      const isResolved = (d.status||'').toLowerCase() === 'resolved';
      const overdue = (!isResolved && d.followup_due_on && new Date(d.followup_due_on) < new Date()) ? 'OVERDUE' : '—';
      setText('fv-overdue', overdue);

      setText('fv-reasons', joinArr(d.reasons, ', '));
      setText('fv-conversation', joinArr(d.conversation, ' / '));
      setText('fv-response', joinArr(d.response, ' / '));
      setText('fv-steps', joinArr(d.next_steps, ', '));
      setText('fv-notes', d.notes || '—');

      const a = document.getElementById('fv-attach');
      const none = document.getElementById('fv-attach-none');
      if (d.attachment_path) {
        a.href = `${baseUrl}/${d.id}/attachments/download`;
        a.textContent = d.attachment_name || 'Download';
        a.classList.remove('d-none');
        none.classList.add('d-none');
      } else {
        a.classList.add('d-none');
        none.classList.remove('d-none');
      }

      const createdHuman = d.created_at ? new Date(d.created_at).toLocaleString() : '';
      const leaderName = d.leader_name || '—';
      setText('fv-meta', `Created ${createdHuman} • by ${leaderName}`);

      loading.classList.add('d-none');
      content.classList.remove('d-none');
    } catch (e) {
      loading.classList.add('d-none');
      errorBox.classList.remove('d-none');
      // Also log to console for quick debugging
      console.error('Follow-up modal load failed:', e);
    }
  });
})();
</script>
@endpush