@php
  $items        = $items        ?? ($this->items ?? []);
  $schedule     = $schedule     ?? [];
  $totalHms     = $totalHms     ?? '00:00:00';
  $startDisplay = $startDisplay ?? now()->format('Y-m-d H:i');
  $endDisplay   = $endDisplay   ?? now()->format('Y-m-d H:i');
@endphp
<div class="card" aria-labelledby="playlist-items-heading">
  <div class="card-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <h6 id="playlist-items-heading" class="card-title mb-0">{{ __('Playlist Items') }}</h6>
      <div class="d-flex align-items-center gap-2">
        <div class="input-group input-group-sm">
          <span class="input-group-text">{{ __('Start') }}</span>
          <input type="datetime-local" wire:model="startAt" class="form-control" aria-label="{{ __('Playlist start') }}">
        </div>
        <span class="badge bg-light text-dark">
          <i class="material-icons-outlined align-middle">timer</i>
          <span class="align-middle">{{ $totalHms }}</span>
        </span>
        <span class="badge bg-primary">
          <i class="material-icons-outlined align-middle">schedule</i>
          <span class="align-middle">{{ $startDisplay }} → {{ $endDisplay }}</span>
        </span>
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-middle mb-0" role="grid" aria-label="{{ __('Playlist items') }}">
      <thead>
        <tr>
          <th scope="col" class="w-auto">{{ __('Title') }}</th>
          <th scope="col" class="text-nowrap">{{ __('Performer') }}</th>
          <th scope="col">{{ __('Category') }}</th>
          <th scope="col">{{ __('Theme') }}</th>
          <th scope="col" class="text-center">{{ __('Year') }}</th>
          <th scope="col">{{ __('Path') }}</th>
          <th scope="col" class="text-center">{{ __('Duration') }}</th>
          <th scope="col" class="text-center">{{ __('Start → End') }}</th>
          <th scope="col" class="text-end">{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody id="playlist-sortable" wire:sortable="persistOrder" role="list" aria-dropeffect="move">
        @foreach($items as $idx => $it)
          @php
            $sched = collect($schedule)->firstWhere('id', $it['itemId']);
          @endphp
          <tr role="listitem" data-id="{{ $it['itemId'] }}">
            <td>
              <div class="fw-semibold">{{ $it['title'] ?? '—' }}</div>
              <div class="text-muted small">{{ $it['path'] ?? '—' }}</div>
            </td>
            <td>{{ $it['performer'] ?? '—' }}</td>
            <td>{{ $it['category'] ?? '—' }}</td>
            <td>{{ $it['theme'] ?? '—' }}</td>
            <td class="text-center">{{ $it['year'] ?? '—' }}</td>
            <td class="small">{{ $it['path'] ?? '—' }}</td>
            <td class="text-center">{{ \App\Support\Time::formatHms($it['duration']) }}</td>
            <td class="text-center">
              {{ optional($sched)['start']->format('H:i') ?? '—' }}
              →
              {{ optional($sched)['end']->format('H:i') ?? '—' }}
            </td>
            <td class="text-end">
              <div class="btn-group" role="group" aria-label="{{ __('Reorder') }}">
                <button class="btn btn-outline-secondary btn-sm" wire:click="moveUp({{ $idx }})" aria-label="{{ __('Move up') }}">
                  <i class="material-icons-outlined">arrow_upward</i>
                </button>
                <button class="btn btn-outline-secondary btn-sm" wire:click="moveDown({{ $idx }})" aria-label="{{ __('Move down') }}">
                  <i class="material-icons-outlined">arrow_downward</i>
                </button>
                <button class="btn btn-outline-secondary btn-sm handle" aria-label="{{ __('Drag to reorder') }}">
                  <i class="material-icons-outlined">drag_handle</i>
                </button>
              </div>
              <button class="btn btn-outline-danger btn-sm ms-1" wire:click="remove({{ $it['itemId'] }})" aria-label="{{ __('Remove') }}">
                <i class="material-icons-outlined">delete</i>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
function initPlaylistSortable(){
  const el = document.getElementById('playlist-sortable');
  if (!el || el.dataset.sortableInit === '1') return;
  el.dataset.sortableInit = '1';
  new Sortable(el, {
    handle: '.handle',
    animation: 150,
    onEnd: function () {
      @this.call('persistOrder');
    }
  });
}

// v2 hydration
document.addEventListener('livewire:load', function () {
  initPlaylistSortable();

  // Re-run after Livewire DOM patches
  Livewire.hook('message.processed', (message, component) => {
    initPlaylistSortable();
  });
});
</script>
@endpush