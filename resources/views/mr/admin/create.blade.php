@include('layouts/admin')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Meeting Reports</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active">Create Meeting</li>
        </ol>
      </div>
    </div>

    @if(session('ok'))
      <div class="alert alert-success d-flex align-items-center justify-content-between">
        <div>
          <strong>{{ session('ok') }}.</strong>
          @if(session('created_link'))
            Shareable link: <a class="ms-1" href="{{ session('created_link') }}" target="_blank">{{ session('created_link') }}</a>
          @endif
        </div>
        @if(session('created_link'))
          <a class="btn btn-sm btn-dark" target="_blank" href="{{ session('created_link') }}">
            <span class="material-icons-outlined align-text-top">open_in_new</span> Open Meeting
          </a>
        @endif
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="card rounded-4">
      <div class="card-body">
        <form method="post" action="{{ route('mr.admin.meetings.store') }}">
          @csrf

          <div class="row g-3">

            <div class="col-12 col-lg-8">
              <h5 class="mb-3">Meeting Details</h5>

              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Title</label>
                  <input name="title" class="form-control" value="{{ old('title','Demo Meeting') }}" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Team (optional)</label>
                  <input name="team_name" class="form-control" value="{{ old('team_name','Comms Team') }}">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Show (optional)</label>
                  <input name="show_name" class="form-control" value="{{ old('show_name','Radio Team') }}">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Date</label>
                  <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Start Time</label>
                  <input type="time" name="start_time" class="form-control" value="{{ old('start_time','09:30') }}" required>
                </div>

                <div class="col-md-4">
                  <label class="form-label">End Time (optional)</label>
                  <input type="time" name="end_time" class="form-control" value="{{ old('end_time','10:30') }}">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Timezone</label>
                  <select name="timezone" class="form-select" required>
                    @foreach($timezones as $tz => $label)
                      <option value="{{ $tz }}" {{ old('timezone','America/Santo_Domingo') === $tz ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Magic Link Role</label>
                  <select name="role" class="form-select" required>
                    <option value="editor" {{ old('role','editor')==='editor'?'selected':'' }}>Editor (can edit)</option>
                    <option value="viewer" {{ old('role')==='viewer'?'selected':'' }}>Viewer (read-only)</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Meeting_type</label>
                  <select name="meeting_type_id" class="form-select" required>
                    <option value="1">Core Team Daily (60 min)</option>
                    <option value="2">Radio Team Standup (30 min)</option>
                    <option value="3">Radio Production Meeting (45 min)</option>
                    <option value="4">Comms Core Weekly (60 min)</option>
                    <option value="5">Comms PAP (60 min)</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label">Expires At (optional)</label>
                  <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                  <div class="form-text">If set, the link stops working after this time.</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <h5 class="mb-3">Quick Seed</h5>

              <div class="mb-3">
                <label class="form-label">Agenda Template</label>
                <div class="btn-group w-100">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-tpl="comms">Comms</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-tpl="radio">Radio</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-tpl="empty">Empty</button>
                </div>
                <textarea id="agendaArea" name="agenda" class="form-control mt-2" rows="8" placeholder="Title | minutes">{{ old('agenda', $agendaTemplates['comms']) }}</textarea>
                <div class="form-text">One item per line. Use <code>Title | minutes</code>.</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Attendees Seed</label>
                <textarea name="attendees" class="form-control" rows="6" placeholder="Name &lt;email&gt;&#10;Name">{{ old('attendees', "Valery\nStacy Casseus <stacy@example.com>\nKevin Andre <kevin@example.com>") }}</textarea>
                <div class="form-text">One per line. Optional email in angle brackets.</div>
              </div>
            </div>

          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-dark">
              <span class="material-icons-outlined align-text-top">add_circle</span>
              Create Meeting & Link
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>
</main>

<script>
(function(){
  const tplBtns = document.querySelectorAll('[data-tpl]');
  const tplMap  = @json($agendaTemplates);
  const area    = document.getElementById('agendaArea');
  tplBtns.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const k = btn.getAttribute('data-tpl');
      area.value = tplMap[k] || '';
    });
  });
})();
</script>
