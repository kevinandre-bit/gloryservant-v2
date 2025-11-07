@include('layouts/admin')
@section('title','Create Alert')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white rounded-2xl border">
  <h1 class="text-xl font-semibold mb-4">Create Alert</h1>

  @if ($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.reports.alerts.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm mb-1">Title</label>
      <input name="title" value="{{ old('title') }}" class="w-full h-10 rounded-xl border px-3" required>
    </div>

    <div>
      <label class="block text-sm mb-1">Message</label>
      <textarea name="message" class="w-full rounded-xl border px-3 py-2" rows="4">{{ old('message') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm mb-1">Severity</label>
        <select name="severity" class="w-full h-10 rounded-xl border px-3">
          <option value="info"     @selected(old('severity')==='info')>Info</option>
          <option value="warning"  @selected(old('severity')==='warning')>Warning</option>
          <option value="critical" @selected(old('severity')==='critical')>Critical</option>
        </select>
      </div>

      @if(!empty($teams) && count($teams) > 1)
        <div>
          <label class="block text-sm mb-1">Team (optional)</label>
          <select name="team_id" class="w-full h-10 rounded-xl border px-3">
            @foreach($teams as $t)
              <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
            @endforeach
          </select>
        </div>
      @endif
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.reports.dashboard') }}" class="h-10 px-4 rounded-xl border">Cancel</a>
      <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white">Create Alert</button>
    </div>
  </form>
</div>
@endsection
