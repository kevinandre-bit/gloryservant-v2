<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maxton | Bootstrap 5 Admin Dashboard Template</title>
  <!--favicon-->
  <link rel="icon" href="assets2/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
  <link href="assets2/css/pace.min.css" rel="stylesheet">
  <script src="assets2/js/pace.min.js"></script>

  <!--plugins-->
  <link href="assets2/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets2/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="assets2/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="assets2/plugins/simplebar/css/simplebar.css">
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <!--bootstrap css-->
  <link href="assets2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <link href="assets2/plugins/bs-stepper/css/bs-stepper.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/plugins/notifications/css/lobibox.min.css">
  
  <link rel="stylesheet" href="assets2/css/extra-icons.css">
  <!--main css-->
  <link href="assets2/css/bootstrap-extended.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/semi-dark.css" rel="stylesheet">
  <link href="sass/bordered-theme.css" rel="stylesheet">
  <link href="sass/responsive.css" rel="stylesheet">

</head>

<body>
<!--start header--> 
  @include ('admin/nav')
  <!--end top header-->

    <!--start sidebar-->
  @include ('admin/sidebar')
  <!--end top sidebar-->
 @section('content')

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Components</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">User Profile</li>
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
      

        <div class="card rounded-4">
          <div class="card-body p-4">
             <div class="position-relative mb-5">
              <img src="assets2/images/gallery/profile-cover.png" class="img-fluid rounded-4 shadow" alt="">
              <div class="profile-avatar position-absolute top-100 start-50 translate-middle">
                @php
                  $avatarSrc = asset('assets2/images/faces/default_user4.jpg');
                  if (!empty($i)) {
                      $candidates = [
                          'assets2/faces/'.$i,
                          'assets2/images/faces/'.$i,
                          'assets/faces/'.$i,
                          'assets/images/faces/'.$i,
                      ];
                      foreach ($candidates as $candidate) {
                          if (file_exists(public_path($candidate))) {
                              $avatarSrc = asset($candidate);
                              break;
                          }
                      }
                  }
                @endphp
                <img class="mg-fluid rounded-circle p-1 bg-grd-danger shadow" src="{{ $avatarSrc }}" width="170" height="170" alt="profile photo"/>
              </div>
             </div>
              <div class="profile-info pt-5 d-flex align-items-center justify-content-between">
                <div class="">
                  <h3>@if(isset($p->firstname) || isset($p->lastname))
                      {{ $p->firstname ?? '' }} {{ $p->lastname ?? '' }}
                  @endif</h3>
                  <p class="mb-0">@isset($p->emailaddress) {{ $p->emailaddress }} @endisset<br>
                  Tabernacle of Glory @isset($c->campus) {{ $c->campus }} @endisset</p>
                </div>
                <div class="">
                  <a href="javascript:;" class="btn btn-primary rounded-5 px-4"><i class="bi bi-chat me-2"></i>Send Message</a>
                </div>
              </div>
              <div class="kewords d-flex align-items-center gap-3 mt-4 overflow-x-auto">
                 <button type="button" class="btn btn-sm btn-light rounded-5 px-4">@isset($c->ministry) {{ $c->ministry }} @endisset</button>
                 <button type="button" class="btn btn-sm btn-light rounded-5 px-4">@isset($u->work_type) {{ $u->work_type }} @endisset</button>
                 <button type="button" class="btn btn-sm btn-light rounded-5 px-4">@isset($p->employmenttype) {{ $p->employmenttype }} @endisset</button>
              </div>
          </div>
        </div>

        <div class="row">
           <div class="col-12 col-xl-8">
            <!--start stepper one--> 
        <div id="stepper1" class="bs-stepper">
          <div class="card">
          
          <div class="card-header">
            <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
              <div class="step" data-target="#test-l-1">
                <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                <div class="bs-stepper-circle">1</div>
                <div class="">
                  <h5 class="mb-0 steper-title">Personal Info</h5>
                  <p class="mb-0 steper-sub-title">Enter Your Details</p>
                </div>
                </div>
              </div>
              <div class="bs-stepper-line"></div>
              <div class="step" data-target="#test-l-2">
                <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                  <div class="bs-stepper-circle">2</div>
                  <div class="">
                    <h5 class="mb-0 steper-title">Volunteer Details</h5>
                    <p class="mb-0 steper-sub-title">Designation</p>
                  </div>
                </div>
                </div>
              </div>
           </div>
            <div class="card-body">
            <div class="bs-stepper-content">
            <form
              action="{{ url('/profile-view-'.$p->id) }}"
              method="POST"
              enctype="multipart/form-data"
              id="edit_employee_form"
            >
              @csrf
              <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
              <h5 class="mb-1">Your Personal Information</h5>
              <p class="mb-4">Enter your personal information to get closer to copanies</p>

              <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label>{{ __('First Name') }}</label>
                    <input type="text" class="form-control uppercase" name="firstname" value="@isset($p->firstname){{ $p->firstname }}@endisset">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Last Name') }}</label>
                    <input type="text" class="form-control uppercase" name="lastname" value="@isset($p->lastname){{ $p->lastname }}@endisset">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Gender') }}</label>
                    <select name="gender" class="ui dropdown form-control uppercase">
                        <option value="">{{ trim($p->gender) !== '' ? $p->gender : 'Unknown' }}</option>
                        <option value="MALE" @isset($p->gender) @if($p->gender == 'MALE') selected @endif @endisset>MALE</option>
                        <option value="FEMALE" @isset($p->gender) @if($p->gender == 'FEMALE') selected @endif @endisset>FEMALE</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Civil Status') }}</label>
                    <select name="civilstatus" class="ui dropdown form-control uppercase">
                        <option value="">{{ trim($p->civilstatus) !== '' ? $p->civilstatus : 'Unknown' }}</option>
                        <option value="SINGLE" @isset($p->civilstatus) @if($p->civilstatus == 'SINGLE') selected @endif @endisset>SINGLE</option>
                        <option value="MARRIED" @isset($p->civilstatus) @if($p->civilstatus == 'MARRIED') selected @endif @endisset>MARRIED</option>
                        <option value="ANULLED" @isset($p->civilstatus) @if($p->civilstatus == 'ANULLED') selected @endif @endisset>ANULLED</option>
                        <option value="WIDOWED" @isset($p->civilstatus) @if($p->civilstatus == 'WIDOWED') selected @endif @endisset>WIDOWED</option>
                        <option value="LEGALLY SEPARATED" @isset($p->civilstatus) @if($p->civilstatus == 'LEGALLY SEPARATED') selected @endif @endisset>LEGALLY SEPARATED</option>
                    </select>
                </div>
                    <div class="col-12 col-lg-6">
                        <label>{{ __('Temperament') }}</label>
                        <input type="text" class="form-control uppercase" name="height" value="@isset($p->height){{ $p->height }}@endisset" placeholder="000">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label>{{ __('Love Language') }}</label>
                        <input type="text" class="form-control uppercase" name="love_language" value="@isset($p->love_language){{ $p->love_language }}@endisset" placeholder="000">
                    </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Email Address (Personal)') }}</label>
                    <input type="email" class="form-control uppercase" name="emailaddress" value="@isset($p->emailaddress){{ $p->emailaddress }}@endisset" class="lowercase">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Mobile Number') }}</label>
                    <input type="text" class="form-control uppercase" class="form-control uppercase" name="mobileno" value="@isset($p->mobileno){{ $p->mobileno }}@endisset">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Age') }}</label>
                    <input type="number" class="form-control uppercase" name="age" value="@isset($p->age){{ $p->age }}@endisset" placeholder="00">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Date of Birth') }}</label>
                    <input type="text" class="form-control uppercase datepicker" name="birthday" value="@isset($p->birthday){{ $p->birthday }}@endisset" class="airdatepicker" placeholder="Date">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('National ID') }}</label>
                    <input type="text" class="form-control uppercase" name="nationalid" value="@isset($p->nationalid){{ $p->nationalid }}@endisset" placeholder="">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Place of Birth') }}</label>
                    <input type="text" class="form-control uppercase" name="birthplace" value="@isset($p->birthplace){{ $p->birthplace }}@endisset" placeholder="City, Province, Country">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Home Address') }}</label>
                    <input type="text" class="form-control uppercase" name="homeaddress" value="@isset($p->homeaddress){{ $p->homeaddress }}@endisset" placeholder="House/Unit Number, Building, Street, City, Province, Country">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Upload Profile photo') }}</label>
                    <input class="form-control file upload" value="" id="imagefile" name="image" type="file" accept="image/png, image/jpeg, image/jpg" onchange="validateFile()">
                </div>
                <br>
                <div class="col-12 col-lg-6">
                  <button class="btn btn-primary px-4" type="button" onclick="stepper1.next()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
                </div>
              </div><!---end row-->
              
              </div>

              <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">

              <h5 class="mb-1">Volunteer Details</h5>
              <p class="mb-4">Enter Your Account Details.</p>

              <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label>{{ __('campus') }}</label>
                    @php
                      $selectedCampus = old('campus', optional($campus_details)->campus); // optional() handles null
                    @endphp

                    <select name="campus" class="form-select text-uppercase ministry">
                      <option value="">{{ __('Select Campus') }}</option>
                      @foreach ($campus as $data)
                        <option value="{{ $data->campus }}"
                          {{ $data->campus == $selectedCampus ? 'selected' : '' }}>
                          {{ $data->campus }}
                        </option>
                      @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                  <label>{{ __('department') }}</label>
                  <select name="department" class="ui search dropdown form-control uppercase">
                    <option value="">Select department</option>
                    @isset($department)
                      @foreach ($department as $data2)
                        <option value="{{ $data2->department }}"
                          @if (strcasecmp(trim($data2->department), trim($campus_details->department ?? '')) === 0) selected @endif>
                          {{ $data2->department }}
                        </option>
                      @endforeach
                    @endisset
                  </select>
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('ministry') }}</label>
                    @php
                      $selectedministry = old('ministry', optional($campus_details)->ministry); // optional() handles null
                    @endphp

                    <select name="ministry" class="form-select text-uppercase ministry">
                      <option value="">{{ __('Select ministry') }}</option>
                      @foreach ($ministry as $data)
                        <option value="{{ $data->ministry }}"
                          {{ $data->ministry == $selectedministry ? 'selected' : '' }}>
                          {{ $data->ministry }}
                        </option>
                      @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('ID Number') }}</label>
                    <input type="text" class="form-control uppercase" name="idno" value="@isset($c->idno){{ $c->idno }}@endisset">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Email Address (campus)') }}</label>
                    <input type="email" class="form-control uppercase" name="corporate_email" value="@isset($c->corporate_email){{ $c->corporate_email }}@endisset" class="lowercase">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Leave Privileges') }}</label>
                    <select name="leaveprivilege" class="ui dropdown form-control uppercase">
                        <option value="">Select Leave Privilege</option>
                        @isset($leavegroup) 
                            @foreach($leavegroup as $lg)
                                <option value="{{ $lg->id }}" @isset($c->leaveprivilege) @if($lg->id == $c->leaveprivilege) selected @endif @endisset>{{ $lg->leavegroup }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <h4 class="ui dividing header">{{ __('Employment Information') }}</h4>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Employment Type') }}</label>
                    <select name="employmenttype" class="ui dropdown form-control uppercase">
                        <option value="">Select Type</option>
                        <option value="Regular" @isset($person_details->employmenttype) @if($person_details->employmenttype == 'Regular') selected @endif @endisset>Regular</option>
                        <option value="Trainee" @isset($person_details->employmenttype) @if($person_details->employmenttype == 'Trainee') selected @endif @endisset>Trainee</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Employment Status') }}</label>
                    <select name="employmentstatus" class="ui dropdown form-control uppercase">
                        <option value="">Select Status</option>
                        <option value="Active" @isset($person_details->employmentstatus) @if($person_details->employmentstatus == 'Active') selected @endif @endisset>Active</option>
                        <option value="Archived" @isset($person_details->employmentstatus) @if($person_details->employmentstatus == 'Archived') selected @endif @endisset>Archived</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Official Start Date') }}</label>
                    <input type="text" class="form-control uppercase datepicker" name="startdate" value="@isset($c->startdate){{ $c->startdate }}@endisset" class="airdatepicker" placeholder="Date">
                </div>
                <div class="col-12 col-lg-6">
                    <label>{{ __('Date Regularized') }}</label>
                    <input type="text" class="form-control uppercase datepicker" name="dateregularized" value="@isset($c->dateregularized){{ $c->dateregularized }}@endisset" class="airdatepicker" placeholder="Date">
                </div>
                <input type="hidden" name="id" value="@isset($e_id){{ $e_id }}@endisset">
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
           <div class="col-12 col-xl-4">
            <div class="card rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">About</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                 <div class="full-info">
                  <div class="info-list d-flex flex-column gap-3">
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">account_circle</span><p class="mb-0">Full Name: @if(isset($p->firstname) || isset($p->lastname))
                        {{ $p->firstname ?? '' }} {{ $p->lastname ?? '' }}
                    @endif</p></div>
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">done</span><p class="mb-0">Status: @isset($p->employmentstatus) {{ $p->employmentstatus }} @endisset</p></div>
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">code</span><p class="mb-0">Ministry: @isset($c->ministry) {{ $c->ministry }} @endisset</p></div>
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">flag</span><p class="mb-0">Date of Birth: @isset($p->birthday) {{ $p->birthday }} @endisset</p></div>
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">language</span><p class="mb-0">Language: English</p></div>
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">send</span><p class="mb-0">Email: @isset($p->emailaddress) {{ $p->emailaddress }} @endisset</p></div>
                    <div class="info-list-item d-flex align-items-center gap-3"><span class="material-icons-outlined">call</span><p class="mb-0">Phone: @isset($p->mobileno) {{ $p->mobileno }} @endisset</p></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Accounts</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>

                <div class="account-list d-flex flex-column gap-4">
                  <div class="account-list-item d-flex align-items-center gap-3">
                    <img src="assets2/images/apps/05.png" width="35" alt="">
                    <div class="flex-grow-1">
                      <h6 class="mb-0">Google</h6>
                      <p class="mb-0">Events and Reserch</p>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" checked>
                    </div>
                  </div>
                  <div class="account-list-item d-flex align-items-center gap-3">
                    <img src="assets2/images/apps/02.png" width="35" alt="">
                    <div class="flex-grow-1">
                      <h6 class="mb-0">Skype</h6>
                      <p class="mb-0">Events and Reserch</p>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" checked>
                    </div>
                  </div>
                  <div class="account-list-item d-flex align-items-center gap-3">
                    <img src="assets2/images/apps/03.png" width="35" alt="">
                    <div class="flex-grow-1">
                      <h6 class="mb-0">Slack</h6>
                      <p class="mb-0">Communication</p>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" checked>
                    </div>
                  </div>
                  <div class="account-list-item d-flex align-items-center gap-3">
                    <img src="assets2/images/apps/06.png" width="35" alt="">
                    <div class="flex-grow-1">
                      <h6 class="mb-0">Instagram</h6>
                      <p class="mb-0">Social Network</p>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" checked>
                    </div>
                  </div>
                  <div class="account-list-item d-flex align-items-center gap-3">
                    <img src="assets2/images/apps/17.png" width="35" alt="">
                    <div class="flex-grow-1">
                      <h6 class="mb-0">Facebook</h6>
                      <p class="mb-0">Social Network</p>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" checked>
                    </div>
                  </div>
                  <div class="account-list-item d-flex align-items-center gap-3">
                    <img src="assets2/images/apps/11.png" width="35" alt="">
                    <div class="flex-grow-1">
                      <h6 class="mb-0">Paypal</h6>
                      <p class="mb-0">Social Network</p>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" checked>
                    </div>
                  </div>
                </div>
              </div>
            </div>
           </div>
        </div><!--end row-->
       


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
  <script src="/assets2/plugins/metismenu/metisMenu.min.js"></script>
  <script src="/assets2/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="/assets2/js/main.js"></script>
  <script src="assets2/plugins/bs-stepper/js/bs-stepper.min.js"></script>
  <script src="assets2/plugins/bs-stepper/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="/assets2/plugins/notifications/js/lobibox.min.js"></script>
  <script src="/assets2/plugins/notifications/js/notifications.min.js"></script>
  <script src="/assets2/plugins/notifications/js/notification-custom-script.js"></script>
  <script>
    
    $(".datepicker").flatpickr();

    $(".time-picker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "Y-m-d H:i",
      });

    $(".date-time").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

    $(".date-format").flatpickr({
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });

    $(".date-range").flatpickr({
      mode: "range",
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });

    $(".date-inline").flatpickr({
      inline: true,
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });

  </script>
<script type="text/javascript">
        $('.airdatepicker').datepicker({ language: 'en', dateFormat: 'yyyy-mm-dd' });
        $('.ui.dropdown.ministry').dropdown({ onChange: function(value, text, $selectedItem) {
            $('.jobposition .menu .item').addClass('hide');
            $('.jobposition .text').text('');
            $('input[name="jobposition"]').val('');
            $('.jobposition .menu .item').each(function() {
                var dept = $(this).attr('data-dept');
                if(dept == value) {$(this).removeClass('hide');};
            });
        }});

        function validateFile() {
            var f = document.getElementById("imagefile").value;
            var d = f.lastIndexOf(".") + 1;
            var ext = f.substr(d, f.length).toLowerCase();
            if (ext == "jpg" || ext == "jpeg" || ext == "png") { } else {
                document.getElementById("imagefile").value="";
                $.notify({
                icon: 'ui icon times',
                message: "Please upload only jpg/jpeg and png image formats."},
                {type: 'danger',timer: 400});
            }
        }

        var selected = "@isset($campus_details->leaveprivilege){{ $campus_details->leaveprivilege }}@endisset";
        var items = selected.split(',');
        $('.ui.dropdown.multiple.leaves').dropdown('set selected', items);
    </script>

</body>

</html>
