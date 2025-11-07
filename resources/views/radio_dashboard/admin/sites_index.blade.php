@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Sites Directory</div>
      <div class="ps-3">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active">Sites</li>
        </ol>
      </div>
      <div class="ms-auto">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#siteModal">
          <i class="material-icons-outlined">add_location_alt</i> New Site
        </button>
      </div>
    </div>

    @if(session('ok'))
      <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="card rounded-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Registered Sites</h5>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
			  <tr>
			  	<th>Nickname</th>
			    <th>Department</th>
			    <th>Arrondissement</th>
			    <th>Commune</th>
			    <th>Owner</th>
			    <th>Representative</th>
			    <th>Rep Phone</th>
			    <th>Rep Email</th>
			    <th>Contract Start</th>
			    <th>Contract End</th>
			    <th>Contract</th>
			  </tr>
			</thead>
			<tbody>
			  @forelse($sites as $s)
			      <tr>
			      	<td>{{ $s->nickname ?? '' }}</td>
			        <td>{{ $s->department_name }}</td>
			        <td>{{ $s->arrondissement_name }}</td>
			        <td>{{ $s->commune_name }}</td>
			        <td>{{ $s->owner }}</td>
			        <td>{{ $s->rep_name }}</td>
			        <td>{{ $s->rep_phone }}</td>
			        <td>{{ $s->rep_email }}</td>
			        <td>{{ $s->contract_start }}</td>
			        <td>{{ $s->contract_end }}</td>
			        <td>
			          @if($s->contract_link)
			            <a href="{{ $s->contract_link }}" target="_blank">Link</a>
			          @endif
			        </td>
			      </tr>
			    @empty
			      <tr><td colspan="10" class="text-center">No sites yet.</td></tr>
			    @endforelse
			</tbody>
          </table>
        </div>

      </div>
    </div>

    {{-- Modal: Create Site --}}
    <div class="modal fade" id="siteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title"><i class="material-icons-outlined me-2">domain_add</i> Register Site</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form method="post" action="{{ route('radio.admin.sites.store') }}">
            @csrf
            <div class="modal-body">
              <div class="row g-3">

                {{-- Geo hierarchy from DB --}}
                <div class="col-md-4">
                  <label class="form-label">Department</label>
                  <select name="department_id" id="department_id" class="form-select" required>
					  <option value="">Select…</option>
					  @foreach(($departments ?? []) as $d)
					    <option value="{{ $d->id }}">{{ $d->name }}</option>
					  @endforeach
					</select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Arrondissement</label>
                  <select name="arrondissement_id" id="arrondissement_id" class="form-select" required disabled>
                    <option value="">Select…</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Commune</label>
                  <select name="commune_id" id="commune_id" class="form-select" required disabled>
                    <option value="">Select…</option>
                  </select>
                </div>

                {{-- Owner --}}
				<div class="col-12">
				  <h6 class="mb-2">Owner</h6>
				  <hr>
				</div>
				<div class="col-md-12">
				  <label class="form-label">Owner Name</label>
				  <input type="text" name="owner" class="form-control" placeholder="Owner name" required>
				</div>

				{{-- Representative --}}
				<div class="col-12">
				  <h6 class="mb-2">Representative</h6>
				</div>
				<div class="col-md-4">
				  <label class="form-label">Representative Name</label>
				  <input type="text" name="rep_name" class="form-control" placeholder="Representative name">
				</div>
				<div class="col-md-4">
				  <label class="form-label">Representative Phone</label>
				  <input type="text" name="rep_phone" class="form-control" placeholder="+509 …">
				</div>
				<div class="col-md-4">
				  <label class="form-label">Representative Email</label>
				  <input type="email" name="rep_email" class="form-control" placeholder="rep@email.com">
				</div>
				<hr>
                <div class="col-md-6">
                  <label class="form-label">Site POC</label>
                  <input type="text" name="poc_name" class="form-control" placeholder="Site contact person">
                </div>

                <div class="col-md-6">
                  <label class="form-label">POC Phone</label>
                  <input type="text" name="poc_phone" class="form-control" placeholder="+509 …">
                </div>
                <hr>
                <div class="col-md-4">
                  <label class="form-label">Contract Link</label>
                  <input type="url" name="contract_link" class="form-control" placeholder="https://…">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Contract Start</label>
                  <input type="date" name="contract_start" class="form-control datepicker">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Contract End</label>
                  <input type="date" name="contract_end" class="form-control datepicker">
                </div>

                <div class="col-12">
                  <label class="form-label">Notes</label>
                  <textarea name="notes" class="form-control" rows="2" placeholder="Power, access, tower info…"></textarea>
                </div>

              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Site</button>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div>
</main>

{{-- Dependent selects --}}
<script>
(function(){
  const depSel = document.getElementById('department_id');
  const arrSel = document.getElementById('arrondissement_id');
  const comSel = document.getElementById('commune_id');

  const ARR_URL = "{{ route('radio.admin.stations.geo.arrondissements', ['department' => '__ID__']) }}";
  const COM_URL = "{{ route('radio.admin.stations.geo.communes', ['arrondissement' => '__ID__']) }}";

  function clearSelect(sel, placeholder){ sel.innerHTML = `<option value="">${placeholder}</option>`; }
  function enable(sel, on){ sel.disabled = !on; }

  async function fetchJson(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
    // try once more with /index.php/ if needed
    if (!res.ok || !(res.headers.get('content-type')||'').includes('application/json')) {
      const u = new URL(url);
      const retry = u.origin + '/index.php' + u.pathname + u.search;
      const res2 = await fetch(retry, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
      if (!res2.ok || !(res2.headers.get('content-type')||'').includes('application/json')) {
        const txt = await res2.text();
        throw new Error('Bad response: ' + res2.status + ' ' + txt.slice(0,200));
      }
      return res2.json();
    }
    return res.json();
  }

  async function loadArrondissements(depId){
    clearSelect(arrSel, 'Select…'); clearSelect(comSel, 'Select…');
    enable(arrSel,false); enable(comSel,false);
    if(!depId) return;

    try{
      const url = ARR_URL.replace('__ID__', encodeURIComponent(depId));
      const data = await fetchJson(url);

      // if backend returned {error: "..."}
      if (!Array.isArray(data)) {
        console.error('Arrondissements API error:', data);
        arrSel.innerHTML = `<option value="">(error loading)</option>`;
        return;
      }

      data.forEach(row=>{
        const o = document.createElement('option');
        o.value = row.id; o.textContent = row.name;
        arrSel.appendChild(o);
      });
      enable(arrSel,true);
    }catch(e){ 
      console.error('Arrondissements load failed', e); 
      arrSel.innerHTML = `<option value="">(server error)</option>`;
    }
  }

  async function loadCommunes(arrId){
    clearSelect(comSel, 'Select…'); enable(comSel,false);
    if(!arrId) return;

    try{
      const url = COM_URL.replace('__ID__', encodeURIComponent(arrId));
      const data = await fetchJson(url);

      if (!Array.isArray(data)) {
        console.error('Communes API error:', data);
        comSel.innerHTML = `<option value="">(error loading)</option>`;
        return;
      }

      data.forEach(row=>{
        const o = document.createElement('option');
        o.value = row.id; o.textContent = row.name;
        comSel.appendChild(o);
      });
      enable(comSel,true);
    }catch(e){ 
      console.error('Communes load failed', e);
      comSel.innerHTML = `<option value="">(server error)</option>`;
    }
  }

  depSel?.addEventListener('change', e => loadArrondissements(e.target.value));
  arrSel?.addEventListener('change', e => loadCommunes(e.target.value));
})();
</script>