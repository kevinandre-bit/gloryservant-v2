@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Directory</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active">Register POC</li>
        </ol>
      </div>
      <div class="ms-auto">
        <a href="{{ route('monitoring.sites.index') }}" class="btn btn-outline-secondary">TX Sites</a>
      </div>
    </div>

    @if(session('ok'))
      <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <strong>Please fix the errors below.</strong>
      </div>
    @endif

    <div class="row g-3">
      <div class="col-12 col-xl-8">
        <div class="card rounded-4">
          <div class="card-body p-4">
            <h5 class="mb-3">POC Details</h5>

            <form method="post" action="{{ route('radio.admin.pocs.store') }}" class="row g-3">
              @csrf

              <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                       placeholder="Jane Smith" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Station / Site</label>
                <select name="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                  <option value="">Select…</option>
                  @foreach($sites as $s)
                    <option value="{{ $s['id'] }}" @selected(old('site_id') == $s['id'])>{{ $s['label'] }}</option>
                  @endforeach
                </select>
                @error('site_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-4">
                <label class="form-label">Phone</label>
                <input name="phone" type="text" class="form-control" placeholder="+509 5555-5555"
                       value="{{ old('phone') }}">
              </div>

              <div class="col-md-4">
                <label class="form-label">WhatsApp</label>
                <input name="whatsapp" type="text" class="form-control" placeholder="+509 5555-5555"
                       value="{{ old('whatsapp') }}">
              </div>

              <div class="col-md-4">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       placeholder="poc@radio.org" value="{{ old('email') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-8">
                <label class="form-label">Availability</label>
                <div class="d-flex flex-wrap gap-3">
                  @php $oldAvail = old('availability', []); @endphp
                  @foreach($availability as $a)
                    @php $id = 'av-'.\Illuminate\Support\Str::slug($a); @endphp
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="availability[]"
                             value="{{ $a }}" id="{{ $id }}" @checked(in_array($a, $oldAvail))>
                      <label class="form-check-label" for="{{ $id }}">{{ $a }}</label>
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="primaryContact" name="primary_contact"
                         @checked(old('primary_contact'))>
                  <label class="form-check-label" for="primaryContact">Primary contact</label>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Best time to call, languages, backup contact…">{{ old('notes') }}</textarea>
              </div>

              <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('monitoring.sites.index') }}" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="card rounded-4">
          <div class="card-body p-4">
            <h6 class="mb-2"><i class="material-icons-outlined">tips_and_updates</i> Tips</h6>
            <ul class="mb-0">
              <li>Mark one POC as Primary per site.</li>
              <li>WhatsApp is great for quick status/photo updates.</li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>