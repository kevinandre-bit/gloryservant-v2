@include('layouts/radio_layout')

@php
  // Fake assignments for UI
  $assignments = $assignments ?? [
    [
      'id' => 101,
      'title' => 'Replace RF jumper & sweep line',
      'site' => 'Les Cayes – 104.7',
      'dept' => 'South',
      'window' => 'Today 10:00–12:00',
      'priority' => 'High',
      'status' => 'Assigned',
      'tech' => 'Jean M.',
      'desc' => 'Weak SNR reported since yesterday. Verify connectors and do quick sweep.',
    ],
    [
      'id' => 102,
      'title' => 'UPS battery health check',
      'site' => 'Hinche – 90.3',
      'dept' => 'Center',
      'window' => 'Today 14:00–15:30',
      'priority' => 'Normal',
      'status' => 'Scheduled',
      'tech' => 'Mickael P.',
      'desc' => 'Preventive maintenance window. Log voltages & runtime.',
    ],
    [
      'id' => 103,
      'title' => 'Antenna visual inspection',
      'site' => 'Port-de-Paix – 93.7',
      'dept' => 'North-West',
      'window' => 'Tomorrow 09:00–10:30',
      'priority' => 'Low',
      'status' => 'Pending',
      'tech' => 'Yves D.',
      'desc' => 'Post-storm check: mounting hardware & coax strain-relief.',
    ],
  ];
@endphp

<main class="main-wrapper">
  <div class="main-content">

    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Technicians</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Assignments</li>
        </ol>
      </div>
      <div class="ms-auto">
        <button class="btn btn-primary d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#newAssignModal">
          <i class="material-icons-outlined">add_task</i> New Assignment
        </button>
      </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Assignment Cards Grid -->
    <div id="assignGrid" class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
      @foreach($assignments as $a)
        <div class="col">
          <div class="card h-100 rounded-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="card-title mb-0">{{ $a['title'] }}</h5>
                <div class="d-flex gap-2">
                  <span class="badge bg-primary">{{ $a['status'] }}</span>
                  <span class="badge
                    @if($a['priority']=='High') bg-danger
                    @elseif($a['priority']=='Normal') bg-warning text-dark
                    @else bg-success @endif">
                    {{ $a['priority'] }}
                  </span>
                </div>
              </div>

              <div class="d-flex flex-column gap-1 mb-2 small">
                <div><i class="material-icons-outlined">location_on</i> {{ $a['site'] }} — {{ $a['dept'] }}</div>
                <div><i class="material-icons-outlined">event</i> {{ $a['window'] }}</div>
                <div><i class="material-icons-outlined">engineering</i> Tech: {{ $a['tech'] }}</div>
              </div>

              <p class="mb-0">{{ $a['desc'] }}</p>

              <div class="mt-3 d-flex flex-wrap gap-2">
                <button class="btn btn-success btn-sm d-flex align-items-center gap-1">
                  <i class="material-icons-outlined fs-6">task_alt</i> Done
                </button>
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                  <i class="material-icons-outlined fs-6">chat</i> Msg
                </button>
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                  <i class="material-icons-outlined fs-6">pin_drop</i> Map
                </button>
              </div>
            </div><!--/card-body-->
          </div>
        </div>
      @endforeach
    </div>

  </div>
</main>

<!-- New Assignment Modal (same as before but without thumbnail input) -->
<div class="modal fade" id="newAssignModal" tabindex="-1" aria-labelledby="newAssignModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="newAssignForm">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center gap-2">
            <i class="material-icons-outlined">assignment_ind</i> New Assignment
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Technician</label>
              <input type="text" class="form-control" name="tech" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Site</label>
              <select class="form-select" name="site" required>
                <option value="" disabled selected>Select site</option>
                <option>Ouest — Port-au-Prince</option>
                <option>Sud — Les Cayes</option>
                <option>Grand’Anse — Jérémie</option>
                <option>Centre — Hinche</option>
                <option>Nord — Cap-Haïtien</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" value="{{ now()->toDateString() }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Start</label>
              <input type="time" class="form-control" name="start" value="08:00" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">End</label>
              <input type="time" class="form-control" name="end" value="16:00" required>
            </div>

            <div class="col-12">
              <label class="form-label">Task</label>
              <input type="text" class="form-control" name="title" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Priority</label>
              <select class="form-select" name="priority" required>
                <option>High</option>
                <option selected>Normal</option>
                <option>Low</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" required>
                <option selected>Assigned</option>
                <option>Scheduled</option>
                <option>Pending</option>
                <option>Completed</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea class="form-control" name="desc" rows="3"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="material-icons-outlined">save</i> Add
          </button>
        </div>
      </form>
    </div>
  </div>
</div>