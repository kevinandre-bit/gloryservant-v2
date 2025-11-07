@include('layouts/radio_layout')

@php
  // fake list preview (UI only)
  $vendors = $vendors ?? [
    ['name'=>'Backhaul Co','type'=>'Backhaul','contact'=>'ops@backhaul.co','phone'=>'+509 555 0101','country'=>'Haiti','city'=>'Port-au-Prince','pay'=>'Bank Transfer'],
    ['name'=>'StreamCloud','type'=>'Hosting','contact'=>'support@streamcloud.com','phone'=>'+1 786 555 0112','country'=>'United States','city'=>'Miami','pay'=>'Card'],
  ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">store</i>Vendors
      </h5>
      <a href="{{ route('finance.vendors.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
        <i class="material-icons-outlined">add_business</i>New Vendor
      </a>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Name</th><th>Type</th><th>Payment</th><th>Country</th><th>City</th><th>Contact</th><th>Phone</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vendors as $v)
                <tr>
                  <td class="fw-semibold">{{ $v['name'] }}</td>
                  <td>{{ $v['type'] }}</td>
                  <td>{{ $v['pay'] ?? '—' }}</td>
                  <td>{{ $v['country'] ?? '—' }}</td>
                  <td>{{ $v['city'] ?? '—' }}</td>
                  <td>{{ $v['contact'] }}</td>
                  <td>{{ $v['phone'] }}</td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center">No vendors yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>