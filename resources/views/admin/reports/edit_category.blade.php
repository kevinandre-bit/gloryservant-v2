@include('layouts/admin')

<main class="main-wrapper">
  <div class="main-content">

    <div class="card mb-3">
      <div class="card-body"><h1 class="h5 m-0">Edit Category</h1></div>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('admin.reports.categories.update', $cat->id) }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name',$cat->name) }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description',$cat->description) }}</textarea>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="active" value="1" id="catActive"
                   {{ old('active',$cat->active) ? 'checked' : '' }}>
            <label class="form-check-label" for="catActive">Active</label>
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