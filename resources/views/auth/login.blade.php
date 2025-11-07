<!doctype html>
@php use App\Classes\permission; @endphp
<html lang="{{ app()->getLocale() }}" data-bs-theme="blue-theme">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- No user context on login page --}}
  <title>Glory Servant - Volunteer Management syste</title>
  <!--favicon-->
  <link rel="icon" href="/assets2/images/logo-icon.png" type="image/png">
  <!-- loader (JS removed to avoid CSP inline-style violations) -->
  <link href="/assets2/css/pace.min.css" rel="stylesheet">

  <!--plugins-->
  <link href="/assets2/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/simplebar/css/simplebar.css">
  <!--bootstrap css-->
  <link href="/assets2/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets2/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  {{-- Removed external CDNs on auth page to satisfy CSP/Zero-Trust --}}
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">
  <link href="/assets2/plugins/bs-stepper/css/bs-stepper.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/plugins/notifications/css/lobibox.min.css">
  <!--main css-->
  <link href="/assets2/css/bootstrap-extended.css" rel="stylesheet">
  <link href="/sass/main.css" rel="stylesheet">
  <link href="/sass/dark-theme.css" rel="stylesheet">
  <link href="/sass/blue-theme.css" rel="stylesheet">
  <link href="/sass/semi-dark.css" rel="stylesheet">
  <link href="/sass/bordered-theme.css" rel="stylesheet">
  <link href="/sass/responsive.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">

  <style nonce="{{ $cspNonce ?? '' }}">
    .online-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin-left: 6px;
      border-radius: 50%;
      background-color: #4ac96c; /* Bootstrap’s “success” green */
      vertical-align: middle;
    }
    .offline-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin-left: 6px;
      border-radius: 50%;
      background-color: gray; /* Bootstrap’s “success” green */
      vertical-align: middle;
    }
  </style>
</head>

<body>

	
    <div class="auth-basic-wrapper d-flex align-items-center justify-content-center">
      <div class="container-fluid my-5 my-lg-0">
        <div class="row">
           <div class="col-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
            <div class="card rounded-4 mb-0 border-top border-4 border-primary border-gradient-1">
              <div class="card-body p-5">
                  <img src="assets2/images/logo1.png" class="mb-4" width="145" alt="">
                  <h4 class="fw-bold">Get Started Now</h4>
                  <p class="mb-0">Enter your credentials to login your account</p>

                  <div class="form-body my-5">
                                        <form class="row g-3 form" action="{{ route('login', [], false) }}" method="POST" autocomplete="on">
                                            @csrf
                                            <div class="col-12">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" id="email" placeholder="john@example.com" required autocomplete="username" inputmode="email">
                                            </div>
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" class="form-control border-end-0" name="password" id="password" placeholder="Enter Password" required autocomplete="current-password">
                                                    <button type="button" class="input-group-text bg-transparent password-toggle" aria-label="Show password"><i class="bi bi-eye-slash-fill" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="remember">Remember Me</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end"> <a href="#">Forgot Password ?</a>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-grd-primary">Login</button>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="text-start">
                                                    <!--<p class="mb-0">Don't have an account yet? <a href="#">Sign up here</a>-->
                                                    </p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                  <div class="separator section-padding">
                    <div class="line"></div>
                    <p class="mb-0 fw-bold">OR SIGN IN WITH</p>
                    <div class="line"></div>
                  </div>

                  <div class="d-flex gap-3 justify-content-center mt-4">
                    <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-danger">
                      <i class="bi bi-google fs-5 text-white"></i>
                    </a>
                    <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-deep-blue">
                      <i class="bi bi-facebook fs-5 text-white"></i>
                    </a>
                    <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-info">
                      <i class="bi bi-linkedin fs-5 text-white"></i>
                    </a>
                    <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-royal">
                      <i class="bi bi-github fs-5 text-white"></i>
                    </a>
                  </div>

              </div>
            </div>
           </div>
        </div><!--end row-->
     </div>
    </div>

    <script src="{{ asset('assets3/js/auth-login.js') }}" defer></script>
</body>

</html>
