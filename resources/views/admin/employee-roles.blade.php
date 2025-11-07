@php use App\Classes\permission; @endphp
@extends('layouts.admin')

    @section('meta')
        <title>Employee Roles | Glory Servant</title>
        <meta name="description" content="Workday roles, view all employee roles, add roles, edit roles, and delete roles.">
    @endsection
    
    @section('content')
    @include('admin.modals.modal-add-roles') 
    @include('admin.modals.modal-edit-role')

    <main class="main-wrapper">
  <div class="main-content">
    <!-- breadcrumb / title -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">User Management</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">User Roles</li>
          </ol>
        </nav>
      </div>

      <div class="ms-auto">
        <div class="d-flex gap-2">
          <a href="{{ url('users') }}" class="btn btn-outline-primary btn-sm">
            <i  data-feather="chevrons-left"></i> Return
          </a>
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#AddRoleModal">
            <i  data-feather="plus"></i> Add
          </button>
        </div>
      </div>
    </div>

    <!-- page header -->
    <div class="row mb-2">
      <div class="col-12">
        <h6 class="mb-0 text-uppercase">User Roles</h6>
        <hr>
      </div>
    </div>

    <!-- errors -->
    @if ($errors->any())
      <div class="row mb-3">
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

    <!-- roles table card -->
    <div class="row">
      <div class="col-12">
        <div class="card rounded-4">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example2" class="table table-striped table-bordered" data-order='[[ 0, "asc" ]]' style="width:100%;">
                <thead>
                  <tr>
                    <th>Role Name</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @isset($roles)
                    @foreach ($roles as $role)
                      <tr>
                        <td>{{ $role->role_name }}</td>
                        <td>
                          @php
                            $state = strtolower($role->state ?? '');
                          @endphp
                          @if($state === 'active' || $state === 'enabled' || $state === '1')
                            <span class="badge bg-success">Active</span>
                          @elseif($state === 'disabled' || $state === '0')
                            <span class="badge bg-danger">Disabled</span>
                          @else
                            <span class="badge bg-secondary">{{ $role->state }}</span>
                          @endif
                        </td>
                        <td class="text-end">
                          <a href="{{ url('/users/roles/permissions/edit/'.$role->id) }}" class="btn btn-sm btn-light me-1" title="Permissions">
                            <i class="material-icons-outlined">manage_accounts</i>
                          </a>

                          <!-- edit button: you can attach data-* for modal editing -->
                          <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit-role" data-id="{{ $role->id }}" title="Edit">
                            <i class="material-icons-outlined">edit</i>
                          </button>

                          <form method="POST" action="{{ url('/users/roles/delete/'.$role->id) }}" class="d-inline" onsubmit="return confirm('Delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                              <i class="material-icons-outlined">delete</i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  @endisset
                </tbody>
              </table>
            </div> <!-- /.table-responsive -->
          </div> <!-- /.card-body -->
        </div> <!-- /.card -->
      </div>
    </div>

    <!-- optional: Add Role Modal (skeleton) -->
    <div class="modal fade" id="AddRoleModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ url('users/roles/add') }}">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Add Role</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Role name</label>
                <input name="role_name" class="form-control" type="text" required>
              </div>
              <div class="mb-3">
                <label class="form-label">State</label>
                <select name="state" class="form-select">
                  <option value="Active">Active</option>
                  <option value="Disabled">Disabled</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>


  </div> <!-- /.main-content -->
</main>

    @endsection

    @section('scripts')
    <script src="{{ asset('assets3/js/admin-employee-roles.js') }}" defer></script>
    
    @endsection
