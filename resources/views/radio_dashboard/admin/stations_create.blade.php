@include('layouts/radio_layout')

<main class="main-wrapper">
  <div class="main-content">

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Stations Directory</div>
      <div class="ms-auto">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stationModal">
          <i class="material-icons-outlined">add_business</i> New Station
        </button>
      </div>
    </div>

    <div class="card rounded-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Registered Stations</h5>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Arrondissement</th>
                <th>Commune</th>
                <th>Frequency</th>
                <th>Status</th>
                <th>On-Air</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($stations as $s)
                <tr>
                  <td class="fw-semibold">{{ $s->name }}</td>
                  <td>{{ $s->department_name }}</td>
                  <td>{{ $s->arrondissement_name }}</td>
                  <td>{{ $s->commune_name }}</td>
                  <td>{{ $s->frequency }}</td>
                  <td>
                    <span class="badge
                      @if($s->frequency_status==='Acquired') bg-success
                      @else bg-warning text-dark @endif">
                      {{ $s->frequency_status }}
                    </span>
                  </td>
                  <td>
                    <form method="post" action="{{ route('radio.admin.stations.toggle', $s->id) }}">
                      @csrf
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" onChange="this.form.submit()" {{ $s->on_air ? 'checked' : '' }}>
                      </div>
                    </form>
                  </td>
                  <td class="text-end text-nowrap">
                    {{-- Add edit/delete later if you want --}}
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center">No stations yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

    {{-- Modal: Create Station --}}
    <div class="modal fade" id="stationModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title"><i class="material-icons-outlined me-2">domain_add</i> Register Station</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form method="post" action="{{ route('radio.admin.stations.store') }}">
            @csrf
            <div class="modal-body">
              <div class="row g-3">

                <div class="col-md-4">
                  <label class="form-label">Station Name</label>
                  <input name="name" type="text" class="form-control" placeholder="Radio XYZ – Cap-Haïtien" required>
                </div>


                <div class="col-md-3">
                  <label class="form-label">Frequency (MHz)</label>
                  <input name="frequency" type="text" class="form-control" placeholder="103.3">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Frequency Status</label>
                  <select name="frequency_status" class="form-select" required>
                    <option value="Acquired">Acquired</option>
                    <option value="Not acquired">Not acquired</option>
                  </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="onAir" name="on_air" value="1">
                    <label class="form-check-label" for="onAir">On-Air</label>
                  </div>
                </div> 

                {{-- Geo hierarchy --}}
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

                

                <div class="col-12">
                  <label class="form-label">Notes</label>
                  <textarea name="notes" class="form-control" rows="2" placeholder="Coverage, tower, power..."></textarea>
                </div>

              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Station</button>
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