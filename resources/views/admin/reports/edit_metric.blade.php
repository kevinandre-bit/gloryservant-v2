@include('layouts/admin')

<main class="main-wrapper">
  <div class="main-content">

    <div class="card mb-3">
      <div class="card-body"><h1 class="h5 m-0">Edit Metric</h1></div>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('admin.reports.metrics.update', $metric->id) }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
              @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ $metric->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name',$metric->name) }}" required>
          </div>


          <div class="mb-3">
            <label class="form-label">Value Type</label>
            <select name="value_type" class="form-select" required>
              @foreach(['status','number','percent','duration','boolean','text'] as $vt)
                <option value="{{ $vt }}" {{ $metric->value_type === $vt ? 'selected' : '' }}>{{ $vt }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Scoring Rules (JSON)</label>
            <textarea name="scoring_rules" rows="5" class="form-control">{{ old('scoring_rules',$metric->scoring_rules) }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Weight</label>
            <input name="weight" type="number" step="0.01" value="{{ old('weight',$metric->weight) }}" class="form-control">
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="active" value="1" id="metActive"
                   {{ old('active',$metric->active) ? 'checked' : '' }}>
            <label class="form-check-label" for="metActive">Active</label>
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