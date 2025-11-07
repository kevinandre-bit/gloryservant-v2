{{-- resources/views/radio_dashboard/Program/tracks/index.blade.php --}}
@extends('layouts.radio_layout')
@php use App\Support\Time; @endphp

@section('meta')
  <title>{{ __('Library') }} | Glory Servant</title>
@endsection

@section('content')
<main class="main-wrapper radio-dashboard-skin">
  <div class="main-content radio-dashboard-content">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <h4 class="mb-0 radio-title-accent">{{ __('Library') }}</h4>
        <span class="radio-chip-accent">{{ $tracks->total() }} {{ __('tracks') }}</span>
      </div>

      <div class="d-flex align-items-center gap-2">
        {{-- Search --}}
        <form method="GET" action="{{ route('program.library.index') }}" class="d-flex">
          <div class="input-group input-group-sm" style="min-width: 320px;">
            <span class="input-group-text bg-light border-0">
              <i class="material-icons-outlined">search</i>
            </span>
            <input name="s"
                   value="{{ request('s') }}"
                   class="form-control border-0"
                   placeholder="{{ __('Search title/performer/category/path/year…') }}">
            @if(request()->filled('s'))
              <a class="btn btn-outline-secondary" href="{{ route('program.library.index') }}"
                 title="{{ __('Clear search') }}">
                <i class="material-icons-outlined">close</i>
              </a>
            @endif
            <button class="btn btn-primary">
              <i class="material-icons-outlined">search</i>
              <span class="d-none d-sm-inline ms-1">{{ __('Search') }}</span>
            </button>
          </div>
        </form>

        {{-- Clear Library (danger) --}}
        @can('manage-playlists')
        <form method="POST" action="{{ route('program.library.clear') }}"
              onsubmit="return confirm('{{ __('Are you sure you want to clear the entire library? This also removes playlist items that reference it.') }}')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-danger btn-sm">
            <i class="material-icons-outlined">delete_sweep</i>
            <span class="ms-1">{{ __('Clear Library') }}</span>
          </button>
        </form>
        @endcan
      </div>
    </div>

    {{-- Table card --}}
    <div class="card">
      <div class="card-body p-0">
        <div id="libTableWrap" class="table-responsive" style="max-height: 70vh; overflow:auto;">
          <table id="libraryTable" class="table table-hover align-middle mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th style="width:28%">{{ __('Title') }}</th>
                <th style="width:34%">{{ __('Path (Full name)') }}</th>
                <th style="width:12%">{{ __('Performer') }}</th>
                <th class="text-center" style="width:10%">{{ __('Duration') }}</th>
                <th style="width:8%">{{ __('Category') }}</th>
                <th class="text-center" style="width:5%">{{ __('Year') }}</th>
                <th class="text-center" style="width:3%">{{ __('Ext') }}</th>
              </tr>
            </thead>

            <tbody id="libTbody"
                   data-next-url="{{ $tracks->appends(request()->query())->nextPageUrl() ?? '' }}">
              @forelse($tracks as $t)
                <tr>
                  <td class="fw-semibold">{{ $t->title ?? '—' }}</td>
                  <td class="text-secondary small">{{ $t->relative_path }}</td>
                  <td>{{ $t->performer ?? '—' }}</td>
                  <td class="text-center">
                    {{ isset($t->duration_seconds) ? \App\Support\Time::formatHms((int)$t->duration_seconds) : ($t->duration ?? '00:00') }}
                  </td>
                  <td>{{ $t->category ?? '—' }}</td>
                  <td class="text-center">{{ $t->year ?? '—' }}</td>
                  <td class="text-center">{{ $t->ext ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">{{ __('No results') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- loader row (shown while fetching) --}}
        <div id="libLoader" class="text-center py-3 d-none">
          <div class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></div>
          <span class="text-secondary ms-2">{{ __('Loading…') }}</span>
        </div>
      </div>

      {{-- Fallback pagination (hidden when JS runs) --}}
      @if($tracks->hasPages())
      <div id="libPagination" class="card-footer border-0 bg-transparent pt-0">
        {!! $tracks->withQueryString()->onEachSide(1)->links('vendor.pagination.radio') !!}
      </div>
      @endif
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
(function(){
  const wrap   = document.getElementById('libTableWrap');
  const tbody  = document.getElementById('libTbody');
  const loader = document.getElementById('libLoader');
  const pager  = document.getElementById('libPagination');

  if (!wrap || !tbody) return;
  if (pager) pager.classList.add('d-none'); // hide paginator when JS enabled

  let nextUrl = tbody.dataset.nextUrl || '';
  let loading = false;

  async function loadMore() {
    if (!nextUrl || loading) return;
    loading = true;
    loader.classList.remove('d-none');

    try {
      const res  = await fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const html = await res.text();

      // Parse the next page and extract its table rows + next page URL
      const tmp = document.createElement('div');
      tmp.innerHTML = html;

      // rows
      const rows = tmp.querySelectorAll('#libraryTable tbody tr');
      rows.forEach(tr => tbody.appendChild(tr));

      // next page
      const next = tmp.querySelector('#libTbody');
      nextUrl = next ? (next.dataset.nextUrl || '') : '';

      // also try Laravel paginator link as fallback
      if (!nextUrl) {
        const a = tmp.querySelector('a[rel="next"]');
        if (a) nextUrl = a.getAttribute('href');
      }
    } catch (e) {
      console.error('Infinite scroll failed:', e);
      nextUrl = ''; // stop trying
    } finally {
      loader.classList.add('d-none');
      loading = false;
    }
  }

  // Use the container as the scroll root
  wrap.addEventListener('scroll', () => {
    const nearBottom = wrap.scrollTop + wrap.clientHeight >= wrap.scrollHeight - 120;
    if (nearBottom) loadMore();
  });

  // Also trigger once if content is short
  if (wrap.scrollHeight <= wrap.clientHeight + 120) loadMore();
})();
</script>
@endpush
