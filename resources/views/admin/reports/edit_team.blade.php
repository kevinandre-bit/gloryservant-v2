@include('layouts/admin')

<main class="main-wrapper">
  <div class="main-content">

    <div class="card mb-3">
      <div class="card-body"><h1 class="h5 m-0">Edit Team</h1></div>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('admin.reports.teams.update', $team->id) }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name',$team->name) }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description',$team->description) }}</textarea>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="active" value="1" id="teamActive"
                   {{ old('active',$team->active) ? 'checked' : '' }}>
            <label class="form-check-label" for="teamActive">Active</label>
          </div>

          <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.setup') }}" class="btn btn-outline-secondary">Back</a>
            <button class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>