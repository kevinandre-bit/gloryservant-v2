@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Inventory</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Items</li>
        </ol>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('inventory.movements') }}">
          <i class="material-icons-outlined">swap_horiz</i> Movements
        </a>
        <a class="btn btn-outline-secondary" href="{{ route('inventory.vendors.index') }}">
          <i class="material-icons-outlined">store</i> Vendors
        </a>
        <a class="btn btn-primary" href="{{ route('inventory.items.create') }}">
          <i class="material-icons-outlined">add_box</i> New Item
        </a>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-3">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>SKU</th>
                <th>Item</th>
                <th>Category</th>
                <th>Site</th>
                <th class="text-end">Qty</th>
                <th>UoM</th>
                <th>Min</th>
                <th>Vendor</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $it)
                <tr>
                  <td class="fw-semibold">{{ $it['sku'] }}</td>
                  <td>{{ $it['name'] }}</td>
                  <td>{{ $it['category'] }}</td>
                  <td>{{ $it['site'] }}</td>
                  <td class="text-end">{{ $it['qty'] }}</td>
                  <td>{{ $it['uom'] }}</td>
                  <td>{{ $it['min'] }}</td>
                  <td>{{ $it['vendor'] }}</td>
                  <td>
                    @if($it['status']==='OK')
                      <span class="badge bg-success">OK</span>
                    @elseif($it['status']==='LOW')
                      <span class="badge bg-warning text-dark">Low</span>
                    @else
                      <span class="badge bg-danger">Out</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="{{ route('inventory.movements') }}" class="btn btn-outline-primary">
                        <i class="material-icons-outlined">download</i> Receive
                      </a>
                      <a href="{{ route('inventory.movements') }}" class="btn btn-outline-secondary">
                        <i class="material-icons-outlined">upload</i> Issue
                      </a>
                      <a href="{{ route('inventory.movements') }}" class="btn btn-outline-dark">
                        <i class="material-icons-outlined">swap_horiz</i> Transfer
                      </a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</main>