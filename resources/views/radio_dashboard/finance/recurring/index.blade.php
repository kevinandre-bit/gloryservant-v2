@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">replay_circle_filled</i>Recurring Rules
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ route('finance.recurring.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
          <i class="material-icons-outlined">add_circle</i>New Rule
        </a>
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <i class="material-icons-outlined">account_balance_wallet</i>Expenses
        </a>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Rule</th><th>Vendor</th><th>Category</th><th>Site</th>
                <th>Amount</th><th>Frequency</th><th>Next Run</th><th>Status</th><th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rules as $r)
              <tr>
                <td class="fw-semibold">{{ $r['name'] }}</td>
                <td>{{ $r['vendor'] }}</td>
                <td>{{ $r['category'] }}</td>
                <td>{{ $r['site'] }}</td>
                <td>${{ number_format($r['amount'],2) }}</td>
                <td>{{ $r['frequency'] }} @if($r['frequency']==='Monthly') (day {{ $r['day'] }}) @endif</td>
                <td>{{ $r['next_run'] }}</td>
                <td>
                  <span class="badge {{ $r['status']==='Active' ? 'bg-success' : 'bg-secondary' }}">{{ $r['status'] }}</span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-dark">Generate Now</button>
                    @if($r['status']==='Active')
                      <button class="btn btn-outline-secondary">Pause</button>
                    @else
                      <button class="btn btn-outline-success">Resume</button>
                    @endif
                    <button class="btn btn-outline-primary">Edit</button>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">Actions are UI only for now.</div>
    </div>
  </div>
</main>
