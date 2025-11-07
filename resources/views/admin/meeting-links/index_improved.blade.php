@extends('layouts.admin')

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h4 class="mb-1">Meeting Links</h4>
        <p class="text-muted mb-0">Create links and schedule sessions</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLinkModal">
        <i class="material-icons-outlined me-1">add</i>
        New Meeting Link
      </button>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
          <div class="card-body">
            <h3 class="mb-0">{{ $stats['total'] }}</h3>
            <small>Total Links</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
          <div class="card-body">
            <h3 class="mb-0">{{ $stats['active'] }}</h3>
            <small>Active Links</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 bg-info text-white">
          <div class="card-body">
            <h3 class="mb-0">{{ $upcomingSessions ?? 0 }}</h3>
            <small>Upcoming Sessions</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
          <div class="card-body">
            <h3 class="mb-0">{{ $stats['expired'] }}</h3>
            <small>Expired Links</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <input type="search" class="form-control" id="searchInput" placeholder="Search meeting links...">
          </div>
          <div class="col-md-3">
            <select class="form-select" id="statusFilter">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="expired">Expired</option>
            </select>
          </div>
          <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100" id="clearFilters">Clear Filters</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Meeting Links Grid -->
    <div class="row g-4" id="meetingLinksGrid">
      @foreach($meetings as $meeting)
        <div class="col-lg-6 col-xl-4" data-meeting-card data-status="{{ $meeting->is_expired ? 'expired' : 'active' }}" data-search="{{ strtolower($meeting->title . ' ' . $meeting->description) }}">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <!-- Header -->
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                  <h5 class="card-title mb-1">{{ $meeting->title }}</h5>
                  <div class="d-flex gap-2 mb-2">
                    <span class="badge {{ $meeting->mode === 'auto' ? 'bg-success' : 'bg-info' }}">
                      {{ $meeting->mode === 'auto' ? 'Auto Check-in' : 'Manual Form' }}
                    </span>
                    <span class="badge {{ $meeting->is_expired ? 'bg-danger' : 'bg-secondary' }}">
                      {{ $meeting->is_expired ? 'Expired' : 'Active' }}
                    </span>
                  </div>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                    <i class="material-icons-outlined">more_vert</i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ $meeting->url }}" target="_blank">Open Link</a></li>
                    <li><a class="dropdown-item copy-btn" href="#" data-link="{{ $meeting->url }}">Copy Link</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-edit-meeting="{{ $meeting->id }}">Edit</a></li>
                    <li><a class="dropdown-item text-danger" href="#" data-delete-meeting="{{ $meeting->id }}">Delete</a></li>
                  </ul>
                </div>
              </div>

              <!-- Description -->
              @if($meeting->description)
                <p class="text-muted small mb-3">{{ Str::limit($meeting->description, 100) }}</p>
              @endif

              <!-- Upcoming Sessions -->
              <div class="border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <small class="text-muted fw-semibold">UPCOMING SESSIONS</small>
                  <button class="btn btn-sm btn-primary" data-schedule-session="{{ $meeting->id }}" data-meeting-title="{{ $meeting->title }}">
                    <i class="material-icons-outlined me-1" style="font-size: 16px;">add</i>
                    Schedule
                  </button>
                </div>
                
                @if($meeting->upcoming_events && $meeting->upcoming_events->count() > 0)
                  <div class="list-group list-group-flush">
                    @foreach($meeting->upcoming_events->take(3) as $event)
                      <div class="list-group-item px-0 py-2 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <div class="fw-semibold small">
                              {{ $event->meeting_date ? \Carbon\Carbon::parse($event->meeting_date)->format('M j, Y') : 'Date TBD' }}
                              @if($event->start_time)
                                • {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                              @endif
                            </div>
                            <div class="text-muted small">{{ ucfirst($event->meeting_type ?? 'meeting') }}</div>
                          </div>
                          <button class="btn btn-sm btn-outline-secondary" data-edit-session="{{ $event->id }}">
                            Edit
                          </button>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  @if($meeting->events_count > 3)
                    <small class="text-muted">+{{ $meeting->events_count - 3 }} more sessions</small>
                  @endif
                @else
                  <div class="text-center py-3">
                    <i class="material-icons-outlined text-muted mb-2" style="font-size: 32px;">event_busy</i>
                    <div class="text-muted small">No sessions scheduled</div>
                  </div>
                @endif
              </div>

              <!-- Footer Info -->
              <div class="border-top pt-3 mt-3">
                <div class="row g-2 small text-muted">
                  <div class="col-6">
                    <strong>Token:</strong> {{ $meeting->token }}
                  </div>
                  <div class="col-6">
                    <strong>Created:</strong> {{ $meeting->created_at->format('M j') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Empty State -->
    <div class="text-center py-5 d-none" id="emptyState">
      <i class="material-icons-outlined text-muted mb-3" style="font-size: 64px;">search_off</i>
      <h5>No meeting links found</h5>
      <p class="text-muted">Try adjusting your search or filters</p>
    </div>
  </div>
</main>

<!-- Simplified Create Modal -->
<div class="modal fade" id="createLinkModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="{{ route('meetings.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Create Meeting Link</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description (optional)</label>
          <textarea name="description" class="form-control" rows="2"></textarea>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Mode</label>
            <select name="mode" class="form-select" required>
              <option value="auto">Auto Check-in</option>
              <option value="form">Manual Form</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Expires</label>
            <input type="date" name="expires_in" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Link</button>
      </div>
    </form>
  </div>
</div>

@include('admin.meeting-sessions._modal_simplified')
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
  // Search and filter functionality
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const clearFilters = document.getElementById('clearFilters');
  const cards = document.querySelectorAll('[data-meeting-card]');
  const emptyState = document.getElementById('emptyState');

  function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;
    let visibleCount = 0;

    cards.forEach(card => {
      const searchText = card.dataset.search || '';
      const status = card.dataset.status || '';
      
      const matchesSearch = !searchTerm || searchText.includes(searchTerm);
      const matchesStatus = statusValue === 'all' || status === statusValue;
      
      const isVisible = matchesSearch && matchesStatus;
      card.classList.toggle('d-none', !isVisible);
      
      if (isVisible) visibleCount++;
    });

    emptyState.classList.toggle('d-none', visibleCount > 0);
  }

  searchInput.addEventListener('input', applyFilters);
  statusFilter.addEventListener('change', applyFilters);
  clearFilters.addEventListener('click', () => {
    searchInput.value = '';
    statusFilter.value = 'all';
    applyFilters();
  });

  // Copy functionality
  document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      navigator.clipboard.writeText(btn.dataset.link);
      btn.textContent = 'Copied!';
      setTimeout(() => btn.textContent = 'Copy Link', 2000);
    });
  });

  // Schedule session buttons
  document.querySelectorAll('[data-schedule-session]').forEach(btn => {
    btn.addEventListener('click', () => {
      const meetingId = btn.dataset.scheduleSession;
      const meetingTitle = btn.dataset.meetingTitle;
      
      // Set up the modal for creating a new session
      const modal = document.getElementById('meetingEventModal');
      const form = document.getElementById('meetingEventForm');
      const linkSelect = document.getElementById('event_meeting_link_select');
      const title = document.getElementById('meetingEventModalTitle');
      
      form.action = '{{ route("meeting-events.store") }}';
      document.getElementById('meetingEventMethodField').value = 'POST';
      linkSelect.value = meetingId;
      title.textContent = `Schedule Session — ${meetingTitle}`;
      
      new bootstrap.Modal(modal).show();
    });
  });
});
</script>
@endpush