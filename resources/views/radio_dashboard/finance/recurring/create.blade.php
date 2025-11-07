@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">add_circle</i>New Recurring Rule
      </h5>
      <a href="{{ route('finance.recurring.index') }}" class="btn btn-light d-flex align-items-center gap-1">
        <i class="material-icons-outlined">arrow_back</i>Back
      </a>
    </div>

    <div class="card rounded-4">
      <div class="card-body">
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Rule Name</label>
              <input type="text" class="form-control" placeholder="e.g., Monthly Hosting">
            </div>
            <div class="col-md-3">
              <label class="form-label">Vendor</label>
              <select class="form-select">@foreach($vendors as $v) <option>{{ $v }}</option> @endforeach</select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Category</label>
              <select class="form-select">@foreach($categories as $c) <option>{{ $c }}</option> @endforeach</select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Amount (USD)</label>
              <input type="number" step="0.01" class="form-control" placeholder="0.00">
            </div>
            <div class="col-md-3">
              <label class="form-label">Site</label>
              <select class="form-select">@foreach($sites as $s) <option>{{ $s }}</option> @endforeach</select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Frequency</label>
              <select class="form-select" id="freqSelect">
                @foreach($frequencies as $f) <option>{{ $f }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-3" id="monthlyDayWrap">
              <label class="form-label">Monthly Day</label>
              <input type="number" min="1" max="28" class="form-control" value="1">
            </div>

            <div class="col-md-3">
              <label class="form-label">Start Date</label>
              <input type="date" class="form-control" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">End Date (optional)</label>
              <input type="date" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="autoApprove">
                <label class="form-check-label" for="autoApprove">Auto-create and mark as Due</label>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary">Save Draft</button>
            <button type="button" class="btn btn-primary">Save Rule</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  const freq = document.getElementById('freqSelect');
  const wrap = document.getElementById('monthlyDayWrap');
  function toggleMonthly(){ wrap.style.display = (freq.value === 'Monthly') ? '' : 'none'; }
  freq.addEventListener('change', toggleMonthly);
  toggleMonthly();
});
</script>
@endpush
