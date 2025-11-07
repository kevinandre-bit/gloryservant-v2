@php use App\Classes\permission; @endphp
<!--start sidebar-->
   <aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
      <div>
        <img src="/assets2/images/logo_gs4.png">
      </div>
      <div class="sidebar-close">
        <span class="material-icons-outlined">close</span>
      </div>
    </div>
    <div class="sidebar-nav">
        <!--navigation-->
        <ul class="metismenu" id="sidenav">
          <li>
            <a href="javascript:;" class="has-arrow">
              <div class="parent-icon"><i class="material-icons-outlined">home</i>
              </div>
              <div class="menu-title">Dashboard</div>
            </a>
            <ul>
              <li><a href="index.php"><i class="material-icons-outlined">arrow_right</i>Analysis</a>
              </li>
            </ul>
          </li>
          <li>
            <a class="has-arrow" href="javascript:;">
              <div class="parent-icon"><i class="fadeIn animated bx bx-user"></i>
              </div>
              <div class="menu-title">Volunteer section</div>
            </a>
            <ul>
              <li><a href="{{ ('employees') }}"><i class="material-icons-outlined">arrow_right</i>Volunteer Sec..</a>
              </li>
              @if(permission::permitted('employee-attendance') === 'success')
              <li><a href="{{ ('attendance') }}"><i class="material-icons-outlined">arrow_right</i>Attendance</a>
              </li>
              @endif
              <li><a href="{{ ('schedules') }}"><i class="material-icons-outlined">arrow_right</i>Schedule</a>
              </li>
            </ul>
          </li>
          <li>
            <a class="has-arrow" href="javascript:;">
              <div class="parent-icon"><i class="fadeIn animated bx bx-pie-chart-alt-2"></i>
              </div>
              <div class="menu-title">Reports</div>
            </a>
            <ul>
              @if(permission::permitted('employee-list') === 'success')
              <li><a href="{{ ('employee-list') }}"><i class="material-icons-outlined">arrow_right</i>Volunteer list</a>
              </li>
              @endif

              @if(permission::permitted('devotions') === 'success')
              <li><a href="{{ ('devotions-reports') }}"><i class="material-icons-outlined">arrow_right</i>Devotion</a>
              </li>
              @endif

              @if(permission::permitted('meeting-attendance') === 'success')
              <li><a href="{{ url('meeting-attendance') }}"><i class="material-icons-outlined">arrow_right</i>Meeting Att..</a>
              </li>
              @endif

              
              @if(permission::permitted('employee-birthdays') === 'success')
              <li><a href="{{ ('volunteer-birthday') }}"><i class="material-icons-outlined">arrow_right</i>Volunteer Birth..</a>
              </li>
              @endif

              @if(permission::permitted('employee-leaves') === 'success')
              <li><a href="{{ ('leaves') }}"><i class="material-icons-outlined">arrow_right</i>Leaves</a>
              </li>
              @endif

              @if(permission::permitted('employee-schedule') === 'success')
              <li><a href="{{ ('reports/employee-schedule') }}"><i class="material-icons-outlined">arrow_right</i>Schedule</a>
              </li>
              @endif
            </ul>
          </li>
          <li class="menu-label">Settings</li>
          
          <li>
            <a href="user-profile.php">
              <div class="parent-icon"><i class="material-icons-outlined">person</i>
              </div>
              <div class="menu-title">User Profile</div>
            </a>
          </li>
          <li>
            <a href="{{ ('computers') }}">
              <div class="parent-icon"><i class="material-icons-outlined">computer</i>
              </div>
              <div class="menu-title">Computers</div>
            </a>
          </li>
          <li>
            <a href="javascrpt:;">
              <div class="parent-icon"><i class="material-icons-outlined">support</i>
              </div>
              <div class="menu-title">Support</div>
            </a>
          </li>
         </ul>
        <!--end navigation-->
    </div>
  </aside>
<!--end sidebar-->