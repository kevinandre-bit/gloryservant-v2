@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">account_balance_wallet</i>Expenses
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ route('finance.expenses.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
          <i class="material-icons-outlined">add_circle</i>New Expense
        </a>
        <a href="{{ route('finance.recurring.create') }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
          <i class="material-icons-outlined">replay_circle_filled</i>New Recurring
        </a>
      </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-2">
      <div class="col-sm-4">
        <div class="card rounded-4">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">This Month</div>
              <h4 class="mb-0">${{ number_format($kpi['monthTotal'],2) }}</h4>
            </div>
            <i class="material-icons-outlined">payments</i>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card rounded-4">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">Due Today</div>
              <h4 class="mb-0">{{ $kpi['dueToday'] }}</h4>
            </div>
            <i class="material-icons-outlined">today</i>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card rounded-4">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="fw-semibold">Overdue</div>
              <h4 class="mb-0">{{ $kpi['overdue'] }}</h4>
            </div>
            <i class="material-icons-outlined">report_gmailerrorred</i>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      {{-- Upcoming (from recurring rules) --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-header bg-transparent border-0 p-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 d-flex align-items-center gap-2"><i class="material-icons-outlined">schedule</i>Upcoming</h6>
            <a href="{{ route('finance.recurring.index') }}" class="btn btn-sm btn-light">Manage Rules</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Date</th><th>Vendor</th><th>Category</th><th>Site</th><th class="text-end">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($upcoming as $row)
                  <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['vendor'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['site'] }}</td>
                    <td class="text-end">${{ number_format($row['amount'],2) }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="5" class="text-center">No upcoming expenses.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- Recent & toggle Paid --}}
      <div class="col-12 col-xl-6 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-header bg-transparent border-0 p-3">
            <h6 class="mb-0 d-flex align-items-center gap-2"><i class="material-icons-outlined">history</i>Recent</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Date</th><th>Vendor</th><th>Category</th><th>Site</th><th class="text-end">Amount</th><th class="text-center">Paid</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recent as $row)
                  <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['vendor'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['site'] }}</td>
                    <td class="text-end">${{ number_format($row['amount'],2) }}</td>
                    <td class="text-center">
                      <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input js-toggle-paid" type="checkbox" {{ $row['status']==='Paid' ? 'checked' : '' }}>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer small">Toggling is UI-only for now.</div>
        </div>
      </div>

      {{-- Overdue --}}
      <div class="col-12 d-flex">
        <div class="card w-100 rounded-4">
          <div class="card-header bg-transparent border-0 p-3">
            <h6 class="mb-0 d-flex align-items-center gap-2"><i class="material-icons-outlined">warning_amber</i>Overdue</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Date</th><th>Vendor</th><th>Category</th><th>Site</th><th class="text-end">Amount</th><th class="text-center">Mark Paid</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($overdue as $row)
                  <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['vendor'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['site'] }}</td>
                    <td class="text-end">${{ number_format($row['amount'],2) }}</td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 mx-auto">
                        <i class="material-icons-outlined">task_alt</i> Paid
                      </button>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="6" class="text-center">No overdue items 🎉</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('.js-toggle-paid').forEach(el=>{
    el.addEventListener('change', (e)=>{
      // UI-only feedback for now
      const row = e.target.closest('tr');
      row.classList.toggle('table-success', e.target.checked);
    });
  });
});
</script>
@endpush
