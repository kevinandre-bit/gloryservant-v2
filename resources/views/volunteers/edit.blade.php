@extends('layouts.admin_v2')

@section('content')
<div class="auth-basic-wrapper d-flex align-items-center justify-content-center">
  <div class="container-fluid my-5 my-lg-0">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
        <div class="card rounded-4 mb-0 border-top border-4 border-primary border-gradient-1">
          <div class="card-body p-5">
            <img src="{{ asset('assets/images/logo1.png') }}" class="mb-4" width="145" alt="">
            <h3 class="mb-3">Edit Volunteer</h3>

            <form action="{{ route('volunteers.update', $vol->id) }}" method="POST" novalidate>
              @csrf
              @method('PUT')

              <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="firstname" class="form-control" value="{{ old('firstname', $vol->firstname) }}" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Middle Initial</label>
                <input type="text" name="mi" class="form-control" value="{{ old('mi', $vol->mi) }}">
              </div>

              <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="lastname" class="form-control" value="{{ old('lastname', $vol->lastname) }}" required>
              </div>

              <hr>

              <div class="mb-3">
                <label class="form-label">ID Number</label>
                <input type="text" name="idno" class="form-control" value="{{ old('idno', $vol->idno) }}" required>
              </div>

              <div class="mb-4">
                {{-- Email --}}
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email"
                         name="email"
                         class="form-control"
                         value="{{ old('email', $vol->email ?? '') }}"
                         placeholder="name@example.com">
                </div>

                {{-- Volunteer Status --}}
                <div class="mb-3">
                  <label class="form-label">Volunteer Status</label>
                  <select name="employmentstatus" class="form-select">
                    <option value="">— Select —</option>
                    @foreach (['Active','Inactive'] as $status)
                      <option value="{{ $status }}"
                        {{ old('employmentstatus', $vol->employmentstatus ?? '') === $status ? 'selected' : '' }}>
                        {{ $status }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Campus --}}
                <div class="mb-3">
                  <label class="form-label">Campus</label>
                  <select name="campus" class="form-select">
                    <option value="">— Select —</option>
                    @foreach ($campusOptions as $camp)
                      @php $camp = (string) $camp; @endphp
                      <option value="{{ $camp }}"
                        {{ old('campus', (string)($vol->campus ?? '')) === $camp ? 'selected' : '' }}>
                        {{ $camp }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
                <a href="{{ route('volunteers.index') }}" class="btn btn-light">Back to list</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Toasts --}}

@endsection