 @php
  // define your role groups
  $globalRoles = ['ADMIN','MANAGER','WEB OPERATOR'];
  $campusOnly = ['CAMPUS POC'];
  $deptOnly    = ['HP TEAM','OVERSEER'];
@endphp
@php
  // Whenever “Communication” is chosen, we’ll expand it to this array
  $communicationGroup = [
    'ADMIN',
    'Graphic Design',
    'Video Editing',
    'SEO',
    'Volunteer Care',
    'Social Media',
    'Audio Editing',
    'Radio_TV',
  ];
@endphp
 <!--start header-->
  @include ('layouts/admin')
  <!--end top header-->


  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Devotion</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Report</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<div class="btn-group">
							<button type="button" class="btn btn-primary">Settings</button>
							<button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">{{ __("Return") }}</a>
								<a class="dropdown-item" href="javascript:;">{{ __("Raw data") }}</a>
							</div>
						</div> 
					</div>
				</div>
				<!--end breadcrumb-->
      
				<div class="">

    <div class="row g-3 align-items-end mb-4">
         {{-- Campus filter --}}
          @if(in_array($role, $globalRoles))
            <div class="col-md-2">
              <label for="filtercampus">Campus</label>
              <select id="filtercampus" class="form-select btn-primary">
                <option value="">All Campus</option>
                @foreach($campuses as $c)
                  <option value="{{ is_string($c) ? $c : $c->campus }}">
                    {{ is_string($c) ? $c : $c->campus }}
                  </option>
                @endforeach
              </select>
            </div> 
          @else
            <div class="col-md-3">
              <label for="filtercampus">Campus</label>
              <input
                id="filtercampus"
                type="text"
                readonly
                class="form-control-plaintext"
                value="{{ $usercampus }}"
              >
            </div>
          @endif

          {{-- Department (same behavior as Campus) --}}
          @if(in_array($role, $globalRoles ?? []))
            <div class="col-md-3">
              <label for="filterdepartment" class="form-label">Department</label>
              <select id="filterdepartment" name="department" class="form-select btn-primary">
                <option value="">All Departments</option>
                @foreach(($departments ?? []) as $d)
                  @php $val = is_string($d) ? $d : ($d->department ?? ''); @endphp
                  @if($val !== '')
                    <option value="{{ $val }}" @selected(request('department')===$val)>{{ $val }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          @else
            <div class="col-md-3">
              <label for="filterdepartment" class="form-label">Department</label>
              <input
                type="text"
                class="form-control-plaintext"
                value="{{ $userDepartment ?? '' }}"
                readonly
              >
              {{-- Hidden so JS can still read/filter with the same ID --}}
              <input type="hidden" id="filterdepartment" name="department" value="{{ $userDepartment ?? '' }}">
            </div>
          @endif

          {{-- ministry filter --}}
        @if(in_array($role, $globalRoles) || in_array($role, $campusOnly))
          <div class="col-md-3">
            <label for="filterministry">Ministry</label>
            <select id="filterministry" class="form-select btn-primary">
              <option value="">All ministries</option>

              {{-- 1) Hard-coded “Overall Communication” entry: --}}
              <option
                value="ADMIN,Graphic Design,Video Editing,SEO,Volunteer Care,Social Media,Audio Editing,Radio_TV"
                @selected(request('ministry') === 'ADMIN,Graphic Design,Video Editing,SEO,Volunteer Care,Social Media,Audio Editing,Radio_TV')
              >
                Overall Communication
              </option>

              {{-- 2) All the individual ministry names --}}
              @foreach($ministries as $d)
                @php
                  // If $d is already a string, use it; otherwise use $d->ministry
                  $deptName = is_string($d) ? $d : (isset($d->ministry) ? $d->ministry : '');
                @endphp
                <option
                  value="{{ $deptName }}"
                  @selected(request('ministry') === $deptName)
                >
                  {{ $deptName }}
                </option>
              @endforeach
            </select>
          </div>
        @else
          <div class="col-md-3">
            <label for="filterministry">ministry</label>
            <input
              id="filterministry"
              type="text"
              readonly
              class="form-control-plaintext"
              value="{{ $userministry }}"
            >
          </div>
        @endif


        <div class="col-md-2">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" id="startDate" class="form-control btn-primary">
        </div>

        <div class="col-md-2">
            <label for="endDate" class="form-label">End Date</label>
            <input type="date" id="endDate" class="form-control btn-primary">
        </div>
    </div>
				<hr>
        <!-- Devotion Growth Rate -->
        <div class="row row-cols-1 row-cols-xl-3">
            <div class="col">
              <div class="card rounded-4">
                <div class="card-body">

                  <div class="d-flex align-items-center gap-3 mb-2">
                    <div>
                      <!-- only the raw total -->
                      <h2 id="totalDevotionsSummary" class="mb-0">0</h2>
                    </div>
                    <div>
                      <!-- just the % growth -->
                      <span id="devotionGrowthBadge"
                            class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                        0%
                      </span>
                    </div>
                  </div>

                  <p class="mb-0">Total Devotions</p>

                  <div class="mt-4">
                    <p class="mb-2 d-flex align-items-center justify-content-between">
                      <span id="leftToGoalText">0 left to Goal</span>
                      <span id="progressPercent">0%</span>
                    </p>
                    <div class="progress w-100" style="height: 6px;">
                      <div id="devotionProgressBar"
                           class="progress-bar bg-grd-purple"
                           style="width: 0%;"></div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col">
            <div class="card rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div class="">
                    <h2 id="noDevotionCount" class="mb-0">0</h2>
                  </div>
                  <div class="">
                    <p
                      class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-danger text-danger bg-opacity-10">
                      <span id="noDevotionBadge" class="dash-lable bg-danger text-danger bg-opacity-10"> 0%</span>
                    </p>
                  </div>
                </div>
                <p class="mb-0">
                  <span id="noDevotionTitle">
                    People With No Devotions Yet
                  </span>
                </p>
                <div class="mt-4">
                  <p class="mb-2 d-flex align-items-center justify-content-between"><span id="noDevotionLeftToGoalText">0 left to Goal</span>
          <span id="noDevotionProgressPercent">0%</span></p>
                  <div class="progress w-100" style="height: 6px;">
                    <div id="noDevotionProgressBar" class="progress-bar bg-grd-danger" style="width: 0%"></div>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <!-- Post at least 6 -->
            <div class="col">
              <div class="card rounded-4">
                <div class="card-body">

                  <!-- Header: raw count + % badge -->
                  <div class="d-flex align-items-center gap-3 mb-2">
                    <div>
                      <h2 id="sixPlusCount" class="mb-0">0</h2>
                    </div>
                    <div>
                      <span id="sixPlusBadge"
                            class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                        0%
                      </span>
                    </div>
                  </div>
                  <p class="mb-0">
                    <span id="sixPlusTitle">
                      People Reached the Weekly Goal (6+ Devotions)
                    </span>
                  </p>
                  <!-- Footer: left-to-goal + progress bar -->
                  <div class="mt-4">
                    <p class="mb-2 d-flex align-items-center justify-content-between">
                      <span id="sixPlusLeftToGoalText">0 left to Goal</span>
                      <span id="sixPlusProgressPercent">0%</span>
                    </p>
                    <div class="progress w-100" style="height: 6px;">
                      <div id="sixPlusProgressBar"
                           class="progress-bar bg-grd-success"
                           style="width: 0%;"></div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
        </div>

        <!-- Devotion table -->
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
                  <tr>
                    <th class="big-n">Name</th>
                    <th class="big-n">ministry</th>
                    <th class="big-n">Campus</th>
                    <th class="big-n"># Devotions</th>
                    <th class="big-n">Percentage</th>
                  </tr>
                </thead>
                <tbody id="filteredList">
                  <tr>
                    <td>Loading…</td>
                  </tr>
                </tbody>
								
							</table>
						</div>
					</div>
				</div>


    </div>
  </main>
  <!--end main wrapper-->


    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->



     <!--start footer-->
     <footer class="page-footer">
      <p class="mb-0">Copyright © 2025. All right reserved.</p>
    </footer>
    <!--top footer-->

  <!--start cart-->
  
  <!--end cart-->


  <!--start switcher-->
  <button class="btn btn-grd btn-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
  </button>
  
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Theme Customizer</h5>
        <p class="mb-0">Customize your theme</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Blue</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>
        </div><!--end row--> 

      </div>
    </div>
  </div>
  <!--start switcher-->
  <script src="{{ asset('assets3/js/admin-report-devotions.js') }}" defer></script>
  <!-- analytics handled by external: admin-report-devotions.js (legacy inline block commented) -->
  <!--
  function $(id) { return document.getElementById(id); }

  function pct(num, den) {
    if (!den || den <= 0) return 0;
    const v = (Number(num) / Number(den)) * 100;
    if (!isFinite(v)) return 0;
    return Math.min(100, Math.max(0, v));
  }

  async function fetchDevotionData() {
    const campus     = $('filtercampus')     ? $('filtercampus').value     : '';
    const ministry   = $('filterministry')   ? $('filterministry').value   : '';
    const department = $('filterdepartment') ? $('filterdepartment').value : '';
    const person     = $('filterperson')     ? $('filterperson').value     : '';
    const start      = $('startDate')        ? $('startDate').value        : '';
    const end        = $('endDate')          ? $('endDate').value          : '';

    if (!start || !end) {
      alert('Please select both Start Date and End Date.');
      return;
    }

    let json;
    try {
      const qs =
        `?campus=${encodeURIComponent(campus || '')}`
        + `&ministry=${encodeURIComponent(ministry || '')}`
        + `&department=${encodeURIComponent(department || '')}`
        + `&person=${encodeURIComponent(person || '')}`
        + `&start_date=${encodeURIComponent(start)}`
        + `&end_date=${encodeURIComponent(end)}`;

      const res = await fetch(`/admin/reports/devotions/data${qs}`);
      if (!res.ok) throw new Error(`Server returned ${res.status}`);
      json = await res.json();
    } catch (err) {
      console.error('Fetch error:', err);
      if ($('filteredList')) {
        $('filteredList').innerHTML =
          `<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>`;
      }
      return;
    }

    const data           = Array.isArray(json.data) ? json.data : [];
    const totalEmployees = Number(json.total_employees) || 0;
    const days           = Math.max(1, Number(json.days) || 1);

    // 1) Sort by percentage desc
    data.sort((a, b) => Number(b.percentage) - Number(a.percentage));

    // 2) Populate table (Department prefers row.department, fallback to row.ministry)
    const tbody = $('filteredList');
    if (tbody) {
      tbody.innerHTML = '';
      if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center">No data available</td></tr>`;
      } else {
        data.forEach(row => {
          const fullName = `${row.firstname || ''} ${row.lastname || ''}`.trim() || '—';
          const dept     = row.department || row.ministry || '—';
          const camp     = row.campus || '—';
          const total    = Number(row.total) || 0;
          const perc     = Number(row.percentage) || 0;

          const tr = document.createElement('tr');
          const tdName = document.createElement('td');
          tdName.textContent = fullName;
          const tdDept = document.createElement('td');
          tdDept.textContent = dept;
          const tdCamp = document.createElement('td');
          tdCamp.textContent = camp;
          const tdTotal = document.createElement('td');
          tdTotal.className = 'text-end';
          tdTotal.textContent = total.toLocaleString();
          const tdPerc = document.createElement('td');
          tdPerc.className = 'text-end';
          tdPerc.textContent = `${perc.toFixed(2)}%`;
          tr.appendChild(tdName);
          tr.appendChild(tdDept);
          tr.appendChild(tdCamp);
          tr.appendChild(tdTotal);
          tr.appendChild(tdPerc);
          tbody.appendChild(tr);
        });
      }
    }

    // 3) Overall (posts-based)
    const totalPosts    = data.reduce((sum, r) => sum + (Number(r.total) || 0), 0);
    const expectedPosts = totalEmployees * days;
    const achievedPct   = pct(totalPosts, Math.max(1, expectedPosts));
    const leftToGoal    = Math.max(0, expectedPosts - totalPosts);

    if ($('totalDevotionsSummary')) $('totalDevotionsSummary').textContent = totalPosts.toLocaleString();
    if ($('progressPercent'))       $('progressPercent').textContent       = `${achievedPct.toFixed(2)}%`;
    if ($('leftToGoalText'))        $('leftToGoalText').textContent        = `${leftToGoal.toLocaleString()} left to Goal`;
    if ($('devotionProgressBar'))   $('devotionProgressBar').style.width   = `${achievedPct}%`;
    if ($('devotionGrowthBadge'))   $('devotionGrowthBadge').textContent   = `${achievedPct.toFixed(2)}%`;

    // 4) No Devotion (people-based)
    const zeroCount   = data.filter(r => (Number(r.total) || 0) === 0).length;
    const zeroPct     = pct(zeroCount, Math.max(1, totalEmployees));
    const postedCount = Math.max(0, totalEmployees - zeroCount);
    const postedPct   = pct(postedCount, Math.max(1, totalEmployees));

    if ($('noDevotionCount'))             $('noDevotionCount').textContent             = zeroCount.toLocaleString();
    if ($('noDevotionBadge'))             $('noDevotionBadge').textContent             = `${zeroPct.toFixed(2)}%`;
    if ($('noDevotionLeftToGoalText'))    $('noDevotionLeftToGoalText').textContent    = `${postedCount.toLocaleString()} left to Goal`;
    if ($('noDevotionProgressPercent'))   $('noDevotionProgressPercent').textContent   = `${postedPct.toFixed(2)}%`;
    if ($('noDevotionProgressBar'))       $('noDevotionProgressBar').style.width       = `${postedPct}%`;

    if ($('noDevotionTitle')) {
      $('noDevotionTitle').textContent =
        zeroCount > 0 ? 'People With No Devotions Yet' : '🎉 Everyone Posted At Least Once';
    }

    // 5) Weekly goal (>=6 in range) — people-based
    const sixPlusCount = data.filter(r => (Number(r.total) || 0) >= 6).length;
    const sixPlusPct   = pct(sixPlusCount, Math.max(1, totalEmployees));
    const missingSix   = Math.max(0, totalEmployees - sixPlusCount);

    if ($('sixPlusCount'))             $('sixPlusCount').textContent             = sixPlusCount.toLocaleString();
    if ($('sixPlusBadge'))             $('sixPlusBadge').textContent             = `${sixPlusPct.toFixed(2)}%`;
    if ($('sixPlusLeftToGoalText'))    $('sixPlusLeftToGoalText').textContent    = `${missingSix.toLocaleString()} left to Goal`;
    if ($('sixPlusProgressPercent'))   $('sixPlusProgressPercent').textContent   = `${sixPlusPct.toFixed(2)}%`;
    if ($('sixPlusProgressBar'))       $('sixPlusProgressBar').style.width       = `${sixPlusPct}%`;

    if ($('sixPlusTitle')) {
      $('sixPlusTitle').textContent =
        sixPlusCount > 0 ? 'People Reached the Weekly Goal (6+ Devotions)'
                         : 'No One Has Reached the Weekly Goal Yet';
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const startEl = $('startDate');
    const endEl   = $('endDate');

    // Compute last Saturday -> following Friday
    const today = new Date();
    const dow = today.getDay(); // Sun=0 … Sat=6
    const offsetToSaturday = (dow === 6) ? 0 : (dow + 1);
    const saturday = new Date(today);
    saturday.setDate(today.getDate() - offsetToSaturday);
    const friday = new Date(saturday);
    friday.setDate(saturday.getDate() + 6);

    const pad = n => String(n).padStart(2, '0');
    const iso = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    if (startEl && !startEl.value) startEl.value = iso(saturday);
    if (endEl && !endEl.value)     endEl.value   = iso(friday);

    // Re-fetch when filters change (added department & person)
    ['filtercampus','filterministry','filterdepartment','filterperson','startDate','endDate'].forEach(id => {
      const el = $(id);
      if (el) el.addEventListener('change', fetchDevotionData);
    });

    // Initial load
    fetchDevotionData();
  });
</script>
-->

</body>

</html>
