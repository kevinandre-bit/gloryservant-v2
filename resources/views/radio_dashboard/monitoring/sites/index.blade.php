@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Monitoring</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="/dashboard"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">TX Sites</li>
        </ol>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="mb-0">All Transmission Sites</h5>
          <a class="btn btn-outline-primary" href="{{ route('monitoring.hub') }}">Back to Hub</a>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
			  <thead class="table-light">
			    <tr>
			      <th>Department</th>
			      <th>Capital</th>
			      <th>Frequency</th>
			      <th>Frequency Status</th>
			      <th>Status</th>
			      <th>RF</th>
			      <th>SNR</th>
			      <th>Uptime</th>
			      <th>Power</th>
			      <th>Last Maintenance</th>
			      <th class="text-end">Actions</th>
			    </tr>
			  </thead>
			  <tbody>
			    @foreach(($sites ?? []) as $s)
			      <tr>
			        <td>{{ $s['department'] }}</td>
			        <td>{{ $s['capital'] }}</td>
			        <td>{{ $s['frequency'] }}</td>
			        <td>
			          <span class="badge {{ $s['freq_status']==='Acquired' ? 'bg-success' : 'bg-warning text-dark' }}">
			            {{ $s['freq_status'] }}
			          </span>
			        </td>
			        <td>
			          <span class="badge {{ $s['online'] ? 'bg-success' : 'bg-danger' }}">
			            {{ $s['online'] ? 'ONLINE' : 'OFFLINE' }}
			          </span>
			        </td>
			        <td>{{ $s['rf'] }}%</td>
			        <td>{{ $s['snr'] }} dB</td>
			        <td>{{ $s['uptime'] }}%</td>
			        <td>
			          @php
			            $p = $s['power'];
			            $cls = $p==='Stable' ? 'bg-success' : ($p==='Flaky' ? 'bg-warning text-dark' : 'bg-danger');
			          @endphp
			          <span class="badge {{ $cls }}">{{ $s['power'] }}</span>
			        </td>
			        <td>{{ $s['last_maint'] }}</td>
			        <td class="text-end">
			          <a class="btn btn-sm btn-light" href="{{ route('monitoring.sites.show', $s['id']) }}">View Site</a>
			        </td>
			      </tr>
			    @endforeach
			  </tbody>
			</table>
        </div>

      </div>
    </div>

  </div>
</main>