@extends('layouts.personal')

    @section('meta')
        <title>My Dashboard | Glory Servant</title>
        <meta name="description" content="Glory Servant my dashboard, view recent attendance, view recent leave of absence, and view previous schedules">
    @endsection

    @section('content')
    @include('personal.modals.modal-post-devotion')
        <div class="container-fluid">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title section-title mb-0">{{ __("My Work Summary") }}</h2>
            </div>
            <div class="col-auto">
                <button class="ui positive button mini btn-add"><i class="ui icon write"></i>{{ __("Post Devotion") }}</button>
            </div>
            <canvas id="canvas" width="300" height="300" class="d-none"></canvas>
        </div>
        @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif


        {{-- Top metrics row --}}
        <div class="row g-3 mb-3">
            @if(\App\Classes\permission::permitted('attendance') === 'success')
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card metric-card gradient-blue shadow-sm">
                    <div class="card-body py-3">
                        <div class="metric-title mb-1">{{ __('Total Hours Today') }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div id="kpi-hours-today-value" class="metric-value">{{ number_format($hoursTodayWorked ?? 0, 2) }} / {{ number_format($hoursTodayExpected ?? 9, 0) }}</div>
                            <i class="fas fa-stopwatch metric-icon"></i>
                        </div>
                        <div id="kpi-hours-today-trend" class="metric-trend mt-1">↑ {{ __('Up to date') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card metric-card gradient-teal shadow-sm">
                    <div class="card-body py-3">
                        <div class="metric-title mb-1">{{ __('Total Hours This Week') }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div id="kpi-hours-week-value" class="metric-value">{{ number_format($hoursWeekWorked ?? 0, 0) }} / {{ number_format($hoursWeekExpected ?? 45, 0) }}</div>
                            <i class="far fa-calendar-alt metric-icon"></i>
                        </div>
                        <div id="kpi-hours-week-trend" class="metric-trend mt-1">↑ {{ __('5% better than last week') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card metric-card gradient-purple shadow-sm">
                    <div class="card-body py-3">
                        <div class="metric-title mb-1">{{ __('Overtime / Extra Hours') }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div id="kpi-overtime-value" class="metric-value">{{ number_format($overtimeHours ?? 0, 1) }}h</div>
                            <i class="fas fa-sync-alt metric-icon"></i>
                        </div>
                        <div id="kpi-overtime-trend" class="metric-trend mt-1">↓ {{ __('2% less than average') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card metric-card gradient-orange shadow-sm">
                    <div class="card-body py-3">
                        <div class="metric-title mb-1">{{ __('Late Arrivals') }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div id="kpi-lates-value" class="metric-value">{{ $lateArrivalsWeek ?? 0 }} {{ __('this week') }}</div>
                            <i class="fas fa-door-open metric-icon"></i>
                        </div>
                        <div id="kpi-lates-trend" class="metric-trend mt-1">↑ {{ __('1% less than average') }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Clock-in widget + Profile side column (match mock layout) --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <div class="widget-title text-end">{{ __('Clock-In Widget') }}</div>
                        </div>
                        <div class="row g-4 align-items-center">
                          <div class="col-12 col-lg-5">
                            <div class="clock-tile">
                              @php
                                  $pct = 0;
                                  if (($hoursTodayExpected ?? 0) > 0) {
                                      $pct = min(100, round(($hoursTodayWorked ?? 0) / max(1, $hoursTodayExpected) * 100));
                                  }
                              @endphp
                              <div class="text-center">
                                <div id="progressRing" class="progress-ring" style="--pg: {{ $pct }}%; --size: 260px;">
                                  <div class="inner" style="background: linear-gradient(145deg, #0d1f2b 0%, #0b1621 100%);">
                                     <div id="dashStatus" class="status-label">{{ $clockedIn ? __('Clocked In') : __('Clocked Out') }}</div>
                                     <div id="dashboardClock" data-tf="{{ $tf ?? 1 }}" class="clock-digits" aria-live="polite"></div>
                                     <div class="worked-summary"><span id="progressRingValue">{{ number_format($hoursTodayWorked ?? 0, 2) }}h</span> / {{ number_format($hoursTodayExpected ?? 9, 0) }}h</div>
                                  </div>
                                </div>
                                <button id="dashClockBtn" type="button" class="btn clock-action w-100 mt-3" data-type="{{ $clockedIn ? 'timeout' : 'timein' }}">
                                  {{ $clockedIn ? __('Clock Out') : __('Clock In') }}
                                </button>
                                <div class="clock-sf mt-1"><span id="dashStatusTime">@ {{ $clockedInAt ?? '' }}</span></div>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 col-lg-7">
                            <div class="row gx-3 gy-5" id="right-cards">
                              <div class="col-12 col-md-6">
                                <div class="mini-card h-100">
                                  <div class="title">{{ __('Today’s Schedule') }}</div>
                                  <ul id="today-schedule-list" class="list-unstyled m-0"></ul>
                                  <div id="today-schedule-empty" class="smallmuted">{{ __('No schedule today') }}</div>
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="mini-card h-100">
                                  <div class="title">{{ __('Upcoming Shift') }}</div>
                                  <div id="upcoming-shift-empty" class="smallmuted">{{ __('No upcoming shifts') }}</div>
                                  <div id="upcoming-shift" class="d-none">
                                    <div class="rowline"><div class="smallmuted">{{ __('Time') }}</div><div id="upcoming-shift-time"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Where') }}</div><div id="upcoming-shift-where"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Starts in') }}</div><div id="upcoming-shift-countdown"></div></div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="mini-card h-100">
                                  <div class="title">{{ __('Last Time Late') }}</div>
                                  <div id="last-late-empty" class="smallmuted">{{ __('No late arrivals on record') }}</div>
                                  <div id="last-late" class="d-none">
                                    <div class="rowline"><div class="smallmuted">{{ __('Date') }}</div><div id="last-late-date"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Scheduled') }}</div><div id="last-late-scheduled"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Checked In') }}</div><div id="last-late-actual"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Minutes Late') }}</div><div id="last-late-mins"></div></div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="mini-card h-100">
                                  <div class="title">{{ __('Last Time Overall') }}</div>
                                  <div id="last-overall-empty" class="smallmuted">{{ __('No attendance sessions yet') }}</div>
                                  <div id="last-overall" class="d-none">
                                    <div class="rowline"><div class="smallmuted">{{ __('Date') }}</div><div id="last-overall-date"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Check-In/Out') }}</div><div id="last-overall-times"></div></div>
                                    <div class="rowline"><div class="smallmuted">{{ __('Length') }}</div><div id="last-overall-length"></div></div>
                                  </div>
                                </div>
                              </div>
                              
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                @php
                    $firstB = optional(($birthdaysToday ?? collect())->first());
                    $secondB = optional(($birthdaysToday ?? collect())->skip(1)->first());
                    $bPrimary = $firstB ? trim(($firstB->firstname ?? '').' '.($firstB->lastname ?? '')) : __('Team Birthdays');
                    $bDate = $firstB && isset($firstB->birthday)
                        ? \Carbon\Carbon::parse($firstB->birthday)->format('M d')
                        : '';
                    $bSecondary = $secondB ? trim(($secondB->firstname ?? '').' '.($secondB->lastname ?? '')) : '';
                @endphp

                <div class="profile-card">
                  <div class="profile-header d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('/assets/images/faces/default_user4.jpg') }}" class="avatar-sm" alt="avatar">
                    <div>
                      <div class="profile-name">{{ auth()->user()->name }}</div>
                      <div class="profile-role">{{ $campusContext->jobposition ?? '' }}</div>
                      <div class="profile-role">{{ $campusContext->campus ?? __('Main Campus') }}</div>
                    </div>
                  </div>

                  <div class="profile-block profile-birthday mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="profile-block-title"><i class="far fa-calendar-alt me-2"></i>{{ $bPrimary }}{{ $bDate ? ' - '.$bDate : '' }}</div>
                        @if($bSecondary)
                          <div class="profile-block-sub">{{ $bSecondary }}</div>
                        @endif
                      </div>
                      <button type="button" class="btn-wishes">{{ __('Send Wishes') }}</button>
                    </div>
                  </div>

                  <div class="profile-block profile-links mb-3">
                    <a href="#" class="profile-pill"><i class="fas fa-users"></i> {{ __('Team Birthdays') }}</a>
                    <span class="pill-separator"></span>
                    <a href="#" class="profile-pill"><i class="far fa-file-alt"></i> {{ __('Policy Updates') }}</a>
                  </div>

                  <div class="profile-block profile-holiday">
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="d-flex align-items-center gap-2">
                        <i class="far fa-sun"></i>
                        <div>
                          <div class="profile-block-title">{{ __('Next Holiday') }}</div>
                          <div class="profile-block-sub" id="ctx-next-holiday">—</div>
                        </div>
                      </div>
                      <button type="button" class="btn-wishes">{{ __('Send Wishes') }}</button>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        @if(\App\Classes\permission::permitted('schedules') === 'success')
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="h6 mb-0">{{ __('Today’s Schedule') }}</div>
                            @if(isset($scheduleStart,$scheduleEnd))
                                <span class="badge bg-light text-dark">{{ $scheduleStart }} – {{ $scheduleEnd }}</span>
                            @endif
                        </div>
                        <ul id="schedule-list" class="mini-timeline"></ul>
                        <div id="schedule-empty" class="text-muted small">{{ __('No assigned schedule today') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Creative Tasks Block (same size as clock-in widget) --}}
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="h6 mb-0">{{ __('My Creative Tasks') }} <span class="badge bg-info">{{ $creativeTasks->count() }}</span></div>
                            <a href="{{ route('personal.creative.dashboard') }}" class="btn btn-primary btn-sm">{{ __('View All') }}</a>
                        </div>
                        @if(isset($creativeTasks) && $creativeTasks->count() > 0)
                            <div class="row g-3">
                                @foreach($creativeTasks as $task)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <i class="fas fa-tasks mt-1" style="color: {{ $task->priority === 'urgent' ? '#dc3545' : ($task->priority === 'high' ? '#fd7e14' : '#6c757d') }}"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">{{ $task->title }}</div>
                                                    <div class="text-muted small">{{ $task->request_title }}</div>
                                                </div>
                                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                            </div>
                                            @if($task->due_at)
                                            <div class="text-muted small mb-2"><i class="far fa-calendar"></i> Due: {{ \Carbon\Carbon::parse($task->due_at)->format('M d, Y') }}</div>
                                            @endif
                                            <a href="{{ route('personal.creative.task.show', $task->id) }}" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted text-center py-5">{{ __('No tasks assigned') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Meetings + Leaves/Requests --}}
        <div class="row g-3 mb-3">
            @if(\App\Classes\permission::permitted('meeting-attendance') === 'success')
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="h6 mb-2">{{ __('Next Meeting') }}</div>
                        <div id="next-meeting" class="text-muted small mb-2">{{ __('No upcoming meetings found') }}</div>
                        <a id="next-meeting-link" href="{{ route('team-attendance') }}" class="btn btn-outline-primary btn-sm">{{ __('Open Meeting Board') }}</a>
                    </div>
                </div>
            </div>
            @endif

            @if(\App\Classes\permission::permitted('leaves') === 'success')
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="h6 mb-0">{{ __('Leaves & Requests') }}</div>
                            <a href="{{ url('personal/leaves/view') }}" class="btn btn-primary btn-sm">{{ __('Request Leave') }}</a>
                        </div>
                        <div id="leave-balance" class="small text-muted mb-2">—</div>
                        <ul id="pending-leaves" class="list-unstyled m-0"></ul>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="h6 mb-2">{{ __('Notifications & Tasks') }} <span id="notif-unread" class="badge bg-primary">0</span></div>
                        <ul id="notif-feed" class="list-unstyled m-0 mb-2"></ul>
                        <ul class="list-unstyled m-0">
                            <li class="mb-2"><a href="{{ route('personal.notifications') }}">{{ __('Open Notifications') }}</a></li>
                            <li class="mb-2"><a href="{{ url('personal/attendance/view') }}">{{ __('View Attendance') }}</a></li>
                            <li class="mb-2"><a href="{{ url('personal/profile/view') }}">{{ __('Update Profile') }}</a></li>
                            <li class="mb-2"><a href="{{ url('personal/devotion/view') }}">{{ __('Submit Devotion') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="h6 mb-2">{{ __('Tasks / Actions') }}</div>
                        <ul class="list-unstyled m-0">
                            <li class="mb-2"><a class="d-flex align-items-center gap-2" href="{{ url('personal/attendance/view') }}"><i class="far fa-clock"></i> {{ __('View Attendance') }}</a></li>
                            <li class="mb-2"><a class="d-flex align-items-center gap-2" href="{{ url('personal/profile/view') }}"><i class="far fa-user"></i> {{ __('Update Profile') }}</a></li>
                            <li class="mb-2"><a class="d-flex align-items-center gap-2" href="{{ url('personal/devotion/view') }}"><i class="fas fa-pen"></i> {{ __('Submit Devotion') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if(\App\Classes\permission::permitted('meeting-attendance') === 'success')
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0 text-uppercase text-muted" style="letter-spacing:.08em;">{{ __('Team / Meeting Sidebar') }}</h6>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-lg-4">
                                <div class="small fw-semibold mb-2">{{ __('Team Members') }}</div>
                                <ul id="team-members" class="list-unstyled m-0"></ul>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="small fw-semibold mb-2">{{ __('Upcoming Meetings') }}</div>
                                <ul id="upcoming-meetings" class="list-unstyled m-0"></ul>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="small fw-semibold mb-2">{{ __('Recent Announcements') }}</div>
                                <ul id="recent-announcements" class="list-unstyled m-0"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">

            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="h6 mb-3">{{ __("Recent Attendance") }}</div>
                        <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __("Date") }}</th>
                                <th class="text-left">{{ __("Time") }}</th>
                                <th class="text-left">{{ __("Description") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($a)
                            @foreach($a as $v)

                            @if($v->timein != '' && $v->timeout == '')
                            <tr>
                                <td>
                                    @php echo e(date('M d, Y', strtotime($v->date))); @endphp
                                </td>
                                <td>
                                    @php
                                        if($tf == 1) {
                                            echo e(date("h:i:s A", strtotime($v->timein)));
                                        } else {
                                            echo e(date("H:i:s", strtotime($v->timein)));
                                        }
                                    @endphp
                                </td>
                                <td>Time-In</td>
                            </tr>
                            @endif
                            
                            @if($v->timein != '' && $v->timeout != '')
                            <tr>
                                <td>
                                    @php echo e(date('M d, Y', strtotime($v->date))); @endphp
                                </td>
                                <td>
                                    @php
                                        if($tf == 1) {
                                            echo e(date("h:i:s A", strtotime($v->timeout)));
                                        } else {
                                            echo e(date("H:i:s", strtotime($v->timeout)));
                                        }
                                    @endphp
                                </td>
                                <td>Time-Out</td>
                            </tr>
                            @endif

                            @if($v->timein != '' && $v->timeout != '')
                            <tr>
                                <td>
                                    @php echo e(date('M d, Y', strtotime($v->date))); @endphp
                                </td>
                                <td>
                                    @php
                                        if($tf == 1) {
                                            echo e(date("h:i:s A", strtotime($v->timein)));
                                        } else {
                                            echo e(date("H:i:s", strtotime($v->timein)));
                                        }
                                    @endphp
                                </td>
                                <td>Time-In</td>
                            </tr>
                            @endif

                            @endforeach
                            @endisset
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="h6 mb-3">{{ __("Previous Schedules") }}</div>
                        <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __("Time") }}</th>
                                <th class="text-left">{{ __("From Date / Until") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($ps)
                            @foreach($ps as $s)
                            <tr>
                                <td>
                                    @php
                                        if ($s->intime != null && $s->outime != null) {
                                            if ($tf == 1) {
                                                echo e(date("h:i A", strtotime($s->intime)));
                                                echo e(" - ");
                                                echo e(date("h:i A", strtotime($s->outime)));
                                            } else {
                                                echo e(date("H:i", strtotime($s->intime)));
                                                echo e(" - ");
                                                echo e(date("H:i", strtotime($s->outime)));
                                            }
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php 
                                        echo e(date('M d',strtotime($s->datefrom)).' - '.date('M d, Y',strtotime($s->dateto)));
                                    @endphp
                                </td>
                            </tr>
                            @endforeach
                            @endisset
                        </tbody>
                        </table>
                    </div>
                </div>
            </div> 
            <!--<div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Intercampus Meeting</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                        <p>📢 Weekly Intercampus Communication Meeting – Join Us!<br>
    
                        🇺🇸 English<br>
                        Dear Volunteers,<br>
                        
                        We invite you to our Intercampus Communication Meeting every Wednesday at 8 PM. <br><br>This is a crucial time to discuss strategies, share updates, and communicate important ministry information. Your presence and input are valuable!<br><br>
                    
                    Join us on Zoom, Click here 👇:
                    <br>🔗 <a href="https://zoom.us/j/91852951178?pwd=etyaNvmK5925jes3hTuwC1XZGm5gng.1">https://zoom.us/j/91852951178?pwd=etyaNvmK5925jes3hTuwC1XZGm5gng.1</a><br><br>
                    <br>📌 Meeting ID: 918 5295 1178
                    <br>🔑 Passcode: 109232<br>
                    
                    <br>See you there!
                    </p>
                    </div>
                </div>
            </div>-->
            
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="h6 mb-3">{{ __("Recent Leaves of Absence") }}</div>
                        <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __("Description") }}</th>
                                <th class="text-left">{{ __("Date") }}</th>
                            </tr>
                        </thead>
                            <tbody>
                                @isset($ald)
                                @foreach($ald as $l)
                                <tr>
                                    <td>{{ $l->type }}</td>
                                    <td>
                                        @php
                                            $fd = date('M', strtotime($l->leavefrom));
                                            $td = date('M', strtotime($l->leaveto));

                                            if($fd == $td){
                                                $var = date('M d', strtotime($l->leavefrom)) .' - '. date('d, Y', strtotime($l->leaveto));
                                            } else {
                                                $var = date('M d', strtotime($l->leavefrom)) .' - '. date('M d, Y', strtotime($l->leaveto));
                                            }
                                        @endphp
                                        {{ $var }}
                                    </td>
                                </tr>
                                @endforeach
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            
            </div>
            
@if(session('success'))
<div class="ui positive message">
    <i class="close icon"></i>
    <div class="header">Success</div>
    <p>{{ session('success') }}</p>
</div>
@endif

        </div>
    </div>
    
    
    <!--<div class="ui modal medium add" id="birthdayModal">
    `
</div>-->

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script src="/assets3/personal-dashboard.js" defer></script>
@endsection


{{-- Firebase removed per request --}}


    @endsection
    
