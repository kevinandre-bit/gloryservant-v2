<!--start header-->
@include('layouts/admin')
<!--end top header-->

@php
  // counts for the tab badges
  $counts = [
    'categories'  => isset($categories) ? $categories->count() : 0,
    'metrics'     => isset($metrics) ? $metrics->count() : 0,
    'teams'       => isset($teams) ? $teams->count() : 0,
    'people'      => isset($people) ? $people->count() : 0,
    'assignments' => isset($assignments) ? $assignments->count() : 0,
  ];

  // prefer the pane that posted the form (each form sends hidden "pane")
  $preferredPane = old('pane') ?? session('pane');
@endphp
<style>
  /* --- Section switcher helpers --- */
  .rs-pane{display:none}
  .rs-pane.is-active{display:block}
  .rs-tab.active{
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0 0 .15rem rgba(13,110,253,.25);
  }
</style>

<!--start main wrapper-->
<main class="main-wrapper">
  <div class="main-content">
  	<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Components</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">REPORT SYSTEM — CREATION</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<div class="btn-group">
							<button type="button" class="btn btn-primary">Settings</button>
							<button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">Action</a>
								<a class="dropdown-item" href="javascript:;">Another action</a>
								<a class="dropdown-item" href="javascript:;">Something else here</a>
								<div class="dropdown-divider"></div>	<a class="dropdown-item" href="javascript:;">Separated link</a>
							</div>
						</div>
					</div>
				</div>

    {{-- ===== Pretty Section Picker (drop-in) ===== --}}
<style>
  .rs-tabs{
    display:flex; flex-wrap:wrap; gap:.5rem;
  }
  .rs-tab{
    --tab-color: var(--bs-primary);
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.12);
    color: #cfd6e4;
    padding:.5rem .85rem;
    border-radius:.8rem;
    display:flex; align-items:center; gap:.5rem;
    transition: all .18s ease;
    line-height:1;
  }
  .rs-tab i{ font-size:1.1rem; opacity:.9 }
  .rs-tab .lbl{font-weight:600; letter-spacing:.2px}
  .rs-tab .badge{
    background: rgba(255,255,255,.12) !important;
    color:#dfe6f4; border:0; padding:.15rem .45rem;
  }
  .rs-tab:hover{
    transform: translateY(-1px);
    border-color: color-mix(in oklab, var(--tab-color) 60%, #fff 0%);
    box-shadow: 0 6px 18px -10px color-mix(in oklab, var(--tab-color) 55%, transparent);
  }
  .rs-tab.active{
    color:#fff;
    background: linear-gradient(135deg,
       color-mix(in oklab, var(--tab-color) 85%, #fff 0%) 0%,
       color-mix(in oklab, var(--tab-color) 55%, #000 0%) 100%);
    border-color: transparent !important;
  }
  .rs-tab.active .badge{
    background: rgba(0,0,0,.12) !important;
    color:#fff;
  }

  /* Per-tab accents (tweak to taste) */
  .rs-tab[data-sec="categories"]  { --tab-color: #0d6efd; } /* teal */
  .rs-tab[data-sec="metrics"]     { --tab-color: #0d6efd; } /* indigo */
  .rs-tab[data-sec="teams"]       { --tab-color: #0d6efd; } /* purple */
  .rs-tab[data-sec="people"]      { --tab-color: #0d6efd; } /* amber */
  .rs-tab[data-sec="assignments"] { --tab-color: #0d6efd; } /* cyan */
</style>

<div class="card mb-3">
  <div class="card-body rs-tabs">
    <button type="button" class="btn rs-tab" data-sec="categories">
      <i class="bx bx-category"></i>
      <span class="lbl">Categories</span>
      <span class="badge">{{ $counts['categories'] ?? 0 }}</span>
    </button>

    <button type="button" class="btn rs-tab" data-sec="metrics">
      <i class="bx bx-slider-alt"></i>
      <span class="lbl">Metrics</span>
      <span class="badge">{{ $counts['metrics'] ?? 0 }}</span>
    </button>

    <button type="button" class="btn rs-tab" data-sec="teams">
      <i class="bx bx-grid-alt"></i>
      <span class="lbl">Teams</span>
      <span class="badge">{{ $counts['teams'] ?? 0 }}</span>
    </button>

    <button type="button" class="btn rs-tab" data-sec="people">
      <i class="bx bx-group"></i>
      <span class="lbl">Team Members</span>
      <span class="badge">{{ $counts['people'] ?? 0 }}</span>
    </button>

    <button type="button" class="btn rs-tab" data-sec="assignments">
      <i class="bx bx-link-alt"></i>
      <span class="lbl">Assignments</span>
      <span class="badge">{{ $counts['assignments'] ?? 0 }}</span>
    </button>
  </div>
</div>

    {{-- ============================ CATEGORIES ============================ --}}
    <section class="rs-pane" data-pane="categories">
      <h6 class="mb-0 text-uppercase">CATEGORIES</h6>
      <hr>
      <div class="row g-3 mb-4">
        <!-- Table -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table id="categoriesTable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Active</th>
                      <th>Created</th>
                      <th style="width:160px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($categories as $c)
                      <tr>
                        <td>{{ $c->name }}</td>
                        <td>{{ ($c->active ?? 0) ? 'Yes' : 'No' }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($c->created_at)->toDateString() }}</td>
                        <td>
                          <a href="{{ route('admin.reports.categories.edit', $c->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                          <form method="post" action="{{ route('admin.reports.categories.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Delete this category? This cannot be undone.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Name</th>
                      <th>Active</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Create -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-3">Categories — Create</h5>
              <form method="post" action="{{ route('admin.reports.categories.store') }}">
                @csrf
                <input type="hidden" name="pane" value="categories">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="active" value="1" id="catActive" checked>
                  <label class="form-check-label" for="catActive">Active</label>
                </div>
                <button class="btn btn-primary w-100">Create Category</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ============================ METRICS ============================ --}}
    
    <section class="rs-pane is-active" data-pane="metrics">
  <h6 class="mb-0 text-uppercase">METRICS</h6>
  <hr>
  <div class="row g-3 mb-4">
    <!-- Table -->
    <div class="col-lg-8">
      <div class="card rounded-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
			    <thead class="table-light">
				  <tr>
				    <th>Name</th>
				    <th>Mode</th>
				    <th>Category</th>     <!-- NEW -->
				    <th>Status Set</th>
				    <th>Weight</th>
				    <th>Active</th>
				    <th style="width:160px;">Actions</th>
				  </tr>
				</thead>
			  <tbody>
			    @forelse($metrics as $m)
			      @php
			        $setName = null;
			        if (!empty($m->status_set_id)) {
			          $set = ($statusSets ?? collect())->firstWhere('id', $m->status_set_id);
			          $setName = $set->name ?? null;
			        }
			      @endphp
			      <tr>
			        <td class="fw-semibold">{{ $m->name }}</td>
			        @php
					  $cats  = $categories ?? collect();
					  $byCat = ($metrics ?? collect())->groupBy(function($m) use ($cats) {
					    $cid = property_exists($m, 'category_id') ? $m->category_id : null;
					    $cat = $cid !== null ? $cats->firstWhere('id', $cid) : null;
					    return $cat->name ?? 'Uncategorized';
					  });
					@endphp
					<td>{{ $cat->name ?? '—' }}</td>
			        <td>
			          <span class="badge bg-secondary-subtle text-secondary">
			            {{ $m->value_mode === 'status_set' ? 'Status Set' : 'Scale (0–100)' }}
			          </span>
			        </td>
			        <td>{{ $setName ?: '—' }}</td>
			        <td>{{ (float)$m->weight }}</td>
			        <td>
			          @if($m->active)
			            <span class="badge bg-success-subtle text-success">Yes</span>
			          @else
			            <span class="badge bg-secondary-subtle text-secondary">No</span>
			          @endif
			        </td>
			        <td>
			          <a href="{{ route('admin.reports.metrics.edit', $m->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
			          <form method="post" action="{{ route('admin.reports.metrics.destroy', $m->id) }}" class="d-inline" onsubmit="return confirm('Delete this metric?');">
			            @csrf @method('DELETE')
			            <button class="btn btn-sm btn-outline-danger">Delete</button>
			          </form>
			        </td>
			      </tr>
			    @empty
			      <tr><td colspan="6" class="text-secondary text-center py-4">No metrics yet.</td></tr>
			    @endforelse
			  </tbody>
			</table>
          </div>
        </div>
      </div>
    </div>

    <!-- Create -->
    <div class="col-lg-4">
      <div class="card rounded-4">
        <div class="card-body">
          <h5 class="mb-3">Create Metric</h5>
          <form method="post" action="{{ route('admin.reports.metrics.store') }}">
            @csrf
            <input type="hidden" name="pane" value="metrics">

            <div class="mb-3">
              <label class="form-label">Name</label>
              <input name="name" class="form-control" placeholder="e.g., Attendance, Completion, Productivity" required>
            </div>
            <div class="mb-3">
      			  <label class="form-label">Category</label>
      			  <select name="category_id" class="form-select" required>
      			    @foreach($categories as $c)
      			      <option value="{{ $c->id }}">{{ $c->name }}</option>
      			    @endforeach
      			  </select>
      			</div>
            
            <div class="mb-3">
              <label class="form-label d-flex align-items-center justify-content-between">
                <span>Value Mode</span>
                <span class="small text-secondary">Pick one</span>
              </label>
              <select name="value_mode" id="value_mode" class="form-select" required>
                <option value="status_set">Status Set (dropdown)</option>
                <option value="scale">Scale (0–100)</option> {{-- FIXED --}}
              </select>
              <div class="form-text text-secondary">Use Status Set for things like “Present/Absent…”, “Completed/Not Completed…”, “Live/Recording/Replay…”.</div>
            </div>

            <div class="mb-3" id="statusSetWrap">
      			  <label class="form-label">Status Set</label>
      			  <select name="status_set_id" class="form-select">
      			    <option value="">— select a status set —</option>
      			    @foreach($statusSets as $s)
      			      <option value="{{ $s->id }}">{{ $s->name }}</option>
      			    @endforeach
      			  </select>
      			  <div class="form-text text-secondary">
      			    Create & manage sets (options + scores) in the “Status Sets” section.
      			  </div>
      			</div>

            <div class="mb-3">
              <label class="form-label">Weight</label>
              <input name="weight" type="number" step="0.01" value="1" class="form-control">
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="active" value="1" id="metActive" checked>
              <label class="form-check-label" for="metActive">Active</label>
            </div>

            <button class="btn btn-primary w-100">Create Metric</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
  (function(){
    const mode = document.getElementById('value_mode');
    const wrap = document.getElementById('statusSetWrap');
    function sync(){ wrap.style.display = (mode.value === 'status_set') ? '' : 'none'; }
    if (mode){ mode.addEventListener('change', sync); sync(); }
  })();
</script>
@endpush
    {{-- ============================ TEAMS ============================ --}}
    <section class="rs-pane" data-pane="teams">
      <h6 class="mb-0 text-uppercase">TEAMS</h6>
      <hr>
      <div class="row g-3 mb-4">
        <!-- Table -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table id="teamsTable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Active</th>
                      <th>Created</th>
                      <th style="width:160px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($teams as $t)
                      <tr>
                        <td>{{ $t->name }}</td>
                        <td>{{ ($t->active ?? 0) ? 'Yes' : 'No' }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($t->created_at)->toDateString() }}</td>
                        <td>
                          <a href="{{ route('admin.reports.teams.edit', $t->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                          <form method="post" action="{{ route('admin.reports.teams.destroy', $t->id) }}" class="d-inline" onsubmit="return confirm('Delete this team? This cannot be undone.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Name</th>
                      <th>Active</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Create -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-3">Teams — Create</h5>
              <form method="post" action="{{ route('admin.reports.teams.store') }}">
                @csrf
                <input type="hidden" name="pane" value="teams">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="active" value="1" id="teamActive" checked>
                  <label class="form-check-label" for="teamActive">Active</label>
                </div>
                <button class="btn btn-primary w-100">Create Team</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ============================ TEAM MEMBERS ============================ --}}
    <section class="rs-pane" data-pane="people">
      <h6 class="mb-0 text-uppercase">TEAM MEMBERS</h6>
      <hr>
      <div class="row g-3 mb-4">
        <!-- Table -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-3">Team Members</h5>
              <div class="table-responsive">
                <table id="peopleTable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>IDNO</th>
                      <th>Team</th>
                      <th>Status</th>
                      <th style="width:160px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($people as $p)
                      <tr>
                        <td>{{ $p->last_name }}, {{ $p->first_name }}</td>
                        <td>{{ $p->idno }}</td>
                        <td>{{ optional($teams->firstWhere('id',$p->team_id))->name }}</td>
                        <td>{{ ($p->status ?? 'active') }}</td>
                        <td>
                          <a href="{{ route('admin.reports.people.edit', $p->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                          <form method="post" action="{{ route('admin.reports.people.destroy', $p->id) }}" class="d-inline" onsubmit="return confirm('Delete this person?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Name</th>
                      <th>IDNO</th>
                      <th>Team</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Create -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-3">Team Member — Create</h5>
              <form method="post" action="{{ route('admin.reports.people.store') }}">
                @csrf
                <input type="hidden" name="pane" value="people">
                <div class="mb-3">
                  <label class="form-label">First Name</label>
                  <input name="first_name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Last Name</label>
                  <input name="last_name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">IDNO (unique)</label>
                  <input name="idno" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Team</label>
                  <select name="team_id" class="form-select" required>
                    @foreach($teams as $t)
                      <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select">
                    <option value="active" selected>active</option>
                    <option value="inactive">inactive</option>
                  </select>
                </div>
                <button class="btn btn-primary w-100">Create Member</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ============================ ASSIGNMENTS ============================ --}}
    <section class="rs-pane" data-pane="assignments">
      <h6 class="mb-0 text-uppercase">ASSIGNMENTS (METRICS ↔ PEOPLE)</h6>
      <hr>
      <div class="row g-3 mb-4">
        <!-- Table -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="m-0">Assignments</h5>
                <form method="get" action="{{ route('admin.reports.setup') }}#assignments" class="d-flex gap-2">
                  <select name="person_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">— Filter by person —</option>
                    @foreach($people as $p)
                      <option value="{{ $p->id }}" {{ (request('person_filter')==$p->id)?'selected':'' }}>
                        {{ $p->last_name }}, {{ $p->first_name }}
                      </option>
                    @endforeach
                  </select>
                  <input type="date" name="date_filter" class="form-control form-control-sm" value="{{ request('date_filter') }}" onchange="this.form.submit()">
                </form>
              </div>

              <div class="table-responsive">
                <table id="assignmentsTable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>Person</th>
                      <th>Metric</th>
                      <th>Category</th>
                      <th>Starts</th>
                      <th>Ends</th>
                      <th style="width:160px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($assignments as $a)
                      <tr>
                        <td>{{ $a->person_last }}, {{ $a->person_first }}</td>
                        <td>{{ $a->metric_name }}</td>
                        <td>{{ $a->category_name }}</td>
                        <td>{{ $a->starts_on ? \Illuminate\Support\Carbon::parse($a->starts_on)->toDateString() : '—' }}</td>
                        <td>{{ $a->ends_on   ? \Illuminate\Support\Carbon::parse($a->ends_on)->toDateString()   : '—' }}</td>
                        <td>
                          <a href="{{ route('admin.reports.assignments.edit', $a->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                          <form method="post" action="{{ route('admin.reports.assignments.destroy', $a->id) }}" class="d-inline" onsubmit="return confirm('Delete this assignment?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Person</th>
                      <th>Metric</th>
                      <th>Category</th>
                      <th>Starts</th>
                      <th>Ends</th>
                      <th>Actions</th>
                    </tr>
                  </tfoot>
                </table>
              </div>

            </div>
          </div>
        </div>

        <!-- Create (Incremental Picker) -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-3">Assignment — Create</h5>
              <form method="post" action="{{ route('admin.reports.assignments.bulk_store') }}" id="assignForm">
                @csrf
                <input type="hidden" name="pane" value="assignments">

                <div class="mb-3">
                  <label class="form-label">Person</label>
                  <select name="person_id" class="form-select" required>
                    @foreach($people as $p)
                      <option value="{{ $p->id }}">{{ $p->last_name }}, {{ $p->first_name }} ({{ $p->idno }})</option>
                    @endforeach
                  </select>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Starts On</label>
                    <input type="date" name="starts_on" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Ends On</label>
                    <input type="date" name="ends_on" class="form-control">
                  </div>
                </div>

                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="active" value="1" id="assActive" checked>
                  <label class="form-check-label" for="assActive">Active</label>
                </div>

                <div class="mb-2 d-flex align-items-center justify-content-between">
                  <label class="form-label m-0">Metrics</label>
                  <small class="text-secondary"><span id="metricCountAssign">0</span> selected</small>
                </div>

                <div id="metricRowsAssign" class="vstack gap-2">
                  <div class="d-flex gap-2 align-items-center metric-row">
                    <select class="form-select metric-select" style="flex:1" required>
  <option value="">— choose a metric —</option>
  @foreach($metrics as $m)
    <option value="{{ $m->id }}">{{ $m->name }}</option>
  @endforeach
</select>
                    <input type="hidden" name="metrics[]" class="metric-hidden">
                    <button type="button" class="btn btn-outline-secondary btn-sm clear-row" title="Clear">Clear</button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">Remove</button>
                  </div>
                </div>

                <button type="button" class="btn btn-outline-primary w-100 mt-2" id="addRowBtnAssign">
                  + Add another metric
                </button>

                {{-- Template for new rows --}}
                <template id="metricRowTplAssign">
                  <div class="d-flex gap-2 align-items-center metric-row">
                    <select class="form-select metric-select" style="flex:1" required>
  <option value="">— choose a metric —</option>
  @foreach($metrics as $m)
    <option value="{{ $m->id }}">{{ $m->name }}</option>
  @endforeach
</select>
                    <input type="hidden" name="metrics[]" class="metric-hidden">
                    <button type="button" class="btn btn-outline-secondary btn-sm clear-row" title="Clear">Clear</button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">Remove</button>
                  </div>
                </template>

                <hr class="my-3">

                <button class="btn btn-primary w-100">Create Assignments</button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </section>

  </div>
</main>
<!--end main wrapper-->

{{-- ============================ SCRIPTS ============================ --}}
<script>
/* Section switcher controller */
(() => {
  const panes = Array.from(document.querySelectorAll('.rs-pane'));
  const tabs  = Array.from(document.querySelectorAll('.rs-tab'));
  const serverPreferred = @json($preferredPane ?? null);

  function qsPane(){
    const u = new URL(window.location.href);
    return u.searchParams.get('sec') || (location.hash ? location.hash.slice(1) : '');
  }
  function savedPane(){ try { return localStorage.getItem('rs-sec') || '' } catch { return '' } }
  function chooseInitial(){
    return serverPreferred || qsPane() || savedPane() || 'categories';
  }
  function showPane(sec, {push=true} = {}){
    panes.forEach(p => p.classList.toggle('is-active', p.dataset.pane === sec));
    tabs.forEach(t => t.classList.toggle('active', t.dataset.sec === sec));
    try { localStorage.setItem('rs-sec', sec) } catch {}
    if (push){
      const url = new URL(location.href);
      url.searchParams.set('sec', sec);
      history.pushState({sec}, '', url);
    }
  }
  const initial = chooseInitial();
  showPane(initial, {push:false});
  tabs.forEach(btn => btn.addEventListener('click', () => showPane(btn.dataset.sec)));
  window.addEventListener('popstate', () => showPane(qsPane() || savedPane() || 'categories', {push:false}));
  window.addEventListener('hashchange', () => showPane(qsPane(), {push:true}));
})();

/* Minimal, self-contained initializer for the Assign metrics picker */
(function(){
  const rowsWrap = document.getElementById('metricRowsAssign');
  const tpl      = document.getElementById('metricRowTplAssign');
  const addBtn   = document.getElementById('addRowBtnAssign');

  if (!rowsWrap || !tpl || !addBtn) return;

  function wireRow(node){
    const sel = node.querySelector('.metric-select');
    const hid = node.querySelector('.metric-hidden');
    const btnClear  = node.querySelector('.clear-row');
    const btnRemove = node.querySelector('.remove-row');

    sel.addEventListener('change', () => { hid.value = sel.value || ''; count(); });
    btnClear.addEventListener('click', () => { sel.value=''; hid.value=''; count(); });
    btnRemove.addEventListener('click', () => { node.remove(); count(); });
  }
  function addRow(){
    const node = tpl.content.firstElementChild.cloneNode(true);
    rowsWrap.appendChild(node);
    wireRow(node);
  }
  function count(){
    const n = rowsWrap.querySelectorAll('.metric-hidden')
                      .length ? Array.from(rowsWrap.querySelectorAll('.metric-hidden')).filter(i=>i.value).length : 0;
    const badge = document.getElementById('metricCountAssign');
    if (badge) badge.textContent = n;
  }

  const first = rowsWrap.querySelector('.metric-row');
  if (first) wireRow(first);
  addBtn.addEventListener('click', (e)=>{ e.preventDefault(); addRow(); });
  count();
})();
</script>