{{-- resources/views/radio_dashboard/Program/playlists/edit.blade.php --}}
@extends('layouts.radio_layout')

@section('meta')
  <title>{{ __('Edit Playlist') }} | Glory Servant</title>
@endsection

@section('content')
<main class="main-wrapper radio-dashboard-skin">
  <div class="main-content radio-dashboard-content">

    {{-- Sticky editor toolbar --}}
    <div class="card mb-3 shadow-sm rounded-3 border-0">
      <div class="card-body py-3 position-sticky top-0 border-bottom" style="z-index: 1020;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

          {{-- Title + ID --}}
          <div class="d-flex align-items-baseline gap-2 flex-wrap">
            <h4 class="mb-0 radio-title-accent">{{ __('Edit Playlist') }}</h4>
            <span class="radio-chip-accent">#{{ $playlist->id }}</span>
          </div>

          {{-- Save form --}}
          <form method="POST" action="{{ route('program.playlists.update', $playlist) }}"
                class="d-flex align-items-center gap-2 flex-wrap">
            @csrf
            @method('PUT')

            {{-- Playlist name --}}
            <div class="input-group input-group-sm" style="width: 500px;">
                <span class="input-group-text bg-light border-0 text-secondary">
                    <i class="material-icons-outlined">queue_music</i>
                </span>
                <input type="text" name="name" 
                       value="{{ old('name', $playlist->name) }}"
                       class="form-control border-0" 
                       placeholder="{{ __('Playlist name') }}">

                <span class="input-group-text bg-light border-0 text-secondary">
                    <i class="material-icons-outlined">equalizer</i>
                </span>
                <input type="number" name="youtube_peak" 
                       value="{{ old('youtube_peak', $playlist->youtube_peak) }}"
                       class="form-control border-0" 
                       placeholder="{{ __('YouTube Peak') }}">
            </div>

            {{-- Status --}}
            @php $status = old('status', $playlist->status ?? 'draft'); @endphp
            <select name="status" class="form-select form-select-sm w-auto">
              <option value="draft"    @selected($status==='draft')>{{ __('Draft') }}</option>
              <option value="approved" @selected($status==='approved')>{{ __('Approved') }}</option>
              <option value="exported" @selected($status==='exported')>{{ __('Exported') }}</option>
            </select>

            {{-- Hidden start_at (synced from preview input below) --}}
            <input type="hidden" name="start_at" id="startAtHidden" value="{{ $startAt }}">
	    
            <a href="{{ route('program.playlists.export.xlsx', $playlist) }}" class="btn btn-secondary btn-sm">
              <i class="material-icons-outlined">file_download</i> XLSX
            </a>
	    
            <a href="{{ route('program.playlists.export.csv', $playlist) }}" class="btn btn-outline-secondary btn-sm">
              <i class="material-icons-outlined">download</i> CSV
            </a>

            <button class="btn btn-primary btn-sm d-flex align-items-center">
              <i class="material-icons-outlined me-1">save</i>{{ __('Save') }}
            </button>

            <a href="{{ route('program.playlists.index') }}" class="btn btn-light btn-sm d-flex align-items-center">
              <i class="material-icons-outlined me-1">arrow_back</i>{{ __('Back') }}
            </a>
          </form>
        </div>
      </div>
    </div>

    {{-- Schedule / totals / clear --}}
    <div class="card mb-3 border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center gap-2">

          {{-- GET preview form (does not save, just recalculates) --}}
          <form method="GET" class="d-flex flex-wrap align-items-center gap-2" role="search">
            {{-- keep current filters on preview apply --}}
            <input type="hidden" name="q" value="{{ $q }}">
            <input type="hidden" name="category" value="{{ $category }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="theme"     value="{{ $theme }}">       {{-- NEW --}}
            <input type="hidden" name="performer" value="{{ $performer }}">   {{-- NEW --}}

            <div class="d-flex align-items-center gap-2">
              <div class="input-group input-group-sm" style="min-width:280px; max-width: 420px;">
                <span class="input-group-text"><i class="material-icons-outlined">schedule</i></span>
                <label for="startAtInput" class="visually-hidden">{{ __('Playlist start') }}</label>
                <input id="startAtInput" type="datetime-local" name="start_at"
                       value="{{ $startAt }}" class="form-control" aria-label="{{ __('Playlist start') }}">
                <button class="btn btn-primary btn-sm" title="{{ __('Apply start time') }}">
                  <i class="material-icons-outlined align-middle">check</i>
                  <span class="align-middle d-none d-sm-inline">{{ __('Apply') }}</span>
                </button>
              </div>

              {{-- Presets --}}
              <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="material-icons-outlined align-middle">bolt</i>
                  <span class="align-middle d-none d-sm-inline">{{ __('Presets') }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><button type="button" class="dropdown-item js-set-start"
                              data-val="{{ now()->format('Y-m-d\TH:i') }}">{{ __('Now') }}</button></li>
                  <li><button type="button" class="dropdown-item js-set-start"
                              data-val="{{ now()->copy()->minute(0)->second(0)->format('Y-m-d\TH:i') }}">{{ __('Top of hour') }}</button></li>
                  <li><button type="button" class="dropdown-item js-set-start"
                              data-val="{{ now()->addMinutes(15)->format('Y-m-d\TH:i') }}">{{ __('+15 min') }}</button></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><button type="button" class="dropdown-item js-set-start"
                              data-val="{{ now()->addDay()->setTime(9,0)->format('Y-m-d\TH:i') }}">{{ __('Tomorrow 09:00') }}</button></li>
                </ul>
              </div>
            </div>
          </form>

          {{-- Totals chips --}}
          <div class="d-flex flex-wrap align-items-center gap-2 ms-sm-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 js-copy"
                    data-copy="{{ $totalHms }}" title="{{ __('Copy total duration') }}">
              <i class="material-icons-outlined align-middle">timer</i>
              <span class="align-middle ms-1">{{ $totalHms }}</span>
            </button>

            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 js-copy"
                    data-copy="{{ $startDisplay }} → {{ $endDisplay }}" title="{{ __('Copy schedule range') }}">
              <i class="material-icons-outlined align-middle">event</i>
              <span class="align-middle ms-1">{{ $startDisplay }} → {{ $endDisplay }}</span>
            </button>
          </div>

          {{-- Clear all --}}
          <div class="ms-auto">
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmClearModal">
              <i class="material-icons-outlined align-middle">delete_sweep</i>
              <span class="align-middle d-none d-sm-inline">{{ __('Clear all') }}</span>
            </button>
          </div>

        </div>
      </div>
    </div>

{{-- Confirm clear modal --}}
<div class="modal fade" id="confirmClearModal" tabindex="-1" aria-labelledby="confirmClearTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmClearTitle">{{ __('Clear playlist') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      <div class="modal-body">
        {{ __('This will remove all items from this playlist. This action cannot be undone.') }}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <form method="POST" action="{{ route('program.playlists.items.clear', $playlist) }}" class="d-inline">
          @csrf @method('DELETE')
          <button class="btn btn-danger">
            <i class="material-icons-outlined align-middle" aria-hidden="true">delete_forever</i>
            <span class="align-middle">{{ __('Clear all') }}</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>



    <div class="row g-3">
      {{-- Left: Library --}}
      <div class="col-lg-4" style="height:100vh;">
  <div class="card rounded-3 border-0 shadow-sm d-flex flex-column" style="height:100%;">

    {{-- Header --}}
    <div class="card-header">
      <h6 class="mb-0">{{ __('Library') }}</h6>
    </div>

    {{-- Filters (non-growing) --}}
    <div class="card-body pb-2 flex-shrink-0">
      @php $resetAllUrl = request()->url(); @endphp

      <form method="GET" class="row g-2">
        {{-- Search --}}
        <div class="col-12">
          <label for="libSearch" class="visually-hidden">{{ __('Search') }}</label>
          <div class="input-group">
            <span class="input-group-text"><i class="material-icons-outlined">search</i></span>
            <input id="libSearch" type="search" name="q" value="{{ $q }}" class="form-control"
                   placeholder="{{ __('Search title/performer/category/theme/year/full name…') }}">
          </div>
        </div>

        @php
          $selCategory  = strtolower(trim((string)($category  ?? '')));
          $selYear      = strtolower(trim((string)($year      ?? '')));
          $selTheme     = strtolower(trim((string)($theme     ?? '')));
          $selPerformer = strtolower(trim((string)($performer ?? '')));
        @endphp

        {{-- Category --}}
        <div class="col-6">
          <label class="form-label" for="libCategory">{{ __('Category') }}</label>
          <select id="libCategory" class="form-select" name="category">
            <option value="" @selected($selCategory==='')>{{ __('All') }}</option>
            @foreach($categories as $c)
              @php $v = strtolower(trim((string)$c)); @endphp
              <option value="{{ trim((string)$c) }}" @selected($selCategory===$v)>{{ $c }}</option>
            @endforeach
          </select>
        </div>

        {{-- Year --}}
        <div class="col-3">
          <label class="form-label" for="libYear">{{ __('Year') }}</label>
          <select id="libYear" class="form-select" name="year">
            <option value="" @selected($selYear==='')>{{ __('All') }}</option>
            @foreach($years as $y)
              @php $v = strtolower(trim((string)$y)); @endphp
              <option value="{{ trim((string)$y) }}" @selected($selYear===$v)>{{ $y }}</option>
            @endforeach
          </select>
        </div>

        {{-- Theme --}}
        <div class="col-3">
          <label class="form-label" for="libTheme">{{ __('Theme') }}</label>
          <select id="libTheme" class="form-select" name="theme">
            <option value="" @selected($selTheme==='')>{{ __('All') }}</option>
            @foreach($themes as $t)
              @php $v = strtolower(trim((string)$t)); @endphp
              <option value="{{ trim((string)$t) }}" @selected($selTheme===$v)>{{ $t }}</option>
            @endforeach
          </select>
        </div>

        {{-- Performer --}}
        <div class="col-7">
          <label class="form-label" for="libPerformer">{{ __('Performer') }}</label>
          <select id="libPerformer" class="form-select" name="performer">
            <option value="" @selected($selPerformer==='')>{{ __('All') }}</option>
            @foreach($performers as $p)
              @php $v = strtolower(trim((string)$p)); @endphp
              <option value="{{ trim((string)$p) }}" @selected($selPerformer===$v)>{{ $p }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-5">
          <label class="form-label" for="libPerformer">{{ __('Filter') }}</label><br>
          <button class="btn btn-primary btn-sm">
            <i class="material-icons-outlined align-middle">filter_alt</i>
            <span class="align-middle">{{ __('Filter') }}</span>
          </button>

          {{-- One clean reset --}}
          <a class="btn btn-light btn-sm" href="{{ $resetAllUrl }}">
            <i class="material-icons-outlined align-middle">backspace</i>
            <span class="align-middle">{{ __('Reset all') }}</span>
          </a>
        </div>
      </form>
    </div><hr>

    {{-- Results (fills remaining height; scrollable) --}}
    <div
      id="libraryList"
      class="list-group list-group-flush js-lib-items flex-grow-1 overflow-auto"
      style="min-height:0;"
      data-next-url="{{ $tracks->nextPageUrl() ? $tracks->nextPageUrl() : '' }}"
    >
      @forelse($tracks as $t)
        <div class="list-group-item d-flex justify-content-between align-items-start">
          <div class="pe-3">
            <div class="fw-semibold">{{ $t->title ?? '—' }}</div>
            <div class="mt-1 small">
              @if($t->performer)<span class="badge bg-light text-secondary border me-1">{{ $t->performer }}</span>@endif
              @if($t->category)<span class="badge bg-light text-secondary border me-1">{{ $t->category }}</span>@endif
              @if($t->theme)<span class="badge bg-light text-secondary border me-1">{{ $t->theme }}</span>@endif
              @if($t->year)<span class="badge bg-light text-secondary border me-1">{{ $t->year }}</span>@endif
              @if($t->duration_seconds)
                <span class="badge bg-light text-secondary border">
                  {{ gmdate('H:i:s', $t->duration_seconds) }}
                </span>
              @endif
            </div>
          </div>
          <form method="POST" action="{{ route('program.playlists.items.store', $playlist) }}" class="ms-auto">
            @csrf
            <input type="hidden" name="track_id" value="{{ $t->id }}">
            <button class="btn btn-success btn-sm" aria-label="{{ __('Add to playlist') }}">
              <i class="bi bi-plus-lg me-1"></i>
            </button>
          </form>
        </div>
      @empty
        <div class="list-group-item text-secondary">{{ __('No results') }}</div>
      @endforelse

      {{-- Infinite scroll sentinel inside the scrollable area --}}
      <div id="libSentinel" class="visually-hidden" aria-hidden="true"></div>
    </div>

    {{-- Footer (non-growing) --}}
    <div class="card-footer d-flex justify-content-between align-items-center flex-shrink-0">
      <div class="small text-secondary">{{ $tracks->total() }} {{ __('items') }}</div>
      <nav class="js-lib-pagination d-none">
        {!! $tracks->appends(request()->query())->onEachSide(1)->links('vendor.pagination.radio') !!}
      </nav>
      <div class="ms-auto ps-2">
        <div class="spinner-border spinner-border-sm text-secondary d-none" role="status" id="libSpinner">
          <span class="visually-hidden">{{ __('Loading…') }}</span>
        </div>
      </div>
    </div>

  </div>
</div>



      {{-- Right: Playlist Items --}}
      <div class="col-lg-8" style="height:100vh;">
  <div class="card rounded-4 d-flex flex-column" style="height:100%;">

    {{-- Header (fixed) --}}
    <div class="card-header flex-shrink-0">
      <h6 class="mb-0">{{ __('Playlist Items') }}</h6>
    </div>

    {{-- Scrollable table area --}}
    <div class="table-responsive flex-grow-1 overflow-auto" style="min-height:0;">
      <table class="table align-middle mb-0">
        <thead class="table-light position-sticky top-0" style="z-index:1;">
          <tr>
            <th style="width:45%">{{ __('Title') }}</th>
            <th>{{ __('Performer') }}</th>
            <th>{{ __('Category') }}</th>
            <th class="text-center">{{ __('Duration') }}</th>
            <th class="text-center">{{ __('Start → End') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>

        @php
          // Expect $schedule to exist; key by item id once
          $schedById = collect($schedule ?? [])->keyBy('id');
        @endphp

        <tbody
          id="plItemsBody"
          class="js-pl-items"
          data-next-url="{{ $items->nextPageUrl() ? $items->nextPageUrl() : '' }}"
        >
          @forelse($items as $it)
            @php
              $sched = $schedById[$it->id] ?? null;
              $d     = (int)($it->track->duration_seconds ?? 0);
            @endphp
            <tr data-item-id="{{ $it->id }}">
              <td>
                <div class="fw-semibold">{{ $it->track->title ?? '—' }}</div>
              </td>
              <td><div class="fw-semibold">{{ $it->track->performer ?? '—' }}</div></td>
              <td><div class="fw-semibold">{{ $it->track->category ?? '—' }}</div></td>
              <td class="text-center">{{ \App\Support\Time::formatHms($d) }}</td>
              <td class="text-center">
                {{ $sched ? $sched['start']->format('H:i') : '—' }}
                &nbsp;→&nbsp;
                {{ $sched ? $sched['end']->format('H:i') : '—' }}
              </td>
              <td class="text-end">
  <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Reorder') }}">
    <form method="POST" action="{{ route('program.playlists.items.up', [$playlist, $it]) }}" class="d-inline">
      @csrf
      <button class="btn btn-outline-secondary btn-xs p-1" title="{{ __('Move up') }}" aria-label="{{ __('Move up') }}">
        <i class="material-icons-outlined" style="font-size: 16px;" aria-hidden="true">arrow_upward</i>
      </button>
    </form>
    <form method="POST" action="{{ route('program.playlists.items.down', [$playlist, $it]) }}" class="d-inline">
      @csrf
      <button class="btn btn-outline-secondary btn-xs p-1 ms-1" title="{{ __('Move down') }}" aria-label="{{ __('Move down') }}">
        <i class="material-icons-outlined" style="font-size: 16px;" aria-hidden="true">arrow_downward</i>
      </button>
    </form>
  </div>

  <form method="POST" class="d-inline" action="{{ route('program.playlists.items.destroy', [$playlist, $it]) }}">
    @csrf @method('DELETE')
    <button class="btn btn-outline-danger btn-xs p-1 ms-1" title="{{ __('Remove') }}" aria-label="{{ __('Remove') }}">
      <i class="material-icons-outlined" style="font-size: 16px;" aria-hidden="true">delete</i>
    </button>
  </form>
</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-secondary text-center py-4">{{ __('No items yet') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Sentinel inside the scrollable area --}}
      <div id="plItemsSentinel" class="visually-hidden" aria-hidden="true"></div>
    </div>

    {{-- Footer (fixed) --}}
    <div class="card-footer d-flex align-items-center justify-content-between flex-shrink-0">
      <div class="small text-secondary">
        {{ $items->total() }} {{ __('items') }}
      </div>
      <div class="d-flex align-items-center gap-2">
        {{-- Fallback pagination hidden when JS is active --}}
        <nav class="js-pl-pagination d-none">
          {!! $items->appends(request()->query())->onEachSide(1)->links('vendor.pagination.radio') !!}
        </nav>
        <div class="spinner-border spinner-border-sm text-secondary d-none" role="status" id="plSpinner">
          <span class="visually-hidden">{{ __('Loading…') }}</span>
        </div>
      </div>
    </div>

  </div>
</div>
      {{-- /Right --}}
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  // Keep POST hidden start_at in sync with the GET preview input
  const src = document.getElementById('startAtInput');
  const hid = document.getElementById('startAtHidden');
  if (src && hid) {
    const sync = () => hid.value = src.value;
    src.addEventListener('input', sync);
    sync();
  }
</script>

<script>
(function(){
  const list     = document.getElementById('libraryList');
  if (!list) return;
  const spinner  = document.getElementById('libSpinner');
  const sentinel = document.getElementById('libSentinel');
  let nextUrl    = list.dataset.nextUrl || '';
  let loading    = false;

  // If there is pagination markup, reveal it only when JS is disabled (we keep it hidden here)
  const pg = document.querySelector('.js-lib-pagination');
  if (pg) pg.classList.add('d-none');

  // Helper: fetch and append next page
  async function loadMore(){
    if (!nextUrl || loading) return;
    loading = true;
    spinner && spinner.classList.remove('d-none');

    try {
      const res = await fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();

      // Create a DOM from response and extract the same list items + next link
      const tmp = document.createElement('div');
      tmp.innerHTML = html;

      // Grab items from the next page's library list
      const nextItems = tmp.querySelectorAll('.js-lib-items > .list-group-item');
      nextItems.forEach(el => list.appendChild(el));

      // Update nextUrl from the next page container's data attribute (if present)
      const nextContainer = tmp.querySelector('#libraryList');
      nextUrl = nextContainer ? (nextContainer.dataset.nextUrl || '') : '';

      // If not found, try Laravel paginator next link
      if (!nextUrl) {
        const aNext = tmp.querySelector('a[rel="next"]');
        nextUrl = aNext ? aNext.getAttribute('href') : '';
      }
    } catch(e) {
      console.error('Infinite scroll error:', e);
      // Stop further attempts on error
      nextUrl = '';
    } finally {
      spinner && spinner.classList.add('d-none');
      loading = false;
    }
  }

  // IntersectionObserver to trigger loading near the bottom of the list
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) loadMore();
    });
  }, { root: list, rootMargin: '0px 0px 200px 0px' });
  io.observe(sentinel);

  // Also load more when user scrolls close to bottom (safety)
  list.addEventListener('scroll', () => {
    const nearBottom = list.scrollTop + list.clientHeight >= list.scrollHeight - 160;
    if (nearBottom) loadMore();
  });
})();
</script>

<script>
(function(){
  // Quick presets: set value into datetime-local and auto-submit
  document.querySelectorAll('.js-set-start').forEach(btn => {
    btn.addEventListener('click', function(){
      const val = this.dataset.val;
      const input = document.getElementById('startAtInput');
      if (input) {
        input.value = val;
        // find closest form and submit
        const form = input.closest('form');
        if (form) form.requestSubmit();
      }
    });
  });

  // Copy-on-click for chips
  document.querySelectorAll('.js-copy').forEach(btn => {
    btn.addEventListener('click', async function(){
      const text = this.dataset.copy || '';
      try {
        await navigator.clipboard.writeText(text);
        this.classList.add('btn-success');
        setTimeout(()=> this.classList.remove('btn-success'), 600);
      } catch (e) {
        // fallback: select temp input
        const tmp = document.createElement('input');
        tmp.value = text;
        document.body.appendChild(tmp);
        tmp.select();
        document.execCommand('copy');
        document.body.removeChild(tmp);
      }
    });
  });
})();
</script>
@endpush
