@php use App\Classes\permission; @endphp
 <!--start header-->
  @include ('layouts/admin')
  <!--end top header--> 
  
  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Volunteers</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Informations</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
        <div class="btn-group">
          <a href="{{ url('employees/new') }}" class="btn btn-primary px-3">
            <i class="material-icons-outlined">add</i> Add
          </a>
        </div>
      </div>
				</div>
				<!--end breadcrumb-->
				<hr>
        <form method="GET" action="{{ url('employees') }}" class="mb-3">
          <div class="row g-2 align-items-end">
            <div class="col-12 col-md-2">
              <label class="form-label">Campus</label>
              <select name="campus" class="form-select">
                <option value="">All</option>
                @foreach($campuses as $c)
                  <option value="{{ $c->campus }}"
                    {{ (request('campus') == $c->campus) ? 'selected' : '' }}>
                    {{ $c->campus }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label">Department</label>
              <select name="department" class="form-select">
                <option value="">All</option>
                @foreach($department as $d)
                  <option value="{{ $d->department }}"
                    {{ (request('department') == $d->department) ? 'selected' : '' }}>
                    {{ $d->department }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label">Ministry</label>
              <select name="ministry" class="form-select">
                <option value="">All</option>
                @foreach($ministries as $m)
                  <option value="{{ $m->ministry }}"
                    {{ (request('ministry') == $m->ministry) ? 'selected' : '' }}>
                    {{ $m->ministry }}
                  </option>
                @endforeach
              </select>
            </div> 
            <div class="col-12 col-md-1">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="all"      {{ request('status','all')==='all' ? 'selected' : '' }}>All</option>
                <option value="active"   {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
                <option value="archived" {{ request('status')==='archived' ? 'selected' : '' }}>Archived</option>
              </select>
            </div>
            {{-- Activity Date Range --}}
            <div class="col-12 col-md-1">
              <label class="form-label">Activity Date — From</label>
              <input type="date"
                     name="from"
                     value="{{ request('from') }}"
                     class="form-control"
                     id="dateFrom">
            </div>
            <div class="col-12 col-md-1">
              <label class="form-label">Activity Date — To</label>
              <input type="date"
                     name="to"
                     value="{{ request('to') }}"
                     class="form-control"
                     id="dateTo">
            </div>
            @if(request('activity_window') === 'custom')
              <div class="col-12 col-md-1">
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
              </div>
              <div class="col-12 col-md-1">
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
              </div>
            @endif
            <div class="col-12 col-md-2">
              <label class="form-label">Activity Type</label>
              <select name="activity" class="form-select">
                <option value="all" {{ (request('activity','all') === 'all') ? 'selected' : '' }}>All</option>
                <option value="attendance" {{ (request('activity') === 'attendance') ? 'selected' : '' }}>Schedule Attendance</option>
                <option value="meeting" {{ (request('activity') === 'meeting') ? 'selected' : '' }}>Meeting Attendance</option>
              </select>
            </div>
            <div class="col-12 col-md-1 d-flex gap-2">
              <button class="btn btn-primary flex-grow-1" type="submit">
                Apply
              </button>
            </div>
          </div>
        </form>

{{-- Member Insight Cards --}}
<div class="row row-cols-2 row-cols-xl-4 mb-4">

  {{-- All members --}}
  <div class="col">
    <div class="card rounded-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h2 class="mb-0">{{ $memberSummary['all'] ?? 0 }}</h2>
          <span class="dash-lable bg-primary text-primary bg-opacity-10">
            All members
          </span>
        </div>
        <p class="mb-0">Matching current filters</p>
        <div class="mt-3">
          <div class="progress w-100" style="height:6px;">
            <div class="progress-bar bg-grd-primary"
                 style="width: {{ ($memberSummary['all'] ?? 0) > 0 ? 100 : 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Active --}}
  <div class="col">
    <div class="card rounded-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h2 class="mb-0">{{ $memberSummary['active'] ?? 0 }}</h2>
          <span class="dash-lable d-flex align-items-center gap-1 rounded mb-0
                       {{ ($memberSummary['active'] ?? 0) > 0 ? 'bg-success text-success bg-opacity-10' : 'bg-secondary text-secondary bg-opacity-10' }}">
            Active
          </span>
        </div>
        <p class="mb-0">Currently Active status</p>
        <div class="mt-3">
          <div class="progress w-100" style="height:6px;">
            @php
              $pctActive = ($memberSummary['all'] ?? 0) > 0
                ? round(($memberSummary['active'] / max(1,$memberSummary['all'])) * 100)
                : 0;
            @endphp
            <div class="progress-bar bg-grd-success" style="width: {{ $pctActive }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Archived --}}
  <div class="col">
    <div class="card rounded-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h2 class="mb-0">{{ $memberSummary['archived'] ?? 0 }}</h2>
          <span class="dash-lable d-flex align-items-center gap-1 rounded mb-0
                       {{ ($memberSummary['archived'] ?? 0) > 0 ? 'bg-warning text-warning bg-opacity-10' : 'bg-secondary text-secondary bg-opacity-10' }}">
            Archived
          </span>
        </div>
        <p class="mb-0">Marked as Archive</p>
        <div class="mt-3">
          <div class="progress w-100" style="height:6px;">
            @php
              $pctArc = ($memberSummary['all'] ?? 0) > 0
                ? round(($memberSummary['archived'] / max(1,$memberSummary['all'])) * 100)
                : 0;
            @endphp
            <div class="progress-bar bg-grd-warning" style="width: {{ $pctArc }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 🔴 Card 4 – No activity in 14+ days --}}
<div class="col">
  <div class="card rounded-4">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3 mb-2">
        <h2 class="mb-0">{{ $memberSummary['stale14d'] ?? 0 }}</h2>
        @php
          $pctStale = ($memberSummary['all'] ?? 0) > 0
            ? round(($memberSummary['stale14d'] / max(1,$memberSummary['all'])) * 100)
            : 0;
        @endphp
        <span class="dash-lable bg-danger text-danger bg-opacity-10">
          {{ $pctStale }}%
        </span>
      </div>
      <p class="mb-0">Last activity > 14 days ago</p>
      <div class="mt-3">
        <div class="progress w-100" style="height:6px;">
          <div class="progress-bar bg-grd-danger" style="width: {{ $pctStale }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>IDNO</th>
										<th>Full name</th>
										<th>Campus</th>
										<th>Department</th>
										<th>ministry</th>
                    <th>Last activity</th>
                    <th>Last attended</th>
                    <th>Status</th>
										<th>Options</th>
									</tr>
								</thead>
								<tbody>
									@isset($data)
								    @foreach ($data as $employee)
								        <tr class="">
								            <td>{{ $employee->idno ?? 'No ID' }}</td>
								            <td>{{ $employee->lastname }}, {{ $employee->firstname }}</td>
								            <td>{{ $employee->campus ?? 'No campus assigned' }}</td>
								            <td>{{ $employee->department }}</td>
								            <td>{{ $employee->ministry }}</td>
                            <td>
                              @php
                                $dt = $employee->last_activity ?? null;
                                $isStale = $dt && \Carbon\Carbon::parse($dt)->lt(now()->subDays(14));
                              @endphp
                              @if($dt && $dt !== '0000-00-00 00:00:00')
                                {{ \Carbon\Carbon::parse($dt)->format('M d, Y h:i A') }}
                                @if($isStale)
                                  <span class="badge bg-danger-subtle text-danger ms-2">14d+</span>
                                @endif
                              @else
                                —
                              @endif
                            </td>
                            <td>
                              @php $t = $employee->last_activity_type ?? null; @endphp
                              @if($t === 'Clock-in')
                                <span class="badge bg-success-subtle text-success">Clock-in</span>
                              @elseif($t === 'Meeting')
                                <span class="badge bg-primary-subtle text-primary">Meeting</span>
                              @else
                                —
                              @endif
                            </td>
								            <td>{{ $employee->employmentstatus == 'Active' ? 'Active' : 'Archived' }}</td>
								            <td class="align-right" >
								                @if(permission::permitted('employee-edit') === 'success')
                                <a class="text-light" href="{{ url('/profile-view-'.$employee->id) }}" class="ui circular basic icon button tiny"><i  data-feather="file"></i>  </a>  |   
								                <button type="button"
																        class="btn btn-link text-danger btn-open-delete"
																        data-id="{{ $employee->reference }}"
																        data-name="{{ $employee->firstname }} {{ $employee->lastname }}">
																  <i data-feather="trash"></i>
																</button>
                                @endif
								                <!--<a class="text-light" href="{{ url('/profile/archive/'.$employee->reference) }}" class="ui circular basic icon button tiny"><i data-feather="archive"></i></a>-->
								            </td>
								        </tr>
								    @endforeach
							    @endisset
								</tbody>
								
							</table>
						</div>
					</div>
				</div>


    </div>
  </main>
  <!--end main wrapper-->


    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->



     <!--start footer-->
     <footer class="page-footer">
      <p class="mb-0">Copyright © 2025. All right reserved.</p>
    </footer>
    <!--top footer-->

  <!--start cart-->
  
  <!--end cart-->


  <!--start switcher-->
  <button class="btn btn-grd btn-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
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
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
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
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
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
  <!--start switcher-->
{{-- One global modal on the page --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Delete Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Are you sure you want to delete:</p>
        <p class="fw-bold mb-3" id="delEmpName">this employee</p>
        <div class="small text-secondary">
          This will also delete Attendance, Schedules, Leaves, User Account, and related records.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>

        {{-- Hidden form that posts to clear() --}}
        <form id="deleteEmployeeForm" action="{{ route('profile.clear') }}" method="POST" class="d-inline">
          @csrf
          <input type="hidden" name="id" id="delEmpId">
          <button type="submit" class="btn btn-danger" id="btnConfirmDelete" disabled>Yes, delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  (function(){
    const from = document.getElementById('dateFrom');
    const to   = document.getElementById('dateTo');
    if(!from || !to) return;

    from.addEventListener('change', ()=>{ if(from.value) to.min = from.value; });
    to.addEventListener('change',   ()=>{ if(to.value)   from.max = to.value; });
    // initialize min/max on load
    if(from.value) to.min = from.value;
    if(to.value)   from.max = to.value;
  })();
</script>
<script>
(function(){
  const modalEl = document.getElementById('confirmDeleteModal');
  const modal = new bootstrap.Modal(modalEl);
  const nameEl = document.getElementById('delEmpName');
  const idInput = document.getElementById('delEmpId');
  const confirmBtn = document.getElementById('btnConfirmDelete');

  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-open-delete');
    if(!btn) return;

    // set values
    idInput.value = btn.dataset.id || '';
    nameEl.textContent = btn.dataset.name || 'this employee';
    confirmBtn.disabled = !idInput.value; // don't allow submit without id

    // show modal
    modal.show();
  });
})();
</script>
  <script src="assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="{{ asset('assets3/js/feather.min.js') }}"></script>
  <script>
		feather.replace()
	</script>
  <script src="assets2/plugins/metismenu/metisMenu.min.js"></script>
  <script src="assets2/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="assets2/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#example').DataTable();
		  } );
	</script>
	<script>
		$(document).ready(function() {
			var table = $('#example2').DataTable( {
				pageLength: 15,
				lengthChange: false,
				buttons: [ 'copy', 'excel', 'pdf', 'print']
			} );
		 
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		} );
	</script>
  <script src="assets2/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="assets2/js/main.js"></script>


</body>

</html>
