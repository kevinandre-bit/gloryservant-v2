@extends('layouts.admin')

@section('meta')
  <title>Case #{{ $case->id }} | Wellness</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="mb-0"><i class="material-icons-outlined me-1">assignment</i> Case #{{ $case->id }}</h5>
      <a href="{{ route('wellness.cases.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="row g-3">
      <div class="col-xl-8">
        <div class="card rounded-4">
          <div class="card-body">
            <div class="mb-2">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary">{{ strtoupper($case->current_status) }}</span>
                <span class="badge {{ $case->severity==='high'?'bg-danger':($case->severity==='medium'?'bg-warning text-secondary':'bg-success') }}">
                  {{ strtoupper($case->severity) }}
                </span>
              </div>
              <div class="mt-2">
                <div class="fw-semibold">{{ $case->volunteer_name }}</div>
                <div class=" small">{{ $case->group_name }}</div>
              </div>
            </div>

            <hr>

            <h6 class="mb-2">Timeline</h6>
            <div class="list-group">
              @forelse($transitions as $t)
                <div class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="fw-semibold">{{ strtoupper($t->from_status) }} → {{ strtoupper($t->to_status) }}</div>
                      <div class=" small">{{ $t->actor_name }} · {{ $t->role }}</div>
                    </div>
                    <div class=" small">{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') }}</div>
                  </div>
                  @if($t->note)
                    <div class="mt-2">{{ $t->note }}</div>
                  @endif
                </div>
              @empty
                <div class="">No updates yet.</div>
              @endforelse
            </div>

            <hr>

            <form class="row g-2" method="post" action="{{ route('wellness.cases.transition',$case->id) }}">
              @csrf
              <div class="col-md-3">
                <label class="form-label">From</label>
                <input class="form-control" name="from_status" value="{{ $case->current_status }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label">To</label>
                <select class="form-select" name="to_status" required>
                  <option value="review">Review</option>
                  <option value="in_progress">In Progress</option>
                  <option value="waiting_consult">Waiting Consult</option>
                  <option value="close_proposed">Close Proposed</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Note</label>
                <input class="form-control" name="note" placeholder="Optional comment">
              </div>
              <div class="col-12 text-end">
                <button class="btn btn-primary">Add Update</button>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="col-xl-4">
        <div class="card rounded-4 mb-3">
          <div class="card-body">
            <h6 class="mb-2">Last Check-ins</h6>
            <ul class="list-group">
              @foreach($checkins as $ci)
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="small ">{{ \Carbon\Carbon::parse($ci->checked_at)->format('Y-m-d') }}</div>
                      <div class="fw-semibold">E{{ $ci->emotional }} • P{{ $ci->physical }} • S{{ $ci->spiritual }}</div>
                    </div>
                    <span class="badge {{ $ci->attended_service ? 'bg-success' : 'bg-secondary' }}">{{ $ci->attended_service ? 'ATTENDED' : 'ABSENT' }}</span>
                  </div>
                  @if($ci->prayer_request)
                    <div class="small mt-1">🙏 {{ $ci->prayer_request }}</div>
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
        </div>

        <div class="card rounded-4">
          <div class="card-body">
            <h6 class="mb-2">Close Case</h6>
            <form method="post" action="{{ route('wellness.cases.proposeClose',$case->id) }}">
              @csrf
              <textarea class="form-control mb-2" name="note" rows="2" placeholder="Closure note / summary"></textarea>
              <button class="btn btn-outline-primary w-100">Propose Close</button>
            </form>
            <hr>
            {{-- Overseer only (gate with policy/middleware in real app) --}}
            <form method="post" action="{{ route('wellness.cases.approveClose',$case->id) }}">
              @csrf
              <textarea class="form-control mb-2" name="note" rows="2" placeholder="Final overseer note"></textarea>
              <button class="btn btn-danger w-100">Approve Close</button>
            </form>
          </div>
        </div>

      </div>
    </div>

  </div>
</main>
@endsection