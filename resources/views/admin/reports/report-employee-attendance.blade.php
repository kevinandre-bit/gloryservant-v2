  @php
  // define your role groups
  $globalRoles = ['ADMIN','MANAGER','WEB OPERATOR'];
  $campusOnly = ['CAMPUS POC'];
  $deptOnly    = ['HP TEAM','OVERSEER'];
@endphp
@php
  // Whenever “Communication” is chosen, we’ll expand it to this array
  $communicationGroup = [
    'ADMIN',
    'Graphic Design',
    'Video Editing',
    'SEO',
    'Volunteer Care',
    'Social Media',
    'Audio Editing',
    'Radio_TV',
  ];
@endphp
 <!--start header-->
  @include ('layouts/admin_v2')
  <!--end top header-->
 <!--start header-->
  @include ('admin/nav')
  <!--end top header-->

    <!--start sidebar-->
  @include ('admin/sidebar')
  <!--end top sidebar-->

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Volunteer's</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Attendance Reports</li>
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
				<!--end breadcrumb-->
				<hr>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
									<tr>
										<tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>campus</th>
                    <th>ministry</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Total Hours</th>
                    <th>Status</th>
                </tr>
									</tr>
								</thead>
								<tbody>
									@forelse ($empAtten as $et)
                      <tr>
                          <td>{{ $et->date }}</td>
                          <td>{{ $et->employee }}</td>
                          <td>{{ $et->campus }}</td>
                          <td>{{ $et->ministry }}</td>
                          <td>{{ $et->timein }}</td>
                          <td>{{ $et->timeout }}</td>
                          <td>{{ $et->totalhours }}</td>
                          <td>{{ $et->status_timein }}</td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="8" class="text-center text-muted">
                              No attendance records found.
                          </td>
                      </tr>
                  @endforelse
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

  <!--bootstrap js-->
  <script src="/assets2/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="/assets2/js/jquery.min.js"></script>
  <!--plugins-->
  <script src="/assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="{{ asset('assets3/js/feather.min.js') }}"></script>
  <script>
		feather.replace()
	</script>
  <script src="/assets2/plugins/metismenu/metisMenu.min.js"></script>
  <script src="/assets2/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="/assets2/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
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
  <script src="/assets2/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="/assets2/js/main.js"></script>


</body>

</html>
