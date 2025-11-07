@extends('layouts.admin')
@php use Illuminate\Support\Str; @endphp

@section('meta')
  <title>Meeting Links — Admin</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div class="d-flex align-items-start gap-2">
        <i class="material-icons-outlined text-primary">link</i>
        <div>
          <h4 class="mb-1">Meeting Links</h4>
          <p class="text-muted mb-0">Group links by category, search quickly, and launch new check-ins.</p>
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-outline-secondary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#manageCategoryModal">
          <i class="material-icons-outlined fs-18">category</i>
          Add Category
        </button>
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createLinkModal">
          <i class="material-icons-outlined fs-18">add</i>
          New Link
        </button>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="text-muted small d-block mb-1">Total Links</span>
            <h3 class="mb-1">{{ $stats['total'] }}</h3>
            <span class="badge bg-primary-subtle text-primary">{{ $stats['categories'] }} categories</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="text-muted small d-block mb-1">Active Links</span>
            <h3 class="mb-1">{{ $stats['active'] }}</h3>
            <span class="text-success small">{{ $stats['expiring_soon'] }} expiring soon</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="text-muted small d-block mb-1">Auto Mode</span>
            <h3 class="mb-1">{{ $stats['auto_count'] }}</h3>
            <span class="text-muted small">Instant volunteer check-in</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="text-muted small d-block mb-1">Manual Forms</span>
            <h3 class="mb-1">{{ $stats['form_count'] }}</h3>
            <span class="text-danger small">{{ $stats['expired'] }} expired links</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3 align-items-center">
          <div class="col-md-4">
            <label for="meetingSearch" class="form-label text-muted small mb-1">Search</label>
            <div class="position-relative">
              <span class="material-icons-outlined text-muted position-absolute top-50 translate-middle-y ms-2">search</span>
              <input type="search" class="form-control ps-5" id="meetingSearch" placeholder="Search title, description, campus...">
            </div>
          </div>
          <div class="col-md-3 col-lg-2">
            <label for="categoryFilter" class="form-label text-muted small mb-1">Category</label>
            <select id="categoryFilter" class="form-select">
              <option value="all" selected>All categories</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->category }}</option>
              @endforeach
              <option value="uncategorized">Uncategorized</option>
            </select>
          </div>
          <div class="col-md-3 col-lg-2">
            <label for="modeFilter" class="form-label text-muted small mb-1">Mode</label>
            <select id="modeFilter" class="form-select">
              <option value="all" selected>Any mode</option>
              <option value="auto">Auto check-in</option>
              <option value="form">Manual form</option>
            </select>
          </div>
          <div class="col-md-3 col-lg-2">
            <label for="statusFilter" class="form-label text-muted small mb-1">Status</label>
            <select id="statusFilter" class="form-select">
              <option value="all" selected>Any status</option>
              <option value="active">Active</option>
              <option value="expired">Expired</option>
            </select>
          </div>
          <div class="col-md-3 col-lg-2">
            <label class="form-label text-muted small mb-1 d-block">Quick actions</label>
            <div class="d-flex flex-wrap gap-2">
              <button type="button" class="btn btn-light btn-sm" id="clearFiltersBtn">Clear</button>
              <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#manageCategoryModal">Manage categories</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    @php
      $orderedGroups = $groupedMeetings;
    @endphp

    @if($meetings->isEmpty())
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
          <h5 class="mb-2">No meeting links yet</h5>
          <p class="text-muted mb-0">Create your first meeting link to start tracking attendance.</p>
        </div>
      </div>
    @else
      <div id="meetingGroups" class="d-flex flex-column gap-4">
        @foreach($orderedGroups as $groupKey => $items)
          @php
            $categoryId = $groupKey === 'uncategorized' ? 'uncategorized' : (string) $groupKey;
            $categoryLabel = $groupKey === 'uncategorized' ? 'Uncategorized' : ($categoryLookup[$groupKey] ?? 'Other');
            $groupCount = $items->count();
          @endphp
          <section class="meeting-category-group" data-category-group="{{ $categoryId }}">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
              <div>
                <h5 class="mb-1">{{ $categoryLabel }}</h5>
                <span class="text-muted small">{{ $groupCount }} {{ Str::plural('link', $groupCount) }}</span>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#manageCategoryModal">
                Manage categories
              </button>
            </div>
            <div class="row g-3">
              @foreach($items as $meeting)
                @php
                  $campusGroup   = is_array($meeting->campus_group) ? $meeting->campus_group : [];
                  $ministryGroup = is_array($meeting->ministry_group) ? $meeting->ministry_group : [];
                  $deptGroup     = is_array($meeting->dept_group) ? $meeting->dept_group : [];
                  $audienceBadges = [];
                  if (!empty($campusGroup)) { $audienceBadges[] = ['Campus', count($campusGroup)]; }
                  if (!empty($ministryGroup)) { $audienceBadges[] = ['Ministry', count($ministryGroup)]; }
                  if (!empty($deptGroup)) { $audienceBadges[] = ['Dept', count($deptGroup)]; }
                  $searchIndex = Str::lower(collect([
                    $meeting->title,
                    $meeting->description,
                    $meeting->category_name,
                    implode(' ', $campusGroup),
                    implode(' ', $ministryGroup),
                    implode(' ', $deptGroup),
                    $meeting->expires_label,
                    $meeting->mode,
                    $meeting->token,
                  ])->filter()->implode(' '));
                  $status = $meeting->is_expired ? 'expired' : 'active';
                @endphp
                <div class="col-xl-4 col-lg-6 col-md-6" data-meeting-card data-category="{{ $meeting->category_id ?? 'uncategorized' }}" data-mode="{{ $meeting->mode }}" data-status="{{ $status }}" data-search="{{ $searchIndex }}">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-3">
                      <div class="d-flex justify-content-between align-items-start gap-3">
                        <div class="w-100">
                          <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge {{ $meeting->mode === 'auto' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' }}">
                              {{ $meeting->mode === 'auto' ? 'Auto check-in' : 'Manual form' }}
                            </span>
                            @if($meeting->require_auth && $meeting->mode === 'auto')
                              <span class="badge bg-warning-subtle text-warning">Auth required</span>
                            @endif
                            <span class="badge {{ $meeting->is_expired ? 'bg-danger' : 'bg-secondary-subtle text-secondary' }}">
                              {{ $meeting->expires_badge }}
                            </span>
                          </div>
                          <h5 class="fw-semibold mb-1 text-truncate" title="{{ $meeting->title }}">{{ $meeting->title }}</h5>
                          @if(!empty($meeting->description))
                            <p class="text-muted small mb-0">{{ Str::limit($meeting->description, 140) }}</p>
                          @endif
                        </div>
                        <div class="text-end small text-muted flex-shrink-0">
                          <div>{{ $meeting->created_label }}</div>
                          <div>Created</div>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap gap-2">
                        @if(empty($audienceBadges))
                          <span class="badge bg-secondary-subtle text-secondary">Audience set per session</span>
                        @else
                          @foreach($audienceBadges as $badge)
                            <span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center gap-1">
                              <span>{{ $badge[0] }}</span>
                              <span class="small">{{ $badge[1] }}</span>
                            </span>
                          @endforeach
                        @endif
                      </div>
                      <div class="small text-muted d-flex flex-wrap gap-3">
                        <span><strong>Expires:</strong> {{ $meeting->expires_label }}</span>
                        <span><strong>Token:</strong> {{ $meeting->token }}</span>
                        <span><strong>Sessions:</strong> {{ $meeting->events_count }}</span>
                      </div>
                      <div class="border rounded-3 p-3 bg-body-tertiary">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <span class="text-uppercase small text-muted fw-semibold">Upcoming sessions</span>
                          <button type="button"
                                  class="btn btn-sm btn-primary"
                                  data-schedule-event
                                  data-link-id="{{ $meeting->id }}"
                                  data-link-title="{{ $meeting->title }}">
                            Schedule
                          </button>
                        </div>
                        @if($meeting->upcoming_events->isNotEmpty())
                          <ul class="list-unstyled mb-0">
                            @foreach($meeting->upcoming_events->take(3) as $event)
                              @php
                                $eventDateLabel = $event->meeting_date_carbon ? $event->meeting_date_carbon->format('M d, Y') : 'Date TBD';
                                $eventTimeLabel = $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('g:i A') : null;
                                $eventTypeLabel = $event->meeting_type === 'training' ? 'Training' : 'Meeting';
                              @endphp
                              <li class="d-flex align-items-start justify-content-between gap-2 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                  <div class="fw-semibold">{{ $eventDateLabel }}@if($eventTimeLabel)<span class="text-muted"> • {{ $eventTimeLabel }}</span>@endif</div>
                                  <div class="text-muted small">{{ $eventTypeLabel }}</div>
                                  @if(!empty($event->video_url))
                                    <a href="{{ $event->video_url }}" target="_blank" rel="noopener noreferrer" class="small">Join link</a>
                                  @endif
                                </div>
                                <div class="d-flex gap-1">
                                  <button type="button"
                                          class="btn btn-outline-secondary btn-sm"
                                          data-edit-event
                                          data-event-id="{{ $event->id }}"
                                          data-link-id="{{ $meeting->id }}"
                                          data-event-title="{{ $event->title }}"
                                          data-event-date="{{ optional($event->meeting_date_carbon)->toDateString() }}"
                                          data-start-time="{{ $event->start_time }}"
                                          data-end-time="{{ $event->end_time }}"
                                          data-frequency="{{ $event->frequency }}"
                                          data-frequency-meta='@json($event->frequency_meta)'
                                          data-video-url="{{ $event->video_url }}"
                                          data-event-notes="{{ $event->notes }}"
                                          data-meeting-type="{{ $event->meeting_type }}"
                                          data-expires-at="{{ $event->expires_at }}"
                                          data-camp='@json($event->campus_group)'
                                          data-mins='@json($event->ministry_group)'
                                          data-dept='@json($event->dept_group)'>
                                    Edit
                                  </button>
                                  <form method="post" action="{{ route('meeting-events.destroy', $event->id) }}" onsubmit="return confirm('Delete this session?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">Delete</button>
                                  </form>
                                </div>
                              </li>
                            @endforeach
                          </ul>
                          @if($meeting->events_count > 3)
                            <div class="text-muted small mt-2">{{ $meeting->events_count - 3 }} more scheduled sessions.</div>
                          @endif
                        @else
                          <div class="text-muted small">No upcoming sessions scheduled.</div>
                        @endif
                      </div>
                      <div class="d-flex flex-wrap align-items-center gap-2 mt-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" data-link="{{ $meeting->url }}">Copy</button>
                        <a href="{{ $meeting->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light">Open</a>
                        @if($meeting->qr_url)
                          <button class="btn btn-sm btn-outline-secondary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#qrModal"
                                  data-title="{{ $meeting->title }}"
                                  data-qr="{{ $meeting->qr_url }}"
                                  data-link="{{ $meeting->url }}">
                            QR
                          </button>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary ms-auto"
                                data-bs-toggle="modal"
                                data-bs-target="#editMeetingModal"
                                data-id="{{ $meeting->id }}"
                                data-title="{{ $meeting->title }}"
                                data-description="{{ $meeting->description }}"
                                data-expires="{{ optional($meeting->expires_at_carbon)->toDateString() }}"
                                data-mode="{{ $meeting->mode }}"
                                data-auth="{{ $meeting->require_auth ? 1 : 0 }}"
                                data-category-id="{{ $meeting->category_id }}">
                          Edit
                        </button>
                        <form method="post" action="{{ route('meetings.destroy', $meeting->id) }}" onsubmit="return confirm('Delete this meeting link?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-link text-danger p-0">Delete</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @endforeach
      </div>
      <div id="meetingEmptyState" class="card border-0 shadow-sm text-center py-5 d-none">
        <div class="card-body">
          <h5 class="mb-2">No matching links</h5>
          <p class="text-muted mb-0">Try adjusting your filters or clear the search to see all links.</p>
        </div>
      </div>
    @endif
  </div>
</main>

<div class="modal fade" id="manageCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" id="createCategoryForm" method="post" action="{{ route('meetings.categories.store') }}">
      @csrf
      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="material-icons-outlined">category</i>
          Add Category
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="categoryFeedback" class="alert d-none"></div>
        <label class="form-label">Category name</label>
        <input type="text" name="category" class="form-control" placeholder="e.g. Weekend Services" required>
        <p class="text-muted small mt-2 mb-0">Categories help you group and filter meeting links. They are optional but recommended.</p>
        @if($categories->isNotEmpty())
          <hr>
          <p class="text-muted small mb-2">Existing categories</p>
          <div class="d-flex flex-wrap gap-2">
            @foreach($categories as $category)
              <span class="badge bg-secondary-subtle text-secondary">{{ $category->category }}</span>
            @endforeach
          </div>
        @endif
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save category</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="meetingEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="post" id="meetingEventForm">
      @csrf
      <input type="hidden" name="_method" id="meetingEventMethodField" value="">
      <input type="hidden" name="meeting_link_id" id="event_meeting_link_id">
      <input type="hidden" id="event_id_field">
      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center gap-2" id="meetingEventModalTitle">
          <i class="material-icons-outlined">event</i>
          Schedule Session
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Session title</label>
            <input type="text" name="title" id="event_title" class="form-control" placeholder="Defaults to meeting link title">
          </div>
          <div class="col-md-6">
            <label class="form-label">Session type</label>
            <select name="meeting_type" id="event_type" class="form-select" required>
              <option value="meeting">Meeting</option>
              <option value="training">Training</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Meeting date</label>
            <input type="date" name="meeting_date" id="event_date" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Start time</label>
            <input type="time" name="start_time" id="event_start_time" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">End time</label>
            <input type="time" name="end_time" id="event_end_time" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Frequency</label>
            <select name="frequency" id="event_frequency" class="form-select">
              <option value="once" selected>Once</option>
              <option value="weekly">Weekly</option>
              <option value="biweekly">Biweekly</option>
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="custom">Custom</option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Frequency details (optional)</label>
            <input type="text" name="frequency_meta[note]" id="event_frequency_note" class="form-control" placeholder="e.g. every second Tuesday">
          </div>
          <div class="col-md-6">
            <label class="form-label">Session expires</label>
            <input type="date" name="expires_at" id="event_expires_at" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Video meeting link</label>
            <input type="url" name="video_url" id="event_video_url" class="form-control" placeholder="https://">
          </div>
          <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" id="event_notes" class="form-control" rows="2" placeholder="Optional details or agenda"></textarea>
          </div>
          <div class="col-12">
            <div class="accordion" id="eventAudienceAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventCampusCollapse">
                    Campus Filters
                  </button>
                </h2>
                <div id="eventCampusCollapse" class="accordion-collapse collapse" data-bs-parent="#eventAudienceAccordion">
                  <div class="accordion-body">
                    <div class="d-flex justify-content-end gap-2 mb-2">
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkCheck('#eventCampusBox', true)">Select all</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkCheck('#eventCampusBox', false)">Clear</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkInvert('#eventCampusBox')">Invert</button>
                    </div>
                    <div class="row g-2" id="eventCampusBox">
                      @foreach ($campuses as $campus)
                        @php
                          $label = data_get($campus, 'label')
                                ?? data_get($campus, 'name')
                                ?? data_get($campus, 'campus_name')
                                ?? data_get($campus, 'campus')
                                ?? null;
                          if ($label === null || $label === '') continue;
                          $value   = data_get($campus, 'id', $label);
                          $inputId = 'event-campus-'.Str::slug($label);
                        @endphp
                        <div class="col-sm-6 col-lg-4">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="campus_group[]" id="{{ $inputId }}" value="{{ $value }}" data-label="{{ $label }}">
                            <label class="form-check-label" for="{{ $inputId }}">{{ $label }}</label>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventMinistryCollapse">
                    Ministry Filters
                  </button>
                </h2>
                <div id="eventMinistryCollapse" class="accordion-collapse collapse" data-bs-parent="#eventAudienceAccordion">
                  <div class="accordion-body">
                    <div class="d-flex justify-content-end gap-2 mb-2">
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkCheck('#eventMinistryBox', true)">Select all</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkCheck('#eventMinistryBox', false)">Clear</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkInvert('#eventMinistryBox')">Invert</button>
                    </div>
                    <div class="row g-2" id="eventMinistryBox">
                      @foreach ($ministries as $ministry)
                        @php
                          $label = data_get($ministry, 'name')
                                ?? data_get($ministry, 'title')
                                ?? data_get($ministry, 'ministry_name')
                                ?? data_get($ministry, 'label');
                          if ($label === null) { continue; }
                          $value   = data_get($ministry, 'id', $label);
                          $inputId = 'event-ministry-'.Str::slug($label);
                        @endphp
                        <div class="col-sm-6 col-lg-4">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ministry_group[]" id="{{ $inputId }}" value="{{ $value }}" data-label="{{ $label }}">
                            <label class="form-check-label" for="{{ $inputId }}">{{ $label }}</label>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventDeptCollapse">
                    Department Filters
                  </button>
                </h2>
                <div id="eventDeptCollapse" class="accordion-collapse collapse" data-bs-parent="#eventAudienceAccordion">
                  <div class="accordion-body">
                    <div class="d-flex justify-content-end gap-2 mb-2">
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkCheck('#eventDeptBox', true)">Select all</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkCheck('#eventDeptBox', false)">Clear</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkInvert('#eventDeptBox')">Invert</button>
                    </div>
                    <div class="row g-2" id="eventDeptBox">
                      @foreach ($departments as $dept)
                        @php
                          $label = data_get($dept, 'label')
                                ?? data_get($dept, 'name')
                                ?? data_get($dept, 'department_name')
                                ?? data_get($dept, 'title');
                          if ($label === null || $label === '') continue;
                          $value   = data_get($dept, 'id', $label);
                          $inputId = 'event-dept-'.Str::slug($label);
                        @endphp
                        <div class="col-sm-6 col-lg-4">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="dept_group[]" id="{{ $inputId }}" value="{{ $value }}" data-label="{{ $label }}">
                            <label class="form-check-label" for="{{ $inputId }}">{{ $label }}</label>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save session</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="createLinkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="post" action="{{ route('meetings.store') }}">
      @csrf
      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="material-icons-outlined">add_link</i>
          Create Meeting Link
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Category</label>
            <div class="d-flex gap-2">
              <select name="category_id" id="create_category_id" class="form-select" data-category-select>
                <option value="">Uncategorized</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->category }}</option>
                @endforeach
              </select>
              <button type="button" class="btn btn-outline-secondary" data-open-category-modal="createLinkModal">
                <span class="material-icons-outlined fs-18">add</span>
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Expires</label>
            <input type="date" name="expires_in" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Mode</label>
            <select name="mode" id="createMode" class="form-select" required>
              <option value="auto" selected>Auto — quick clock-in</option>
              <option value="form">Manual form submission</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Optional"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Require authentication</label>
            <div class="form-check form-switch mt-2">
              <input class="form-check-input" type="checkbox" id="createRequireAuthChk" checked>
              <label class="form-check-label" for="createRequireAuthChk">Volunteers must sign in</label>
            </div>
            <input type="hidden" name="require_auth" id="createRequireAuthValue" value="1">
          </div>
          <div class="col-12">
            <div class="alert alert-info border-0 mb-0">
              Audience filters and invite lists are configured when you schedule a meeting or training session.
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Create link</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="post" id="editMeetingForm">
      @csrf
      @method('PUT')
      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="material-icons-outlined">edit</i>
          Edit Meeting Link
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="edit_title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Category</label>
            <div class="d-flex gap-2">
              <select name="category_id" id="edit_category_id" class="form-select" data-category-select>
                <option value="">Uncategorized</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->category }}</option>
                @endforeach
              </select>
              <button type="button" class="btn btn-outline-secondary" data-open-category-modal="editMeetingModal">
                <span class="material-icons-outlined fs-18">add</span>
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Expires</label>
            <input type="date" name="expires_in" id="edit_expires" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Mode</label>
            <select name="mode" id="editMode" class="form-select" required>
              <option value="auto">Auto — quick clock-in</option>
              <option value="form">Manual form submission</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" id="edit_description" class="form-control" rows="2" placeholder="Optional"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Require authentication</label>
            <div class="form-check form-switch mt-2">
              <input class="form-check-input" type="checkbox" id="editRequireAuthChk">
              <label class="form-check-label" for="editRequireAuthChk">Volunteers must sign in</label>
            </div>
            <input type="hidden" name="require_auth" id="editRequireAuthValue" value="1">
          </div>
          <div class="col-12">
            <div class="alert alert-info border-0 mb-0">
              Audience filters are configured per meeting or training session. Schedule or edit a session to adjust who is invited.
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="qrModalTitle">QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="qrModalImage" src="" alt="QR Code" class="img-fluid rounded mb-3">
        <div class="input-group input-group-sm">
          <input id="qrModalLink" class="form-control" readonly>
          <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('qrModalLink').value)">
            Copy
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
  window.bulkCheck = function(selector, checked) {
    document.querySelectorAll(selector + ' input[type="checkbox"]').forEach(cb => cb.checked = !!checked);
  };
  window.bulkInvert = function(selector) {
    document.querySelectorAll(selector + ' input[type="checkbox"]').forEach(cb => cb.checked = !cb.checked);
  };

  document.querySelectorAll('.copy-btn').forEach(button => {
    button.addEventListener('click', () => {
      navigator.clipboard.writeText(button.dataset.link).then(() => {
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        button.textContent = 'Copied';
        setTimeout(() => {
          button.classList.remove('btn-success');
          button.classList.add('btn-outline-secondary');
          button.textContent = 'Copy';
        }, 2000);
      });
    });
  });

  const searchInput = document.getElementById('meetingSearch');
  const categoryFilter = document.getElementById('categoryFilter');
  const modeFilter = document.getElementById('modeFilter');
  const statusFilter = document.getElementById('statusFilter');
  const clearFilters = document.getElementById('clearFiltersBtn');
  const cards = () => Array.from(document.querySelectorAll('[data-meeting-card]'));
  const groups = () => Array.from(document.querySelectorAll('[data-category-group]'));
  const emptyState = document.getElementById('meetingEmptyState');

  function applyFilters() {
    const term = (searchInput?.value || '').trim().toLowerCase();
    const categoryValue = categoryFilter?.value || 'all';
    const modeValue = modeFilter?.value || 'all';
    const statusValue = statusFilter?.value || 'all';
    let visibleCards = 0;

    cards().forEach(card => {
      const matchesTerm = !term || (card.dataset.search || '').includes(term);
      const matchesCategory = categoryValue === 'all' || card.dataset.category === categoryValue;
      const matchesMode = modeValue === 'all' || card.dataset.mode === modeValue;
      const matchesStatus = statusValue === 'all' || card.dataset.status === statusValue;
      const isVisible = matchesTerm && matchesCategory && matchesMode && matchesStatus;
      card.classList.toggle('d-none', !isVisible);
      if (isVisible) {
        visibleCards += 1;
      }
    });

    groups().forEach(group => {
      const hasVisible = group.querySelector('[data-meeting-card]:not(.d-none)');
      group.classList.toggle('d-none', !hasVisible);
    });

    if (emptyState) {
      emptyState.classList.toggle('d-none', visibleCards !== 0);
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }
  categoryFilter?.addEventListener('change', applyFilters);
  modeFilter?.addEventListener('change', applyFilters);
  statusFilter?.addEventListener('change', applyFilters);
  clearFilters?.addEventListener('click', () => {
    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = 'all';
    if (modeFilter) modeFilter.value = 'all';
    if (statusFilter) statusFilter.value = 'all';
    applyFilters();
  });
  applyFilters();

  const createModeSel = document.getElementById('createMode');
  const createAuthChk = document.getElementById('createRequireAuthChk');
  const createAuthValue = document.getElementById('createRequireAuthValue');
  const editModeSel = document.getElementById('editMode');
  const editAuthChk = document.getElementById('editRequireAuthChk');
  const editAuthValue = document.getElementById('editRequireAuthValue');

  const configureModeAuth = (modeSel, authChk, hiddenInput) => {
    if (!modeSel || !authChk || !hiddenInput) return () => {};
    const sync = () => {
      if (modeSel.value === 'form') {
        authChk.disabled = true;
        authChk.checked = false;
        hiddenInput.value = '0';
      } else {
        authChk.disabled = false;
        hiddenInput.value = authChk.checked ? '1' : '0';
      }
    };
    modeSel.addEventListener('change', sync);
    authChk.addEventListener('change', sync);
    return sync;
  };

  const syncCreateAuth = configureModeAuth(createModeSel, createAuthChk, createAuthValue);
  syncCreateAuth();
  const syncEditAuth = configureModeAuth(editModeSel, editAuthChk, editAuthValue);

  const editModal = document.getElementById('editMeetingModal');
  if (editModal) {
    editModal.addEventListener('show.bs.modal', event => {
      const trigger = event.relatedTarget;
      if (!trigger) return;
      const form = document.getElementById('editMeetingForm');
      const id = trigger.getAttribute('data-id');
      if (form && id) {
        form.action = `{{ url('meeting-links') }}/${id}`;
      }
      document.getElementById('edit_title').value = trigger.getAttribute('data-title') || '';
      document.getElementById('edit_description').value = trigger.getAttribute('data-description') || '';
      document.getElementById('edit_expires').value = trigger.getAttribute('data-expires') || '';

      const modeValue = trigger.getAttribute('data-mode') || 'auto';
      if (editModeSel) {
        editModeSel.value = modeValue;
      }
      if (editAuthChk && editAuthValue) {
        const auth = trigger.getAttribute('data-auth') === '1';
        editAuthChk.checked = auth;
        editAuthValue.value = auth ? '1' : '0';
      }
      if (syncEditAuth) {
        syncEditAuth();
      }

      const categorySelect = document.getElementById('edit_category_id');
      if (categorySelect) {
        categorySelect.value = trigger.getAttribute('data-category-id') || '';
      }

    });
  }

  const eventModalEl = document.getElementById('meetingEventModal');
  const eventForm = document.getElementById('meetingEventForm');
  const eventMethodField = document.getElementById('meetingEventMethodField');
  const eventLinkInput = document.getElementById('event_meeting_link_id');
  const eventIdField = document.getElementById('event_id_field');
  const eventTitleInput = document.getElementById('event_title');
  const eventDateInput = document.getElementById('event_date');
  const eventStartInput = document.getElementById('event_start_time');
  const eventEndInput = document.getElementById('event_end_time');
  const eventTypeSelect = document.getElementById('event_type');
  const eventFrequencySelect = document.getElementById('event_frequency');
  const eventFrequencyNote = document.getElementById('event_frequency_note');
  const eventExpiresInput = document.getElementById('event_expires_at');
  const eventVideoInput = document.getElementById('event_video_url');
  const eventNotesInput = document.getElementById('event_notes');
  const eventModalTitle = document.getElementById('meetingEventModalTitle');

  const resetEventForm = () => {
    if (!eventForm) return;
    eventForm.reset();
    if (eventMethodField) eventMethodField.value = '';
    if (eventIdField) eventIdField.value = '';
    ['#eventCampusBox', '#eventMinistryBox', '#eventDeptBox'].forEach(selector => bulkCheck(selector, false));
  };

  const applyAudienceSelection = (selector, values) => {
    const set = new Set((values || []).map(String));
    document.querySelectorAll(selector + ' input[type=\"checkbox\"]').forEach(cb => {
      cb.checked = set.has(cb.value);
    });
  };

  const safeJsonParse = (value, fallback = null) => {
    if (!value) return fallback;
    try {
      return JSON.parse(value);
    } catch (_err) {
      return fallback;
    }
  };

  document.querySelectorAll('[data-schedule-event]').forEach(button => {
    button.addEventListener('click', () => {
      if (!eventModalEl || !window.bootstrap) return;
      resetEventForm();
      if (eventForm) {
        eventForm.action = `{{ route('meeting-events.store') }}`;
      }
      if (eventLinkInput) {
        eventLinkInput.value = button.getAttribute('data-link-id') || '';
      }
      if (eventModalTitle) {
        const linkTitle = button.getAttribute('data-link-title') || 'Session';
        eventModalTitle.textContent = `Schedule Session — ${linkTitle}`;
      }
      const modalInstance = window.bootstrap.Modal.getOrCreateInstance(eventModalEl);
      modalInstance.show();
    });
  });

  document.querySelectorAll('[data-edit-event]').forEach(button => {
    button.addEventListener('click', () => {
      if (!eventModalEl || !window.bootstrap) return;
      resetEventForm();
      const eventId = button.getAttribute('data-event-id');
      if (eventForm && eventId) {
        eventForm.action = `{{ url('meeting-events') }}/${eventId}`;
        if (eventMethodField) eventMethodField.value = 'PUT';
        if (eventIdField) eventIdField.value = eventId;
      }
      if (eventLinkInput) {
        eventLinkInput.value = button.getAttribute('data-link-id') || '';
      }
      if (eventTitleInput) {
        eventTitleInput.value = button.getAttribute('data-event-title') || '';
      }
      if (eventDateInput) {
        eventDateInput.value = button.getAttribute('data-event-date') || '';
      }
      if (eventStartInput) {
        const startRaw = button.getAttribute('data-start-time') || '';
        eventStartInput.value = startRaw ? startRaw.substring(0,5) : '';
      }
      if (eventEndInput) {
        const endRaw = button.getAttribute('data-end-time') || '';
        eventEndInput.value = endRaw ? endRaw.substring(0,5) : '';
      }
      if (eventTypeSelect) {
        eventTypeSelect.value = button.getAttribute('data-meeting-type') || 'meeting';
      }
      if (eventFrequencySelect) {
        eventFrequencySelect.value = button.getAttribute('data-frequency') || 'once';
      }
      if (eventFrequencyNote) {
        const meta = safeJsonParse(button.getAttribute('data-frequency-meta'));
        eventFrequencyNote.value = meta && typeof meta === 'object' ? (meta.note ?? '') : '';
      }
      if (eventExpiresInput) {
        const rawExpires = button.getAttribute('data-expires-at') || '';
        eventExpiresInput.value = rawExpires ? rawExpires.substring(0,10) : '';
      }
      if (eventVideoInput) {
        eventVideoInput.value = button.getAttribute('data-video-url') || '';
      }
      if (eventNotesInput) {
        eventNotesInput.value = button.getAttribute('data-event-notes') || '';
      }
      applyAudienceSelection('#eventCampusBox', safeJsonParse(button.getAttribute('data-camp'), []));
      applyAudienceSelection('#eventMinistryBox', safeJsonParse(button.getAttribute('data-mins'), []));
      applyAudienceSelection('#eventDeptBox', safeJsonParse(button.getAttribute('data-dept'), []));
      if (eventModalTitle) {
        const linkTitle = button.closest('[data-meeting-card]')?.querySelector('h5')?.textContent || 'Session';
        eventModalTitle.textContent = `Edit Session — ${linkTitle.trim()}`;
      }
      const modalInstance = window.bootstrap.Modal.getOrCreateInstance(eventModalEl);
      modalInstance.show();
    });
  });

  const qrModal = document.getElementById('qrModal');
  if (qrModal) {
    qrModal.addEventListener('show.bs.modal', ev => {
      const trigger = ev.relatedTarget;
      if (!trigger) return;
      document.getElementById('qrModalTitle').textContent = trigger.getAttribute('data-title') || 'QR Code';
      document.getElementById('qrModalImage').src = trigger.getAttribute('data-qr') || '';
      document.getElementById('qrModalLink').value = trigger.getAttribute('data-link') || '';
    });
  }

  const categoryForm = document.getElementById('createCategoryForm');
  const categoryFeedback = document.getElementById('categoryFeedback');
  const categoryModalEl = document.getElementById('manageCategoryModal');
  const categorySelects = document.querySelectorAll('[data-category-select]');
  const categoryFilterSelect = document.getElementById('categoryFilter');

  const appendCategoryOption = (id, label) => {
    const value = String(id);
    categorySelects.forEach(select => {
      const exists = Array.from(select.options).some(opt => opt.value === value);
      if (!exists) {
        const option = new Option(label, value, false, false);
        select.add(option);
      }
    });
    if (categoryFilterSelect) {
      const exists = Array.from(categoryFilterSelect.options).some(opt => opt.value === value);
      if (!exists) {
        const option = new Option(label, value, false, false);
        categoryFilterSelect.add(option, categoryFilterSelect.options.length - 1);
      }
    }
  };

  if (categoryForm) {
    categoryForm.addEventListener('submit', async event => {
      event.preventDefault();
      if (categoryFeedback) {
        categoryFeedback.classList.add('d-none');
        categoryFeedback.classList.remove('alert-danger', 'alert-success');
      }
      const submitBtn = categoryForm.querySelector('[type="submit"]');
      submitBtn?.setAttribute('disabled', 'disabled');
      try {
        const formData = new FormData(categoryForm);
        const response = await fetch(categoryForm.action, {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: formData
        });
        const payload = await response.json();
        if (!response.ok) {
          const message = payload?.message || 'Unable to add category.';
          if (categoryFeedback) {
            categoryFeedback.textContent = message;
            categoryFeedback.classList.remove('d-none');
            categoryFeedback.classList.add('alert', 'alert-danger');
          }
        } else {
          appendCategoryOption(payload.id, payload.category);
          categoryForm.reset();
          if (categoryFeedback) {
            categoryFeedback.textContent = 'Category added successfully.';
            categoryFeedback.classList.remove('d-none');
            categoryFeedback.classList.add('alert', 'alert-success');
          }
          setTimeout(() => {
            if (window.bootstrap && categoryModalEl) {
              const modalInstance = window.bootstrap.Modal.getInstance(categoryModalEl) || new window.bootstrap.Modal(categoryModalEl);
              modalInstance.hide();
            }
          }, 600);
        }
      } catch (error) {
        if (categoryFeedback) {
          categoryFeedback.textContent = 'Something went wrong. Please try again.';
          categoryFeedback.classList.remove('d-none');
          categoryFeedback.classList.add('alert', 'alert-danger');
        }
      } finally {
        submitBtn?.removeAttribute('disabled');
      }
    });
  }

  document.querySelectorAll('[data-open-category-modal]').forEach(button => {
    button.addEventListener('click', () => {
      if (!window.bootstrap || !categoryModalEl) return;
      const modalId = button.getAttribute('data-open-category-modal');
      if (!modalId) return;
      const categoryModal = window.bootstrap.Modal.getOrCreateInstance(categoryModalEl);
      categoryModalEl.dataset.reopenModalId = modalId;
      const currentModalEl = document.getElementById(modalId);
      if (currentModalEl) {
        const currentInstance = window.bootstrap.Modal.getInstance(currentModalEl) || new window.bootstrap.Modal(currentModalEl);
        currentInstance.hide();
      }
      setTimeout(() => categoryModal.show(), 200);
    });
  });

  if (categoryModalEl && window.bootstrap) {
    categoryModalEl.addEventListener('hidden.bs.modal', () => {
      const reopenId = categoryModalEl.dataset.reopenModalId;
      if (reopenId) {
        const modalEl = document.getElementById(reopenId);
        if (modalEl) {
          const instance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
          setTimeout(() => instance.show(), 200);
        }
        delete categoryModalEl.dataset.reopenModalId;
      }
    });
  }
})();
</script>
@endpush
