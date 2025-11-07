@include('layouts/radio_layout')

@php
  // UI-only defaults if controller hasn't provided arrays yet
  $types           = $types           ?? ['Utility','Telco','Hosting','Backhaul','Retail','Other'];
  $paymentMethods  = $paymentMethods  ?? ['Bank Transfer','Mobile Money','Cash','Check','Card'];
  $countries       = $countries       ?? ['Haiti','United States','Canada','Dominican Republic','France','Other'];
  $currencies      = $currencies      ?? ['HTG','USD','CAD','EUR','DOP'];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="material-icons-outlined">add_business</i>New Vendor
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ route('finance.vendors.index') }}" class="btn btn-light d-flex align-items-center gap-1">
          <i class="material-icons-outlined">arrow_back</i>Back
        </a>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-1">
          <i class="material-icons-outlined">save</i>Save Vendor
        </button>
      </div>
    </div>

    <form>
      {{-- ===== Vendor Basics ===== --}}
      <div class="card rounded-4 mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="material-icons-outlined">store</i>
            <h6 class="mb-0">Vendor Basics</h6>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Vendor Name</label>
              <input type="text" class="form-control" placeholder="Vendor Inc.">
            </div>
            <div class="col-md-4">
              <label class="form-label">Type</label>
              <select class="form-select">
                @foreach($types as $t) <option>{{ $t }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Payment Method</label>
              <select class="form-select">
                @foreach($paymentMethods as $pm) <option>{{ $pm }}</option> @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Contact Person (optional)</label>
              <input type="text" class="form-control" placeholder="Full name">
            </div>
            <div class="col-md-4">
              <label class="form-label">Contact Email</label>
              <input type="email" class="form-control" placeholder="ops@vendor.com">
            </div>
            <div class="col-md-4">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" placeholder="+509 ...">
            </div>

            <div class="col-md-8">
              <label class="form-label">Website (optional)</label>
              <input type="url" class="form-control" placeholder="https://">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tax ID (optional)</label>
              <input type="text" class="form-control" placeholder="TIN / NIF">
            </div>
          </div>
        </div>
      </div>

      {{-- ===== Location ===== --}}
      <div class="card rounded-4 mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="material-icons-outlined">location_on</i>
            <h6 class="mb-0">Location</h6>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Country</label>
              <select class="form-select">
                @foreach($countries as $c) <option>{{ $c }}</option> @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" class="form-control" placeholder="City">
            </div>
            <div class="col-md-4">
              <label class="form-label">Address (optional)</label>
              <input type="text" class="form-control" placeholder="Street, number">
            </div>
          </div>
        </div>
      </div>

      {{-- ===== Bank & Payment Details ===== --}}
      <div class="card rounded-4 mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="material-icons-outlined">account_balance</i>
            <h6 class="mb-0">Bank & Payment Details</h6>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Bank Name</label>
              <input type="text" class="form-control" placeholder="Bank of ...">
            </div>
            <div class="col-md-4">
              <label class="form-label">Account Holder Name</label>
              <input type="text" class="form-control" placeholder="Company / Person">
            </div>
            <div class="col-md-4">
              <label class="form-label">Account Number / IBAN</label>
              <input type="text" class="form-control" placeholder="XXXXXXXXX">
            </div>

            <div class="col-md-4">
              <label class="form-label">SWIFT / BIC (optional)</label>
              <input type="text" class="form-control" placeholder="XXXXHTPP">
            </div>
            <div class="col-md-4">
              <label class="form-label">Routing Number (optional)</label>
              <input type="text" class="form-control" placeholder="#### ####">
            </div>
            <div class="col-md-4">
              <label class="form-label">Currency</label>
              <select class="form-select">
                @foreach($currencies as $cur) <option>{{ $cur }}</option> @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- ===== Notes ===== --}}
      <div class="card rounded-4 mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="material-icons-outlined">notes</i>
            <h6 class="mb-0">Notes</h6>
          </div>
          <textarea class="form-control" rows="4" placeholder="Internal notes, service areas, SLAs, escalation contacts..."></textarea>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary">
          <i class="material-icons-outlined">drafts</i>Save Draft
        </button>
        <button type="button" class="btn btn-primary">
          <i class="material-icons-outlined">save</i>Save Vendor
        </button>
      </div>
    </form>

  </div>
</main>