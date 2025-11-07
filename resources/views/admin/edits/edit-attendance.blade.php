//@extends('layouts.admin_v2')      {{-- this pulls in the HTML <head>, header, footer, etc --}}
@include('admin.nav')                {{-- if nav lives outside your main layout --}}
@include('admin.sidebar2')

@section('content')

<h1>ANmweeyyyyy</h1>
    <div class="">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-title">{{ __('Edit Attendance') }}</h2>
            </div>    
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
              <div class="card">
                <div class="card-header">
                  <h5 class="mb-0">Edit Attendance</h5>
                </div>
                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif

                  <form id="edit_attendance_form" action="{{ url('attendance/update') }}" method="POST">
                    @csrf

                    @if($a->timeout != null)
                      <div class="row mb-3">
                        <div class="col-sm-6">
                          <label class="form-label">{{ __('Employee') }}</label>
                          <input type="text" name="employee" readonly class="form-control-plaintext" value="@isset($a->employee){{ $a->employee }}@endisset">
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label">{{ __('Date') }}</label>
                          <input type="text" name="date" readonly class="form-control-plaintext" value="@isset($a->date){{ $a->date }}@endisset">
                        </div>
                      </div>
                    @else
                      <div class="mb-3">
                        <label class="form-label">{{ __('Employee') }}</label>
                        <input type="text" name="employee" readonly class="form-control-plaintext" value="@isset($a->employee){{ $a->employee }}@endisset">
                      </div>
                    @endif

                    <div class="row mb-3">
                      <div class="col-sm-6">
                        <label class="form-label">{{ __('Time In') }}</label>
                        @isset($a->timein)
                          @php
                            if($tf == 1) {
                              $t_in = date("h:i:s A", strtotime($a->timein));
                            } else {
                              $t_in = date("H:i:s", strtotime($a->timein));
                            }
                            $t_in_date = date("m/d/Y", strtotime($a->timein));
                          @endphp
                        @endisset
                        <input type="hidden" name="timein_date" value="@isset($t_in_date){{ $t_in_date }}@endisset">
                        <input type="text" name="timein" class="form-control" placeholder="00:00:00 AM" value="@isset($t_in){{ $t_in }}@endisset">
                      </div>

                      @if($a->timeout == null)
                        <div class="col-sm-6">
                          <label class="form-label">{{ __('Time In Date') }}</label>
                          <input type="text" name="date" readonly class="form-control-plaintext" value="@isset($a->date){{ $a->date }}@endisset">
                        </div>
                      @endif
                    </div>

                    <div class="row mb-3">
                      <div class="col-sm-6">
                        <label class="form-label">{{ __('Time Out') }}</label>
                        @isset($a->timeout)
                          @php
                            if($tf == 1) {
                              $t_out = date("h:i:s A", strtotime($a->timeout));
                            } else {
                              $t_out = date("H:i:s", strtotime($a->timeout));
                            }
                            $t_out_date = date("m/d/Y", strtotime($a->timeout));
                          @endphp
                        @endisset
                        <input type="hidden" name="timeout_date" value="@if($a->timeout){{ $t_out_date }}@endif">
                        <input type="text" name="timeout" class="form-control" placeholder="00:00:00 AM" value="@if($a->timeout){{ $t_out }}@endif">
                      </div>

                      @if($a->timeout == null)
                        <div class="col-sm-6">
                          <label class="form-label">{{ __('Time Out Date') }}</label>
                          <input type="text" name="timeout_date" class="form-control" placeholder="MM/DD/YYYY">
                        </div>
                      @endif
                    </div>

                    <div class="mb-3">
                      <label class="form-label">{{ __('Reason') }}</label>
                      <textarea name="reason" rows="5" class="form-control">@isset($a->reason){{ $a->reason }}@endisset</textarea>
                    </div>

                    <input type="hidden" name="id" value="@isset($e_id){{ $e_id }}@endisset">
                    <input type="hidden" name="idno" value="@isset($a->idno){{ $a->idno }}@endisset">

                    <div class="d-flex justify-content-end gap-2">
                      <button type="submit" class="btn btn-success"><i class="bi bi-check2"></i> {{ __('Update') }}</button>
                      <a href="{{ url('attendance') }}" class="btn btn-secondary"><i class="bi bi-x"></i> {{ __('Cancel') }}</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </div>
    </div>

    @endsection

    <!--bootstrap js-->
  <script src="/assets/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="/assets/js/jquery.min.js"></script>
  <!--plugins-->
  <script src="/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="/assets/plugins/metismenu/metisMenu.min.js"></script>
  <script src="/assets/plugins/validation/jquery.validate.min.js"></script>
    <script src="/assets/plugins/validation/validation-script.js"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
            (function () {
              'use strict'
    
              // Fetch all the forms we want to apply custom Bootstrap validation styles to
              var forms = document.querySelectorAll('.needs-validation')
    
              // Loop over them and prevent submission
              Array.prototype.slice.call(forms)
                .forEach(function (form) {
                  form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                      event.preventDefault()
                      event.stopPropagation()
                    }
    
                    form.classList.add('was-validated')
                  }, false)
                })
            })()
    </script>
  <script src="/assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="/assets/js/main.js"></script>
