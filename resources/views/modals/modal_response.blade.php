@extends('layouts.app')


@section('content')
  {{-- hidden page content, if any --}}
@endsection

@push('modals')
<!-- Success / Error Modal -->
<div class="modal fade" id="clockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      @if($error)
        <h5 class="text-danger mb-3">Oops!</h5>
        <p>{{ $error }}</p>
      @else
        <h5 class="text-success mb-3">Clock-In Successful!</h5>
        <p>You clocked in on <strong>{{ $date }}</strong> at <strong>{{ $time }}</strong>.</p>
        {{-- Live timer --}}
        <div id="timer" class="fs-2 mt-2"></div>
      @endif
      <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">
        {{ $error ? 'Try Again' : 'Close' }}
      </button>
    </div>
  </div>
</div>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Show the modal immediately
    let modalEl = document.getElementById('clockModal');
    let bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();

    @if(!isset($error))
    // start a live timer if no error
    let start = new Date();
    let el    = document.getElementById('timer');
    function tick() {
      let diff = Math.floor((new Date() - start) / 1000);
      let h = String(Math.floor(diff/3600)).padStart(2,'0'),
          m = String(Math.floor(diff%3600/60)).padStart(2,'0'),
          s = String(diff%60).padStart(2,'0');
      el.textContent = `${h}:${m}:${s}`;
    }
    setInterval(tick, 1000);
    tick();
    @endif
  });
</script>
@endpush