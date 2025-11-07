<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Glory Servant - Volunteer Management system</title>
  <!--favicon-->
	<link rel="icon" href="assets2/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
	<link href="assets2/css/pace.min.css" rel="stylesheet">
	<script src="assets2/js/pace.min.js"></script>

  <!--plugins-->
  <link href="assets2/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets2/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="assets2/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="assets2/plugins/simplebar/css/simplebar.css">
  <!--bootstrap css-->
  <link href="assets2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <link href="assets2/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets2/css/extra-icons.css">
  <link rel="stylesheet" href="/assets2/plugins/notifications/css/lobibox.min.css">
  <link href="/assets2/plugins/bs-stepper/css/bs-stepper.css" rel="stylesheet">
  <!--main css-->
  <link href="assets2/css/bootstrap-extended.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/semi-dark.css" rel="stylesheet">
  <link href="sass/bordered-theme.css" rel="stylesheet">
  <link href="sass/responsive.css" rel="stylesheet">

</head>

<body>
<!--start header-->
  @include ('admin/nav')
  <!--end top header-->

    <!--start sidebar-->
  @include ('admin/sidebar')
  <!--end top sidebar-->



  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Inventory</div>
        <div class="ps-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
              <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Equipments</li>
            </ol>
          </nav>
        </div>
        <div class="ms-auto">
            <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal"
                  data-bs-target="#FormModal"><i class="bi bi-plus-lg me-2"></i>Add Equipment</button>
        </div>
      </div>
      <!--end breadcrumb-->

      <div class="card mt-4">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example2" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th><i class="fadeIn animated bx bx-images"></i></th>
                    <th>Name</th>
                    <th>Serial #</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Date Acquired</th>
                    <th>Action</th>
                  </tr>
                </thead> 
                <tbody> 
                  @foreach($equipment as $item)
                    <tr>
                      <td><img src="assets2/images/equipment_photos/{{ $item->photo }}" width="48" height="32"></td>
                      <td>{{ $item->name }}</td>
                      <td>{{ $item->serial_number }}</td>
                      <td>{{ $item->category }}</td>
                      <td>{{ $item->location }}</td>
                      <td>
                        <span class="badge bg-{{ $item->status == 'Active' ? 'success' : 'secondary' }}">
                          {{ $item->status }}
                        </span>
                      </td>
                      <td>{{ $item->acquired_at->format('M d, Y') }}</td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-filter dropdown-toggle dropdown-toggle-nocaret"
                                  type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="">Edit</a></li>
                            <li>
                              <form action="{{ route('admin.inventory-equipment.destroy', $item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button class="dropdown-item text-danger" type="submit">
                                  Delete
                                </button>
                              </form>
                            </li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                
              </table>
            </div>
          </div>
        </div>
        <!-- Modal -->
                <div class="modal fade" id="FormModal">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header border-bottom-0 py-2 bg-grd-info">
                        <h5 class="modal-title">Registration Form</h5>
                        <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                          <i class="material-icons-outlined">close</i>
                        </a>
                      </div>
                      <div class="modal-body">
                        <div class="form-body">
                            <form action="{{ route('admin.inventory-equipment.store') }}" method="POST" enctype="multipart/form-data">
                              @csrf
                              <div class="row">
                                <!-- main form -->
                                <div class="">
                                  <div class="card mb-4">
                                    <div class="card-body">
                                      <div class="mb-4">
                                        <label for="name" class="form-label">Equipment Name</label>
                                        <input name="name" id="name" type="text" class="form-control" placeholder="e.g. Projector X200" required>
                                      </div>

                                      <div class="mb-4">
                                        <label for="serial_number" class="form-label">Serial Number</label>
                                        <input name="serial_number" id="serial_number" type="text" class="form-control" placeholder="e.g. SN-12345" required>
                                      </div>

                                      <div class="mb-4">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Describe the equipment..."></textarea>
                                      </div>
                                      {{-- preview & cropping canvas --}}
                                      <div class="mb-4" >
                                        <img id="preview" style="max-width:30%; display:none; margin: 0 auto;">
                                      </div>
                                      <div class="mb-4">
                                        <label for="photo" class="form-label">Upload Photo</label>
                                        <input name="photo" id="photo" type="file" class="form-control" accept="image/*">
                                      </div>
                                      

                                      <!-- Details & Status -->
                                      <div class="mb-4">
                                        <h5 class="mb-3">Details & Status</h5>
                                        <div class="row mb-4">
                                          <div class="mb-4">
                                            <label for="category" class="form-label">Category</label>
                                            <select name="category" id="category" class="form-select" required>
                                              <option value="" disabled selected>Choose category…</option>
                                                @foreach($categories as $cat)
                                                  <option value="{{ $cat }}">{{ $cat }}</option>
                                                @endforeach
                                            </select>
                                          </div>

                                          <div class="mb-4">
                                            <label for="location" class="form-label">Location (Campus)</label>
                                            <select name="location" id="location" class="form-select" required>
                                              <option value="" disabled selected>Select location</option>
                                              @foreach($campuses as $comp)
                                                <option value="{{ $comp->campus }}">{{ $comp->campus }}</option>
                                              @endforeach
                                            </select>
                                          </div>

                                          <div class="mb-4">
                                            <label for="name" class="form-label">Specific location</label>
                                            <input name="spec_loc" id="spec_loc" type="text" class="form-control" placeholder="e.g. Server Room" required>
                                          </div>

                                          <div class="mb-4">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-select">
                                              <option value="Active">Active</option>
                                              <option value="In Repair">In Repair</option>
                                              <option value="Retired">Retired</option>
                                            </select>
                                          </div>
                                        </div>
                                      </div>

                                      <!-- Acquisition & Quantity -->
                                      <div class="mb-4">
                                        <h5 class="mb-3">Acquisition</h5>
                                        <div class="row g-3">
                                          <div class="col-md-6">
                                            <label for="acquired_at" class="form-label">Date Acquired</label>
                                            <input name="acquired_at" id="acquired_at" type="date" class="form-control">
                                          </div>
                                          <div class="col-md-6">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <input name="quantity" id="quantity" type="number" class="form-control" min="1" value="1">
                                          </div>
                                        </div>
                                      </div>

                                      <!-- Cost -->
                                      <div class="mb-4">
                                        <label for="cost" class="form-label">Cost (per unit)</label>
                                        <input name="cost" id="cost" type="number" step="0.01" class="form-control" placeholder="0.00">
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                
                              <div class="col-md-12">
                              <div class="d-md-flex d-grid align-items-center gap-3">
                                <button type="submit" class="btn btn-grd-danger px-4">Submit</button>
                                <button type="button" data-bs-dismiss="modal" class="btn btn-grd-info px-4">cancel</button>
                              </div>
                              </div>
                            </form>
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
    <!--top footer-->




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
  <div id="flash-data" hidden data-success="{{ session('success') }}"></div>
{{-- include Cropper.js --}}
<link  href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet"/>
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>

<script src="{{ asset('assets3/js/admin-inventory-equipment.js') }}" defer></script>
  <!--bootstrap js-->
  <script src="assets2/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="assets2/js/jquery.min.js"></script>
  <!--plugins-->
  <script src="assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="assets2/plugins/metismenu/metisMenu.min.js"></script>
  <script src="assets2/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="assets2/js/main.js"></script>
  <script src="assets2/plugins/datatable/js/jquery.dataTables.min.js"></script>
  <script src="assets2/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
  <script src="/assets2/plugins/bs-stepper/js/bs-stepper.min.js"></script>
  <script src="/assets2/plugins/bs-stepper/js/main.js"></script>
  <script src="/assets2/plugins/notifications/js/lobibox.min.js"></script>
  <script src="/assets2/plugins/notifications/js/notifications.min.js"></script>
  <script src="/assets2/plugins/notifications/js/notification-custom-script.js"></script>
  


</body>

</html>
