@include('layouts/admin')
@section('title','Member Detail')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-2xl border space-y-6">
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-xl font-semibold">{{ $user->name }}</h1>
      <div class="text-sm text-gray-600">{{ $user->email }}</div>
      @if(!empty($user->team_name))
        <div class="text-sm mt-1"><span class="text-gray-500">Team:</span> {{ $user->team_name }}</div>
      @endif
    </div>

    <div class="text-right">
      @if(session('status'))
        <div class="mb-2 text-sm p-2 rounded-lg bg-green-50 text-green-700">{{ session('status') }}</div>
      @endif
      @if($tempPw)
        <div class="mb-2 text-xs p-2 rounded-lg bg-amber-50 text-amber-700">
          Temporary password: <strong>{{ $tempPw }}</strong>
        </div>
      @endif
      <a href="{{ route('admin.reports.dashboard') }}" class="h-9 inline-flex items-center px-3 rounded-xl border">Back</a>
    </div>
  </div>

  <div>
    <h2 class="text-sm font-semibold mb-2">Recent Attendance</h2>
    @if(!empty($attendance) && count($attendance))
      <div class="overflow-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="text-left p-3">Check-in</th>
              <th class="text-left p-3">Check-out</th>
              <th class="text-left p-3">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($attendance as $a)
              <tr class="border-t">
                <td class="p-3">{{ is_string($a->check_in_at) ? $a->check_in_at : \Illuminate\Support\Optional::from($a->check_in_at)->format('Y-m-d H:i:s') }}</td>
                <td class="p-3">{{ is_string($a->check_out_at) ? ($a->check_out_at ?? '—') : ($a->check_out_at ? \Illuminate\Support\Optional::from($a->check_out_at)->format('Y-m-d H:i:s') : '—') }}</td>
                <td class="p-3">{{ $a->status ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-sm text-gray-500">No attendance records.</div>
    @endif
  </div>
</div>
@endsection
