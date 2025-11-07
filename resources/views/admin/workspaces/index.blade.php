{{-- resources/views/admin/workspaces/index.blade.php --}}
@extends('layouts.new_layout')

@php
  /** @var string|null $cspNonce */
  $cspNonce = $cspNonce
    ?? (app('view')->shared('cspNonce') ?? (app()->bound('csp_nonce') ? app('csp_nonce') : null));
  $DATE_FMT = 'M j'; // display-only

  // local helper to compute effective priority (min of project/task)
  $effPriority = function(string $proj, string $task): string {
      $map = ['A'=>0,'B'=>1,'C'=>2,'D'=>3];
      $inv = ['A','B','C','D'];
      $a = $map[$proj]  ?? 3;
      $b = $map[$task]  ?? 3;
      return $inv[min($a,$b)];
  };
@endphp

@section('styles')
  <style nonce="{{ $cspNonce }}">
    .ws-sidebar { background:#f8f9fa; border-right:1px solid #e9ecef; }
    .priority-badge {
      width:24px; height:24px; display:flex; align-items:center; justify-content:center;
      font-weight:700; color:#fff; border-radius:50%; font-size:.75rem;
    }
    .priority-A { background:#ef4444; }
    .priority-B { background:#f59e0b; }
    .priority-C { background:#3b82f6; }
    .priority-D { background:#10b981; }
    .nav-link.active { background:#007bff!important; color:#fff!important; }
    .task-row { border-bottom:1px solid #f0f0f0; padding:12px 0; }
    .task-row:hover { background:#f8f9fa; }
  </style>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex" style="min-height:100vh;">
      {{-- Left Sidebar --}}
      <div class="ws-sidebar p-4" style="width:280px;">
        {{-- Current Workspace Header --}}
        @php $ws = $workspaces->firstWhere('id', $selectedId); @endphp
        @if($ws)
          <div class="mb-4">
            <div class="d-flex align-items-center mb-2">
              <i class="material-icons-outlined text-primary me-2">workspaces</i>
              <h6 class="mb-0 fw-bold">{{ $ws->name }}</h6>
            </div>
            <div class="small text-muted">Workspace ID: {{ substr($ws->id, 0, 8) }}...</div>
          </div>
        @endif

        {{-- Navigation --}}
        <div class="mb-4">
          <h6 class="text-muted text-uppercase small fw-bold mb-3">NAVIGATION</h6>
          <div class="nav-item mb-2">
            <a href="#" class="nav-link d-flex align-items-center p-2 rounded" id="nav-workload" data-view="workload">
              <i class="material-icons-outlined me-2">view_list</i> Workload
            </a>
          </div>
          <div class="nav-item mb-2">
            <a href="#" class="nav-link d-flex align-items-center p-2 rounded active" id="nav-projects" data-view="projects">
              <i class="material-icons-outlined me-2">dashboard</i> Projects
            </a>
          </div>
        </div>

        {{-- Workspaces List --}}
        <div class="mb-4">
          <h6 class="text-muted text-uppercase small fw-bold mb-3">WORKSPACES</h6>

          @foreach($workspaces as $workspace)
            <div class="nav-item mb-2">
              <a href="{{ route('admin.workspaces.index', ['ws'=>$workspace->id]) }}"
                 class="nav-link d-flex align-items-center justify-content-between p-2 rounded {{ $workspace->id === $selectedId ? 'active bg-light' : 'text-dark' }}">
                <div class="d-flex align-items-center">
                  <i class="material-icons-outlined me-2">folder</i>
                  <span>{{ $workspace->name }}</span>
                </div>

                @if($workspace->id === $selectedId)
                  <form method="POST" action="{{ route('admin.workspaces.destroy', $workspace) }}" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-link text-danger p-0" data-confirm="Delete this workspace?">
                      <i class="material-icons-outlined" style="font-size:18px;">delete</i>
                    </button>
                  </form>
                @endif
              </a>
            </div>
          @endforeach

          {{-- Add Workspace --}}
          <div class="border-2 border-dashed border-primary rounded p-3 text-center mt-3">
            <form method="POST" action="{{ route('admin.workspaces.store') }}" class="d-flex gap-2">
              @csrf
              <input name="name" class="form-control form-control-sm" placeholder="Add Workspace" required>
              <button class="btn btn-primary btn-sm" type="submit">
                <i class="material-icons-outlined">add</i>
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- Main Content Area --}}
      <div class="flex-grow-1 p-4">
        @if(!$ws)
          <div class="text-center py-5">
            <i class="material-icons-outlined text-muted" style="font-size:64px;">workspaces</i>
            <h4 class="mt-3 text-muted">Welcome to Workspaces</h4>
            <p class="text-muted">Create or select a workspace to get started with your projects.</p>
          </div>
        @else
          {{-- Projects View --}}
          <section id="view-projects">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="d-flex align-items-center">
                <i class="material-icons-outlined text-primary me-2" style="font-size:28px;">dashboard</i>
                <h4 class="mb-0 fw-bold">Projects</h4>
              </div>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="material-icons-outlined me-1">add</i>Add New Project
              </button>
            </div>

            <div class="row g-3">
            @forelse($ws->projects as $p)
              @php $progress = $p->progressPct(); @endphp
              <div class="col-md-6 col-lg-4">
                <div class="card h-100" style="border-left:4px solid {{ $p->priority === 'A' ? '#ef4444' : ($p->priority === 'B' ? '#f59e0b' : ($p->priority === 'C' ? '#3b82f6' : '#10b981')) }};">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h6 class="mb-0 fw-bold">{{ $p->name }}</h6>
                      <div class="d-flex align-items-center gap-2">
                        <span class="priority-badge priority-{{ $p->priority }}">{{ $p->priority }}</span>
                        <form method="POST" action="{{ route('admin.projects.destroy', $p) }}" class="d-inline">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-link text-muted p-0" data-confirm="Delete project?">
                            <i class="material-icons-outlined" style="font-size:18px;">delete</i>
                          </button>
                        </form>
                      </div>
                    </div>

                    <div class="text-muted small mb-3">
                      <i class="material-icons-outlined me-1" style="font-size:16px">schedule</i>
                      Due: {{ optional($p->deadline)->format('M j') ?? 'No due date' }}
                    </div>

                    <div class="mb-3">
                      <div class="small text-muted mb-1">Progress: {{ $progress }}%</div>
                      <div class="progress" style="height:6px;">
                        <div class="progress-bar"
                             style="width:{{ $progress }}%; background:{{ $p->priority === 'A' ? '#ef4444' : ($p->priority === 'B' ? '#f59e0b' : ($p->priority === 'C' ? '#3b82f6' : '#10b981')) }};"
                             role="progressbar"></div>
                      </div>
                    </div>

                    <div class="small text-muted mb-3">{{ $p->tasks->count() }} tasks total</div>

                    <button class="btn btn-primary btn-sm w-100 view-tasks-btn"
                            data-project-id="{{ $p->id }}"
                            data-project-name="{{ $p->name }}"
                            data-deadline="{{ optional($p->deadline)->format('M j') ?? 'No due date' }}"
                            data-progress="{{ $progress }}">
                      View Tasks <i class="material-icons-outlined ms-1" style="font-size:16px;">open_in_new</i>
                    </button>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="text-center py-5">
                  <i class="material-icons-outlined text-muted" style="font-size:64px;">assignment</i>
                  <h5 class="mt-3 text-muted">No Projects Yet</h5>
                  <p class="text-muted">Create your first project to get started.</p>
                </div>
              </div>
            @endforelse
            </div>
          </section>

          {{-- Workload View --}}
          <section id="view-workload" class="d-none">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="d-flex align-items-center">
                <i class="material-icons-outlined text-primary me-2" style="font-size:28px;">view_list</i>
                <h4 class="mb-0 fw-bold">Workload View</h4>
              </div>
              <div class="d-flex align-items-center gap-3">
                <label class="small text-muted">Sort By:</label>
                <select id="wlSort" class="form-select form-select-sm" style="width:auto">
                  <option value="priority" selected>Effective Priority (A–D)</option>
                  <option value="deadline">Due Date</option>
                  <option value="project">Project Name</option>
                </select>
                <button id="wlOrder" class="btn btn-sm btn-outline-primary" type="button" aria-pressed="false">
                  <i class="material-icons-outlined me-1">south</i> Ascending
                </button>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <h6 class="mb-0">Active Tasks (<span id="activeTaskCount">0</span>)</h6>
              </div>
              <div class="card-body p-0">
                <div id="wlList">
                  @if($ws)
                    @foreach($ws->projects as $project)
                      @foreach($project->tasks->where('completed', false) as $task)
                        @php $ep = $effPriority($project->priority, $task->priority); @endphp
                        <div class="task-row d-flex align-items-center justify-content-between px-3">
                          <div class="d-flex align-items-center">
                            <form method="POST" action="{{ route('admin.tasks.toggle', $task) }}" class="me-3 js-auto-submit-form">
                              @csrf @method('PATCH')
                              <input type="checkbox" class="form-check-input js-auto-submit">
                            </form>
                            <div>
                              <div class="fw-semibold">{{ $task->title }}</div>
                              <div class="small text-muted">
                                <i class="material-icons-outlined me-1" style="font-size:14px">layers</i>{{ $project->name }}
                              </div>
                            </div>
                          </div>
                          <div class="d-flex align-items-center gap-2">
                            <span class="priority-badge priority-{{ $ep }}">{{ $ep }}</span>
                            <div class="small text-muted">
                              <i class="material-icons-outlined" style="font-size:14px">event</i> {{ $task->deadline ? $task->deadline->format('M j') : '—' }}
                            </div>
                          </div>
                        </div>
                      @endforeach
                    @endforeach
                  @endif
                </div>
              </div>
            </div>

            <div class="card mt-4">
              <div class="card-header">
                <h6 class="mb-0">Completed Tasks (<span id="completedTaskCount">0</span>)</h6>
              </div>
              <div class="card-body p-0">
                <div id="completedList">
                  @if($ws)
                    @foreach($ws->projects as $project)
                      @foreach($project->tasks->where('completed', true) as $task)
                        @php $ep = $effPriority($project->priority, $task->priority); @endphp
                        <div class="task-row d-flex align-items-center justify-content-between px-3">
                          <div class="d-flex align-items-center">
                            <form method="POST" action="{{ route('admin.tasks.toggle', $task) }}" class="me-3 js-auto-submit-form">
                              @csrf @method('PATCH')
                              <input type="checkbox" class="form-check-input js-auto-submit" checked>
                            </form>
                            <div>
                              <div class="fw-semibold text-decoration-line-through text-muted">{{ $task->title }}</div>
                              <div class="small text-muted">
                                <i class="material-icons-outlined me-1" style="font-size:14px">layers</i>{{ $project->name }}
                              </div>
                            </div>
                          </div>
                          <div class="d-flex align-items-center gap-2">
                            <span class="priority-badge priority-{{ $ep }}">{{ $ep }}</span>
                            <div class="small text-muted">
                              <i class="material-icons-outlined" style="font-size:14px">event</i> {{ $task->deadline ? $task->deadline->format('M j') : '—' }}
                            </div>
                          </div>
                        </div>
                      @endforeach
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
          </section>

          {{-- Project Tasks View --}}
          <section id="view-project-tasks" class="d-none">
            <div class="d-flex align-items-center mb-4">
              <button class="btn btn-link text-primary p-0 me-3" id="btnBackProjects" data-view="projects">
                <i class="material-icons-outlined me-1">arrow_back</i>Back to Projects
              </button>
              <div class="d-flex align-items-center">
                <i class="material-icons-outlined text-primary me-2" style="font-size:28px;">assignment</i>
                <div>
                  <h4 class="mb-0 fw-bold" id="project-title">Project Name</h4>
                  <div class="small text-muted" id="project-info">Due: — | Progress: 0%</div>
                </div>
              </div>
              <button class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="material-icons-outlined me-1">add</i>Add Task
              </button>
            </div>

            <div class="card">
              <div class="card-header">
                <h6 class="mb-0">Tasks</h6>
              </div>
              <div class="card-body p-0">
                <div id="projectTasksList">
                  @if($ws)
                    @foreach($ws->projects as $p)
                      <div class="project-tasks" data-project-id="{{ $p->id }}" style="display:none;">
                        @forelse($p->tasks as $t)
                          <div class="task-row d-flex align-items-center justify-content-between px-3">
                            <div class="d-flex align-items-center">
                              <form method="POST" action="{{ route('admin.tasks.toggle', $t) }}" class="me-3 js-auto-submit-form">
                                @csrf @method('PATCH')
                                <input type="checkbox" class="form-check-input js-auto-submit" {{ $t->completed ? 'checked' : '' }}>
                              </form>
                              <div>
                                <div class="fw-semibold {{ $t->completed ? 'text-decoration-line-through text-muted' : '' }}">{{ $t->title }}</div>
                                <div class="small text-muted">
                                  Priority:
                                  <span class="priority-badge priority-{{ $t->priority }}">{{ $t->priority }}</span>
                                  @if($t->deadline) | Due: {{ $t->deadline->format('M j') }} @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        @empty
                          <div class="text-center text-muted py-4">No tasks yet. Add one above.</div>
                        @endforelse
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
          </section>
        @endif
      </div>
    </div>
  </div>
</main>

{{-- Add Project Modal --}}
<div class="modal fade" id="addProjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('admin.projects.store') }}">
        @csrf
        <input type="hidden" name="workspace_id" value="{{ $ws->id ?? '' }}">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Project Name</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deadline</label>
            <input name="deadline" type="date" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
              <option value="A">🔴 High (A)</option>
              <option value="B">🟡 Medium (B)</option>
              <option value="C" selected>🔵 Normal (C)</option>
              <option value="D">🟢 Low (D)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Project</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Add Task Modal --}}
<div class="modal fade" id="addTaskModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('admin.tasks.store') }}">
        @csrf
        <input type="hidden" name="project_id" id="task-project-id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Task Title</label>
            <input name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deadline</label>
            <input name="deadline" type="date" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
              <option value="A">🔴 High (A)</option>
              <option value="B">🟡 Medium (B)</option>
              <option value="C" selected>🔵 Normal (C)</option>
              <option value="D">🟢 Low (D)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Task</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script nonce="{{ $cspNonce }}">
  (function () {
    let currentView = 'projects';
    let currentProject = null;

    const $  = (sel, root=document) => root.querySelector(sel);
    const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

    function setView(view) {
      currentView = view;

      // nav state
      $$('.nav-link').forEach(a => a.classList.remove('active'));
      const nav = document.getElementById('nav-' + view);
      if (nav) nav.classList.add('active');

      // sections
      $('#view-projects')?.classList.toggle('d-none', view !== 'projects');
      $('#view-workload')?.classList.toggle('d-none', view !== 'workload');
      $('#view-project-tasks')?.classList.toggle('d-none', view !== 'project-tasks');

      if (view === 'workload') loadWorkload();
    }

    function viewProjectTasks(projectId, projectName, deadline, progress) {
      currentProject = { id: projectId, name: projectName };

      // header
      $('#project-title').textContent = projectName;
      $('#project-info').textContent  = `Due: ${deadline} | Progress: ${progress}%`;
      $('#task-project-id').value     = projectId;

      // show matching task list
      $$('.project-tasks').forEach(el => {
        el.style.display = (el.dataset.projectId === projectId) ? 'block' : 'none';
      });

      setView('project-tasks');
    }

    function loadWorkload() {
      const activeTasks    = $$('#wlList .task-row').length;
      const completedTasks = $$('#completedList .task-row').length;
      $('#activeTaskCount').textContent    = activeTasks;
      $('#completedTaskCount').textContent = completedTasks;
    }

    // Thin alias
    function renderWorkload() { loadWorkload(); }

    // ---- EVENT BINDINGS (CSP-safe) ----

    // Sidebar / back buttons
    $('#nav-workload')?.addEventListener('click', (e) => { e.preventDefault(); setView('workload'); });
    $('#nav-projects')?.addEventListener('click', (e) => { e.preventDefault(); setView('projects'); });
    $('#btnBackProjects')?.addEventListener('click', (e) => { e.preventDefault(); setView('projects'); });

    // View Tasks (delegated)
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.view-tasks-btn');
      if (!btn) return;
      e.preventDefault();
      viewProjectTasks(
        btn.dataset.projectId,
        btn.dataset.projectName,
        btn.dataset.deadline,
        parseInt(btn.dataset.progress || '0', 10)
      );
    });

    // Auto-submit checkboxes (no inline onchange)
    document.addEventListener('change', (e) => {
      if (!e.target.matches('.js-auto-submit')) return;
      const form = e.target.closest('.js-auto-submit-form');
      if (form) form.submit();
    });

    // Confirm delete (no inline onclick)
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-confirm]');
      if (!btn) return;
      const msg = btn.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) {
        e.preventDefault();
        e.stopPropagation();
      }
    });

    // Workload sort controls
    $('#wlSort')?.addEventListener('change', renderWorkload);
    $('#wlOrder')?.addEventListener('click', function(){
      const pressed = this.getAttribute('aria-pressed') === 'true';
      this.setAttribute('aria-pressed', String(!pressed));
      this.innerHTML = pressed
        ? '<i class="material-icons-outlined me-1">south</i> Ascending'
        : '<i class="material-icons-outlined me-1">north</i> Descending';
      renderWorkload();
    });

    // INIT
    setView('projects');
  })();
</script>
@endsection