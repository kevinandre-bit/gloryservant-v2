{{-- resources/views/clock.blade.php --}}
@extends('layouts.clock')

@section('head')
  {{-- ensure CSRF token meta --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
  <div class="fixedcenter">
    <div class="clockwrapper">
      <div class="clockinout">
        <button class="btnclock timein active" data-type="timein">{{ __("Time In") }}</button>
        <button class="btnclock timeout" data-type="timeout">{{ __("Time Out") }}</button>
      </div>
    </div>

    <div class="clockwrapper">
      <div class="timeclock">
        <span id="show_day"  class="clock-text"></span>
        <span id="show_time" class="clock-time"></span>
        <span id="show_date" class="clock-day"></span>
      </div>
    </div>

    <div class="clockwrapper">
      <div class="userinput">
        {{-- prevent default GET on Enter --}}
        <form class="ui form">
          @isset($cc)
            @if($cc==='on')
              <div class="inline field comment">
                <textarea name="comment" class="uppercase lightblue" rows="1"
                          placeholder="Enter comment"></textarea>
              </div>
            @endif
          @endisset

          <div class="inline field">
            <input @if($rfid==='on') id="rfid" @endif
                   class="enter_idno uppercase @if($rfid==='on') mr-0 @endif"
                   name="idno" type="text"
                   placeholder="{{ __('ENTER YOUR ID') }}"
                   required autofocus>

            @if($rfid!=='on')
              <button id="btnclockin" type="button"
                      class="ui positive large icon button">
                {{ __("Confirm") }}
              </button>
            @endif

            <input type="hidden" id="_url" value="{{ url('') }}">
          </div>
        </form>
      </div>
    </div>

    <div class="message-after">
      <p>
        <span id="greetings">{{ __("Welcome!") }}</span>
        <span id="fullname"></span>
      </p>
      <p id="messagewrap">
        <span id="type"></span>
        <span id="message"></span>
        <span id="time"></span>
      </p>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  {{-- Use local Moment assets from layout; only load SweetAlert2 CDN (CSP-allowlisted) --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- inject timezone for JS --}}
  <script nonce="{{ $cspNonce ?? '' }}"> var timezone = @json($tz); </script>

  <script nonce="{{ $cspNonce ?? '' }}" type="text/javascript">
  // prevent full-page submit on Enter
  $('form.ui.form').on('submit', function(e){ e.preventDefault(); });

  var elTime = document.getElementById('show_time'),
      elDate = document.getElementById('show_date'),
      elDay  = document.getElementById('show_day');

  function setTime(){
    var now = moment().tz(timezone);
    @if($tf==1)
      elTime.innerHTML = now.format("hh:mm:ss A");
    @else
      elTime.innerHTML = now.format("HH:mm:ss");
    @endif
    elDate.innerHTML = now.format('MMMM D, YYYY');
    elDay.innerHTML  = now.format('dddd');
  }
  setTime();
  setInterval(setTime,1000);

  $('.btnclock').click(function(){
    if($(this).data('type')==='timein'){
      $('.comment').slideDown(200).show();
    } else {
      $('.comment').slideUp(200);
    }
    $('input[name="idno"]').focus();
    $('.btnclock').removeClass('active animated fadeIn');
    $(this).addClass('active animated fadeIn');
  });

  // Show a modal that, upon Allow, triggers the browser's location prompt
  function acquirePositionWithPrompt(){
    return new Promise(function(resolve, reject){
      if(!('geolocation' in navigator)){
        Swal.fire('Geolocation Not Supported','Please use a compatible browser.','error');
        return reject(new Error('geolocation_unsupported'));
      }

      function requestPosition(){
        navigator.geolocation.getCurrentPosition(function(pos){
          resolve(pos);
        }, function(err){
          if (err && (err.code === 1 || err.code === err.PERMISSION_DENIED)) {
            Swal.fire({
              icon: 'warning',
              title: 'Location Blocked',
              html: 'Location is blocked for this site.<br>Allow it in your browser\'s address bar or Site Settings, then try again.',
              showCancelButton: true,
              confirmButtonText: 'Try Again',
              cancelButtonText: 'Cancel'
            }).then(r => { if (r.isConfirmed) requestPosition(); else reject(err); });
          } else {
            Swal.fire('Location Unavailable','Unable to get your location. Please try again.','info')
              .then(() => reject(err || new Error('geolocation_unavailable')));
          }
        }, { enableHighAccuracy: false, timeout: 15000, maximumAge: 0 });
      }

      if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'geolocation' }).then(function(status){
          if (status.state === 'granted') {
            requestPosition();
          } else if (status.state === 'prompt') {
            Swal.fire({
              icon: 'info',
              title: 'Allow Location?',
              text: 'We use your location to verify on‑site clock‑ins.',
              showCancelButton: true,
              confirmButtonText: 'Allow Location',
              cancelButtonText: 'Not Now'
            }).then(r => { if (r.isConfirmed) requestPosition(); else reject(new Error('user_cancelled')); });
          } else { // denied
            Swal.fire({
              icon: 'warning',
              title: 'Location Blocked',
              html: 'Location is blocked for this site.<br>Open Site Settings and enable Location for this domain, then press Try Again.',
              showCancelButton: true,
              confirmButtonText: 'Try Again',
              cancelButtonText: 'Cancel'
            }).then(r => { if (r.isConfirmed) requestPosition(); else reject(new Error('permission_denied')); });
          }
        }).catch(function(){
          Swal.fire({
            icon: 'info', title: 'Allow Location?', text: 'We use your location to verify on‑site clock‑ins.',
            showCancelButton: true, confirmButtonText: 'Allow Location', cancelButtonText: 'Not Now'
          }).then(r => { if (r.isConfirmed) requestPosition(); else reject(new Error('user_cancelled')); });
        });
      } else {
        Swal.fire({
          icon: 'info', title: 'Allow Location?', text: 'We use your location to verify on‑site clock‑ins.',
          showCancelButton: true, confirmButtonText: 'Allow Location', cancelButtonText: 'Not Now'
        }).then(r => { if (r.isConfirmed) requestPosition(); else reject(new Error('user_cancelled')); });
      }
    });
  }

  // For timeout: try to read location only if already granted, otherwise skip silently
  function getPositionIfGranted(){
    return new Promise(function(resolve){
      if(!('geolocation' in navigator)) return resolve(null);
      if(!(navigator.permissions && navigator.permissions.query)) return resolve(null);
      navigator.permissions.query({ name: 'geolocation' }).then(function(status){
        if(status.state === 'granted'){
          navigator.geolocation.getCurrentPosition(function(pos){ resolve(pos); }, function(){ resolve(null); }, { maximumAge: 30_000 });
        } else {
          resolve(null);
        }
      }).catch(function(){ resolve(null); });
    });
  }

  function submitClockIn(idno){
    var url     = $('#_url').val(),
        type    = $('.btnclock.active').data('type'),
        comment = $('textarea[name="comment"]').val();

    function doSubmit(pos){
      $.ajax({
        url: url+'/attendance/addWebApp',
        type: 'post',
        dataType: 'json',
        data: {
          idno:            idno,
          type:            type,
          clockin_comment: comment,
          latitude:        pos ? pos.coords.latitude  : undefined,
          longitude:       pos ? pos.coords.longitude : undefined
        },
        headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
        success: function(res){
          if(res.error){
            console.error('Clock-in error payload:',res);
            $('.message-after').addClass('notok').show().removeClass('ok');
            $('#fullname').text(res.employee);
            $('#message').text(res.error);
            $('#type,#time').hide();
          } else {
            $('.message-after').addClass('ok').show().removeClass('notok');
            function typeText(c){ return c==='timein' ? "{{ __('Time In at') }}" : "{{ __('Time Out at') }}"; }
            $('#type').text(typeText(res.type)).show();
            $('#fullname').text(res.firstname+' '+res.lastname);
            $('#message').text('');
            $('#time').html('<span id="clocktime">'+res.time+'</span>.'+
                            '<span id="clockstatus">{{ __("Success!") }}</span>').show();
          }
        },
        error: function(xhr,status,err){
          var msg = xhr.responseJSON?.error || xhr.responseText || "Something went wrong while clocking in.";
          console.error('Clock-in AJAX failed:',status,err,xhr.responseText);
          Swal.fire("Error", msg, "error");
        }
      });
    }

    if (type === 'timeout') {
      // Do not prompt; only use coordinates if already granted
      getPositionIfGranted().then(function(pos){ doSubmit(pos); });
    } else {
      acquirePositionWithPrompt().then(function(pos){ doSubmit(pos); })
        .catch(function(){ /* user cancelled or blocked; UI already handled */ });
    }
  }

  @if($rfid==='on')
    $('#rfid').on('input',function(){
      let idno = $(this).val().toUpperCase();
      setTimeout(()=>$(this).val(''),600);
      submitClockIn(idno);
    });
  @else
    $('#btnclockin').click(function(){
      let idno = $('input[name="idno"]').val().toUpperCase();
      submitClockIn(idno);
    });
  @endif
  </script>
@endsection
