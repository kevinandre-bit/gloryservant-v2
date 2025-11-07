@extends('layouts.admin')
    
    @section('meta')
        <title>Edit Permissions | Glory Servant</title>
        <meta name="description" content="Workday edit user permissions.">
    @endsection 

   @section('content')

<main class="main-wrapper">
  <div class="main-content"> 

    <!-- breadcrumb / header -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">User Management</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ url('users/roles') }}">Roles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Permissions</li>
          </ol>
        </nav>
      </div>

      <div class="ms-auto">
        <a href="{{ url('roles') }}" class="btn btn-outline-primary btn-sm">
          <i data-feather="chevrons-left"></i> Return
        </a>
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-12">
        <h6 class="mb-0 text-uppercase">Edit Permissions</h6>
        <hr>
      </div>
    </div>

    <!-- errors -->
    @if ($errors->any())
      <div class="row">
        <div class="col-12">
          <div class="alert alert-danger">
            <strong>There were some errors with your submission</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    @endif

    <div class="row">
      <div class="col-12">
        <div class="card rounded-4">
          <div class="card-body">

            <form action="{{ url('users/roles/permissions/update') }}" method="post" id="perm-form">
  @csrf

  <!-- Sticky toolbar -->
  <div class="position-sticky top-0 z-3 py-3 mb-3 shadow-sm" style="margin-left:-1.5rem;margin-right:-1.5rem;padding-left:1.5rem!important;padding-right:1.5rem!important;background:var(--bs-body-bg);">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <div class="input-group" style="max-width:420px;">
        <span class="input-group-text"><i class="bx bx-search"></i></span>
        <input type="search" id="perm-search" class="form-control" placeholder="Search permissions… (e.g. radio, schedule, logs)">
      </div>

      <div class="vr mx-1 d-none d-md-block"></div>

      <button class="btn btn-primary ms-auto" type="submit">
        <i class="bx bx-save"></i> Save Changes
      </button>
      <a href="{{ url('roles') }}" class="btn btn-outline-secondary">
        <i class="bx bx-x-circle"></i> Cancel
      </a>
    </div>
    
    <div class="card border-primary mb-0">
      <div class="card-body p-3">
        <div class="row align-items-center">
          <div class="col-md-3">
            <label class="form-label mb-md-0 fw-semibold">
              <i class="bx bx-filter-alt text-primary"></i> Data Access Scope
            </label>
          </div>
          <div class="col-md-9">
            <select name="scope_level" class="form-select" required>
              <option value="all" @if(isset($role) && $role->scope_level == 'all') selected @endif>All Data (Full Access)</option>
              <option value="campus" @if(isset($role) && $role->scope_level == 'campus') selected @endif>Campus Only</option>
              <option value="ministry" @if(isset($role) && $role->scope_level == 'ministry') selected @endif>Ministry Only</option>
              <option value="department" @if(isset($role) && $role->scope_level == 'department') selected @endif>Department Only</option>
            </select>
            <small class="text-muted d-block mt-1">
              <i class="bx bx-info-circle"></i> Controls what attendance, leaves, and schedules data users with this role can access
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- LEFT: Core app -->
    <div class="col-lg-6">

      {{-- DASHBOARD --}}
      <div class="card border-0 shadow-sm" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-dashboard" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Dashboard</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-dashboard" class="collapse">
          <div class="card-body">
            <div class="form-check form-switch" data-item data-label="dashboard open dashboard page">
              <input class="form-check-input" type="checkbox" name="perms[]" value="1"
                @isset($d) @if(in_array('1',$d)) checked @endif @endisset>
              <label class="form-check-label">Open Dashboard page</label>
            </div>
          </div>
        </div>
      </div>

      {{-- Volunteer --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-employees" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Volunteers</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-employees" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="employees open employees page list directory">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2"
                @isset($d) @if(in_array('2',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Employees page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="employees view profile">
                <input class="form-check-input" type="checkbox" name="perms[]" value="202"
                  @isset($d) @if(in_array('202',$d)) checked @endif @endisset>
                <label class="form-check-label">View Volunteer profile</label>
              </div>
              <div class="form-check form-switch" data-item data-label="employees add">
                <input class="form-check-input" type="checkbox" name="perms[]" value="201"
                  @isset($d) @if(in_array('201',$d)) checked @endif @endisset>
                <label class="form-check-label">Add Volunteer</label>
              </div>
              <div class="form-check form-switch" data-item data-label="employees edit">
                <input class="form-check-input" type="checkbox" name="perms[]" value="203"
                  @isset($d) @if(in_array('203',$d)) checked @endif @endisset>
                <label class="form-check-label">Edit Volunteer</label>
              </div>
              <div class="form-check form-switch" data-item data-label="employees delete">
                <input class="form-check-input" type="checkbox" name="perms[]" value="204"
                  @isset($d) @if(in_array('204',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Volunteer</label>
              </div>
              <div class="form-check form-switch" data-item data-label="employees archive">
                <input class="form-check-input" type="checkbox" name="perms[]" value="25"
                  @isset($d) @if(in_array('25',$d)) checked @endif @endisset>
                <label class="form-check-label">Archive Volunteer</label>
              </div>
              <div class="form-check form-switch" data-item data-label="employees volunteers list">
                <input class="form-check-input" type="checkbox" name="perms[]" value="206"
                  @isset($d) @if(in_array('206',$d)) checked @endif @endisset>
                <label class="form-check-label">List of Volunteers</label>
              </div>
              <div class="form-check form-switch" data-item data-label="employees volunteer birthdays">
                <input class="form-check-input" type="checkbox" name="perms[]" value="207"
                  @isset($d) @if(in_array('207',$d)) checked @endif @endisset>
                <label class="form-check-label">Volunteer Birthday</label>
              </div>
            </div> <br>
          <div class="form-check mb-2">
            <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="8"
              @isset($d) @if(in_array('8', $d)) checked @endif @endisset>
            <label class="form-check-label fw-semibold">Open Users page</label>
          </div>
          <div class="ms-3 js-children">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="perms[]" value="801"
                @isset($d) @if(in_array('801', $d)) checked @endif @endisset>
              <label class="form-check-label">Add User</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="perms[]" value="802"
                @isset($d) @if(in_array('802', $d)) checked @endif @endisset>
              <label class="form-check-label">Edit User</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="perms[]" value="803"
                @isset($d) @if(in_array('803', $d)) checked @endif @endisset>
              <label class="form-check-label">Delete User</label>
            </div>
          </div>
          </div>
        </div>
      </div>

      {{-- DOMAIN DATA --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button"
                    data-bs-toggle="collapse" data-bs-target="#sec-domain" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Domain Data</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-domain" class="collapse">
          <div class="card-body">

            {{-- CAMPUS --}}
            <div class="form-check mb-2" data-item data-label="campus open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="11"
                @isset($d) @if(in_array('11',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Campus</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="campus add">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1101"
                  @isset($d) @if(in_array('1101',$d)) checked @endif @endisset>
                <label class="form-check-label">Add Campus</label>
              </div>
              <div class="form-check form-switch" data-item data-label="campus delete">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1102"
                  @isset($d) @if(in_array('1102',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Campus</label>
              </div>
            </div>

            {{-- MINISTRIES --}}
            <div class="form-check mt-3 mb-2" data-item data-label="ministries open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="12"
                @isset($d) @if(in_array('12',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Ministries</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="ministries add">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1201"
                  @isset($d) @if(in_array('1201',$d)) checked @endif @endisset>
                <label class="form-check-label">Add Ministry</label>
              </div>
              <div class="form-check form-switch" data-item data-label="ministries delete">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1202"
                  @isset($d) @if(in_array('1202',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Ministry</label>
              </div>
            </div>

            {{-- JOB TITLES --}}
            <div class="form-check mt-3 mb-2" data-item data-label="jobtitles open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="13"
                @isset($d) @if(in_array('13',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Job Titles</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="jobtitles add">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1301"
                  @isset($d) @if(in_array('1301',$d)) checked @endif @endisset>
                <label class="form-check-label">Add Job Title</label>
              </div>
              <div class="form-check form-switch" data-item data-label="jobtitles delete">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1302"
                  @isset($d) @if(in_array('1302',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Job Title</label>
              </div>
            </div>

            {{-- LEAVE TYPES --}}
            <div class="form-check mt-3 mb-2" data-item data-label="leavetypes open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="14"
                @isset($d) @if(in_array('14',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Leave Types</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="leavetypes add">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1401"
                  @isset($d) @if(in_array('1401',$d)) checked @endif @endisset>
                <label class="form-check-label">Add Leave Type</label>
              </div>
              <div class="form-check form-switch" data-item data-label="leavetypes delete">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1402"
                  @isset($d) @if(in_array('1402',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Leave Type</label>
              </div>
            </div>


          </div>
        </div>
      </div>

      {{-- MEETINGS --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button"
                    data-bs-toggle="collapse" data-bs-target="#sec-meeting" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Meetings</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-meeting" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="meeting open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="17"
                @isset($d) @if(in_array('17',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Meeting page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="meeting links">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1701"
                  @isset($d) @if(in_array('1701',$d)) checked @endif @endisset>
                <label class="form-check-label">Meeting Links</label>
              </div>
              <div class="form-check form-switch" data-item data-label="meeting attendance">
                <input class="form-check-input" type="checkbox" name="perms[]" value="1702"
                  @isset($d) @if(in_array('1702',$d)) checked @endif @endisset>
                <label class="form-check-label">Meeting Attendance</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ATTENDANCES --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-att" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Attendances</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-att" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="attendance open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="3"
                @isset($d) @if(in_array('3',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Attendances page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="attendance edit">
                <input class="form-check-input" type="checkbox" name="perms[]" value="31"
                  @isset($d) @if(in_array('31',$d)) checked @endif @endisset>
                <label class="form-check-label">Edit Attendance</label>
              </div>
              <div class="form-check form-switch" data-item data-label="attendance delete">
                <input class="form-check-input" type="checkbox" name="perms[]" value="32"
                  @isset($d) @if(in_array('32',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Attendance</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- SCHEDULES --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button"
                    data-bs-toggle="collapse" data-bs-target="#sec-sched" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Schedules</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>

        <div id="sec-sched" class="collapse show">
          <div class="card-body">
            {{-- Parent --}}
            <div class="form-check mb-2" data-item data-label="schedules open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="4"
                @isset($d) @if(in_array('4',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Schedules page</label>
            </div>

            {{-- Children --}}
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="schedules add">
                <input class="form-check-input js-child" type="checkbox" name="perms[]" value="401"
                  @isset($d) @if(in_array('401',$d)) checked @endif @endisset>
                <label class="form-check-label">Add Schedule</label>
              </div>
              <div class="form-check form-switch" data-item data-label="schedules edit">
                <input class="form-check-input js-child" type="checkbox" name="perms[]" value="402"
                  @isset($d) @if(in_array('402',$d)) checked @endif @endisset>
                <label class="form-check-label">Edit Schedule</label>
              </div>
              <div class="form-check form-switch" data-item data-label="schedules delete">
                <input class="form-check-input js-child" type="checkbox" name="perms[]" value="403"
                  @isset($d) @if(in_array('403',$d)) checked @endif @endisset>
                <label class="form-check-label">Delete Schedule</label>
              </div>
              <div class="form-check form-switch" data-item data-label="schedules archive">
                <input class="form-check-input js-child" type="checkbox" name="perms[]" value="404"
                  @isset($d) @if(in_array('404',$d)) checked @endif @endisset>
                <label class="form-check-label">Archive Schedule</label>
              </div>
            </div>
          </div>
        </div>
      </div>

    {{-- Follow up --}}

    {{-- Wellness Followups --}}
    <div class="card border-0 shadow-sm mt-3" data-section>
      <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-link p-0 section-toggle" type="button"
                  data-bs-toggle="collapse" data-bs-target="#sec-wellness" aria-expanded="true">
            <i class="bx bx-chevron-down fs-4 align-middle"></i>
          </button>
          <h5 class="mb-0">Wellness Followups</h5>
          <span class="badge bg-secondary ms-2" data-count>0</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
          <span class="text-muted">·</span>
          <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
        </div>
      </div>
      <div id="sec-wellness" class="collapse">
        <div class="card-body">
          {{-- Parent --}}
          <div class="form-check mb-2">
            <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="7000"
              @isset($d) @if(in_array('7000',$d)) checked @endif @endisset>
            <label class="form-check-label fw-semibold">Open Wellness Followups page</label>
          </div>

          {{-- Children --}}
          <div class="ms-3 js-children d-grid gap-2">
            <div class="form-check form-switch">
              <input class="form-check-input js-child" type="checkbox" name="perms[]" value="7001"
                @isset($d) @if(in_array('7001',$d)) checked @endif @endisset>
              <label class="form-check-label">Create Followup</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input js-child" type="checkbox" name="perms[]" value="7002"
                @isset($d) @if(in_array('7002',$d)) checked @endif @endisset>
              <label class="form-check-label">View Followup</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Admin Followups --}}
    <div class="card border-0 shadow-sm mt-3" data-section>
      <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-link p-0 section-toggle" type="button"
                  data-bs-toggle="collapse" data-bs-target="#sec-admin-followups" aria-expanded="true">
            <i class="bx bx-chevron-down fs-4 align-middle"></i>
          </button>
          <h5 class="mb-0">Admin Followups</h5>
          <span class="badge bg-secondary ms-2" data-count>0</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
          <span class="text-muted">·</span>
          <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
        </div>
      </div>
      <div id="sec-admin-followups" class="collapse">
        <div class="card-body">
          {{-- Parent --}}
          <div class="form-check mb-2">
            <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="7010"
              @isset($d) @if(in_array('7010',$d)) checked @endif @endisset>
            <label class="form-check-label fw-semibold">Open Admin Followups page</label>
          </div>

          {{-- Children --}}
          <div class="ms-3 js-children d-grid gap-2">
            <div class="form-check form-switch">
              <input class="form-check-input js-child" type="checkbox" name="perms[]" value="7011"
                @isset($d) @if(in_array('7011',$d)) checked @endif @endisset>
              <label class="form-check-label">Assign Followup</label>
            </div>
          </div>
        </div>
      </div>
    </div>


      {{-- REPORT SYSTEM --}}
<div class="card border-0 shadow-sm mt-3" data-section>
  <div class="card-header bg-light d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm btn-link p-0 section-toggle" type="button"
              data-bs-toggle="collapse" data-bs-target="#sec-report-system" aria-expanded="true">
        <i class="bx bx-chevron-down fs-4 align-middle"></i>
      </button>
      <h5 class="mb-0">Report System</h5>
      <span class="badge bg-secondary ms-2" data-count>0</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
      <span class="text-muted">·</span>
      <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
    </div>
  </div>

  <div id="sec-report-system" class="collapse">
    <div class="card-body">

      {{-- Root open --}}
      <div class="form-check mb-2" data-item data-label="report system open module">
        <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="60"
          @isset($d) @if(in_array('60',$d)) checked @endif @endisset>
        <label class="form-check-label fw-semibold">Open Report System</label>
      </div>

      <div class="ms-3 js-children d-grid gap-3">

        {{-- Setup --}}
        <div>
          <div class="fw-semibold mb-1">Setup</div>
          <div class="d-grid gap-2">
            @foreach ([
              6001=>'View Setup',
              6002=>'Create / Edit (categories, metrics, status sets)',
              6003=>'Delete in Setup',
              6004=>'Edit Assignments',
              6005=>'Delete Assignments',
            ] as $pid=>$label)
              <div class="form-check form-switch" data-item data-label="report {{ strtolower($label) }}">
                <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                  @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                <label class="form-check-label">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Entry --}}
        <div>
          <div class="fw-semibold mb-1">Daily Entry</div>
          <div class="d-grid gap-2">
            @foreach ([
              6101=>'Open Entry Page',
              6102=>'Save Entries',
              6103=>'Delete/Clear Entries',
            ] as $pid=>$label)
              <div class="form-check form-switch" data-item data-label="report entry {{ strtolower($label) }}">
                <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                  @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                <label class="form-check-label">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Analytics --}}
        <div>
          <div class="fw-semibold mb-1">Analytics / Reports</div>
          <div class="d-grid gap-2">
            @foreach ([
              6201=>'Weekly Report',
              6202=>'Monthly Report',
              6203=>'Quarterly Report',
              6204=>'Export (CSV/PDF)',
            ] as $pid=>$label)
              <div class="form-check form-switch" data-item data-label="report analytics {{ strtolower($label) }}">
                <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                  @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                <label class="form-check-label">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

      {{-- QUICK GROUPS (Settings, Reports, Users) --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-quick" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Quick Groups</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-quick" class="collapse">
          <div class="card-body">
            <div class="form-check form-switch mb-2" data-item data-label="settings open page">
              <input class="form-check-input" type="checkbox" name="perms[]" value="9"
                @isset($d) @if(in_array('9',$d)) checked @endif @endisset>
              <label class="form-check-label">Open Settings page</label>
            </div>
            <div class="ms-3 d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="settings update">
                <input class="form-check-input" type="checkbox" name="perms[]" value="91"
                  @isset($d) @if(in_array('91',$d)) checked @endif @endisset>
                <label class="form-check-label">Update Settings</label>
              </div>
            </div>

            <hr>

            <div class="form-check form-switch mb-2" data-item data-label="reports open page">
              <input class="form-check-input" type="checkbox" name="perms[]" value="7"
                @isset($d) @if(in_array('7',$d)) checked @endif @endisset>
              <label class="form-check-label">Open Reports page</label>
            </div>
            <div class="ms-3 d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="reports birthdays">
                <input class="form-check-input" type="checkbox" name="perms[]" value="75"
                  @isset($d) @if(in_array('75',$d)) checked @endif @endisset>
                <label class="form-check-label">Volunteer Birthdays</label>
              </div>
              <div class="form-check form-switch" data-item data-label="reports attendance">
                <input class="form-check-input" type="checkbox" name="perms[]" value="74"
                  @isset($d) @if(in_array('74',$d)) checked @endif @endisset>
                <label class="form-check-label">Volunteer Attendance Report</label>
              </div>
            </div>

            <hr>

            <div class="form-check form-switch mb-2" data-item data-label="users open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="8"
                @isset($d) @if(in_array('8',$d)) checked @endif @endisset>
              <label class="form-check-label">Open Users page</label>
            </div>

            {{-- ROLES under Users --}}
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="users roles open page">
                <input class="form-check-input" type="checkbox" name="perms[]" value="10"
                  @isset($d) @if(in_array('10',$d)) checked @endif @endisset>
                <label class="form-check-label">Open Roles page</label>
              </div>

              <div class="ms-3 d-grid gap-2">
                <div class="form-check form-switch" data-item data-label="users roles add">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="1001"
                    @isset($d) @if(in_array('1001',$d)) checked @endif @endisset>
                  <label class="form-check-label">Add Role</label>
                </div>
                <div class="form-check form-switch" data-item data-label="users roles edit">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="1002"
                    @isset($d) @if(in_array('1002',$d)) checked @endif @endisset>
                  <label class="form-check-label">Edit Role</label>
                </div>
                <div class="form-check form-switch" data-item data-label="users roles permission">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="1003"
                    @isset($d) @if(in_array('1003',$d)) checked @endif @endisset>
                  <label class="form-check-label">Manage Role Permissions</label>
                </div>
                <div class="form-check form-switch" data-item data-label="users roles delete">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="1004"
                    @isset($d) @if(in_array('1004',$d)) checked @endif @endisset>
                  <label class="form-check-label">Delete Role</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT: Radio domain -->
    <div class="col-lg-6">

      {{-- RADIO OPS ROOT --}}
      <div class="card border-0 shadow-sm" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-radio" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Radio Ops</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-radio" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="radio open ops page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2000"
                @isset($d) @if(in_array('2000',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Radio Ops page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="radio dashboard">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2001"
                  @isset($d) @if(in_array('2001',$d)) checked @endif @endisset>
                <label class="form-check-label">Radio Dashboard</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- PROGRAMMING --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-prog" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Programming</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-prog" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="programming open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2100"
                @isset($d) @if(in_array('2100',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Programming page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="programming batches">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2101"
                  @isset($d) @if(in_array('2101',$d)) checked @endif @endisset>
                <label class="form-check-label">Batches</label>
              </div>
              <div class="form-check form-switch" data-item data-label="programming upload">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2102"
                  @isset($d) @if(in_array('2102',$d)) checked @endif @endisset>
                <label class="form-check-label">Upload</label>
              </div>
              <div class="form-check form-switch" data-item data-label="programming template">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2103"
                  @isset($d) @if(in_array('2103',$d)) checked @endif @endisset>
                <label class="form-check-label">Template</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- PLAYOUT --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-playout" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Playout</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-playout" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="playout open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2200"
                @isset($d) @if(in_array('2200',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Playout page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="playout logs">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2201"
                  @isset($d) @if(in_array('2201',$d)) checked @endif @endisset>
                <label class="form-check-label">Logs</label>
              </div>
              <div class="form-check form-switch" data-item data-label="playout deviations">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2202"
                  @isset($d) @if(in_array('2202',$d)) checked @endif @endisset>
                <label class="form-check-label">Deviations</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- TECHNICIANS --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-tech" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Technicians</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-tech" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="tech open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2300"
                @isset($d) @if(in_array('2300',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Technicians page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="tech schedule">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2301"
                  @isset($d) @if(in_array('2301',$d)) checked @endif @endisset>
                <label class="form-check-label">Schedule</label>
              </div>
              <div class="form-check form-switch" data-item data-label="tech assignments">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2302"
                  @isset($d) @if(in_array('2302',$d)) checked @endif @endisset>
                <label class="form-check-label">Assignments</label>
              </div>
              <div class="form-check form-switch" data-item data-label="tech checkins">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2303"
                  @isset($d) @if(in_array('2303',$d)) checked @endif @endisset>
                <label class="form-check-label">Check-ins</label>
              </div>
              <div class="form-check form-switch" data-item data-label="tech availability">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2304"
                  @isset($d) @if(in_array('2304',$d)) checked @endif @endisset>
                <label class="form-check-label">Availability</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- MAINTENANCE --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-maint" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Maintenance</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-maint" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="maintenance open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2400"
                @isset($d) @if(in_array('2400',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Maintenance page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="maintenance tasks">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2401"
                  @isset($d) @if(in_array('2401',$d)) checked @endif @endisset>
                <label class="form-check-label">Tasks</label>
              </div>
              <div class="form-check form-switch" data-item data-label="maintenance calendar">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2402"
                  @isset($d) @if(in_array('2402',$d)) checked @endif @endisset>
                <label class="form-check-label">Calendar</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- MONITORING --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-monitor" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Monitoring</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-monitor" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="monitoring open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2500"
                @isset($d) @if(in_array('2500',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Monitoring page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              <div class="form-check form-switch" data-item data-label="monitoring source">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2501"
                  @isset($d) @if(in_array('2501',$d)) checked @endif @endisset>
                <label class="form-check-label">Source</label>
              </div>
              <div class="form-check form-switch" data-item data-label="monitoring hub">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2502"
                  @isset($d) @if(in_array('2502',$d)) checked @endif @endisset>
                <label class="form-check-label">Hub</label>
              </div>
              <div class="form-check form-switch" data-item data-label="monitoring sites">
                <input class="form-check-input" type="checkbox" name="perms[]" value="2503"
                  @isset($d) @if(in_array('2503',$d)) checked @endif @endisset>
                <label class="form-check-label">Sites</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- RADIO REPORTS --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-rpt" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Radio Reports</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-rpt" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="reports open radio reports">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="2600"
                @isset($d) @if(in_array('2600',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Radio Reports page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              @foreach([2601=>'Daily Admin',2602=>'Weekly',2603=>'Daily Tech',2604=>'Daily Ops',2605=>'Daily Admin (UI)',2606=>'Studio'] as $pid=>$label)
                <div class="form-check form-switch" data-item data-label="radio reports {{ strtolower($label) }}">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                    @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                  <label class="form-check-label">{{ $label }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- FINANCE --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-fin" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Finance</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-fin" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="finance open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="3000"
                @isset($d) @if(in_array('3000',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Finance page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              @foreach([3001=>'Expenses',3002=>'Recurring',3003=>'Vendors'] as $pid=>$label)
                <div class="form-check form-switch" data-item data-label="finance {{ strtolower($label) }}">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                    @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                  <label class="form-check-label">{{ $label }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      {{-- DIRECTORY --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-dir" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Directory</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-dir" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="directory open page">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="4000"
                @isset($d) @if(in_array('4000',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Directory page</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              @foreach([4001=>'Register Station',4002=>'Register Technician',4003=>'Register POC',4004=>'Inventory'] as $pid=>$label)
                <div class="form-check form-switch" data-item data-label="directory {{ strtolower($label) }}">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                    @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                  <label class="form-check-label">{{ $label }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- RADIO SETTINGS --}}
      <div class="card border-0 shadow-sm mt-3" data-section>
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link p-0 section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-rsettings" aria-expanded="true">
              <i class="bx bx-chevron-down fs-4 align-middle"></i>
            </button>
            <h5 class="mb-0">Radio Settings</h5>
            <span class="badge bg-secondary ms-2" data-count>0</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link btn-sm p-0" data-select="all">All</button>
            <span class="text-muted">·</span>
            <button type="button" class="btn btn-link btn-sm p-0" data-select="none">None</button>
          </div>
        </div>
        <div id="sec-rsettings" class="collapse">
          <div class="card-body">
            <div class="form-check mb-2" data-item data-label="radio settings open">
              <input class="form-check-input js-parent" type="checkbox" name="perms[]" value="5000"
                @isset($d) @if(in_array('5000',$d)) checked @endif @endisset>
              <label class="form-check-label fw-semibold">Open Radio Settings</label>
            </div>
            <div class="ms-3 js-children d-grid gap-2">
              @foreach([5001=>'Users',5002=>'Computers',5003=>'Inventory',5004=>'Support'] as $pid=>$label)
                <div class="form-check form-switch" data-item data-label="radio settings {{ strtolower($label) }}">
                  <input class="form-check-input" type="checkbox" name="perms[]" value="{{ $pid }}"
                    @isset($d) @if(in_array((string)$pid,$d)) checked @endif @endisset>
                  <label class="form-check-label">{{ $label }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

    </div>
  </div><!-- /.row -->

  <div class="d-flex justify-content-end gap-2 mt-4">
    <input type="hidden" name="role_id" value="{{ $id ?? '' }}">
    <button class="btn btn-primary" type="submit"><i class="bx bx-save"></i> Set Permission</button>
    <a href="{{ url('roles') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>

{{-- UX helpers --}}
<style>
  [data-section] .card-header { border-bottom: 1px solid rgba(0,0,0,.05); }
  .section-toggle { transform: translateY(-1px); }
  [data-item].match-hide { display:none !important; }
  [data-section].match-hide { display:none !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sections = document.querySelectorAll('[data-section]');
  const search   = document.getElementById('perm-search');

  // counts
  function refreshCounts() {
    sections.forEach(sec => {
      const checks = sec.querySelectorAll('input[type="checkbox"][name="perms[]"]');
      const checked = Array.from(checks).filter(c => c.checked).length;
      const badge = sec.querySelector('[data-count]');
      if (badge) { badge.textContent = checked; badge.className = 'badge ' + (checked ? 'bg-primary' : 'bg-secondary'); }
    });
  }

  // section select all/none
  sections.forEach(sec => {
    const allBtn  = sec.querySelector('[data-select="all"]');
    const noneBtn = sec.querySelector('[data-select="none"]');
    const checks  = sec.querySelectorAll('input[type="checkbox"][name="perms[]"]');
    allBtn?.addEventListener('click', () => { checks.forEach(c=>c.checked=true); refreshCounts(); });
    noneBtn?.addEventListener('click', () => { checks.forEach(c=>c.checked=false); refreshCounts(); });
  });

  // parent -> children linkage
  document.querySelectorAll('.js-parent').forEach(parent => {
    parent.addEventListener('change', e => {
      const box = parent.closest('.card-body')?.querySelector('.js-children');
      if (!box) return;
      box.querySelectorAll('input[type="checkbox"][name="perms[]"]').forEach(c => c.checked = parent.checked);
      refreshCounts();
    });
  });

  // any change updates counts
  document.querySelectorAll('input[type="checkbox"][name="perms[]"]').forEach(c => {
    c.addEventListener('change', refreshCounts);
  });
  refreshCounts();

  // search filter (client-side)
  function norm(s){ return (s||'').toString().toLowerCase(); }
  search?.addEventListener('input', () => {
    const q = norm(search.value);
    sections.forEach(sec => {
      // show items that match label or are checked even if not matching (so user sees selected)
      let anyVisible = false;
      sec.querySelectorAll('[data-item]').forEach(it => {
        const label = norm(it.getAttribute('data-label'));
        const input = it.querySelector('input[type="checkbox"]');
        const match = !q || label.includes(q) || (input && input.checked);
        it.classList.toggle('match-hide', !match);
        if (match) anyVisible = true;
      });
      sec.classList.toggle('match-hide', !anyVisible);
    });
  });
});
</script>
