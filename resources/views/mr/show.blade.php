@include('layouts/admin')

@php
  // Safe shorthands for JS boot
  $MEETING = $meeting ?? (object)[];
  $AGENDA = collect($agenda ?? [])->map(fn($r)=>[
    'id'=>(int)$r->id,'title'=>$r->title,'planned'=>(int)($r->planned_minutes??0),
    'covered'=>(bool)$r->is_covered,'started_at'=>$r->started_at,'ended_at'=>$r->ended_at,
    'actual_seconds'=>(int)($r->actual_seconds??0)
  ]);
  $ATTENDEES = collect($attendees ?? [])->map(fn($r)=>[
    'id'=>(int)$r->id,'name'=>$r->attendee_name,'email'=>$r->attendee_email,
    'status'=>$r->status,'checked_in_at'=>$r->checked_in_at
  ]);
  $TASKS = collect($tasks ?? [])->map(fn($r)=>[
    'id'=>(int)$r->id,'title'=>$r->title,'url'=>$r->asana_permalink_url,
    'due_on'=>$r->due_on,'status'=>$r->last_seen_status
  ]);
  $PROJECTS = collect($projects ?? [])->map(fn($p)=>[
    'project_gid'=>$p->project_gid,'project_name'=>$p->project_name,
    'section_gid'=>$p->section_gid ?? null,'section_name'=>$p->section_name ?? null
  ]);

  // Single source of truth for Google Doc (use meeting fields if set, else your provided link)
  $DOC_URL = $MEETING->drive_file_url
           ?? ($MEETING->google_doc_url ?? null)
           ?? 'https://docs.google.com/document/d/1Scxvj9uaKfEr0FrKvSjEcWA5HjWu8lzEVlg-NrvCXZg/edit';
@endphp

<main class="main-wrapper">
  <div class="main-content container-fluid py-3">

    {{-- Top Bar --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="d-flex align-items-center gap-3">
        {{-- Toggle left panel (inside-grid collapse) --}}
        <button id="btnTogglePanel" class="btn btn-outline-secondary d-none d-md-inline-flex"
                data-bs-toggle="collapse" data-bs-target="#leftPanelCollapse" aria-expanded="false">
          <span class="material-icons-outlined me-1">view_sidebar</span> Panel
        </button>

        <div class="rounded-3 bg-dark text-white d-grid place-items-center fw-bold" style="width:38px;height:38px;display:grid;">MR</div>
        <div>
          <div class="text-secondary small">
            {{ $MEETING->team_name ?? '' }} {{ !empty($MEETING->show_name) ? '• '.$MEETING->show_name : '' }}
            @if(!empty($MEETING->starts_at) && !empty($MEETING->ends_at))
              — {{ \Carbon\Carbon::parse($MEETING->starts_at)->format('D H:i') }}
              – {{ \Carbon\Carbon::parse($MEETING->ends_at)->format('H:i') }}
            @endif
          </div>
          <h4 class="mb-0">{{ $MEETING->title ?? 'Meeting' }}</h4>
        </div>
        <div class="ms-2 d-none d-md-flex align-items-center gap-2">
          <span class="badge bg-light text-secondary border">Autosaved</span>
          <span class="badge bg-light text-secondary border">{{ $MEETING->timezone ?? 'America/Santo_Domingo' }}</span>
          @if(!empty($MEETING->status)) <span class="badge bg-primary">{{ $MEETING->status }}</span> @endif
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        @if(!empty($DOC_URL))
          <a class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer" href="{{ $DOC_URL }}">
            <span class="material-icons-outlined align-text-top">open_in_new</span> Open Doc
          </a>
        @endif

        @if(empty($DOC_URL))
          <button id="btnPublish" class="btn btn-dark">
            <span class="material-icons-outlined align-text-top">cloud_upload</span> Publish to Google Doc
          </button>
        @endif
      </div>
    </div>

    {{-- Inside-grid layout: Left Panel (collapse) • Notes (Docs or CKEditor) • Tasks --}}
    <div class="row g-3 align-items-stretch">

      {{-- LEFT PANEL: collapse-horizontal INSIDE the main grid --}}
      <div class="col-auto d-none d-md-block">
        <div id="leftPanelCollapse" class="collapse collapse-horizontal" style="min-width:0;">
          <div class="card rounded-4 shadow-sm" style="width:320px;">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="fw-semibold">Panel</div>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#leftPanelCollapse">
                  <span class="material-icons-outlined">close</span>
                </button>
              </div>

              <ul class="nav nav-pills mb-3" id="mrTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-agenda" type="button">Agenda</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-attendees" type="button">Attendees</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-metrics" type="button">Metrics</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-checklists" type="button">Checklists</button></li>
              </ul>

              <div class="tab-content">
                {{-- Agenda --}}
                <div class="tab-pane fade show active" id="tab-agenda">
                  <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-2">Timeboxed Agenda</h6>
                    <span class="text-secondary small">Keep on time</span>
                  </div>
                  <div id="agendaList" class="mt-2"></div>
                </div>

                {{-- Attendees --}}
                <div class="tab-pane fade" id="tab-attendees">
                  <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-2">Attendees</h6>
                    <button class="btn btn-sm btn-outline-secondary" id="btnAddAttendee">
                      <span class="material-icons-outlined">person_add</span> Add
                    </button>
                  </div>
                  <div id="attendeeList" class="mt-2"></div>
                </div>

                {{-- Metrics --}}
                <div class="tab-pane fade" id="tab-metrics">
                  <div class="row g-2">
                    <div class="col-6">
                      <label class="form-label small">Weekly Reach</label>
                      <input class="form-control form-control-sm" placeholder="128000">
                    </div>
                    <div class="col-6">
                      <label class="form-label small">Subscriptions</label>
                      <input class="form-control form-control-sm" placeholder="5400">
                    </div>
                    <div class="col-12">
                      <label class="form-label small">Radio Analytics</label>
                      <input class="form-control form-control-sm" placeholder="Notes / KPI">
                    </div>
                  </div>
                </div>

                {{-- Checklists --}}
                <div class="tab-pane fade" id="tab-checklists">
                  <div class="form-check"><input class="form-check-input" type="checkbox" id="chk1"><label class="form-check-label" for="chk1">Thumbnails ready</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" id="chk2"><label class="form-check-label" for="chk2">Titles finalized</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" id="chk3"><label class="form-check-label" for="chk3">Segments outlined</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" id="chk4"><label class="form-check-label" for="chk4">Promo scheduled</label></div>
                </div>
              </div>
            </div>
          </div>
        </div> {{-- /collapse --}}
      </div>

      {{-- CENTER: Notes --}}
      <div class="col-12 col-md">
        <div class="card rounded-4 shadow-sm h-100">
          @if(!empty($DOC_URL))
            {{-- Google Docs launcher mode --}}
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
              <div class="d-flex align-items-center justify-content-between mb-2 w-100">
                <h6 class="mb-0 text-uppercase small fw-bold">Meeting Notes</h6>
                <span class="text-secondary small">Google Docs • Live collaboration</span>
              </div>

              <p class="text-secondary mb-3">This meeting uses a Google Doc for notes.</p>

              <a href="{{ $DOC_URL }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark btn-lg d-inline-flex align-items-center gap-2">
                <span class="material-icons-outlined">description</span>
                Open Google Docs Editor
              </a>

              <div class="mt-3 small text-secondary">
                Opens in a new tab with the full toolbar and collaboration features.
              </div>

              <div class="mt-4 w-100" style="max-width:720px;">
                <div class="input-group">
                  <input type="text" class="form-control" value="{{ $DOC_URL }}" readonly>
                  <button class="btn btn-outline-secondary" type="button" id="btnCopyDocUrl">
                    <span class="material-icons-outlined" style="font-size:18px;">content_copy</span>
                  </button>
                </div>
                <div id="copyHint" class="form-text">Share this link with collaborators (ensure Drive sharing is set appropriately).</div>
              </div>
            </div>
          @else
            {{-- CKEditor fallback mode (original editor) --}}
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-uppercase small fw-bold">Meeting Notes</h6>
                <span class="text-secondary small">Docs-style editor • Autosave</span>
              </div>

              <!-- Decoupled toolbar + editor containers -->
              <div id="mrToolbar"></div>
              <div id="mrEditor" class="flex-grow-1 border-0"></div>

              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="text-secondary small">
                  <span id="mrWordCount">0</span> words • <span id="mrCharCount">0</span> characters
                </div>
                <div id="mrStatus" class="text-secondary small">Autosave ready</div>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- RIGHT: Asana Tasks --}}
      <div class="col-12 col-md-3">
        <div class="card rounded-4 h-100 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h6 class="mb-0 text-uppercase small fw-bold">Asana Tasks</h6>
              <div class="btn-group">
                <button id="btnNewTask" class="btn btn-sm btn-dark">+ New</button>
                <button id="btnLinkTask" class="btn btn-sm btn-outline-secondary">Link</button>
              </div>
            </div>

            <div class="d-flex gap-2 mb-3">
              <select id="selProject" class="form-select form-select-sm">
                @forelse($PROJECTS as $p)
                  <option value="{{ $p['project_gid'] }}">{{ $p['project_name'] }}</option>
                @empty
                  <option value="0000">Select Project</option>
                @endforelse
              </select>
              <select id="selSection" class="form-select form-select-sm">
                <option value="">Section</option>
                @foreach($PROJECTS->whereNotNull('section_gid') as $p)
                  <option value="{{ $p['section_gid'] }}">{{ $p['section_name'] }}</option>
                @endforeach
              </select>
            </div>

            <div id="taskList" class="flex-grow-1 overflow-auto small"></div>
            <div id="syncPill" class="text-secondary small mt-2">Synced • just now</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Mobile: small toggle button to open/close left panel --}}
    <div class="d-md-none mt-3">
      <button class="btn btn-dark w-100" data-bs-toggle="collapse" data-bs-target="#leftPanelCollapse">
        <span class="material-icons-outlined me-1">view_sidebar</span> Panel
      </button>
    </div>

  </div>
</main>

  <!--start switcher-->
  <button class="btn btn-grd btn-grd-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
  </button>
  
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Theme Customizer</h5>
        <p class="mb-0">Customize your theme</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Blue</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>
        </div><!--end row-->

      </div>
    </div>
  </div>

{{-- Modals (Tasks / Attendee) --}}
<div class="modal fade" id="taskCreateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-end">
    <div class="modal-content rounded-4">
      <div class="modal-header"><h5 class="modal-title"><span class="material-icons-outlined">add_task</span> Create Asana Task</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label small">Title</label><input id="taskTitle" class="form-control" placeholder="Agenda: Review Key Metrics — follow-ups"></div>
        <div class="row g-2">
          <div class="col-6"><label class="form-label small">Due date</label><input id="taskDue" type="date" class="form-control"></div>
          <div class="col-6"><label class="form-label small">Assignee (Asana GID)</label><input id="taskAssignee" class="form-control" placeholder="(optional)"></div>
        </div>
        <div class="form-text">Project uses the selection at the top of the panel.</div>
      </div>
      <div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button id="btnCreateTaskConfirm" class="btn btn-dark">Create in Asana</button></div>
    </div>
  </div>
</div>

<div class="modal fade" id="taskLinkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-end">
    <div class="modal-content rounded-4">
      <div class="modal-header"><h5 class="modal-title"><span class="material-icons-outlined">link</span> Link existing Asana Task</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label small">Asana Task GID</label><input id="linkGid" class="form-control" placeholder="1200…"></div>
        <div class="mb-2"><label class="form-label small">Permalink URL</label><input id="linkUrl" class="form-control" placeholder="https://app.asana.com/0/…"></div>
        <div class="mb-2"><label class="form-label small">Title (for display)</label><input id="linkTitle" class="form-control" placeholder="Task title"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button id="btnLinkTaskConfirm" class="btn btn-dark">Link Task</button></div>
    </div>
  </div>
</div>

<div class="modal fade" id="attendeeAddModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header"><h5 class="modal-title"><span class="material-icons-outlined">person_add</span> Add attendee</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label small">Name</label><input id="attName" class="form-control" placeholder="Full name"></div>
        <div class="mb-2"><label class="form-label small">Email</label><input id="attEmail" class="form-control" placeholder="(optional)"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button id="btnAddAttendeeConfirm" class="btn btn-dark">Add</button></div>
    </div>
  </div>
</div>

<style>
/* Keep your attendee styles */
.attendance-item { border:none !important; margin:0 !important; border-radius:0; width:100%; background:transparent; }
.avatar { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; font-weight:600; font-size:.9rem; background:var(--bs-gray-200); color:var(--bs-gray-800); }
.status-select { border:none; min-width:110px; padding-left:1rem; padding-right:1.5rem; background-position:right .75rem center; color:#fff; }
.status-present{ background-color:var(--bs-success)!important; color:#fff; }
.status-late{ background-color:var(--bs-warning)!important; color:#000; }
.status-excused{ background-color:var(--bs-info)!important; color:#000; }
.status-absent{ background-color:var(--bs-danger)!important; color:#fff; }

/* CKEditor “Docs-like” look — only applied/used when no DOC_URL */
:root{ --page-w: 816px; --page-pad: 2.2rem; }
.ck-toolbar{ border-radius:.75rem .75rem 0 0 !important; }
.ck-content{
  min-height:560px; padding:var(--page-pad); background:#fff;
  border-radius:0 0 .75rem .75rem; box-shadow:0 1px 2px rgba(0,0,0,.04);
}
.ck.ck-editor__editable_inline{ width:var(--page-w); margin:0 auto; }
.ck-content table{ width:100%; border-collapse:collapse; }
.ck-content table th,.ck-content table td{ border:1px solid #e9ecef; padding:.5rem; }
</style>

<script>
function applyStatusColor(selectEl) {
  selectEl.classList.remove('status-present','status-late','status-excused','status-absent');
  selectEl.classList.add(`status-${selectEl.value}`);
}
document.addEventListener('change', e => {
  if (e.target.matches('.status-select')) {
    applyStatusColor(e.target);
    const id   = e.target.dataset.id;
    const stat = e.target.value;
    console.log(`Update user ${id} to status: ${stat}`);
    // TODO: send to backend
  }
});
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.status-select').forEach(applyStatusColor);
});

// Copy Google Doc URL (launcher mode)
document.addEventListener('click', (e)=>{
  if(e.target.closest('#btnCopyDocUrl')){
    const input = e.target.closest('.input-group').querySelector('input');
    input.select(); input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    const hint = document.getElementById('copyHint');
    if(hint){ hint.textContent = 'Copied!'; setTimeout(()=> hint.textContent='Share this link with collaborators (ensure Drive sharing is set appropriately).', 1500); }
  }
});
</script>

{{-- CKEditor assets + init ONLY when there is no Google Doc URL --}}
@if(empty($DOC_URL))
  {{-- CKEditor 5 Decoupled Document build (replaces TinyMCE) --}}
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/decoupled-document/ckeditor.js"></script>

  <script>
  (function(){
    // Persist panel state between visits
    const key = 'mr.leftpanel.open';
    const collapseEl = document.getElementById('leftPanelCollapse');
    const bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: false });
    if(localStorage.getItem(key) === '1'){ bsCollapse.show(); }
    collapseEl.addEventListener('shown.bs.collapse', ()=> localStorage.setItem(key,'1'));
    collapseEl.addEventListener('hidden.bs.collapse', ()=> localStorage.setItem(key,'0'));

    const meetingId = {{ (int)($MEETING->id ?? 0) }};
    const csrf      = "{{ csrf_token() }}";
    const saveUrl   = "{{ route('mr.notes.save', ['meeting' => '__MID__']) }}".replace('__MID__', meetingId);
    const uploadUrl = "{{ route('mr.notes.upload', ['meeting' => '__MID__']) }}".replace('__MID__', meetingId);
    const publishUrl= "{{ route('mr.publish', ['meeting' => '__MID__']) }}".replace('__MID__', meetingId);

    // Seed HTML (keep same logic as before)
    const seedHtml  = @json($notes->content ?? "<h2>Highlights</h2><ul><li>Weekly reach up 8%</li></ul><h2>Decisions</h2><ul><li>—</li></ul><h2>Notes</h2><p>Type here…</p>");
    const draftKey  = `mr.notes.draft.${meetingId}`;
    const localDraft= localStorage.getItem(draftKey);
    const startData = localDraft && localDraft.length ? localDraft : seedHtml;

    // Debounce util
    const debounce = (fn, ms) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };
    const setStatus = (txt)=>{ const el=document.getElementById('mrStatus'); if(el) el.textContent = txt; };

    // CKEditor upload adapter -> your Laravel upload route returns { url }
    class LaravelUploadAdapter{
      constructor(loader){ this.loader = loader; }
      upload(){
        return this.loader.file.then(file => new Promise(async (resolve, reject)=>{
          try{
            const fd = new FormData(); fd.append('upload', file);
            const res = await fetch(uploadUrl, { method:'POST', headers:{'X-CSRF-TOKEN': csrf}, body: fd });
            const data = await res.json();
            if(!res.ok || !data?.url) return reject('Upload failed');
            resolve({ default: data.url });
          }catch(e){ reject(e); }
        }));
      }
      abort(){}
    }
    function LaravelUploadPlugin(editor){
      editor.plugins.get('FileRepository').createUploadAdapter = loader => new LaravelUploadAdapter(loader);
    }

    // Create CKEditor (Decoupled/Docs style)
    DecoupledEditor.create(document.querySelector('#mrEditor'), {
      extraPlugins: [ LaravelUploadPlugin ],
      placeholder: 'Type your meeting notes…',
      toolbar: {
        items: [
          'undo','redo','|','heading','|',
          'fontFamily','fontSize','bold','italic','underline','strikethrough','removeFormat','|',
          'fontColor','fontBackgroundColor','|',
          'alignment','outdent','indent','|',
          'bulletedList','numberedList','todoList','|',
          'link','imageUpload','insertTable','blockQuote','horizontalLine','code','codeBlock','|',
          'specialCharacters','findAndReplace','subscript','superscript'
        ],
        shouldNotGroupWhenFull: true
      },
      image: { toolbar: ['imageStyle:inline','imageStyle:block','imageStyle:side','|','imageTextAlternative','linkImage'] },
      table: { contentToolbar: ['tableColumn','tableRow','mergeTableCells','tableProperties','tableCellProperties'] },
      link: { decorators: { addTargetToExternalLinks: true } }
    }).then(editor => {
      // Mount toolbar
      document.getElementById('mrToolbar').appendChild(editor.ui.view.toolbar.element);

      // Load initial content
      editor.setData(startData);

      // Word/char counts
      const wc = document.getElementById('mrWordCount');
      const cc = document.getElementById('mrCharCount');
      const updateCounts = () => {
        const txt = editor.getData().replace(/<[^>]+>/g,' ');
        const words = txt.trim().split(/\s+/).filter(Boolean);
        wc.textContent = words.length;
        cc.textContent = txt.replace(/\s+/g,'').length;
      };
      updateCounts();
      editor.model.document.on('change:data', updateCounts);

      // Debounced autosave to backend + localStorage
      const doSave = debounce(async ()=>{
        const content = editor.getData();
        localStorage.setItem(draftKey, content);
        setStatus('Saving…');
        try{
          await fetch(saveUrl, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ content, draft_json: null })
          });
          setStatus('Saved');
        }catch(e){
          console.error('Save failed', e);
          setStatus('Save failed (offline?)');
        }
      }, 800);
      editor.model.document.on('change:data', doSave);

      // Publish -> Google Doc
      document.getElementById('btnPublish')?.addEventListener('click', async ()=>{
        try{
          const res = await fetch(publishUrl, { method:'POST', headers:{'X-CSRF-TOKEN': csrf} });
          const data = await res.json();
          if(data?.url) window.open(data.url, '_blank');
        }catch(e){ console.error(e); }
      });

      // Expose for debugging
      window.mrEditor = editor;
    }).catch(err => {
      console.error(err);
      alert('Editor failed to load. Check the CKEditor CDN URL or network.');
    });
  })();
  </script>
@endif

<script>
/* Keep your Agenda/Attendees/Tasks render + actions exactly as before */
(function(){
  const meetingId = {{ (int)($MEETING->id ?? 0) }};
  const csrf = "{{ csrf_token() }}";
  const routes = {
    agendaStart:"{{ route('mr.agenda.start', ['meeting' => '__MID__','item'=>'__IID__']) }}",
    agendaStop: "{{ route('mr.agenda.stop',  ['meeting' => '__MID__','item'=>'__IID__']) }}",
    agendaToggle:"{{ route('mr.agenda.toggle',['meeting' => '__MID__','item'=>'__IID__']) }}",
    attAdd:     "{{ route('mr.attendee.add', ['meeting' => '__MID__']) }}".replace('__MID__', meetingId),
    attStatus:  "{{ route('mr.attendee.status', ['meeting' => '__MID__','attendee'=>'__AID__']) }}",
    taskCreate: "{{ route('mr.tasks.create', ['meeting' => '__MID__']) }}".replace('__MID__', meetingId),
    taskLink:   "{{ route('mr.tasks.link',   ['meeting' => '__MID__']) }}".replace('__MID__', meetingId),
    taskComplete:"{{ route('mr.tasks.complete', ['meeting' => '__MID__','task'=>'__TID__']) }}",
  };

  // Boot data
  let agenda = @json($AGENDA);
  let attendees = @json($ATTENDEES);
  let tasks = @json($TASKS);

  function $(sel,root){ return (root||document).querySelector(sel); }
  function create(el, cls){ const e=document.createElement(el); if(cls) e.className=cls; return e; }

  // Agenda render
  const agendaList = $('#agendaList');
  function renderAgenda(){
    if(!agendaList) return; agendaList.innerHTML='';
    agenda.forEach((it, idx) => {
      const card = create('div', 'border rounded-3 p-3 mb-2');
      const plannedSec = (it.planned || 0) * 60;
      const elapsedSec = (it.actual_seconds || 0);
      const pct = plannedSec ? Math.min(100, Math.round((elapsedSec / plannedSec) * 100)) : 0;

      const rowTop = create('div', 'd-flex align-items-start gap-2');
      rowTop.innerHTML = `
        <div class="badge bg-light text-secondary border flex-shrink-0">${idx + 1}</div>
        <div class="flex-grow-1 min-w-0">
          <div class="fw-semibold text-truncate" title="${it.title}">${it.title}</div>
          <div class="small text-secondary">
            ${it.planned || 0}m planned • ${Math.floor(elapsedSec / 60)}m ${elapsedSec % 60}s elapsed
          </div>
        </div>
      `;

      const bar = create('div', 'progress mt-1');
      bar.innerHTML = `<div class="progress-bar" role="progressbar" style="width:${pct}%"></div>`;

      const rowBottom = create('div', 'd-flex align-items-center gap-2 mt-3');
      const btnStart = create('button', 'btn btn-sm btn-success'); btnStart.textContent = 'Start';
      const btnStop  = create('button', 'btn btn-sm btn-danger');  btnStop.textContent  = 'Stop';
      const lbl = create('label', 'form-check small ms-2 d-inline-flex align-items-center gap-2');
      lbl.innerHTML = `<input class="form-check-input" type="checkbox" ${it.covered ? 'checked' : ''}><span>Covered</span>`;

      rowBottom.appendChild(btnStart);
      rowBottom.appendChild(btnStop);
      rowBottom.appendChild(lbl);

      card.appendChild(rowTop);
      card.appendChild(bar);
      card.appendChild(rowBottom);
      agendaList.appendChild(card);

      btnStart.addEventListener('click', async ()=>{
        await fetch(routes.agendaStart.replace('__MID__',meetingId).replace('__IID__', it.id), {method:'POST', headers:{'X-CSRF-TOKEN':csrf}});
      });
      btnStop.addEventListener('click', async ()=>{
        await fetch(routes.agendaStop.replace('__MID__',meetingId).replace('__IID__', it.id), {method:'POST', headers:{'X-CSRF-TOKEN':csrf}});
      });
      lbl.querySelector('input').addEventListener('change', async ()=>{
        await fetch(routes.agendaToggle.replace('__MID__',meetingId).replace('__IID__', it.id), {method:'POST', headers:{'X-CSRF-TOKEN':csrf}});
      });
    });
  }
  renderAgenda();

  // Attendees render
  const attendeeList = $('#attendeeList');
  function renderAttendees(){
    if(!attendeeList) return; attendeeList.innerHTML='';
    attendees.forEach(a=>{
      const row = create('div','d-flex align-items-center justify-content-between p-2  mb-1');
      row.innerHTML = `
        <div class="attendance-item card shadow-sm p-3 mb-3">
          <div class="d-flex align-items-center rounded-2 gap-3">
            <div class="avatar flex-shrink-0">${(a.name||'?').split(' ').map(p=>p[0]).slice(0,2).join('').toUpperCase()}</div>
            <div class="flex-grow-1 min-w-0">
              <div class="fw-semibold text-truncate" title="${a.name||''}">${a.name||''}</div>
              <div class="small text-secondary text-truncate">${a.email||''}</div>
            </div>
            <div class="flex-shrink-0">
              <select class="form-select form-select-sm status-select fw-semibold rounded-pill" data-id="${a.id}" aria-label="Attendance status">
                <option value="present"  ${a.status==='present'?'selected':''}>Present</option>
                <option value="late"     ${a.status==='late'?'selected':''}>Late</option>
                <option value="excused"  ${a.status==='excused'?'selected':''}>Excused</option>
                <option value="absent"   ${a.status==='absent'?'selected':''}>Absent</option>
              </select>
            </div>
          </div>
        </div>
      `;
      attendeeList.appendChild(row);
    });
  }
  renderAttendees();

  document.getElementById('btnAddAttendee')?.addEventListener('click', ()=> new bootstrap.Modal(document.getElementById('attendeeAddModal')).show());
  document.getElementById('btnAddAttendeeConfirm')?.addEventListener('click', async ()=>{
    const name = (document.getElementById('attName')?.value||'').trim();
    const email = (document.getElementById('attEmail')?.value||'').trim();
    if(!name){ return alert('Name required'); }
    const res = await fetch(routes.attAdd, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf}, body: JSON.stringify({ name, email }) });
    const data = await res.json();
    attendees.push({ id:data.id, name, email, status:'present' }); renderAttendees();
    bootstrap.Modal.getInstance(document.getElementById('attendeeAddModal')).hide();
    document.getElementById('attName').value=''; document.getElementById('attEmail').value='';
  });

  // Tasks
  const taskList = document.getElementById('taskList');
  function renderTasks(){
    if(!taskList) return; taskList.innerHTML = '';
    tasks.forEach(t=>{
      const row = create('div','d-flex align-items-center gap-2 p-2 rounded-3 border mb-2');
      row.innerHTML = `
        <input class="form-check-input mt-0 me-1" type="checkbox" ${t.status==='completed'?'checked':''} data-id="${t.id}">
        <div class="flex-grow-1">
          <div class="fw-semibold text-truncate">${t.title}</div>
          <div class="small text-secondary">${t.due_on||'No due'} • ${t.status||'incomplete'}</div>
        </div>
        <a class="btn btn-sm btn-outline-secondary" href="${t.url||'#'}" target="_blank" title="Open in Asana">
          <span class="material-icons-outlined">open_in_new</span>
        </a>`;
      taskList.appendChild(row);
    });
    taskList.querySelectorAll('input[type=checkbox]').forEach(chk=>{
      chk.addEventListener('change', async ()=>{
        const id = chk.getAttribute('data-id');
        await fetch(routes.taskComplete.replace('__MID__',meetingId).replace('__TID__', id), { method:'POST', headers:{'X-CSRF-TOKEN':csrf} });
        tasks = tasks.map(t => t.id==id ? {...t, status: 'completed'} : t); renderTasks();
      });
    });
  }
  renderTasks();

  const mdNew = new bootstrap.Modal(document.getElementById('taskCreateModal'));
  const mdLink= new bootstrap.Modal(document.getElementById('taskLinkModal'));
  document.getElementById('btnNewTask')?.addEventListener('click', ()=> mdNew.show());
  document.getElementById('btnLinkTask')?.addEventListener('click', ()=> mdLink.show());
  document.getElementById('btnCreateTaskConfirm')?.addEventListener('click', async ()=>{
    const title = (document.getElementById('taskTitle')?.value||'').trim();
    const due   = document.getElementById('taskDue')?.value||null;
    const assign= document.getElementById('taskAssignee')?.value||null;
    const proj  = document.getElementById('selProject')?.value||'0000';
    const res = await fetch(routes.taskCreate, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf}, body: JSON.stringify({ title, due_on: due, assignee_gid: assign, project_gid: proj }) });
    const data = await res.json();
    tasks.unshift({ id:data.id, title, url:data.url, due_on: due, status: 'incomplete' }); renderTasks();
    mdNew.hide(); document.getElementById('taskTitle').value=''; document.getElementById('taskDue').value=''; document.getElementById('taskAssignee').value='';
  });
  document.getElementById('btnLinkTaskConfirm')?.addEventListener('click', async ()=>{
    const gid  = (document.getElementById('linkGid')?.value||'').trim();
    const url  = (document.getElementById('linkUrl')?.value||'').trim();
    const title= (document.getElementById('linkTitle')?.value||'').trim() || 'Linked task';
    const res = await fetch(routes.taskLink, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf}, body: JSON.stringify({ asana_task_gid: gid, asana_permalink_url: url, title }) });
    const data = await res.json();
    tasks.unshift({ id:data.id, title, url, due_on:null, status:'incomplete' }); renderTasks();
    mdLink.hide(); document.getElementById('linkGid').value=''; document.getElementById('linkUrl').value=''; document.getElementById('linkTitle').value='';
  });
})();
</script>