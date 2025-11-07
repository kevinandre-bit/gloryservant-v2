@php use App\Classes\permission; @endphp

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
    <!--navigation-->
    <ul class="metismenu" id="sidenav">

      {{-- 🏠 Dashboard --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="material-icons-outlined">home</i></div>
          <div class="menu-title">Dashboard</div>
        </a>
        <ul>
          <li><a href="{{ url('dashboard') }}"><i class="material-icons-outlined">chevron_right</i>Analysis</a></li>
        </ul>
      </li>

      <li class="nav-item">
  <a href="{{ route('admin.workspaces.index') }}" class="nav-link d-flex align-items-center gap-2">
    <i class="material-icons-outlined">dashboard_customize</i>
    <span>Workspaces</span>
  </a>
</li>

      {{-- 🙋 Volunteer Section --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="material-icons-outlined">groups</i></div>
          <div class="menu-title">Volunteer Section</div>
        </a>
        <ul>
          @if(permission::permitted('employee-list') === 'success')
            <li><a href="{{ url('employees') }}"><i class="material-icons-outlined">chevron_right</i>Volunteer List</a></li>
          @endif

          @if(permission::permitted('employee-attendance') === 'success')
            <li><a href="{{ url('attendance') }}"><i class="material-icons-outlined">chevron_right</i>Raw Data Attendance</a></li>
            <li><a href="{{ route('admin.time_tracking.dashboard') }}"><i class="material-icons-outlined">chevron_right</i>Attendance Dashboard</a></li>
            <li><a href="{{ route('admin.time_tracking.individual') }}"><i class="material-icons-outlined">chevron_right</i>Individual Time Tracking</a></li>
          @endif

          @if(permission::permitted('schedules') === 'success')
            <li><a href="{{ url('schedules') }}"><i class="material-icons-outlined">chevron_right</i>Schedule</a></li>
          @endif

          @if(permission::permitted('devotions') === 'success')
            <li><a href="{{ url('devotions-reports') }}"><i class="material-icons-outlined">chevron_right</i>Devotion</a></li>
          @endif

          @if(permission::permitted('meeting-attendance') === 'success')
            <li><a href="{{ url('meeting-attendance-view') }}"><i class="material-icons-outlined">chevron_right</i>Meeting Attendance</a></li>
          @endif

          @if(permission::permitted('meeting-links') === 'success')
            <li><a href="{{ route('meetings.index') }}"><i class="material-icons-outlined">chevron_right</i>Meeting Links (Old)</a></li>
            <li><a href="{{ route('meetings.improved') }}"><i class="material-icons-outlined">chevron_right</i>Meeting Links (New)</a></li>
          @endif

          @if(permission::permitted('meeting-links') === 'success')
            <li><a href="{{ route('meeting-sessions.calendar') }}"><i class="material-icons-outlined">chevron_right</i>Sessions Calendar (Old)</a></li>
            <li><a href="{{ route('meeting-sessions.improved') }}"><i class="material-icons-outlined">chevron_right</i>Sessions Calendar (New)</a></li>
          @endif

          @if(permission::permitted('employee-birthdays') === 'success')
            <li><a href="{{ url('volunteer-birthday') }}"><i class="material-icons-outlined">chevron_right</i>Volunteer Birthday</a></li>
          @endif

          @if(permission::permitted('leaves') === 'success')
            <li><a href="{{ url('leaves') }}"><i class="material-icons-outlined">chevron_right</i>Leaves</a></li>
          @endif
        </ul>
      </li>

      {{-- 📊 Reports --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="bx bx-pie-chart-alt-2"></i></div>
          <div class="menu-title">Reports</div>
        </a>
        <ul>
          @if(permission::permitted('report-setup-view') === 'success')
            <li><a href="{{ route('admin.reports.setup') }}"><i class="material-icons-outlined">chevron_right</i>Report Setup</a></li>
          @endif

          @if(permission::permitted('report-entry-view') === 'success')
            <li><a href="{{ route('admin.reports.entry') }}"><i class="material-icons-outlined">chevron_right</i>Report Entry</a></li>
          @endif

          @if(permission::permitted('report-view-weekly') === 'success')
            <li><a href="{{ route('admin.reports.dashboard') }}"><i class="material-icons-outlined">chevron_right</i>Report Dashboard</a></li>
          @endif

          @if(isset($firstPerson))
            <li><a href="{{ route('admin.reports.people.show', ['id' => $firstPerson->id]) }}"><i class="material-icons-outlined">chevron_right</i>Member Report</a></li>
          @else
            <li><a href="javascript:void(0)" class="text-muted"><i class="material-icons-outlined">chevron_right</i>Member Report (No members)</a></li>
          @endif
        </ul>
      </li>

      {{-- 🎨 Creative Workload --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="material-icons-outlined">palette</i></div>
          <div class="menu-title">Creative Workload</div>
        </a>
        <ul>
          <li><a href="{{ route('admin.creative.index') }}"><i class="material-icons-outlined">chevron_right</i>Dashboard</a></li>
          <li><a href="{{ route('admin.creative.requests.index') }}"><i class="material-icons-outlined">chevron_right</i>Requests</a></li>
          <li><a href="{{ route('admin.creative.reports.index') }}"><i class="material-icons-outlined">chevron_right</i>Reports</a></li>
          <li><a href="{{ route('admin.creative.insights.index') }}"><i class="material-icons-outlined">chevron_right</i>Advanced Insights</a></li>
          <li><a href="{{ route('admin.creative.insights.realtime') }}"><i class="material-icons-outlined">chevron_right</i>Real-time Monitor</a></li>
          <li><a href="{{ route('admin.creative.test') }}"><i class="material-icons-outlined">chevron_right</i>System Test</a></li>
        </ul>
      </li>

      {{-- 💖 Wellness --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="bx bx-heart"></i></div>
          <div class="menu-title">Wellness</div>
        </a>
        <ul>
          @if(permission::permitted('wellness-followups') === 'success')
            <li><a href="{{ route('wellness.followups.index') }}"><i class="material-icons-outlined">chevron_right</i>My Cases</a></li>
          @endif

          @if(permission::permitted('admin-followups') === 'success')
            <li><a href="{{ route('admin.followups.index') }}"><i class="material-icons-outlined">chevron_right</i>Case Dashboard</a></li>
          @endif
        </ul>
      </li>

      {{-- 👥 Small Groups --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="lni lni-slideshare"></i></div>
          <div class="menu-title">Small Groups</div>
        </a>
        <ul>
          <li><a href="{{ route('small.index') }}"><i class="material-icons-outlined">chevron_right</i>Create Small Group</a></li>
        </ul>
      </li>

      {{-- 🌟 Monthly Digital Gift --}}
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="material-icons-outlined">auto_awesome</i></div>
          <div class="menu-title">Monthly Digital Gift</div>
        </a>
        <ul>
          <li><a href="{{ route('admin.monthly-digital-gift.edit') }}"><i class="material-icons-outlined">chevron_right</i>Manage Gift</a></li>
          <li><a href="{{ route('monthly-digital-gift.show') }}" target="_blank" rel="noopener"><i class="material-icons-outlined">chevron_right</i>View Public Page</a></li>
        </ul>
      </li>

      {{-- ⚙️ Settings --}}
      <li class="menu-label">Settings</li>

      @if(permission::permitted('users') === 'success')
        <li>
          <a href="{{ route('users') }}">
            <div class="parent-icon"><i class="lni lni-users"></i></div>
            <div class="menu-title">Users</div>
          </a>
        </li>
      @endif

      @if(permission::permitted('radio-settings-computers') === 'success')
        <li>
          <a href="{{ url('computers') }}">
            <div class="parent-icon"><i class="material-icons-outlined">computer</i></div>
            <div class="menu-title">Computers</div>
          </a>
        </li>
      @endif

      @if(permission::permitted('radio-settings-inventory') === 'success')
        <li>
          <a href="{{ url('inventory') }}">
            <div class="parent-icon"><i class="bx bx-package"></i></div>
            <div class="menu-title">Inventory</div>
          </a>
        </li>
      @endif

      {{-- 💬 Support --}}
      <li>
        <a href="javascript:void(0);">
          <div class="parent-icon"><i class="material-icons-outlined">support</i></div>
          <div class="menu-title">Support</div>
        </a>
      </li>
    </ul>
    <!--end navigation-->
  </div>
</aside>
<!--end sidebar-->
