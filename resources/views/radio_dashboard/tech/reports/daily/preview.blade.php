@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">
    <div class="card rounded-4">
      <div class="card-body p-4">
        <h5 class="mb-3">Preview — Submitted Values (UI-only)</h5>
        <pre class="mb-0">{{ json_encode($data ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        <hr>
        <a href="{{ url()->previous() }}" class="btn btn-light mt-3">Back</a>
      </div>
    </div>
  </div>
</main>
