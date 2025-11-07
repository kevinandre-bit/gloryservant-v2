@include('layouts/admin')

<main class="main-wrapper">
  <div class="main-content">
    <div class="card">
      <div class="card-body">
        <h5>Edit Team Member</h5>
        <form method="post" action="{{ route('admin.reports.people.update', $person->id) }}">
          @csrf @method('PUT')

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input name="first_name" class="form-control" value="{{ old('first_name', $person->first_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input name="last_name" class="form-control" value="{{ old('last_name', $person->last_name) }}" required>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-md-6">
              <label class="form-label">IDNO</label>
              <input name="idno" class="form-control" value="{{ old('idno', $person->idno) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Team</label>
              <select name="team_id" class="form-select" required>
                @foreach($teams as $t)
                  <option value="{{ $t->id }}" @selected($t->id == $person->team_id)>{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mt-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active"   @selected(($person->status ?? 'active') === 'active')>active</option>
              <option value="inactive" @selected(($person->status ?? 'active') === 'inactive')>inactive</option>
            </select>
          </div>

          <div class="mt-3 d-flex gap-2">
            <a href="{{ route('admin.reports.setup') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>