 <!--start header-->
  @include ('layouts/admin')
  <!--end top header-->
 
  <!--end top sidebar-->
  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
          <div class="breadcrumb-title pe-3">Dashboard</div>
          <div class="ps-3">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Analysis</li>
              </ol>
            </nav>
          </div>
          <div class="ms-auto">
            <div class="btn-group">
              <div class="">
                <!-- Trigger Button -->
                  <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#ScannerModal">
                    📷 Scan QR Code
                  </button>

                  <!-- Scanner Modal -->
                  <div class="modal fade" id="ScannerModal">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        
                        <!-- Header -->
                        <div class="modal-header border-bottom-0 py-2">
                          <h5 class="modal-title">
                            <i class="fadeIn animated bx bx-scan"></i> Scan QR Code
                          </h5>
                          <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                            <i class="material-icons-outlined">close</i>
                          </a>
                        </div>

                        <!-- Scanner UI -->
                        <div class="modal-body">
                          <div id="scanner-container" style="position:relative; width:300px; height:300px; margin:0 auto;">
                            <video id="video" playsinline muted style="width:100%; height:100%; object-fit:cover;"></video>

                            <!-- Overlay -->
                            <div id="overlay" style="
                              position:absolute; top:0; left:0; width:100%; height:100%;
                              pointer-events:none;
                            ">
                              <div style="background:rgba(0,0,0,0.5); width:100%; height:25%;"></div>
                              <div style="display:flex; width:100%; height:50%;">
                                <div style="background:rgba(0,0,0,0.5); width:25%;"></div> 
                                <div style="flex:1; border:4px solid #0f0;"></div>
                                <div style="background:rgba(0,0,0,0.5); width:25%;"></div>
                              </div>
                              <div style="background:rgba(0,0,0,0.5); width:100%; height:25%;"></div>
                            </div>
                          </div>

                          <!-- Hidden Canvas -->
                          <canvas id="canvas" width="300" height="300" style="display:none;"></canvas>

                          <div class="mt-3 text-center text-muted">
                            Align the QR code within the green box to scan.
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Hidden by default: camera feed will go here -->
                  <div id="qr-reader" style="width:300px; height:300px; display:none;"></div>
                  <!-- Button trigger modal -->
                  <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal"
                    data-bs-target="#BasicModal">  <i class="fadeIn animated bx bx-scan"></i>
                 QR Code!</button>

                  <!-- Modal -->
                  <div class="modal fade" id="BasicModal">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header border-bottom-0 py-2"> 
                          <h5 class="modal-title"><i class="fadeIn animated bx bx-scan"></i> Scan to Clock In</h5>
                          <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                            <i class="material-icons-outlined">close</i>
                          </a>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mb-3">
                        <img src="{{ $dailyQrUrl }}"
                             class="w-50 rounded-4"
                             alt="Daily QR Code">
                      </div>
                      <div class="modal-body d-flex align-items-center">
                        
                          <br>Simply point your device’s camera at this QR code and you’ll be automatically clocked in—no additional steps required.</div>
                        <!--<div class="modal-footer border-top-0">
                          <button type="button" class="btn btn-grd-danger" data-bs-dismiss="modal">Delete</button>
                          <button type="button" class="btn btn-grd-info">Save changes</button>
                        </div>-->
                      </div>
                    </div>
                  </div>
              </div>
              
            </div>
          </div>
        </div>
        <!--end breadcrumb--> 
     
        <div class="row">
          <!-- Welcome -->
          <div class="col-xxl-8 d-flex align-items-stretch">
            <div class="card w-100 overflow-hidden rounded-4">
              <div class="card-body position-relative p-4">
                <div class="row">
                  <div class="col-12 col-sm-7">
                    <div class="d-flex align-items-center gap-3 mb-5">
                      
                      @php
                        // choose uploaded or generic
                        $src = ($userAvatar
                                  && file_exists(public_path("assets2/images/faces/{$userAvatar}")))
                              ? asset("assets2/images/faces/{$userAvatar}")
                              : asset("assets2/images/avatars/"
                                      . strtolower(Auth::user()->gender)
                                      . "/{$userGeneric}");
                      @endphp
                      <img src="{{ $userAvatar ?? asset('images/avatar-default.png') }}" class="rounded-circle bg-grd-info p-1" width="60" height="60" alt="{{ Auth::user()->name }}">
                      <div>
                        <p class="mb-0 fw-semibold">Welcome back</p>
                        <h4 class="fw-semibold mb-0 fs-4">{{ Auth::user()->name }}!</h4>
                      </div>
                    </div>

                    <div class="d-flex align-items-center gap-5">
                      {{-- Today's Attendance --}}
                      <div>
                        <h4 class="mb-1 fw-semibold d-flex align-items-center">
                          {{ $todayCount }}
                          <i class="ti ti-arrow-{{ $growthVsLastWeek >= 0 ? 'up-right' : 'down-left' }}
                             fs-5 lh-base {{ $growthVsLastWeek >= 0 ? 'text-success' : 'text-danger' }}"></i>
                        </h4>
                        <p class="mb-3">Today's Attendance</p>
                        <div class="progress mb-0" style="height:5px;">
                          <div class="progress-bar bg-grd-success"
                               role="progressbar"
                               style="width: {{ $attendancePercent }}%;"
                               aria-valuenow="{{ $attendancePercent }}"
                               aria-valuemin="0" aria-valuemax="100">
                          </div>
                        </div>
                      </div>

                      <div class="vr"></div>

                      {{-- Growth Rate --}}
                      <div>
                        <h4 class="mb-1 fw-semibold d-flex align-items-center">
                          {{ $growthVsLastWeek }}%
                          <i class="ti ti-arrow-{{ $growthVsLastWeek >= 0 ? 'up-right' : 'down-left' }}
                             fs-5 lh-base {{ $growthVsLastWeek >= 0 ? 'text-success' : 'text-danger' }}"></i>
                        </h4>
                        <p class="mb-3">Growth vs Same Day Last Week</p>
                        <div class="progress mb-0" style="height:5px;">
                          <div class="progress-bar bg-grd-{{ $growthVsLastWeek >= 0 ? 'success' : 'danger' }}"
                               role="progressbar"
                               style="width: {{ abs($growthVsLastWeek) }}%;"
                               aria-valuenow="{{ abs($growthVsLastWeek) }}"
                               aria-valuemin="0" aria-valuemax="100">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 col-sm-5">
                    <div class="welcome-back-img pt-4 text-center">
                       @php
                          // Determine avatar source
                          $src = ($userAvatar && file_exists(public_path("assets2/images/faces/{$userAvatar}")))
                              ? asset("assets2/images/faces/{$userAvatar}")
                              : asset("assets2/images/avatars/".strtolower(Auth::user()->gender)."/{$userGeneric}");

                          // Map bucket → message and progress %
                          switch ($bucket) {
                              case 1:
                                  $levelName = "Legend";
                                  $threshold = 1500;
                                  break;
                              case 2:
                                  $levelName = "Champion";
                                  $threshold = 1378;
                                  break;
                              case 3:
                                  $levelName = "Master";
                                  $threshold = 1257;
                                  break;
                              case 4:
                                  $levelName = "Veteran";
                                  $threshold = 1135;
                                  break;
                              case 5:
                                  $levelName = "Elite";
                                  $threshold = 1013;
                                  break;
                              case 6:
                                  $levelName = "Pro";
                                  $threshold = 892;
                                  break;
                              case 7:
                                  $levelName = "Expert";
                                  $threshold = 770;
                                  break;
                              case 8:
                                  $levelName = "Skilled";
                                  $threshold = 648;
                                  break;
                              case 9:
                                  $levelName = "Contributor";
                                  $threshold = 527;
                                  break;
                              case 10:
                                  $levelName = "Participant";
                                  $threshold = 405;
                                  break;
                              case 11:
                                  $levelName = "Novice";
                                  $threshold = 283;
                                  break;
                              case 12:
                                  $levelName = "Newcomer";
                                  $threshold = 162;
                                  break;
                              case 13:
                                  $levelName = "Seed";
                                  $threshold = 40;
                                  break;
                              default:
                                  $levelName = "Starter";
                                  $threshold = 0;
                                  break;
                          }

                          // calculate how many hours are left
                          $missing = max(0, $threshold - $myHours);

                          if ($missing > 0) {
                              $message = "🌱 {$levelName}! {$missing}h to next badge.";
                          } else {
                              $message = "🏆 {$levelName}! You’ve unlocked the top badge!";
                          }
                      @endphp
                      <img src="{{ asset('assets2/images/gallery/'.$welcomeImage) }}" height="180" alt="Welcome back">

                      {{-- Friendly message --}}
                      <p class="mb-0">{{ $message }}</p>
                    </div>
                  </div>
                </div><!-- end row -->
              </div>
            </div>
          </div>
          <!-- Active volunteers -->
          <div class="col-xl-6 col-xxl-2 d-flex align-items-stretch">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-1">
                  <div>
                    <h5 class="mb-0">{{ $activeOnSchedule }}</h5>
                    <p class="mb-0">Volunteers on schedule</p>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                       data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                </div>

                <div class="chart-container2">
                  <div id="chart1" ></div>
                </div>
                <!-- expose your controller value to JS -->
                <script>
                  // JSON-encode to be safe (handles numbers, strings, etc.)
                  window.attendancePct = @json($attendanceSchedulePct);
                </script>


                <div class="text-center">
                  @php $absCount = abs($volunteerIncreaseCount); @endphp
                  <p class="mb-0 font-12">
                    {{ $absCount }}
                    volunteer{{ $absCount === 1 ? '' : 's' }}
                    {{ $volunteerIncreaseCount >= 0 ? 'increased' : 'decreased' }}
                    from last week
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Hours -->
          <div class="col-xl-6 col-xxl-2 d-flex align-items-stretch">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0">{{ $weeklyTotals[0]['hours'] }}</h5>
                    <p class="mb-0">Current Week’s Service Hours</p>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                </div>
                <div class="chart-container2">
                  <div id="chart2"></div>
                  <!-- expose your controller value to JS -->
                <script>
                    // Optional: keep the whole array available
                    window.weeklyTotals = @json($weeklyTotals);

                    // Export each week’s hours separately
                    window.week0Hours = @json($weeklyTotals[0]['hours']);
                    window.week1Hours = @json($weeklyTotals[1]['hours']); // last week
                    window.week2Hours = @json($weeklyTotals[2]['hours']);
                    window.week3Hours = @json($weeklyTotals[3]['hours']);
                    window.week4Hours = @json($weeklyTotals[4]['hours']);
                    window.week5Hours = @json($weeklyTotals[5]['hours']);
                    window.week6Hours = @json($weeklyTotals[6]['hours']);
                    window.week7Hours = @json($weeklyTotals[7]['hours']);
                    window.week8Hours = @json($weeklyTotals[8]['hours']);
                </script>
                </div>
                <div class="text-center">
                  @php
                      // ensure you have these from your controller
                      $pct = $hoursPctChange;
                      $isPositive = $pct >= 0;
                      $signClick = $clickGrowth > 0 ? '+' : ($clickGrowth < 0 ? '' : '');
                      $signView  = $viewGrowth  > 0 ? '+' : ($viewGrowth  < 0 ? '' : '');
                  @endphp
                  <p class="mb-0 font-12">
                  <span class="{{ $isPositive ? 'text-success' : 'text-danger' }} me-1">
                    {{ $signClick }}{{ abs($pct) }}%
                  </span>
                  from last week
                </p>
                </div>
              </div>
            </div>
          </div>
          <!-- Monthly Hours -->
          <div class="col-xl-6 col-xxl-8 d-flex align-items-stretch">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="text-center">
                  <h6 class="mb-0">Weekly Volunteer attendance</h6>
                </div>
                <div class="mt-4" id="chart5"></div>
                <script>
                    // Optional: keep the whole array available
                    window.weeklyAttendance = @json($weeklyAttendance);

                    // Export each week’s count separately
                    window.week0Att = @json($weeklyAttendance[0]['count']);
                    window.week1Att = @json($weeklyAttendance[1]['count']);
                    window.week2Att = @json($weeklyAttendance[2]['count']);
                    window.week3Att = @json($weeklyAttendance[3]['count']);
                    window.week4Att = @json($weeklyAttendance[4]['count']);
                    window.week5Att = @json($weeklyAttendance[5]['count']);
                    window.week6Att = @json($weeklyAttendance[6]['count']);
                    window.week7Att = @json($weeklyAttendance[7]['count']);
                    window.week8Att = @json($weeklyAttendance[8]['count']);
                    window.week9Att = @json($weeklyAttendance[9]['count']);
                    window.week10Att = @json($weeklyAttendance[10]['count']);
                    window.week11Att = @json($weeklyAttendance[11]['count']);


                    window.week0Start = @json($weeklyAttendance[0]['week_start']);
                    window.week1Start = @json($weeklyAttendance[1]['week_start']);
                    window.week2Start = @json($weeklyAttendance[2]['week_start']);
                    window.week3Start = @json($weeklyAttendance[3]['week_start']);
                    window.week4Start = @json($weeklyAttendance[4]['week_start']);
                    window.week5Start = @json($weeklyAttendance[5]['week_start']);
                    window.week6Start = @json($weeklyAttendance[6]['week_start']);
                    window.week7Start = @json($weeklyAttendance[7]['week_start']);
                    window.week8Start = @json($weeklyAttendance[8]['week_start']);
                    window.week9Start = @json($weeklyAttendance[9]['week_start']);
                    window.week10Start = @json($weeklyAttendance[10]['week_start']);
                    window.week11Start = @json($weeklyAttendance[11]['week_start']);
                </script>
                <p>Attended this week</p>
                @php
                  $pos = $attendancePctChange >= 0;
                @endphp

                <div class="d-flex align-items-center gap-3 mt-4">
                  <div>
                    <h1 class="mb-0 text-primary">
                      {{ $currentWeekAttendees }}
                    </h1>
                  </div>
                  <div class="d-flex align-items-center align-self-end">
                    <p class="mb-0 {{ $pos ? 'text-success' : 'text-danger' }}">
                      {{ abs($attendancePctChange) }}%
                    </p>
                    <span class="material-icons-outlined {{ $pos ? 'text-success' : 'text-danger' }}">
                      {{ $pos ? 'expand_less' : 'expand_more' }}
                    </span><span> than last week!</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Volunteer of the Week -->
          <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body">
                <div class="position-relative">
                  <img src="/assets2/images/gallery/14.png" class="img-fluid rounded" alt="">
                  <div class="position-absolute top-100 start-50 translate-middle">
                    <img src="{{ $star->avatar_url ?? asset('images/avatar-default.png') }}" width="140" height="140" class="rounded-circle raised p-1 bg-primary" alt="Avatar">
                  </div>
                </div>
                <div class="text-center mt-5 pt-4">
                  <h5 class="mb-2">{{ $star->firstname ?? 'N/A' }} {{ $star->lastname ?? '' }}</h5>
                  <p class="mb-0"><b>Campus:</b><i> {{ $star->campus ?? 'N/A' }}</i><b> | Dept:</b> <i>{{ $star->ministry ?? 'N/A' }}</i>
                    | Week ({{ $weekStart->format('M j') }}–{{ $weekEnd->format('M j') }})</p>
                </div>
                <div class="d-flex align-items-center justify-content-around mt-5">
                  <div class="d-flex flex-column gap-2">
                    <h4 class="mb-0">{{ $star->actual_devotions ?? 0 }}</h4>
                    <p class="mb-0">Devotion</p>
                  </div>
                  <div class="d-flex flex-column gap-2">
                    <h4 class="mb-0">{{ $star->actual_hours ?? 0 }}h</h4>
                    <p class="mb-0">Attendance Hours</p>
                  </div>
                  <div class="d-flex flex-column gap-2">
                    <h4 class="mb-0">{{ $star->actual_meetings ?? 0 }}</h4>
                    <p class="mb-0">Activity Att</p>
                  </div>
                </div>
                <hr>
              </div>
            </div>
          </div>
          <!-- Devotion Growth -->
          <div class="col-xl-6 col-xxl-8 d-flex align-items-stretch">
            <div class="card w-100 rounded-4">

              <div class="card-body">
                <div class="">
                      <h5 class="mb-0">Devotion report</h5>
                    </div>
                <div id="chart8"></div>
                <div class="d-flex align-items-center gap-3 mt-4">
                  <div class="">
                    <h1 class="mb-0">{{$currentDevotionPercent}}%</h1>
                  </div>
                  <div class="d-flex align-items-center align-self-end gap-2">
                    @php
                      // Determine sign, icon, and color based on completionGrowth
                      $isUp    = $completionGrowth >= 0;
                      $icon    = $isUp ? 'trending_up'   : 'trending_down';
                      $color   = $isUp ? 'text-success'  : 'text-danger';
                      $sign    = $isUp ? '+' : '';   // prefix “+” for positive values
                    @endphp

                    <div class="d-flex align-items-center gap-2">
                      <span class="material-icons-outlined {{ $color }}">{{ $icon }}</span>
                      <p class="mb-0 {{ $color }}">
                        {{ $sign }}{{ $completionGrowth }}%
                      </p>
                    </div>
                  </div>
                </div>
                <p class="mb-4">Devotion Growth</p>
                <script>
                        // make the whole array available
                        window.devotionCompletionPercentByCycle = @json($devotionCompletionPercentByCycle);

                        @for ($i = 0; $i <= 8; $i++)
                          // export each month’s click count separately
                          window.w=devotionWeek{{ $i }} = @json($devotionCompletionPercentByCycle[$i]);
                        @endfor
                      </script>
                <div class="d-flex flex-column gap-3">
                  <div class="">
                    <p class="mb-1">Post all devotions <span class="float-end">{{$currentFullEntryCount}}</span></p>
                    <div class="progress" style="height: 5px;">
                      <div class="progress-bar bg-grd-primary" style="width: {{$currentFullPercent}}%"></div>
                    </div>
                  </div>
                  <div class="">
                    <p class="mb-1">Post at least 6 <span class="float-end">{{$currentSixPlusEntryCount}}</span></p>
                    <div class="progress" style="height: 5px;">
                      <div class="progress-bar bg-grd-warning" style="width: {{$currentSixPlusPercent}}%"></div>
                    </div>
                  </div>
                  <div class="">
                    <p class="mb-1">No devotion Post <span class="float-end">{{$currentZeroEntryCount}}</span></p>
                    <div class="progress" style="height: 5px;">
                      <div class="progress-bar bg-grd-info" style="width: {{$currentZeroPercent}}%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Attendee Volunteers -->
          <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
            <div class="card w-100 rounded-4 d-flex flex-column">
              <div class="card-header border-0 p-3 border-bottom">
                <div class="d-flex align-items-start justify-content-between">
                  <h5 class="mb-0"><i class="fadeIn animated bx bx-user me-1"></i> Recent Attendee</h5>
                  <div class="dropdown">
                    <a href="#" class="dropdown-toggle-nocaret options dropdown-toggle" data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </div>
                </div>
              </div>

              {{-- Scrollable body --}}
              <div class="card-body p-0 overflow-auto" style="max-height: 360px;">
                <div class="user-list p-3">
                  <div class="d-flex flex-column gap-3">
                    @forelse($recentAttendees as $att)
                      <div class="d-flex align-items-center gap-3">
                        <img
                          src="{{ $att->avatar_url ?: asset('images/avatar-default.png') }}"
                          width="45" height="45"
                          class="rounded-circle object-fit-cover"
                          alt="{{ $att->firstname ?? 'User' }}"
                        >

                        <div class="flex-grow-1 min-w-0">
                          <h6 class="mb-0 text-truncate">
                            {{ trim(($att->firstname ?? '').' '.($att->lastname ?? '')) ?: '—' }}
                          </h6>
                          <p class="mb-0 text-muted small text-truncate">
                            {{ $att->ministry ?? '—' }}
                          </p>
                        </div>

                        <span class="ms-auto me-0 d-inline-flex align-items-center">
                          {{-- online/offline dot (style below) --}}
                          <span class="presence-dot {{ ($att->is_online ?? false) ? 'online' : 'offline' }}"
                                title="{{ ($att->is_online ?? false) ? 'Online' : 'Offline' }}"></span>
                        </span>
                      </div>
                    @empty
                      <div class="text-center text-muted py-4">
                        No recent attendees.
                      </div>
                    @endforelse
                  </div>
                </div>
              </div>

              {{-- Sticky footer --}}
              <div class="card-footer bg-transparent p-3 mt-auto">
                <div class="d-flex align-items-center justify-content-between gap-3">
                  <a href="#" class="sharelink"><i class="material-icons-outlined">share</i></a>
                  <a href="#" class="sharelink"><i class="material-icons-outlined">textsms</i></a>
                  <a href="#" class="sharelink"><i class="material-icons-outlined">email</i></a>
                  <a href="#" class="sharelink"><i class="material-icons-outlined">attach_file</i></a>
                  <a href="#" class="sharelink"><i class="material-icons-outlined">event</i></a>
                </div>
              </div>
            </div>
          </div>

          <!-- Devices-->
          <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex flex-column gap-3">
                  <div class="d-flex align-items-start justify-content-between">
                    <div class="">
                      <h5 class="mb-0">Device Type</h5>
                    </div>
                    <div class="dropdown">
                      <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <span class="material-icons-outlined fs-5">more_vert</span>
                      </a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                        <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                        <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="position-relative">
                    <div class="piechart-legend">
                      <h2 class="mb-1">68%</h2>
                      <h6 class="mb-0">Total Views</h6>
                    </div>
                    <div id="chart6"></div>
                  </div>
                  <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <p class="mb-0 d-flex align-items-center gap-2 w-25"><span
                          class="material-icons-outlined fs-6 text-primary">desktop_windows</span>Desktop</p>
                      <div class="">
                        <p class="mb-0">35%</p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <p class="mb-0 d-flex align-items-center gap-2 w-25"><span
                          class="material-icons-outlined fs-6 text-danger">tablet_mac</span>Tablet</p>
                      <div class="">
                        <p class="mb-0">48%</p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <p class="mb-0 d-flex align-items-center gap-2 w-25"><span
                          class="material-icons-outlined fs-6 text-success">phone_android</span>Mobile</p>
                      <div class="">
                        <p class="mb-0">27%</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Total Click -->
          <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
            <div class="row">
              <!-- Total Click -->
              <div class="col-md-12 d-flex align-items-stretch">
                <div class="card w-100 rounded-4">
                  <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-1">
                      <div class="">
                        <h5 class="mb-0">{{ number_format($clicksByMonth[0] / 1000, 1) }}K</h5>
                        <p class="mb-0">Total Clicks this months</p>
                      </div>
                      <script>
                        // make the whole array available
                        window.clicksByMonth = @json($clicksByMonth);

                        @for ($i = 0; $i <= 8; $i++)
                          // export each month’s click count separately
                          window.w=month{{ $i }}Clicks = @json($clicksByMonth[$i]);
                        @endfor
                      </script>
                      <div class="dropdown">
                        <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                          data-bs-toggle="dropdown">
                          <span class="material-icons-outlined fs-5">more_vert</span>
                        </a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                          <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                          <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                        </ul>
                      </div>
                    </div>
                    <div class="chart-container2">
                      <div id="chart3"></div>
                    </div>
                    <div class="text-center">
                      @php
                        $signClick = $clickGrowth > 0 ? '+' : ($clickGrowth < 0 ? '' : '');
                        $signView  = $viewGrowth  > 0 ? '+' : ($viewGrowth  < 0 ? '' : '');
                      @endphp

                      <p class="mb-0 font-12">
                        <span class="{{ $clickGrowth >= 0 ? 'text-success me-1' : 'text-danger me-1' }}">
                          {{ $signClick }}{{ $clickGrowth }}%
                        </span>
                        from last month
                      </p>
                    </div>
                  </div>
                </div>
              </div>

            <!-- Total Views -->
                <div class="col-md-12 d-flex align-items-stretch">
                  <div class="card w-100 rounded-4">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between mb-1">
                        <div class="">
                          <h5 class="mb-0">{{ number_format($viewsByMonth[0] / 1000, 1) }}K</h5>
                          <p class="mb-0">Total Views this month</p>
                        </div>
                        <div class="dropdown">
                          <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                            data-bs-toggle="dropdown">
                            <span class="material-icons-outlined fs-5">more_vert</span>
                          </a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                          </ul>
                        </div>
                      </div>
                      <div class="chart-container2">
                        <div id="chart4"></div>
                      </div>
                      <div class="text-center">
                        <p class="mb-0 font-12">35K users increased from last month</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>

          <!--Computers-->
          <div class="col d-flex">
            <div class="card w-100 rounded-4">
              <div class="card-header p-3 bg-transparent">
                <div class="d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Computers in Use</h5>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle" data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </div>
                </div>
              </div>

              {{-- Make body p-0 so our inner .computer-list can pad itself --}}
              <div class="card-body p-0">
                {{-- Scrollable container --}}
                <div 
                  class="computer-list p-3" 
                  style="max-height: 420px; overflow-y: auto;"
                >
                  <div class="d-flex flex-column gap-3">
                    @foreach($computerStatuses as $c)
                      <div class="d-flex align-items-center gap-3">
                        <div class="font-22">
                          <i class="fadeIn animated bx bx-desktop"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-0">{{ $c['computer'] }}</h6>
                          @if($c['status'] === 'active')
                            <p class="mb-0">User : <i>{{ $c['user'] }}</i></p>
                          @else
                            <p class="mb-0">Last user : <i>{{ $c['user'] }}</i></p>
                          @endif
                        </div>
                        <div>
                          @if($c['status'] === 'active')
                            <span class="online-indicator" title="Currently clocked-in"></span>
                          @else
                            <span class="offline-indicator" title="Last used"></span>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
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
  <!--end footer-->

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



   {{-- Only include the modal markup when a scan just occurred --}}
  @if(session('clockSuccess') || session('clockError'))
    @php
      $error  = session('clockError');
      $token  = session('clockToken');
      $action = session('clockAction', 'timein');
    @endphp

    <!-- Clock-In/Out Confirmation Modal -->
    <div class="modal fade" id="clockModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
          @if($error)
            <h5 class="text-danger mb-3">Oops!</h5>
            <p>{{ $error }}</p>

            @if($token && $action === 'timein')
              <a href="{{ url('/scan') . '?token=' . $token . '&action=timeout' }}"
                 class="btn btn-warning mt-3">
                Clock Out Now
              </a>
              <hr>
            @endif

            <button type="button"
                    class="btn btn-secondary mt-2"
                    data-bs-dismiss="modal">
              Close
            </button>
          @else
            @php($info = session('clockSuccess'))
            @php($verb = $action === 'timeout' ? 'Clock-Out' : 'Clock-In')
            <h5 class="text-success mb-3">{{ $verb }} Successful!</h5>
            <p>
              You {{ strtolower($verb) }} on
              <strong>{{ $info['date'] }}</strong> at
              <strong>{{ $info['time'] }}</strong>.
            </p>
            {{-- Only show timer for Clock-In --}}
            @if($action === 'timein')
              <div id="timer" class="fs-2 mt-3"></div>
            @endif
            <button type="button"
                    class="btn btn-primary mt-3"
                    data-bs-dismiss="modal">
              OK
            </button>
          @endif
        </div>
      </div>
    </div>
  @endif

@push('scripts')
  {{-- Make sure Bootstrap’s JS bundle is loaded before this --}}

  @if(session('clockSuccess') || session('clockError'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalEl = document.getElementById('clockModal');
      if (!modalEl) return;
      const bsModal = new bootstrap.Modal(modalEl);
      bsModal.show();

      @if(session('clockSuccess') && session('clockAction') === 'timein')
        // Start live timer only on Clock-In
        const start = new Date();
        const lbl   = document.getElementById('timer');
        setInterval(() => {
          const s   = Math.floor((new Date() - start) / 1000);
          const h   = String(Math.floor(s/3600)).padStart(2,'0'),
                m   = String(Math.floor((s%3600)/60)).padStart(2,'0'),
                sec = String(s%60).padStart(2,'0');
          lbl.textContent = `${h}:${m}:${sec}`;
        }, 1000);
      @endif
    });
  </script>
  @endif

<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
(function () {
  const modalEl   = document.getElementById('ScannerModal'); // <-- your scanner modal id
  const container = document.getElementById('scanner-container');
  const video     = document.getElementById('video');
  const canvas    = document.getElementById('canvas');
  const ctx       = canvas.getContext('2d');

  let stream = null;
  let scanning = false;
  let rafId = null;

  async function startCamera() {
    // Try rear camera first; fall back to any camera
    const constraintsPrimary = { video: { facingMode: { ideal: 'environment' } }, audio: false };
    const constraintsFallback = { video: true, audio: false };

    try {
      stream = await navigator.mediaDevices.getUserMedia(constraintsPrimary);
    } catch (e) {
      console.warn('Rear camera not available, falling back to default:', e);
      stream = await navigator.mediaDevices.getUserMedia(constraintsFallback);
    }
    video.srcObject = stream;

    // iOS/Safari love this
    await video.play();
    if (video.readyState < 2) {
      await new Promise(res => video.addEventListener('loadedmetadata', res, { once: true }));
    }
  }

  function stopCamera() {
    if (rafId) cancelAnimationFrame(rafId);
    scanning = false;
    if (stream) {
      stream.getTracks().forEach(t => t.stop());
      stream = null;
    }
    video.srcObject = null;
  }

  function scanLoop() {
    if (!scanning) return;

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
      // compute center crop each frame (based on real video size)
      const vw = video.videoWidth;
      const vh = video.videoHeight;
      if (vw && vh) {
        const sx = vw * 0.25;
        const sy = vh * 0.25;
        const sw = vw * 0.5;
        const sh = vh * 0.5;

        ctx.drawImage(video, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code && code.data) {
          stopCamera();
          // hide modal if you want it to close after detection:
          const bsModal = bootstrap.Modal.getInstance(modalEl);
          if (bsModal) bsModal.hide();
          window.location.href = `/scan?token=${encodeURIComponent(code.data)}`;
          return;
        }
      }
    }
    rafId = requestAnimationFrame(scanLoop);
  }

  async function startScan() {
    try {
      container.style.display = 'block';
      await startCamera();
      scanning = true;
      scanLoop();
    } catch (err) {
      console.error('Camera error:', err);
      alert('Unable to access camera. Please allow camera permission and use HTTPS.');
    }
  }

  // Hook into Bootstrap modal lifecycle
  modalEl.addEventListener('shown.bs.modal', startScan);
  modalEl.addEventListener('hidden.bs.modal', () => {
    container.style.display = 'none';
    stopCamera();
  });

  // If you STILL want the old #launchScanner button to work (optional):
  const legacyBtn = document.getElementById('launchScanner');
  if (legacyBtn) {
    legacyBtn.addEventListener('click', () => {
      // open the modal programmatically to keep one code path
      const m = new bootstrap.Modal(modalEl);
      m.show();
    });
  }
})();
</script>

    <script>
    $('.message .close').on('click', function () {
        $(this).closest('.message').transition('fade');
    });
</script>
