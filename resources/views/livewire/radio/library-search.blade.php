@php
  $categories = $categories ?? collect();
  $years = $years ?? collect();
@endphp

<div class="card" aria-labelledby="library-heading">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h6 id="library-heading" class="card-title mb-0">{{ __('Library') }}</h6>
  </div>

  <div class="card-body">
    <form wire:submit.prevent class="row g-2 align-items-end" novalidate>
      <div class="col-12">
        <div class="input-group">
          <span class="input-group-text"><i class="material-icons-outlined">search</i></span>
          <input wire:model.debounce.300ms="q" type="search" class="form-control"
                 placeholder="{{ __('Search title/performer/category/theme/year/path…') }}"
                 aria-label="{{ __('Search') }}">
          @if(!empty($this->q))
            <button class="btn btn-outline-secondary" wire:click="$set('q','')" type="button" aria-label="{{ __('Clear') }}">
              <i class="material-icons-outlined">close</i>
            </button>
          @endif
        </div>
      </div>
      <div class="col-6">
        <label class="form-label">{{ __('Category') }}</label>
        <select class="form-select" wire:model="category">
  <option value="">{{ __('All') }}</option>   {{-- value MUST be "" --}}
  @foreach($categories as $c)
    <option value="{{ $c }}">{{ $c }}</option>
  @endforeach
</select>
      </div>
      <div class="col-6">
        <label class="form-label">{{ __('Year') }}</label>
        <select class="form-select" wire:model="year">
  <option value="">{{ __('All') }}</option>   {{-- value MUST be "" --}}
  @foreach($years as $y)
    <option value="{{ $y }}">{{ $y }}</option>
  @endforeach
</select>
      </div>
    </form>
  </div>

  <div class="list-group list-group-flush" role="listbox" aria-label="{{ __('Search results') }}">
    @forelse($tracks as $t)
      <div class="list-group-item d-flex align-items-center justify-content-between" role="option">
        <div>
          <div class="fw-semibold">{{ $t->filename ?? '—' }}</div>
          <div class="text-muted small">
            {{ $t->relative_path ? $t->relative_path.'/' : '' }}{{ $t->filename }}
          </div>
          <div class="mt-1">
            @if($t->performer)<span class="badge bg-light text-dark me-1">{{ $t->performer }}</span>@endif
            @if($t->category)<span class="badge bg-secondary me-1">{{ $t->category }}</span>@endif
            @if($t->theme)<span class="badge bg-info text-dark me-1">{{ $t->theme }}</span>@endif
            @if($t->year)<span class="badge bg-light text-dark">{{ $t->year }}</span>@endif
          </div>
        </div>
        <button wire:click="add({{ $t->id }})" class="btn btn-success btn-sm" aria-label="{{ __('Add to playlist') }}">
          <i class="material-icons-outlined">add</i>
          <span class="d-none d-md-inline">{{ __('Add') }}</span>
        </button>
      </div>
    @empty
      <div class="list-group-item text-muted">{{ __('No results') }}</div>
    @endforelse
  </div>

  <div class="card-footer">
    @if($tracks instanceof \Illuminate\Contracts\Pagination\Paginator)
      {{ $tracks->links() }}
    @endif
  </div>
</div>