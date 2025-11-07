
@extends('layouts.admin')

@php use App\Classes\permission; @endphp
{{-- Section for setting meta information for the page --}}
@section('meta')
    <title>New Employee | Glory Servant</title>
    <meta name="description" content="Workday add new employee, delete employee, edit employee">
@endsection

{{-- Section to include custom styles, e.g., datepicker CSS --}}
@section('styles')
    <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
@endsection

{{-- Main content section of the page --}}
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
                                <li class="breadcrumb-item active" aria-current="page">Wizard</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="ms-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary">Settings</button>
                            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">    <a class="dropdown-item" href="javascript:;">Action</a>
                                <a class="dropdown-item" href="javascript:;">Another action</a>
                                <a class="dropdown-item" href="javascript:;">Something else here</a>
                                <div class="dropdown-divider"></div>    <a class="dropdown-item" href="javascript:;">Separated link</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end breadcrumb-->


{{-- ===== Stepper: Volunteer Profile Wizard ===== --}}
<div id="stepper1" class="bs-stepper">
  <div class="card">

    {{-- Header / Steps --}}
    <div class="card-header">
      <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
        <div class="step" data-target="#step-1">
          <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="step-1">
            <div class="bs-stepper-circle">1</div>
            <div>
              <h5 class="mb-0 steper-title">{{ __('Personal Info') }}</h5>
              <p class="mb-0 steper-sub-title">{{ __('Enter Your Details') }}</p>
            </div>
          </div>
        </div>
        <div class="bs-stepper-line"></div>

        <div class="step" data-target="#step-2">
          <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="step-2">
            <div class="bs-stepper-circle">2</div>
            <div>
              <h5 class="mb-0 steper-title">{{ __('Volunteer Details') }}</h5>
              <p class="mb-0 steper-sub-title">{{ __('Ministry & Role') }}</p>
            </div>
          </div>
        </div>
        <div class="bs-stepper-line"></div>

        <div class="step" data-target="#step-3">
          <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="step-3">
            <div class="bs-stepper-circle">3</div>
            <div>
              <h5 class="mb-0 steper-title">{{ __('Volunteering Info') }}</h5>
              <p class="mb-0 steper-sub-title">{{ __('Service Details') }}</p>
            </div>
          </div>
        </div>
        <div class="bs-stepper-line"></div>

        <div class="step" data-target="#step-4">
          <div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="step-4">
            <div class="bs-stepper-circle">4</div>
            <div>
              <h5 class="mb-0 steper-title">{{ __('Finish') }}</h5>
              <p class="mb-0 steper-sub-title">{{ __('Review & Submit') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Body / Content --}}
    <div class="card-body">
      {{-- Display validation errors --}}
      @if ($errors->any())
        <div class="alert alert-danger">
            <h6>{{ __('There were some errors with your submission') }}</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <div class="bs-stepper-content">
        <form id="add_employee_form" action="{{ url('employee/add') }}" method="post" enctype="multipart/form-data">
          @csrf

          {{-- ================= STEP 1: Personal Information ================= --}}
          <div id="step-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
            <h5 class="mb-3">{{ __('Your Personal Information') }}</h5>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{{ __('First Name') }}</label>
                <input type="text" class="form-control text-uppercase" name="firstname" value="{{ old('firstname') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('Middle Name') }}</label>
                <input type="text" class="form-control text-uppercase" name="mi" value="{{ old('mi') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('Last Name') }}</label>
                <input type="text" class="form-control text-uppercase" name="lastname" value="{{ old('lastname') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Gender') }}</label>
                <select name="gender" class="form-select text-uppercase">
                  <option value="">{{ __('Select Gender') }}</option>
                  <option value="MALE"   {{ old('gender')=='MALE'?'selected':'' }}>MALE</option>
                  <option value="FEMALE" {{ old('gender')=='FEMALE'?'selected':'' }}>FEMALE</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Civil Status') }}</label>
                <select name="civilstatus" class="form-select text-uppercase">
                  <option value="">{{ __('Select Civil Status') }}</option>
                  <option value="SINGLE"             {{ old('civilstatus')=='SINGLE'?'selected':'' }}>SINGLE</option>
                  <option value="MARRIED"            {{ old('civilstatus')=='MARRIED'?'selected':'' }}>MARRIED</option>
                  <option value="LEGALLY SEPARATED"  {{ old('civilstatus')=='LEGALLY SEPARATED'?'selected':'' }}>LEGALLY SEPARATED</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Temperament') }}</label>
                <select name="temperament" class="form-select text-uppercase">
                  <option value="">{{ __('Select Temperament') }}</option>

                  <optgroup label="Mixed Temperaments">
                    <option value="CHOLERIC-SANGUINE"    {{ old('temperament')=='CHOLERIC-SANGUINE'?'selected':'' }}>Choleric-Sanguine</option>
                    <option value="CHOLERIC-MELANCHOLIC" {{ old('temperament')=='CHOLERIC-MELANCHOLIC'?'selected':'' }}>Choleric-Melancholic</option>
                    <option value="CHOLERIC-PHLEGMATIC"  {{ old('temperament')=='CHOLERIC-PHLEGMATIC'?'selected':'' }}>Choleric-Phlegmatic</option>

                    <option value="SANGUINE-CHOLERIC"    {{ old('temperament')=='SANGUINE-CHOLERIC'?'selected':'' }}>Sanguine-Choleric</option>
                    <option value="SANGUINE-MELANCHOLIC" {{ old('temperament')=='SANGUINE-MELANCHOLIC'?'selected':'' }}>Sanguine-Melancholic</option>
                    <option value="SANGUINE-PHLEGMATIC"  {{ old('temperament')=='SANGUINE-PHLEGMATIC'?'selected':'' }}>Sanguine-Phlegmatic</option>

                    <option value="MELANCHOLIC-CHOLERIC" {{ old('temperament')=='MELANCHOLIC-CHOLERIC'?'selected':'' }}>Melancholic-Choleric</option>
                    <option value="MELANCHOLIC-SANGUINE" {{ old('temperament')=='MELANCHOLIC-SANGUINE'?'selected':'' }}>Melancholic-Sanguine</option>
                    <option value="MELANCHOLIC-PHLEGMATIC" {{ old('temperament')=='MELANCHOLIC-PHLEGMATIC'?'selected':'' }}>Melancholic-Phlegmatic</option>

                    <option value="PHLEGMATIC-CHOLERIC"  {{ old('temperament')=='PHLEGMATIC-CHOLERIC'?'selected':'' }}>Phlegmatic-Choleric</option>
                    <option value="PHLEGMATIC-SANGUINE"  {{ old('temperament')=='PHLEGMATIC-SANGUINE'?'selected':'' }}>Phlegmatic-Sanguine</option>
                    <option value="PHLEGMATIC-MELANCHOLIC" {{ old('temperament')=='PHLEGMATIC-MELANCHOLIC'?'selected':'' }}>Phlegmatic-Melancholic</option>
                  </optgroup>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('Love Language') }}</label>
                <select name="love_language" class="form-select text-uppercase">
                  <option value="">{{ __('Select Love Language') }}</option>
                  <option value="WORDS"      {{ old('love_language')=='WORDS'?'selected':'' }}>Words of Affirmation</option>
                  <option value="SERVICE"    {{ old('love_language')=='SERVICE'?'selected':'' }}>Acts of Service</option>
                  <option value="GIFTS"      {{ old('love_language')=='GIFTS'?'selected':'' }}>Receiving Gifts</option>
                  <option value="TIME"       {{ old('love_language')=='TIME'?'selected':'' }}>Quality Time</option>
                  <option value="TOUCH"      {{ old('love_language')=='TOUCH'?'selected':'' }}>Physical Touch</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Email Address (Personal)') }}</label>
                <input type="email" class="form-control text-lowercase" name="emailaddress" value="{{ old('emailaddress') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('Mobile Number') }}</label>
                <input type="text" class="form-control" name="mobileno" value="{{ old('mobileno') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Date of Birth') }}</label>
                <input type="date" class="form-control airdatepicker" name="birthday" value="{{ old('birthday') }}" data-position="top right" placeholder="{{ __('Date') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('National ID') }}</label>
                <input type="text" class="form-control text-uppercase" name="nationalid" value="{{ old('nationalid') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('Place of Birth') }}</label>
                <input type="text" class="form-control text-uppercase" name="birthplace" value="{{ old('birthplace') }}">
              </div>

              <div class="col-12">
                <label class="form-label">{{ __('Home Address') }}</label>
                <input type="text" class="form-control text-uppercase" name="homeaddress" value="{{ old('homeaddress') }}">
              </div>

              {{-- ================= Profile Photo with Crop ================= --}}
              <div class="col-12">
                  <label class="form-label">{{ __('Upload Profile Photo') }}</label>
                  <input type="file"
                         class="form-control"
                         id="imageInput"
                         name="image"
                         accept="image/png, image/jpeg, image/jpg">
                  {{-- Will hold the cropped image as base64 so server can save it --}}
                  <input type="hidden" name="image_cropped" id="imageCropped">
                  <div class="form-text">Choose an image and crop it to a square.</div>
                </div>

                {{-- Optional preview of the cropped result --}}
                <div class="col-12 mt-2">
                  <img id="avatarPreview" src="" alt="Preview"
                       class="rounded"
                       style="display:none; width:120px; temperament:120px; object-fit:cover;">
                </div>

                  <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" onclick="stepper1.next()">
                      {{ __('Next') }}
                    </button>
                  </div>
                </div>
              </div>

          {{-- ================= STEP 2: Volunteer Details ================= --}}
          <div id="step-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">
            <h5 class="mb-3">{{ __('Volunteer Details') }}</h5>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{{ __('Campus') }}</label>
                <select name="campus" class="form-select text-uppercase">
                  <option value="">{{ __('Select Campus') }}</option>
                  @isset($campus)
                    @foreach ($campus as $data)
                      <option value="{{ $data->campus }}" {{ old('campus')==$data->campus?'selected':'' }}>
                        {{ $data->campus }}
                      </option>
                    @endforeach
                  @endisset
                </select>
              </div> 

              <div class="col-md-6">
                <label class="form-label">{{ __('Department') }}</label>
                <select name="department" class="form-select text-uppercase ministry">
                  <option value="">{{ __('Select Depparment') }}</option>
                  @isset($department)
                    @foreach ($department as $data)
                      <option value="{{ $data->department }}" {{ old('department')==$data->department?'selected':'' }}>
                        {{ $data->department }}
                      </option>
                    @endforeach
                  @endisset
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Ministry') }}</label>
                <select name="ministry" class="form-select text-uppercase ministry">
                  <option value="">{{ __('Select Ministry') }}</option>
                  @isset($ministry)
                    @foreach ($ministry as $data)
                      <option value="{{ $data->ministry }}" {{ old('ministry')==$data->ministry?'selected':'' }}>
                        {{ $data->ministry }}
                      </option>
                    @endforeach
                  @endisset
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Email Address (campus)') }}</label>
                <input type="email" class="form-control text-lowercase" name="corporate_email" value="{{ old('corporate_email') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Leave Group') }}</label>
                <select name="leaveprivilege" class="form-select text-uppercase">
                  <option value="">{{ __('Select Leave Privilege') }}</option>
                  @isset($leavegroup)
                    @foreach ($leavegroup as $lg)
                      <option value="{{ $lg->id }}" {{ old('leaveprivilege')==$lg->id?'selected':'' }}>
                        {{ $lg->leavegroup }}
                      </option>
                    @endforeach
                  @endisset
                </select>
              </div>

              <div class="col-12 d-flex justify-content-between">
                <button type="button" class="btn btn-info" onclick="stepper1.previous()">{{ __('Previous') }}</button>
                <button type="button" class="btn btn-primary" onclick="stepper1.next()">{{ __('Next') }}</button>
              </div>
            </div>
          </div>

          {{-- ================= STEP 3: Volunteering Info ================= --}}
          <div id="step-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
            <h5 class="mb-3">{{ __('Volunteering Information') }}</h5>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{{ __('Volunteering Type') }}</label>
                <select name="employmenttype" class="form-select text-uppercase">
                  <option value="">{{ __('Select Type') }}</option>
                  <option value="Full time" {{ old('employmenttype')=='Full time'?'selected':'' }}>Full time</option>
                  <option value="Part time" {{ old('employmenttype')=='Part time'?'selected':'' }}>Part time</option>
                  <option value="Trainee"   {{ old('employmenttype')=='Trainee'?'selected':'' }}>Trainee</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Volunteering Status') }}</label>
                <select name="employmentstatus" class="form-select text-uppercase">
                  <option value="">{{ __('Select Status') }}</option>
                  <option value="Active"   {{ old('employmentstatus')=='Active'?'selected':'' }}>Active</option>
                  <option value="Archived" {{ old('employmentstatus')=='Archived'?'selected':'' }}>Archived</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Official Start Date') }}</label>
                <input type="date" class="form-control airdatepicker text-uppercase" name="startdate" value="{{ old('startdate') }}" data-position="top right" placeholder="{{ __('Date') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">{{ __('Date Regularized') }}</label>
                <input type="date" class="form-control airdatepicker text-uppercase" name="dateregularized" value="{{ old('dateregularized') }}" data-position="top right" placeholder="{{ __('Date') }}">
              </div>

              <div class="col-12 d-flex justify-content-between">
                <button type="button" class="btn btn-info" onclick="stepper1.previous()">{{ __('Previous') }}</button>
                <button type="button" class="btn btn-primary" onclick="stepper1.next()">{{ __('Next') }}</button>
              </div>
            </div>
          </div>

          {{-- ================= STEP 4: Review & Submit ================= --}}
          <div id="step-4" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger4">
            <h5 class="mb-3">{{ __('Review & Submit') }}</h5>

            {{-- (Optional) You can render a summary here if you’d like --}}

            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-info" onclick="stepper1.previous()">{{ __('Previous') }}</button>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> {{ __('Save') }}
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>



{{-- ================= Crop Modal ================= --}}
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Crop Profile Photo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
  <div id="cropBox"
       style="width:100%;temperament:60vh;max-temperament:70vh;overflow:hidden;position:relative;background:#111;border-radius:.5rem;">
    <img id="cropImage" alt="Source"
         style="display:block;max-width:100%;width:100%;temperament:auto;">
  </div>

  <div class="d-flex gap-2 mt-3">
    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomIn">Zoom +</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomOut">Zoom −</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRotateLeft">Rotate ⟲</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRotateRight">Rotate ⟳</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnReset">Reset</button>
  </div>
</div>

      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-dark" id="btnCropSave" type="button">Crop & Use</button>
      </div>
    </div>
  </div>
</div>

{{-- ================= Styles & Scripts ================= --}}
{{-- If your layout already includes Bootstrap 5 bundle, remove the two Bootstrap includes below --}}
<link  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@if (config('assets.use_cdn'))
  <link href="{{ config('assets.cdn.cropper_css') }}" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
@else
  <link href="{{ asset(config('assets.local.cropper_css')) }}" rel="stylesheet">
@endif

@if (config('assets.use_cdn'))
  <script src="{{ config('assets.cdn.cropper_js') }}" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@else
  <script src="{{ asset(config('assets.local.cropper_js')) }}" defer></script>
@endif
{{-- ===== Stepper / Helpers Scripts handled by admin-new-employee.js ===== --}}


    </div>
  </main>

  <!--end main wrapper-->
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@endsection

{{-- Scripts for additional frontend behavior --}}
@section('scripts')
<script src="{{ asset('assets3/js/admin-new-employee.js') }}" defer></script>

@endsection
