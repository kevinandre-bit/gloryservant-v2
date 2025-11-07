@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Inventory</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Items</a></li>
          <li class="breadcrumb-item active" aria-current="page">Movements</li>
        </ol>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-5">
        <div class="card rounded-4">
          <div class="card-body">
            <h5 class="mb-3">Quick Movement</h5>

            <ul class="nav nav-tabs mb-3" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-receive" type="button" role="tab">
                  <i class="material-icons-outlined">download</i> Receive
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-issue" type="button" role="tab">
                  <i class="material-icons-outlined">upload</i> Issue
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-transfer" type="button" role="tab">
                  <i class="material-icons-outlined">swap_horiz</i> Transfer
                </button>
              </li>
            </ul>

            <div class="tab-content">
              {{-- Receive --}}
              <div class="tab-pane fade show active" id="tab-receive" role="tabpanel">
                <form>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label">Item</label>
                      <select class="form-select">
                        @foreach($items as $it)
                          <option>{{ $it['sku'] }} — {{ $it['name'] }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-6">
                      <label class="form-label">Qty</label>
                      <input type="number" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-6">
                      <label class="form-label">To Site</label>
                      <select class="form-select">
                        @foreach($sites as $s) <option>{{ $s }}</option> @endforeach
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Reference / Notes</label>
                      <input class="form-control" placeholder="PO-2025-001, shipment note, etc.">
                    </div>
                  </div>
                  <div class="mt-3 text-end">
                    <button class="btn btn-primary" type="button"><i class="material-icons-outlined">save</i> Add Receive</button>
                  </div>
                </form>
              </div>

              {{-- Issue --}}
              <div class="tab-pane fade" id="tab-issue" role="tabpanel">
                <form>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label">Item</label>
                      <select class="form-select">
                        @foreach($items as $it)
                          <option>{{ $it['sku'] }} — {{ $it['name'] }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-6">
                      <label class="form-label">Qty</label>
                      <input type="number" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-6">
                      <label class="form-label">For Site</label>
                      <select class="form-select">
                        @foreach($sites as $s) <option>{{ $s }}</option> @endforeach
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Reason / Ticket</label>
                      <input class="form-control" placeholder="Maintenance task or incident reference">
                    </div>
                  </div>
                  <div class="mt-3 text-end">
                    <button class="btn btn-outline-secondary" type="button"><i class="material-icons-outlined">save</i> Add Issue</button>
                  </div>
                </form>
              </div>

              {{-- Transfer --}}
              <div class="tab-pane fade" id="tab-transfer" role="tabpanel">
                <form>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label">Item</label>
                      <select class="form-select">
                        @foreach($items as $it)
                          <option>{{ $it['sku'] }} — {{ $it['name'] }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-6">
                      <label class="form-label">Qty</label>
                      <input type="number" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-6">
                      <label class="form-label">From</label>
                      <select class="form-select">
                        @foreach($sites as $s) <option>{{ $s }}</option> @endforeach
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label">To</label>
                      <select class="form-select">
                        @foreach($sites as $s) <option>{{ $s }}</option> @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="mt-3 text-end">
                    <button class="btn btn-dark" type="button"><i class="material-icons-outlined">save</i> Add Transfer</button>
                  </div>
                </form>
              </div>
            </div><!-- /tab-content -->
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-7">
        <div class="card rounded-4">
          <div class="card-body">
            <h5 class="mb-3">Recent Movements</h5>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>When</th>
                    <th>Type</th>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th>From</th>
                    <th>To</th>
                    <th>By</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($movements as $m)
                    <tr>
                      <td>{{ $m['when'] }}</td>
                      <td>
                        @if($m['type']==='RECEIVE')
                          <span class="badge bg-success">Receive</span>
                        @elseif($m['type']==='ISSUE')
                          <span class="badge bg-warning text-dark">Issue</span>
                        @else
                          <span class="badge bg-info">Transfer</span>
                        @endif
                      </td>
                      <td>{{ $m['sku'] }} — {{ $m['name'] }}</td>
                      <td class="text-end">{{ $m['qty'] }}</td>
                      <td>{{ $m['from'] ?? '—' }}</td>
                      <td>{{ $m['to'] ?? '—' }}</td>
                      <td>{{ $m['by'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</main>