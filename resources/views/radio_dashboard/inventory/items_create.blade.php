@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Inventory</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Items</a></li>
          <li class="breadcrumb-item active" aria-current="page">New Item</li>
        </ol>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body">
        <h5 class="mb-3">Create Item</h5>
        <form>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">SKU</label>
              <input class="form-control" placeholder="UPS-BATT-9AH">
            </div>
            <div class="col-md-8">
              <label class="form-label">Item Name</label>
              <input class="form-control" placeholder="UPS Battery 12V 9Ah">
            </div>
            <div class="col-md-4">
              <label class="form-label">Category</label>
              <select class="form-select">
                @foreach($categories as $c) <option>{{ $c }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Default Site</label>
              <select class="form-select">
                @foreach($sites as $s) <option>{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Vendor</label>
              <select class="form-select">
                @foreach($vendors as $v) <option>{{ $v }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Unit of Measure</label>
              <input class="form-control" placeholder="unit / roll / pack">
            </div>
            <div class="col-md-3">
              <label class="form-label">Min Stock</label>
              <input type="number" class="form-control" value="1" min="0">
            </div>
            <div class="col-md-3">
              <label class="form-label">Initial Qty</label>
              <input type="number" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-3">
              <label class="form-label">Reorder Qty</label>
              <input type="number" class="form-control" value="5" min="0">
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea class="form-control" rows="3" placeholder="Compatibility, supplier lead time, etc."></textarea>
            </div>
          </div>
          <div class="mt-3 d-flex justify-content-end gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-light">Cancel</a>
            <button type="button" class="btn btn-primary"><i class="material-icons-outlined">save</i> Save</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>