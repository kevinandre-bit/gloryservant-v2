 <!--start header-->
  @include ('layouts/admin')
  <!--end top header-->

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
      <div class="row mb-3">
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Volunteers</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Attendance</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<div class="btn-group">
							<button type="button" class="btn btn-primary">Settings</button>
							<button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
								<a class="dropdown-item" href="javascript:;">View web Clock</a>
								<a class="dropdown-item" href="javascript:;">Manual entry</a>
								
							</div>
						</div>
					</div>
				</div>
				<!--end breadcrumb-->
      
				<hr>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>{{ __('Date') }}</th>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Time In') }}</th>
                                <th>{{ __('Time Out') }}</th>
                                <th>{{ __('Total Hours') }}</th>
                                <th>{{ __('Status (In/Out)') }}</th>
                                {{-- Conditionally display Comment column if enabled --}}
                                @isset($ss)
                                    @if($ss->clock_comment == "on")
                                        <th>Comment</th>
                                    @endif
                                @endisset
                                <th>Options</th>
									</tr>
								</thead>
								<tbody>
									{{-- Populate table with attendance records --}}
                            @isset($data)
                            @foreach ($data as $d)
                            <tr>
                                <td>{{ $d->date }}</td>
                                <td>{{ $d->employee }}</td>
                                <td>
                                    {{-- Format Time In depending on 12h or 24h format --}}
                                    @php 
                                        if($ss->time_format == 1) {
                                            echo e(date('h:i:s A', strtotime($d->timein)));
                                        } else {
                                            echo e(date('H:i:s', strtotime($d->timein)));
                                        }
                                    @endphp
                                </td>
                                <td>
                                    {{-- Format Time Out if exists --}}
                                    @isset($d->timeout)
                                        @php 
                                            if($ss->time_format == 1) {
                                                echo e(date('h:i:s A', strtotime($d->timeout)));
                                            } else {
                                                echo e(date('H:i:s', strtotime($d->timeout)));
                                            }
                                        @endphp
                                    @endisset
                                </td>
                                <td>
                                    {{-- Display total working hours in hr/min format --}}
                                    @isset($d->totalhours)
                                        @if($d->totalhours != null) 
                                            @php
                                                if(stripos($d->totalhours, ".") === false) {
                                                    $h = $d->totalhours;
                                                } else {
                                                    $HM = explode('.', $d->totalhours); 
                                                    $h = $HM[0]; 
                                                    $m = $HM[1];
                                                }
                                            @endphp
                                        @endif
                                        @if($d->totalhours != null)
                                            @if(stripos($d->totalhours, ".") === false) 
                                                {{ $h }} hr
                                            @else 
                                                {{ $h }} hr {{ $m }} mins
                                            @endif
                                        @endif
                                    @endisset
                                </td>
                                <td>
                                    {{-- Status for Time In / Time Out with color coding --}}
                                    @if($d->status_timein != null OR $d->status_timeout != null) 
                                        <span class="@if($d->status_timein == 'Late In') orange @else blue @endif">{{ $d->status_timein }}</span> / 
                                        @isset($d->status_timeout) 
                                            <span class="@if($d->status_timeout == 'Early Out') red @else green @endif">
                                                {{ $d->status_timeout }}
                                            </span> 
                                        @endisset
                                    @else
                                        <span class="blue">{{ $d->status_timein }}</span>
                                    @endif 
                                </td>
                                {{-- Optional comment display --}}
                                @isset($ss)
                                    @if($ss->clock_comment == "on")
                                        <td>{{ $d->comment }}</td>
                                    @endif
                                @endisset
                                <td class="align-right">
                                    {{-- Edit and Delete buttons --}}
                                    <button
                                      type="button"
                                      class="btn  gap-2"
                                      data-bs-toggle="modal"
                                      data-bs-target="#FormModal"
                                      data-id="{{ $d->id }}"
                                      data-idno="{{ $d->idno }}"
                                      data-name="{{ $d->employee }}"
                                      data-date="{{ $d->date }}"
                                      data-timein_date="{{ \Carbon\Carbon::parse($d->timein)->format('Y-m-d') }}"
                                      data-timein="{{ \Carbon\Carbon::parse($d->timein)->format('H:i') }}"
                                      data-timeout_date="{{ $d->timeout ? \Carbon\Carbon::parse($d->timeout)->format('Y-m-d') : '' }}"
                                      data-timeout="{{ $d->timeout ? \Carbon\Carbon::parse($d->timeout)->format('H:i') : '' }}"
                                      data-reason="{{ addslashes($d->reason) }}"
                                    >
                                      <i class="text-white" data-feather="edit"></i>
                                    </button>
                                    <a href="{{ url('/attendance/delete/'.$d->id) }}" class="ui circular basic icon button tiny"><i class="text-primary" data-feather="trash"></i></a>
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
    <!-- Modal -->
    <div class="modal fade" id="FormModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-bottom-0 py-2 bg-grd-info">
            <h5 class="modal-title">Edit volunteer Attendance</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="attendanceForm" class="row g-3" method="POST" action="{{ url('attendance/update') }}">
              @csrf
              <!-- encrypted id -->
              <input type="hidden" name="id" id="modalId">

              <!-- idno if you need it -->
              <input type="hidden" name="idno" id="modalIdno">

              <div class="col-md-6">
                <label class="form-label">Volunteer</label>
                <input type="text" class="form-control" name="name" id="modalName" readonly>
              </div>

              <!-- Time In Date -->
              <div class="col-md-6">
                <label class="form-label">Date</label>
                <input
                  type="date"
                  class="form-control datepicker"
                  name="timein_date"
                  id="modalDate"
                  readonly
                  required
                >
              </div>

              <!-- Time In Time -->
              <div class="col-md-6">
                <label class="form-label">Time In</label>
                <input type="time" class="form-control time-picker" name="timein" value="{{ $d->id }}" id="modalTimeIn" required>
              </div>

              <!-- Time Out Date (hidden or readonly) -->
              <input
                type="date"
                class="form-control d-none"
                name="timeout_date"
                id="modalTimeoutDate"
              >

              <!-- Time Out Time -->
              <div class="col-md-6">
                <label class="form-label">Time Out</label>
                <input type="time" class="form-control time-picker" name="timeout" id="modalTimeOut" required>
              </div>


              <div class="col-md-12">
                <label class="form-label">Reason</label>
                <textarea class="form-control" name="reason" id="modalReason" rows="2"></textarea>
              </div>

              <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
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
 

  
  <script src="{{ asset('assets3/js/admin-attendance.js') }}" defer></script>

</body>

</html>
