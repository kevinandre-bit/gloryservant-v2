@extends('layouts.admin')

@section('meta')
  <title>Volunteer Follow-ups</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0">
        <i class="material-icons-outlined me-1">fact_check</i> Volunteer Follow-ups
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.followups.index') }}" class="btn btn-outline-secondary">
          <i class="material-icons-outlined me-1">insights</i> Admin Dashboard
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#followupModal">
          <i class="material-icons-outlined me-1">add_task</i> New Report
        </button>
      </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-3">
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class="small">Total Reports</div>
          <div class="display-6 fw-semibold">{{ (int)($kpis->total ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class="small">Last 7 days</div>
          <div class="display-6 fw-semibold">{{ (int)($kpis->last7 ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class="small">This Month</div>
          <div class="display-6 fw-semibold">{{ (int)($kpis->this_month ?? 0) }}</div>
        </div></div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card rounded-4 h-100"><div class="card-body">
          <div class="small">Unique Volunteers</div>
          <div class="display-6 fw-semibold">{{ (int)($kpis->unique_people ?? 0) }}</div>
        </div></div>
      </div>
    </div>

    {{-- Filters --}}
    <form class="card rounded-4 mb-3" method="get" action="{{ route('wellness.followups.index') }}">
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" class="form-control" name="from" value="{{ $from }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" class="form-control" name="to" value="{{ $to }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="name / group / notes">
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-primary">Apply</button>
          </div>
        </div>
      </div>
    </form>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle mb-0">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Volunteer</th>
                    <th>Group</th>
                    <th>Reason(s)</th>
                    <th>Conversation</th>
                    <th>Status</th>
                    <th>Severity</th>
                    <th>Next Due</th>
                    <th>Overdue</th>
                    <th>Response</th>
                    <th>Next Step(s)</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($followups as $f)
                    @php
                      $reasons = (function($j){
                        if ($j===null || $j==='') return '—';
                        $v = json_decode($j, true);
                        if (json_last_error()!==JSON_ERROR_NONE) return (string)$j;
                        return is_array($v) ? (implode(', ', array_filter($v)) ?: '—') : (string)$v;
                      })($f->reasons_json);

                      $conv = (function($j){
                        if ($j===null || $j==='') return '—';
                        $v = json_decode($j, true);
                        if (json_last_error()!==JSON_ERROR_NONE) return (string)$j;
                        return is_array($v) ? (implode(' / ', array_filter($v)) ?: '—') : (string)$v;
                      })($f->conversation_json);

                      $resp = (function($j){
                        if ($j===null || $j==='') return '—';
                        $v = json_decode($j, true);
                        if (json_last_error()!==JSON_ERROR_NONE) return (string)$j;
                        return is_array($v) ? (implode(' / ', array_filter($v)) ?: '—') : (string)$v;
                      })($f->response_json);

                      $steps = (function($j){
                        if ($j===null || $j==='') return '—';
                        $v = json_decode($j, true);
                        if (json_last_error()!==JSON_ERROR_NONE) return (string)$j;
                        return is_array($v) ? (implode(', ', array_filter($v)) ?: '—') : (string)$v;
                      })($f->next_steps_json);

                      $sev = $f->severity ?? 'medium';
                      $sevCls = $sev==='high' ? 'bg-danger' : ($sev==='low' ? 'bg-success' : 'bg-warning text-dark');
                      $isResolved = ($f->status ?? '') === 'resolved';
                      $isOverdue = !$isResolved && $f->followup_due_on && \Carbon\Carbon::parse($f->followup_due_on)->isPast();
                    @endphp
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($f->meeting_date)->toFormattedDateString() }}</td>
                      <td>{{ $f->volunteer_name }}</td>
                      <td>{{ $f->group_name ?: '—' }}</td>
                      <td>{{ $reasons }}</td>
                      <td>{{ $conv }}</td>
                      <td><span class="badge bg-light text-secondary">{{ strtoupper($f->status ?? 'open') }}</span></td>
                      <td><span class="badge {{ $sevCls }}">{{ strtoupper($sev) }}</span></td>
                      <td>{{ $f->followup_due_on ?: '—' }}</td>
                      <td>
                        @if($isOverdue)
                          <span class="badge bg-danger">OVERDUE</span>
                        @else
                          —
                        @endif
                      </td>
                      <td>{{ $resp }}</td>
                      <td>{{ $steps }}</td>
                      <td>{{ \Carbon\Carbon::parse($f->created_at)->diffForHumans() }}</td>
                      <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary view-followup-btn"
                                data-id="{{ $f->id }}"
                                data-bs-toggle="modal"
                                data-bs-target="#followupViewModal">
                          View
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th>Date</th>
                    <th>Volunteer</th>
                    <th>Group</th>
                    <th>Reason(s)</th>
                    <th>Conversation</th>
                    <th>Status</th>
                    <th>Severity</th>
                    <th>Next Due</th>
                    <th>Overdue</th>
                    <th>Response</th>
                    <th>Next Step(s)</th>
                    <th>Created</th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="mt-2">
              {{ $followups->links() }}
            </div>
          </div>
        </div>
      </div>

      {{-- Timeline --}}
      <div class="col-lg-4">
        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="mb-3">Recent Activity</h6>
            <div class="timeline">
              @forelse($timeline as $t)
                @php
                  $convArr = [];
                  if (!empty($t->conversation_json)) {
                    $tmp = json_decode($t->conversation_json, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                      $convArr = is_array($tmp) ? $tmp : [$tmp];
                    }
                  }
                  $convText = implode(' / ', array_filter($convArr)) ?: '—';
                @endphp
                <div class="tl-item mb-3">
                  <div class="tl-dot b-primary"></div>
                  <div class="tl-content">
                    <div class="fw-semibold">{{ $t->volunteer_name }}</div>
                    <div class="small text-muted">{{ $t->leader_name }} • {{ \Carbon\Carbon::parse($t->created_at)->diffForHumans() }}</div>
                    <div class="small mt-1">{{ $convText }}</div>
                  </div>
                </div>
              @empty
                <div class="text-muted small">No recent activity.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

{{-- Modal: New Follow-up --}}
<div class="modal fade" id="followupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" method="post" action="{{ route('wellness.followups.store') }}">
      @csrf
      <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="material-icons-outlined me-2 text-primary">assignment</i> Volunteer Follow-up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">Leader</label>
            <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
            <input type="hidden" name="leader_id" value="{{ auth()->id() }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Volunteer</label>
            <select class="form-select" name="volunteer_id" required>
              <option value="">Select…</option>
              @foreach($volunteerOptions as $o)
                <option value="{{ $o['id'] }}">{{ $o['label'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Date of Meeting</label>
            <input type="date" name="meeting_date" class="form-control" value="{{ now()->toDateString() }}" required>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Reason for Follow-up</label>
            <select class="form-select" name="reasons[]" id="reasonSelect" required>
              <option value="Did not participate in meeting">Did not participate in meeting</option>
              <option value="Did not respect schedule">Did not respect schedule</option>
              <option value="Did not post devotion">Did not post devotion</option>
              <option value="Did not attend service">Did not attend service</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Conversation Outcome</label>
            <select class="form-select" name="conversation" required>
              <option value="Positive & encouraging">Positive & encouraging</option>
              <option value="Corrective, received well">Corrective, received well</option>
              <option value="Volunteer needs more follow-up">Volunteer needs more follow-up</option>
              <option value="Unresolved / concern remains">Unresolved / concern remains</option>
            </select>
          </div>
        </div>

        <div class="mb-3 d-none" id="reasonOtherWrap">
          <input type="text" class="form-control" name="reason_other" placeholder="Please specify reason…">
        </div>

        <div class="mb-3">
          <label class="form-label">Volunteer’s Response</label>
          <select class="form-select" name="response" id="responseSelect" required>
            <option value="Acknowledged & committed">Acknowledged & committed</option>
            <option value="Gave reason / explanation">Gave reason / explanation</option>
            <option value="Resistant / disagreed">Resistant / disagreed</option>
            <option value="other">Other</option>
          </select>
          <input type="text" class="form-control mt-2 d-none" id="responseOther" name="response_other" placeholder="Please specify response…">
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Next Step / Action Plan</label>
            <select class="form-select" name="next_steps[]" id="nextStepSelect" required>
              <option value="No further action">No further action</option>
              <option value="Monitor progress">Monitor progress</option>
              <option value="Another meeting">Another meeting</option>
              <option value="Refer to higher leadership">Refer to higher leadership</option>
              <option value="other">Other</option>
            </select>
            <input type="text" class="form-control mt-2 d-none" id="nextOther" name="next_step_other" placeholder="Please specify action…">
          </div>
          <div class="col-md-6">
            <label class="form-label">Next Step Due Date</label>
            <input type="date" name="followup_due_on" class="form-control" required>
          </div>
        </div>

        <div class="mb-2">
          <label class="form-label">Leader’s Notes (optional)</label>
          <textarea class="form-control" name="notes" rows="3" placeholder="Key points, commitments, context…"></textarea>
        </div>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Follow-up</button>
      </div>
    </form>
  </div>
</div>

@endsection

{{-- View Modal (details) --}}
<div class="modal fade" id="followupViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="material-icons-outlined me-2 text-primary">visibility</i> Follow-up Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="fv-loading" class="text-muted">Loading…</div>

        <div id="fv-content" class="d-none">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="card rounded-4 h-100">
                <div class="card-body">
                  <h6 class="mb-2">Volunteer</h6>
                  <div class="fw-semibold" id="fv-name">—</div>
                  <div class="text-muted small" id="fv-group">—</div>
                  <hr>
                  <div class="small"><strong>Campus:</strong> <span id="fv-campus">—</span></div>
                  <div class="small"><strong>Ministry:</strong> <span id="fv-ministry">—</span></div>
                  <div class="small"><strong>Department:</strong> <span id="fv-dept">—</span></div>
                  <div class="small mt-2"><strong>Phone:</strong> <span id="fv-phone">—</span></div>
                  <div class="small"><strong>Email:</strong> <span id="fv-email">—</span></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card rounded-4 h-100">
                <div class="card-body">
                  <h6 class="mb-2">Report</h6>
                  <div class="small"><strong>Date:</strong> <span id="fv-date">—</span></div>
                  <div class="small"><strong>Status:</strong> <span id="fv-status" class="badge bg-light text-secondary">—</span></div>
                  <div class="small"><strong>Severity:</strong> <span id="fv-sev" class="badge bg-secondary">—</span></div>
                  <div class="small mt-2"><strong>Reason(s):</strong> <span id="fv-reasons">—</span></div>
                  <div class="small"><strong>Conversation:</strong> <span id="fv-conv">—</span></div>
                  <div class="small"><strong>Response:</strong> <span id="fv-resp">—</span></div>
                  <div class="small"><strong>Next Step(s):</strong> <span id="fv-steps">—</span></div>
                  <div class="small mt-2"><strong>Notes:</strong><div id="fv-notes" class="border rounded p-2">—</div></div>
                  <div class="small mt-2"><strong>Attachment:</strong>
                    <a id="fv-attach" href="#" target="_blank" class="d-none">Download</a>
                    <span id="fv-attach-none"> —</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Inline recent timeline (last 7) --}}
          <div class="card rounded-4">
            <div class="card-body">
              <h6 class="mb-2">Recent (7) Follow-ups by You</h6>
              <div class="timeline">
                @foreach($timeline as $t)
                  @php
                    $convDecoded = json_decode($t->conversation_json ?? '[]', true);
                    $convMini = is_array($convDecoded) ? $convDecoded : ($convDecoded ? [$convDecoded] : []);
                  @endphp
                  <div class="tl-item mb-2 d-flex">
                    <div class="tl-dot b-primary me-2"></div>
                    <div>
                      <div class="fw-semibold">{{ $t->volunteer_name }}</div>
                      <div class="small text-muted">{{ $t->leader_name }} • {{ \Carbon\Carbon::parse($t->created_at)->diffForHumans() }}</div>
                      <div class="small mt-1">{{ implode(' / ', $convMini ?: []) ?: '—' }}</div>
                    </div>
                  </div>
                @endforeach
                @if($timeline->isEmpty())
                  <div class="text-muted small">No recent activity.</div>
                @endif
              </div>
            </div>
          </div>

        </div> {{-- fv-content --}}
      </div>
      <div class="modal-footer bg-light">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.timeline .tl-dot{width:10px;height:10px;border-radius:50%;background:#6c757d;margin-top:.45rem}
.timeline .tl-dot.b-primary{background:#0d6efd}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const viewModal = document.getElementById('followupViewModal');
  const baseUrl = `{{ url('/wellness/followups') }}`;

  viewModal?.addEventListener('show.bs.modal', async (ev) => {
    const btn = ev.relatedTarget;
    const id = btn?.getAttribute('data-id');
    if(!id) return;

    const loading = document.getElementById('fv-loading');
    const content = document.getElementById('fv-content');

    // reset UI
    loading.classList.remove('d-none');
    content.classList.add('d-none');

    try{
      const res = await fetch(`${baseUrl}/${id}`);
      if(!res.ok) throw new Error('HTTP ' + res.status);
      const d = await res.json();

      // Volunteer
      document.getElementById('fv-name').textContent   = d.volunteer_name || '—';
      document.getElementById('fv-group').textContent  = d.group_name || '—';
      document.getElementById('fv-campus').textContent = d.campus || '—';
      document.getElementById('fv-ministry').textContent = d.ministry || '—';
      document.getElementById('fv-dept').textContent   = d.department || '—';
      document.getElementById('fv-phone').textContent  = d.phone || '—';
      document.getElementById('fv-email').textContent  = d.email || '—';

      // Report
      document.getElementById('fv-date').textContent   = (d.meeting_date || '').substring(0,10);
      document.getElementById('fv-status').textContent = (d.status || '').toUpperCase();
      document.getElementById('fv-sev').textContent    = (d.severity || '').toUpperCase();

      const join = (x)=> (Array.isArray(x)&&x.length) ? x.join(', ') : '—';
      document.getElementById('fv-reasons').textContent = join(d.reasons||[]);
      document.getElementById('fv-conv').textContent    = join(d.conversation||[]);
      document.getElementById('fv-resp').textContent    = join(d.response||[]);
      document.getElementById('fv-steps').textContent   = join(d.next_steps||[]);
      document.getElementById('fv-notes').textContent   = d.notes || '—';

      const a = document.getElementById('fv-attach');
      const none = document.getElementById('fv-attach-none');

      if (d.attachment_path){
        a.href = `${baseUrl}/${d.id}/attachments/download`;
        a.textContent = d.attachment_name || 'Download';
        a.classList.remove('d-none');
        none.classList.add('d-none');
      } else {
        a.classList.add('d-none');
        none.classList.remove('d-none');
      }

      loading.classList.add('d-none');
      content.classList.remove('d-none');
    }catch(e){
      loading.textContent = 'Failed to load.';
    }
  });

  // show/hide "Other" inputs
  const reasonSelect   = document.getElementById('reasonSelect');
  const reasonOther    = document.getElementById('reasonOtherWrap');
  const responseSelect = document.getElementById('responseSelect');
  const responseOther  = document.getElementById('responseOther');
  const nextStepSelect = document.getElementById('nextStepSelect');
  const nextOther      = document.getElementById('nextOther');

  reasonSelect?.addEventListener('change', e => {
    reasonOther.classList.toggle('d-none', e.target.value !== 'other');
  });
  responseSelect?.addEventListener('change', e => {
    responseOther.classList.toggle('d-none', e.target.value !== 'other');
  });
  nextStepSelect?.addEventListener('change', e => {
    nextOther.classList.toggle('d-none', e.target.value !== 'other');
  });
});
</script>
@endpush