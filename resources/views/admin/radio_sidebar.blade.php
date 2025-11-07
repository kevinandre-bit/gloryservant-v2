@php
  use App\Classes\permission;
  // tiny helpers for active state
  function activeMenu($patterns) {
    foreach ((array)$patterns as $p) {
      if (request()->routeIs($p)) return 'mm-active';
    }
    return '';
  }
  function activeLink($patterns) {
    foreach ((array)$patterns as $p) {
      if (request()->routeIs($p)) return 'active';
    }
    return '';
  }
@endphp

<!--start sidebar--> 
<aside class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div class="logo-icon">
      <img src="/assets2/images/logo-icon.png" class="logo-img" alt="">
    </div>
    <div class="logo-name flex-grow-1">
      <h5 class="mb-0">G. Servant</h5>
    </div>
    <div class="sidebar-close">
      <span class="material-icons-outlined">close</span>
    </div>
  </div>

  <div class="sidebar-nav">
    <ul class="metismenu" id="sidenav">

      {{-- ========= RADIO OPS ========= --}}
      <li class="{{ activeMenu(['dashboard.index','program.*','playout.*','tech.*','maintenance.*','monitoring.*','reports.*']) }}">
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class="material-icons-outlined">radio</i></div>
          <div class="menu-title">Radio Ops</div>
        </a>
        <ul>
          <li><a class="{{ activeLink('dashboard.index') }}" href="{{ route('dashboard.index') }}">
            <i class="material-icons-outlined">dashboard</i>Dashboard</a></li>
            <li><a class="{{ activeLink('tech.checkins.index') }}" href="{{ route('tech.checkins.index') }}"><i class="material-icons-outlined">cell_tower</i> On Air Report</a></li>

          @if(permission::permitted('radio-programming') === 'success')
            <li class="{{ activeMenu(['program.schedules.*','program.playlists.*']) }}">
              <a class="has-arrow" href="javascript:;"><i class="material-icons-outlined">event_note</i>Programming</a>
              <ul>

                <li><a class="{{ activeLink('program.playlists.index') }}" href="{{ route('program.playlists.index') }}">
                  <i class="material-icons-outlined">queue_music</i>Playlists</a></li>
                <li>
                  <a class="{{ activeLink('program.library.upload') }}" href="{{ route('program.library.upload') }}">
                    <i class="material-icons-outlined">file_upload</i>Upload Library
                  </a>
                </li>
                <li>
                  <a href="{{ route('program.library.index') }}"><i class="material-icons-outlined">list_alt</i>Library</a></li>
                </li>
              </ul>
            </li>
          @endif

          @if(permission::permitted('radio-playout') === 'success')
            <!--<li class="{{ activeMenu(['playout.*']) }}">
              <a class="has-arrow" href="javascript:;"><i class="material-icons-outlined">playlist_play</i>Playout</a>
              <ul>
                <li><a class="{{ activeLink('playout.logs.index') }}" href="{{ route('playout.logs.index') }}">
                  <i class="material-icons-outlined">article</i>Logs</a></li>
                <li><a class="{{ activeLink('playout.deviations.index') }}" href="{{ route('playout.deviations.index') }}">
                  <i class="material-icons-outlined">report_gmailerrorred</i>Deviations</a></li>
              </ul>
            </li>-->
          @endif

          @if(permission::permitted('radio-monitoring') === 'success')
            <li class="{{ activeMenu(['monitoring.*']) }}">
              <a class="has-arrow" href="javascript:;"><i class="material-icons-outlined">hub</i>Monitoring</a>
              <ul>
                <li><a class="{{ activeLink('monitoring.source') }}" href="{{ route('monitoring.source') }}">
                  <i class="material-icons-outlined">cast</i>Source (Miami)</a></li>
                <li><a class="{{ activeLink('monitoring.hub') }}" href="{{ route('monitoring.hub') }}">
                  <i class="material-icons-outlined">lan</i>Hub (PAP)</a></li>
                <li><a class="{{ activeLink('monitoring.sites.index') }}" href="{{ route('monitoring.sites.index') }}">
                  <i class="material-icons-outlined">rss_feed</i>TX Sites</a></li>
              </ul>
            </li>
          @endif


          @if(permission::permitted('radio-maintenance') === 'success')
            <li class="{{ activeMenu(['maintenance.*']) }}">
              <a class="has-arrow" href="javascript:;"><i class="material-icons-outlined">build_circle</i>Maintenance</a>
              <ul>
                <li><a class="{{ activeLink('maintenance.tasks.index') }}" href="{{ route('maintenance.tasks.index') }}">
                  <i class="material-icons-outlined">checklist</i>Tasks</a></li>
                <li><a class="{{ activeLink('maintenance.calendar.index') }}" href="{{ route('maintenance.calendar.index') }}">
                  <i class="material-icons-outlined">event_available</i>Calendar</a></li>
              </ul>
            </li>
          @endif

          
          @if(permission::permitted('radio-tech') === 'success')
            <li class="{{ activeMenu(['tech.*']) }}">
              <a class="has-arrow" href="javascript:;"><i class="material-icons-outlined">engineering</i>Technicians</a>
              <ul>
                <li><a class="{{ activeLink('tech.schedule.index') }}" href="{{ route('tech.schedule.index') }}">
                  <i class="material-icons-outlined">calendar_month</i>Schedule</a></li>
                <li><a class="{{ activeLink('tech.assignments.index') }}" href="{{ route('tech.assignments.index') }}">
                  <i class="material-icons-outlined">assignment_ind</i>Assignments</a></li>
                <li><a class="{{ activeLink('tech.availability.index') }}" href="{{ route('tech.availability.index') }}">
                  <i class="material-icons-outlined">schedule</i>Availability</a></li>
              </ul>
            </li>
          @endif

          @if(permission::permitted('radio-reports') === 'success')
            <li class="{{ activeMenu(['reports.*']) }}">
              <a class="has-arrow" href="javascript:;"><i class="material-icons-outlined">summarize</i>Reports</a>
              <ul>
                <li><a class="{{ activeLink('reports.daily') }}" href="{{ route('reports.daily') }}">
                  <i class="material-icons-outlined">today</i>Daily (Admin)</a></li>
                <li><a class="{{ activeLink('reports.weekly') }}" href="{{ route('reports.weekly') }}">
                  <i class="material-icons-outlined">date_range</i>Weekly Summary</a></li>
                {{-- role-focused shortcuts (UI only) --}}
                <li><a class="{{ activeLink('reports.daily.tech') }}" href="{{ route('reports.daily.tech') }}">
                  <i class="material-icons-outlined">handyman</i>Daily — Technician</a></li>
                <li><a class="{{ activeLink('reports.daily.op') }}" href="{{ route('reports.daily.op') }}">
                  <i class="material-icons-outlined">settings_voice</i>Daily — Operator</a></li>
                <li><a class="{{ activeLink('reports.daily.admin') }}" href="{{ route('reports.daily.admin') }}">
                  <i class="material-icons-outlined">admin_panel_settings</i>Daily — Admin</a></li>
                  <li><a class="{{ activeLink('reports.studio') }}" href="{{ route('reports.studio') }}"><i class="material-icons-outlined">analytics</i>Report Studio</a></li>

              </ul>
            </li>
          @endif
        </ul>
      </li>
      {{-- ========= /RADIO OPS ========= --}}
      
      @if(permission::permitted('finance') === 'success')
        <li class="{{ activeMenu(['finance.*']) }}">
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon"><i class="material-icons-outlined">savings</i></div>
            <div class="menu-title">Finance</div>
          </a>
          <ul>
            @if(permission::permitted('finance-expenses') === 'success')
              <li><a class="{{ activeLink('finance.expenses.index') }}" href="{{ route('finance.expenses.index') }}">
              <i class="material-icons-outlined">account_balance_wallet</i>Expenses</a></li>
            @endif
              @if(permission::permitted('finance-recurring') === 'success')
                <li><a class="{{ activeLink('finance.recurring.index') }}" href="{{ route('finance.recurring.index') }}">
                <i class="material-icons-outlined">replay_circle_filled</i>Recurring</a></li>
              @endif
              @if(permission::permitted('finance-vendors') === 'success')
                <li><a class="{{ activeLink('finance.vendors.index') }}" href="{{ route('finance.vendors.index') }}">
                <i class="material-icons-outlined">store</i>Vendors</a></li>
              @endif
          </ul>
        </li>
      @endif

      {{-- ========= DIRECTORY (Register) ========= --}}
      @if(permission::permitted('directory') === 'success')
      <li class="{{ activeMenu(['radio.admin.*']) }}">
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class="material-icons-outlined">badge</i></div>
          <div class="menu-title">Directory</div>
        </a>
        <ul>
          @if(permission::permitted('directory') === 'success')
            <li>
              <a href="{{ route('radio.admin.sites.index') }}">
                <div class="parent-icon"><i class="material-icons-outlined">location_city</i></div>
                <div class="menu-title">Sites Directory</div>
              </a>
            </li>
              @if(permission::permitted('directory-register-station') === 'success')
                  <li><a class="{{ activeLink('radio.admin.stations.create') }}" href="{{ route('radio.admin.stations.index') }}">
                    <i class="material-icons-outlined">radio</i>Register Station</a></li>
              @endif
              @if(permission::permitted('directory-register-tech') === 'success')
                <li><a class="{{ activeLink('radio.admin.techs.create') }}" href="{{ route('radio.admin.techs.index') }}">
                <i class="material-icons-outlined">engineering</i>Technician list</a></li>
              @endif
              @if(permission::permitted('directory-register-poc') === 'success')
                <li><a class="{{ activeLink('radio.admin.pocs.create') }}" href="{{ route('radio.admin.pocs.create') }}">
                <i class="material-icons-outlined">contacts</i>Register POC</a></li>
              @endif
              @if(permission::permitted('directory-inventory') === 'success')
                <li>
                  <a href="{{ route('inventory.index') }}">
                    <div class="parent-icon"><i class="material-icons-outlined">inventory_2</i></div>
                    <div class="menu-title">Inventory</div>
                  </a>
                </li>
              @endif
          @endif
        </ul>
      </li>
      @endif
      {{-- ========= /DIRECTORY ========= --}}

      

    </ul>
  </div>
</aside>
<!--end sidebar-->
