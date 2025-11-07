@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">note_add</i>New Expense
      </h5>
      <a href="{{ route('finance.expenses.index') }}" class="btn btn-light d-flex align-items-center gap-1">
        <i class="material-icons-outlined">arrow_back</i>Back
      </a>
    </div>

    <div class="card rounded-4">
      <div class="card-body">
        <form>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Vendor</label>
              <select class="form-select">
                @foreach($vendors as $v) <option>{{ $v }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Category</label>
              <select class="form-select">
                @foreach($categories as $c) <option>{{ $c }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Site</label>
              <select class="form-select">
                @foreach($sites as $s) <option>{{ $s }}</option> @endforeach
              </select>
            </div>

            <div class="col-md-8">
              <label class="form-label">Description</label>
              <input type="text" class="form-control" placeholder="e.g., EDH bill August 2025">
            </div>
            <div class="col-md-4">
              <label class="form-label">Amount (USD)</label>
              <input type="number" step="0.01" class="form-control" placeholder="0.00">
            </div>

            <div class="col-md-6">
              <label class="form-label">Attachment</label>
              <input type="file" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="paidSwitch">
                <label class="form-check-label" for="paidSwitch">Mark as Paid</label>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary">Save Draft</button>
            <button type="button" class="btn btn-primary">Save Expense</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>
