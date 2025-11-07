 <!--start header-->
  @include ('layouts/admin')

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Computer</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item active" aria-current="page">Section</li>
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
      

        <!--start stepper one--> 
			   
        <h6 class="text-uppercase">Computer Details</h6>
        <hr>
				<div id="stepper1" class="bs-stepper">
				  <div class="card">
					
					<div class="card-header">
						<div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
							<div class="step" data-target="#test-l-1">
							  <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
								<div class="bs-stepper-circle">1</div>
								<div class="">
									<h5 class="mb-0 steper-title">Identification</h5>
									<p class="mb-0 steper-sub-title">Asset & Serial Info</p>
								</div>
							  </div>
							</div>
							<div class="bs-stepper-line"></div>
							<div class="step" data-target="#test-l-2">
								<div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
								  <div class="bs-stepper-circle">2</div>
								  <div class="">
									  <h5 class="mb-0 steper-title">Hardware</h5>
									  <p class="mb-0 steper-sub-title">CPU, RAM & Storage</p>
								  </div>
								</div>
							  </div>
							<div class="bs-stepper-line"></div>
							<div class="step" data-target="#test-l-3">
								<div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
								  <div class="bs-stepper-circle">3</div>
								  <div class="">
									  <h5 class="mb-0 steper-title">Software</h5>
									  <p class="mb-0 steper-sub-title">OS & Apps</p>
								  </div>
								</div>
							  </div>
							  <div class="bs-stepper-line"></div>
								<div class="step" data-target="#test-l-4">
									<div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="test-l-4">
									<div class="bs-stepper-circle">4</div>
									<div class="">
										<h5 class="mb-0 steper-title">Assignment</h5>
										<p class="mb-0 steper-sub-title">User & Warranty</p>
									</div>
									</div>
								</div>
						  </div>
					 </div>
				    <div class="card-body">
					  <div class="bs-stepper-content">
						<form action="{{ route('admin.computers.store') }}" method="POST">
							@csrf
							  <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
								<h5 class="mb-1">Identification & Inventory</h5>
								<p class="mb-4">Enter asset and serial information</p>

								<div class="row g-3">
									<div class="col-12 col-lg-6">
	                <label class="form-label">Hostname</label>
	                <input type="text" class="form-control" name="hostname" placeholder="Computer Name" required>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label for="location" class="form-label">Location (Campus)</label>
	                  <select name="location" id="location" class="form-select" required>
	                    <option value="" disabled selected>Select location</option>
	                    @foreach($campuses as $comp)
	                      <option value="{{ $comp->campus }}">{{ $comp->campus }}</option>
	                    @endforeach
	                  </select>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Serial Number</label>
	                <input type="text" class="form-control" name="serial_number" placeholder="Serial Number" required>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Model & Manufacturer</label>
	                <input type="text" class="form-control" name="manufacturer" placeholder="e.g. Dell OptiPlex">
	              </div>
									<div class="col-12 col-lg-6">
										<button type="button" class="btn btn-primary px-4" onclick="stepper1.next()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
									</div>
								</div><!---end row-->
								
							  </div>

							  <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">

								<h5 class="mb-1">Hardware Specification</h5>
								<p class="mb-4">Enter CPU, RAM, and storage details</p>

								<div class="row g-3">
									<div class="col-12 col-lg-6">
	                <label class="form-label">CPU Model & Cores</label>
	                <input type="text" class="form-control" name="cpu_model" placeholder="e.g. Intel i7-9700K, 8 cores" required>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">RAM (GB)</label>
	                <input type="number" class="form-control" name="ram_gb" placeholder="e.g. 16" required>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Storage Type</label>
	                <select class="form-select" name="storage_type" required>
	                  <option selected>---</option>
	                  <option value="SSD">SSD</option>
	                  <option value="HDD">HDD</option>
	                </select>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Storage Capacity (GB)</label>
	                <input type="number" class="form-control" name="storage_capacity_gb" placeholder="e.g. 512" required>
	              </div>
									<div class="col-12">
										<div class="d-flex align-items-center gap-3">
										  <button type="button" class="btn btn-grd-info px-4" onclick="stepper1.previous()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
											<button type="button" class="btn btn-primary px-4" onclick="stepper1.next()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
										</div>
									</div>
								</div><!---end row-->
								
							  </div>

							  <div id="test-l-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
								<h5 class="mb-1">Software & Configuration</h5>
	            <p class="mb-4">Enter OS and installed applications</p>
	            <div class="row g-3">
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Operating System</label>
	                <input type="text" class="form-control" name="operating_system" placeholder="e.g. Windows 10 Pro" required>
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">OS Version / Build</label>
	                <input type="text" class="form-control" name="os_version" placeholder="e.g. 10.0.19044">
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Installed Applications</label>
	                <input type="text" class="form-control" name="installed_applications" placeholder="List comma-separated">
	              </div>
									<div class="col-12">
										<div class="d-flex align-items-center gap-3">
										  <button type="button" class="btn btn-grd-info px-4" onclick="stepper1.previous()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
											<button type="button" class="btn btn-primary px-4" onclick="stepper1.next()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
										</div>
									</div>
								</div><!---end row-->
								
							  </div>

							  <div id="test-l-4" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger4">
								<h5 class="mb-1">Assignment & Warranty</h5>
	            <p class="mb-4">Enter user and warranty details</p>
	            <div class="row g-3">
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Assigned User</label>
	                <input type="text" class="form-control" name="assigned_user" placeholder="Employee Name or ID">
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">ministry</label>
	                <input type="text" class="form-control" name="ministry" placeholder="ministry Name">
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Purchase Date</label>
	                <input type="date" class="form-control" name="purchase_date">
	              </div>
	              <div class="col-12 col-lg-6">
	                <label class="form-label">Warranty Expiration</label>
	                <input type="date" class="form-control" name="warranty_expiration">
	              </div>
									<div class="col-12">
										<div class="d-flex align-items-center gap-3">
										  <button type="button" class="btn btn-grd-info px-4" onclick="stepper1.previous()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
											<button type="submit" class="btn btn-primary px-4">Submit<i class='bx bx-right-arrow-alt ms-2'></i></button>
										</div>
									</div>
								</div><!---end row-->
								
							  </div>
						</form>
					  </div>
					   
					</div>
				   </div>
				 </div>
				<!--end stepper one--> 


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



<script>
  @if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'bi bi-check2-circle',
        // safely inject the PHP string into JS
        msg: @json(session('success'))
      });
    });
  @endif
</script>

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
