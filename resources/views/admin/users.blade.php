@extends('layouts.admin')
@php use App\Classes\permission; @endphp
@section('meta')
  <title>{{ __('Users') }} | Glory Servant</title>
@endsection 

@section('content')
<main class="main-wrapper"> 
  <div class="main-content">

    {{-- Breadcrumb / header --}}
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Users</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">User Management</li>
          </ol>
        </nav>
      </div>

      <div class="ms-auto">
        <div class="btn-group">
          <a href="{{ url('roles') }}" class="btn btn-outline-secondary px-3 me-2">
            <i class="material-icons-outlined">person</i> Roles
          </a>
          <a href="javascript:void(0)" class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="material-icons-outlined">add</i> Add
          </a>
        </div>
      </div>
    </div>
    <!-- end breadcrumb -->

    <div class="card rounded-4">
      <div class="card-body">
        {{-- Errors --}}
        @if ($errors->any())
          <div class="alert alert-danger">
            <strong>{{ __('There were some errors with your submission') }}</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

       

        {{-- transient alert for work-type updates --}}
        <div id="workTypeAlert" class="alert d-none position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1055;"></div>

        {{-- Search --}}
        <div class="mb-3">
          <input type="text" id="tableSearch" class="form-control" placeholder="Search users...">
        </div>

        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered align-middle">
            <thead>
              <tr>
                <th>{{ __('IDno') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Work type') }}</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              @isset($users_roles)
                @foreach ($users_roles as $val)
                  <tr>
                    <td>{{ $val->idno }}</td>
                    <td>{{ $val->name }}</td>
                    <td>{{ $val->email }}</td>
                    <td>{{ $val->role_name }}</td>
                    <td>{{ $val->acc_type == 2 ? __('Admin') : __('Employee') }}</td>
                    <td>
                      @if($val->status == 1)
                        <span class="badge bg-success">Enabled</span>
                      @else
                        <span class="badge bg-secondary">Disabled</span>
                      @endif
                    </td>
                    <td>
                      <select class="form-select form-select-sm update-work-type" data-reference="{{ $val->reference }}">
                        <option value="Onsite"  {{ strcasecmp($val->work_type ?? '', 'Onsite') === 0 ? 'selected' : '' }}>Onsite</option>
                        <option value="Online"  {{ strcasecmp($val->work_type ?? '', 'Online') === 0 ? 'selected' : '' }}>Online</option>
                        <option value=""        {{ empty($val->work_type) ? 'selected' : '' }}>N/A</option>
                      </select>
                    </td>
                    <td class="text-end">
                      <button type="button" class="btn gap-2"
                              data-bs-toggle="modal"
                              data-bs-target="#FormModal"
                              data-id="{{ $val->reference }}"
                              data-idno="{{ $val->idno }}"
                              data-email="{{ $val->email }}"
                              data-name="{{ $val->name }}"
                              data-roleid="{{ $val->role_id }}"
                              data-acc="{{ $val->acc_type }}"
                              data-status="{{ $val->status }}"
                              data-scopelevel="{{ $val->scope_level ?? 'inherit' }}"
                              data-worktype="{{ $val->work_type }}">
                        <i class="text-white" data-feather="edit"></i>
                      </button>

                      <button type="button"
                              class="btn btn-sm btn-outline-primary btn-user-permissions"
                              data-bs-toggle="modal"
                              data-bs-target="#userPermissionsModal"
                              data-user-id="{{ $val->id }}"
                              data-user-name="{{ $val->name }}"
                              data-permissions-url="{{ url("users/{$val->id}/permissions/json") }}"
                              data-update-url="{{ url("users/{$val->id}/permissions") }}"
                              title="Manage permissions">
                        <i class="material-icons-outlined">lock</i>
                      </button>

                      <form method="POST" action="{{ url('/users/delete/'.$val->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                          <i class="material-icons-outlined">delete_outline</i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              @endisset
            </tbody>
          </table>
        </div>
      </div>

      {{-- Scripts moved to external: public/assets3/js/admin-users.js --}}

      <style nonce="{{ $cspNonce ?? '' }}">
        #userPermissionsModal .modal-dialog { max-width: 960px; }

        #userPermissionsShell {
          max-height: calc(100vh - 210px);
          display: flex;
        }

        .permissions-modal .modal-content {
          background: #0d1117;
          color: #c9d1d9;
          border-radius: 18px;
          border: 1px solid rgba(240, 246, 252, 0.08);
          box-shadow: 0 24px 48px rgba(0,0,0,0.45);
        }
        .permissions-modal .modal-header,
        .permissions-modal .modal-footer {
          background: #11161d;
          border-color: rgba(240, 246, 252, 0.08);
        }
        .permissions-modal .modal-title { color: #f0f6fc; font-weight: 600; }

        .permissions-modal .btn-primary {
          background: #238636; border-color: #238636;
          padding: 0.65rem 1.4rem; font-weight: 600; border-radius: 10px;
        }
        .permissions-modal .btn-primary:hover { background: #2ea043; border-color: #2ea043; }

        .permissions-modal .btn-outline-secondary {
          color: #c9d1d9; border-color: rgba(201, 209, 217, 0.4);
          padding: 0.65rem 1.4rem; border-radius: 10px;
        }
        .permissions-modal .btn-outline-secondary:hover {
          background: rgba(201, 209, 217, 0.1); color: #f0f6fc;
        }

        /* Sidebar container */
        .permissions-sidebar {
          width: 260px;
          background: #161b22;
          display: flex; flex-direction: column;
          max-height: calc(100vh - 210px);
          overflow: hidden;
        }
        .permissions-sidebar .sidebar-title {
          letter-spacing: 0.12em; font-weight: 700;
        }

        /* Stop global scroll; we’ll scroll inside categories */
        .permissions-sidebar .nav {
          overflow: hidden; /* key change */
          display: block;
        }

        .category-section {
          border-bottom: 1px solid rgba(240, 246, 252, 0.06);
          display: flex; flex-direction: column; /* key change */
        }

        .permissions-sidebar .section-toggle {
          width: 100%; background: transparent; border: 0; color: #94a3b8;
          font-size: 0.75rem; letter-spacing: 0.16em; text-transform: uppercase;
          padding: 0.9rem 1.35rem; display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        }
        .category-section.expanded .section-toggle { color: #f0f6fc; }

        .permissions-sidebar .section-toggle .chevron {
          width: .75rem; height: .75rem;
          border-right: 2px solid currentColor; border-bottom: 2px solid currentColor;
          transform: rotate(-45deg); transition: transform .2s;
        }
        .category-section.expanded .section-toggle .chevron { transform: rotate(45deg); }

        /* Category items: scroll INSIDE each block */
        .category-items {
          display: none;
          padding-bottom: .75rem;
          overflow-y: auto; overflow-x: hidden;             /* key change */
          max-height: var(--cat-items-max, 320px);          /* key change */
          margin-right: 2px; scrollbar-gutter: stable;
        }
        .category-items.show { display: block; }

        .permissions-sidebar .category-items .nav-link {
          color: #c9d1d9; padding: 0.9rem 1.35rem; border-radius: 0;
          border-left: 3px solid transparent; transition: background .2s, border-color .2s;
          font-weight: 500; display: block; width: 100%; text-align: left; white-space: normal;
        }
        .permissions-sidebar .category-items .nav-link:hover { background: rgba(56,139,253,.12); }
        .permissions-sidebar .category-items .nav-link.active {
          background: rgba(56,139,253,.2); border-left-color: #388bfd; color: #f0f6fc; font-weight: 600;
        }

        .permissions-body {
          background: #0d1117; padding: 1.75rem; flex: 1; overflow-y: auto;
          max-height: calc(100vh - 210px);
        }
        .permissions-pane { display: none; }
        .permissions-pane.active { display: block; }

        .permission-row {
          background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08);
          border-radius: 12px; padding: 1.15rem 1.5rem; margin-bottom: 1rem;
        }
        .permission-label { color: #e6edf3; font-weight: 600; font-size: 1rem; }
        .permission-meta { color: #8b949e; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .45rem; }

        .segmented .btn { min-width: 90px; border-radius: 999px !important; font-weight: 600; }
        .segmented .btn-outline-secondary { color: #c9d1d9; border-color: rgba(201,209,217,.3); }
        .segmented .btn-outline-success  { color: #3fb950; border-color: rgba(63,185,80,.4); }
        .segmented .btn-outline-danger   { color: #f85149; border-color: rgba(248,81,73,.4); }

        .segmented .btn-check:checked + .btn-outline-secondary,
        .segmented .btn-outline-secondary:hover { background: #30363d; border-color: #30363d; color: #f0f6fc; }
        .segmented .btn-check:checked + .btn-outline-success,
        .segmented .btn-outline-success:hover { background: #2ea043; border-color: #2ea043; color: #fff; }
        .segmented .btn-check:checked + .btn-outline-danger,
        .segmented .btn-outline-danger:hover { background: #f85149; border-color: #f85149; color: #fff; }

        /* Slim scrollbars for category lists */
        .permissions-sidebar .category-items::-webkit-scrollbar { width: 8px; }
        .permissions-sidebar .category-items::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 8px; }
        .permissions-sidebar .category-items:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); }

        @media (max-width: 768px) {
          #userPermissionsShell { flex-direction: column; }
          .permissions-sidebar { width: 100%; border-right: 0; border-bottom: 1px solid rgba(240, 246, 252, 0.08); }
          .permissions-sidebar .nav-link { border-left: none; border-bottom: 1px solid rgba(240, 246, 252, 0.06); }
          .permissions-body { padding: 1.25rem; }
        }
      </style>
    </div>
  </div>
</main>

<script id="roles-json" type="application/json">{!! json_encode($roles ?? []) !!}</script>

<!-- Edit Modal (unchanged) -->
<div class="modal fade" id="FormModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Edit Volunteer') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>

      <form id="edit_user_form" class="row g-3" method="POST" action="{{ url('users/update/user') }}">
        @csrf
        {{-- Hidden fields --}}
        <input type="hidden" name="reference" id="modalReference">
        <input type="hidden" name="idno" id="modalIdno">
        <input type="hidden" name="work_type" id="modalWorkType">

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Volunteer') }}</label>
              <input type="text" name="employee" id="modalName" class="readonly uppercase form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('Email') }}</label>
              <input type="text" name="email" id="modalEmail" class="readonly lowercase form-control" readonly>
            </div>

            <div class="col-md-12">
              <label class="form-label">{{ __('Account Type') }}</label>
              <div class="d-flex gap-4 mt-1">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="acc_type" id="modalAccVolunteer" value="1">
                  <label class="form-check-label" for="modalAccVolunteer">{{ __('Volunteer') }}</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="acc_type" id="modalAccAdmin" value="2">
                  <label class="form-check-label" for="modalAccAdmin">{{ __('Admin') }}</label>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('Role') }}</label>
              <select class="form-control uppercase" name="role_id" id="modalRoleId">
                <option value="">{{ __('Select Role') }}</option>
              </select>
              
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('Status') }}</label>
              <select class="ui dropdown uppercase form-control" name="status" id="modalStatus">
                <option value="">{{ __('Select Status') }}</option>
                <option value="1">{{ __('Enabled') }}</option>
                <option value="0">{{ __('Disabled') }}</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label"><i class="material-icons-outlined" style="font-size:1rem;vertical-align:middle;">filter_alt</i> {{ __('Data Access Scope') }}</label>
              <select class="form-control" name="scope_level" id="modalScopeLevel">
                <option value="inherit">{{ __('Inherit from Role') }}</option>
                <option value="all">{{ __('All Data (Full Access)') }}</option>
                <option value="campus">{{ __('Campus Only') }}</option>
                <option value="ministry">{{ __('Ministry Only') }}</option>
                <option value="department">{{ __('Department Only') }}</option>
              </select>
              <small class="text-muted d-block mt-1">
                <i class="material-icons-outlined" style="font-size:0.8rem;vertical-align:middle;">info</i> {{ __('Override role scope or inherit from role') }}
              </small>
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('New Password') }}</label>
              <input type="password" name="password" id="modalNewPassword" class="form-control" placeholder="********">
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('Confirm New Password') }}</label>
              <input type="password" name="password_confirmation" id="modalConfirmPassword" class="form-control" placeholder="********">
            </div>
          </div>

          <div class="col-12 d-none mt-2" id="modalErrors">
            <div class="ui error message">
              <i class="close icon"></i>
              <div class="header"></div>
              <ul class="list"></ul>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Permissions Modal -->
<div class="modal fade permissions-modal" id="userPermissionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Permissions — <span data-user-name></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      <form method="POST" action="#">
        @csrf
        <div class="modal-body p-0">
          <div id="userPermissionsAlert" class="alert alert-danger d-none rounded-0" role="alert"></div>

          <div class="px-3 py-3 border-bottom" style="background: #161b22;">
            <div class="row align-items-center">
              <div class="col-md-3">
                <label class="form-label mb-md-0 fw-semibold text-muted small">
                  <i class="material-icons-outlined" style="font-size:1rem;vertical-align:middle;">filter_alt</i> Data Access Scope
                </label>
              </div>
              <div class="col-md-9">
                <select name="scope_level" id="userScopeLevel" class="form-select form-select-sm" required>
                  <option value="inherit">Inherit from Role</option>
                  <option value="all">All Data (Full Access)</option>
                  <option value="campus">Campus Only</option>
                  <option value="ministry">Ministry Only</option>
                  <option value="department">Department Only</option>
                </select>
                <small class="text-muted d-block mt-1" style="font-size:0.7rem;">
                  <i class="material-icons-outlined" style="font-size:0.8rem;vertical-align:middle;">info</i> Override role scope or inherit from role
                </small>
              </div>
            </div>
          </div>

          <div id="userPermissionsShell" class="d-flex" style="min-height: 420px;">
            <aside id="userPermissionsSidebar" class="permissions-sidebar border-end d-none">
              <div class="sidebar-title px-3 pt-3 pb-2 text-uppercase text-muted small">Categories</div>
              <nav class="nav flex-column" id="userPermissionsCategoryNav"></nav>
            </aside>

            <section id="userPermissionsContent" class="flex-grow-1">
              <div id="userPermissionsBody" class="permissions-body"></div>
              <div id="userPermissionsEmpty" class="text-muted px-4 py-5"></div>
            </section>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add New User') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="add_user_form" action="{{ url('users/register') }}" method="POST" accept-charset="utf-8">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('Employee') }}</label>
            <select class="form-select text-uppercase" id="selEmployee" name="name" required>
              <option value="">{{ __('Select Employee') }}</option>
              @isset($employees)
                @foreach ($employees as $emp)
                  <option value="{{ $emp->lastname }}, {{ $emp->firstname }}"
                          data-email="{{ $emp->emailaddress }}"
                          data-ref="{{ $emp->id }}">
                    {{ $emp->lastname }}, {{ $emp->firstname }}
                  </option>
                @endforeach
              @endisset
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control text-lowercase" id="txtEmail" name="email" value="" readonly required>
          </div>

          <div class="mb-3">
            <label class="form-label d-block">{{ __('Choose Account Type') }}</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="acc_type" id="accVolunteer" value="1" required>
              <label class="form-check-label" for="accVolunteer">{{ __('Volunteer') }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="acc_type" id="accAdmin" value="2">
              <label class="form-check-label" for="accAdmin">{{ __('Admin') }}</label>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('Role') }}</label>
            <select class="form-select text-uppercase" name="role_id" required>
              <option value="">{{ __('Select Role') }}</option>
              @isset($roles)
                @foreach ($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                @endforeach
              @endisset
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('Status') }}</label>
            <select class="form-select text-uppercase" name="status" required>
              <option value="">{{ __('Select Status') }}</option>
              <option value="1">Enabled</option>
              <option value="0">Disabled</option>
            </select>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Password') }}</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Confirm Password') }}</label>
              <input type="password" class="form-control" name="password_confirmation" required>
            </div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger mt-3">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <input type="hidden" name="ref" id="hdnRef" value="">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-check-circle me-1"></i> {{ __('Register') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- moved to external: admin-users.js -->
@endsection

@section('scripts')
<script src="{{ asset('assets3/js/admin-users.js') }}" defer></script>
@endsection
