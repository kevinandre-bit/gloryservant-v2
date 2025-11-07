<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maxton | Bootstrap 5 Admin Dashboard Template</title>
  <!--favicon-->
	<link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet">
	<script src="assets/js/pace.min.js"></script>

  <!--plugins-->
  <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets/plugins/metismenu/metisMenu.min.css">
  <link href="assets/plugins/fancy-file-uploader/fancy_fileupload.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="assets/plugins/simplebar/css/simplebar.css">
  <!--bootstrap css-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
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
					<div class="breadcrumb-title pe-3">Components</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Starter Page</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<div class="btn-group">
							<button type="button" class="btn btn-primary">Settings</button>
							<button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">Action</a>
								<a class="dropdown-item" href="javascript:;">Another action</a>
								<a class="dropdown-item" href="javascript:;">Something else here</a>
								<div class="dropdown-divider"></div>	<a class="dropdown-item" href="javascript:;">Separated link</a>
							</div>
						</div>
					</div>
				</div>
				<!--end breadcrumb-->
        <div class="row">
          <div class="col-12 col-lg-8">
              <div class="card">
                 <div class="card-body">
                   <div class="mb-4">
                      <h5 class="mb-3">Product Title</h5>
                      <input type="text" class="form-control" placeholder="write title here....">
                   </div>
                   <div class="mb-4">
                     <h5 class="mb-3">Product Description</h5>
                     <textarea class="form-control" cols="4" rows="6" placeholder="write a description here.."></textarea>
                   </div>
                   <div class="mb-4">
                    <h5 class="mb-3">Display images</h5>
                    <input id="fancy-file-upload" type="file" name="files" accept=".jpg, .png, image/jpeg, image/png" multiple>
                  </div>
                  <div class="mb-4">
                    <h5 class="mb-3">Inventory</h5>
                    
                    <div class="row g-3">
                      <div class="col-12 col-lg-3">
                        <div class="nav flex-column nav-pills border rounded vertical-pills overflow-hidden">
                          <button class="nav-link px-4 rounded-0" data-bs-toggle="pill" data-bs-target="#Pricing" type="button"><i class="bi bi-tag-fill me-2"></i>Pricing</button>
                          <button class="nav-link px-4 rounded-0" data-bs-toggle="pill" data-bs-target="#Restock" type="button"><i class="bi bi-box-seam-fill me-2"></i>Restock</button>
                          <button class="nav-link active px-4 rounded-0" data-bs-toggle="pill" data-bs-target="#Shipping" type="button"><i class="bi bi-truck-front-fill me-2"></i>Shipping</button>
                          <button class="nav-link px-4 rounded-0" data-bs-toggle="pill" data-bs-target="#GlobalDelivery" type="button"><i class="bi bi-globe me-2"></i>Global Delivery</button>
                          <button class="nav-link px-4 rounded-0" data-bs-toggle="pill" data-bs-target="#Attributes" type="button"><i class="bi bi-hdd-rack-fill me-2"></i>Attributes</button>
                          <button class="nav-link px-4 rounded-0" data-bs-toggle="pill" data-bs-target="#Advanced" type="button"><i class="bi bi-handbag-fill me-2"></i>Advanced</button>
                        </div>
                      </div>
                      <div class="col-12 col-lg-9">
                        <div class="tab-content">
                          <div class="tab-pane fade" id="Pricing">
                            <div class="row g-3">
                              <div class="col-12 col-lg-6">
                                <h6 class="mb-2">Regular price</h6>
                                <input class="form-control" type="text" placeholder="$$$">
                              </div>
                              <div class="col-12 col-lg-6">
                                <h6 class="mb-2">Sale price</h6>
                                <input class="form-control" type="text" placeholder="$$$">
                              </div>
                            </div>
                          </div>
                          <div class="tab-pane fade" id="Restock">
                            <h6 class="mb-3">Add to Stock</h6>
                            <div class="row g-3">
                              <div class="col-sm-7">
                                <input class="form-control" type="number" placeholder="Quantity">
                              </div>
                              <div class="col-sm">
                                <button class="btn btn-outline-primary"><i class="bi bi-check2 me-2"></i>Confirm</button>
                              </div>
                            </div>
                            <table class="mt-3">
                              <thead>
                                <tr>
                                  <th style="width: 200px;"></th>
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-1000 py-1">Product in stock now:</td>
                                  <td class="text-700 fw-semi-bold py-1">$2,059<button class="btn p-0 ms-2" type="button"><i class="bi bi-arrow-clockwise"></i></button></td>
                                </tr>
                                <tr>
                                  <td class="text-1000 py-1">Product in transit:</td>
                                  <td class="text-700 fw-semi-bold py-1">3000</td>
                                </tr>
                                <tr>
                                  <td class="text-1000 py-1">Last time restocked:</td>
                                  <td class="text-700 fw-semi-bold py-1">25th March, 2020</td>
                                </tr>
                                <tr>
                                  <td class="text-1000 py-1">Total stock over lifetime:</td>
                                  <td class="text-700 fw-semi-bold py-1">50,000</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <div class="tab-pane fade show active" id="Shipping">
                            <div class="d-flex flex-column h-100">
                              <h6 class="mb-3">Shipping Type</h6>
                              <div class="flex-1">
                                <div class="mb-4">
                                  <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="shippingRadio" id="fullfilledBySeller">
                                    <label class="form-check-label fw-bold" for="fullfilledBySeller">Fullfilled by Seller</label></div>
                                  <div class="ps-4">
                                    <p class="mb-0">You’ll be responsible for product delivery. <br>Any damage or delay during shipping may cost you a Damage fee.</p>
                                  </div>
                                </div>
                                <div class="mb-4">
                                  <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="shippingRadio" id="fullfilledByPhoenix" checked="checked">
                                    <label class="form-check-label fw-bold d-flex align-items-center" for="fullfilledByPhoenix">Fullfilled by Admin <span class="badge bg-warning text-dark ms-2">Recommended</span></label></div>
                                  <div class="ps-4">
                                    <p class="mb-0">Your product, Our responsibility.<br>For a measly fee, we will handle the delivery process for you.</p>
                                  </div>
                                </div>
                              </div>
                              <p class="fs--1 fw-semi-bold mb-0">See our <a class="fw-bold" href="#!">Delivery terms and conditions </a>for details.</p>
                            </div>
                          </div>
                          <div class="tab-pane fade" id="GlobalDelivery">
                            <div class="d-flex flex-column h-100">
                              <h6 class="mb-3">Global Delivery</h6>
                              <div class="flex-1">
                                <div class="mb-4">
                                  <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="shippingRadio" id="Worldwidedelivery">
                                    <label class="form-check-label fw-bold" for="Worldwidedelivery">Worldwide delivery</label>
                                  </div>
                                  <div class="ps-4">
                                    <p class="mb-0">Only available with Shipping method: <a href="#!">Fullfilled by Admin</a></p>
                                  </div>
                                </div>
                                <div class="mb-4">
                                  <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="shippingRadio" id="SelectedCountries" checked="checked">
                                    <label class="form-check-label fw-bold d-flex align-items-center" for="SelectedCountries">Selected Countries</label>
                                  </div>
                                  <div class="ps-4">
                                    <input class="form-control" type="text" placeholder="Type Country name">
                                  </div>
                                </div>
                                <div class="mb-0">
                                  <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="shippingRadio" id="Localdelivery">
                                    <label class="form-check-label fw-bold" for="Localdelivery">Local delivery</label>
                                  </div>
                                  <div class="ps-4">
                                    <p class="mb-0">Only available with Shipping method: <a href="#!">Fullfilled by Admin</a></p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="tab-pane fade" id="Attributes">
                            <h6 class="mb-3">Attributes</h6>
                            <div class="form-check">
                              <input class="form-check-input" id="fragileCheck" type="checkbox">
                              <label class="form-check-label text-900 fs-0" for="fragileCheck">Fragile Product</label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" id="biodegradableCheck" type="checkbox">
                              <label class="form-check-label text-900 fs-0" for="biodegradableCheck">Biodegradable</label>
                            </div>
                            <div class="mb-3">
                              <div class="form-check"><input class="form-check-input" id="frozenCheck" type="checkbox" checked="checked">
                                <label class="form-check-label text-900 fs-0" for="frozenCheck">Frozen Product</label>
                                <input class="form-control" type="text" placeholder="Max. allowed Temperature" style="max-width: 350px;">
                              </div>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" id="productCheck" type="checkbox" checked="checked">
                              <label class="form-check-label text-900 fs-0" for="productCheck">Expiry Date of Product</label>
                              <input class="form-control" id="inventory" type="date">
                            </div>
                          </div>
                          <div class="tab-pane fade" id="Advanced">
                            <h6 class="mb-3">Advanced</h6>
                            <div class="row g-3">
                              <div class="col-12 col-lg-6">
                                <label class="mb-2">Product ID Type</label>
                                <select class="form-select">
                                  <option selected="selected">ISBN</option>
                                  <option value="1">UPC</option>
                                  <option value="2">EAN</option>
                                  <option value="3">JAN</option>
                                </select>
                              </div>
                              <div class="col-12 col-lg-6">
                                <label class="mb-2">Product ID</label>
                                <input class="form-control" type="text" placeholder="ISBN Number">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                     </div>
                   </div> 
                 </div>
              </div>
          </div> 
          <div class="col-12 col-lg-4">
             <div class="card">
                <div class="card-body">
                   <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-danger flex-fill"><i class="bi bi-x-circle me-2"></i>Discard</button>
                    <button type="button" class="btn btn-outline-success flex-fill"><i class="bi bi-cloud-download me-2"></i>Save Draft</button>
                    <button type="button" class="btn btn-outline-primary flex-fill"><i class="bi bi-send me-2"></i>Publish</button>
                   </div>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                   <h5 class="mb-3">Organize</h5>
                      <div class="row g-3">
                          <div class="col-12">
                            <label for="AddCategory" class="form-label">Category</label>
                            <select class="form-select" id="AddCategory">
                              <option value="0">Topwear</option>
                              <option value="1">Bottomwear</option>
                              <option value="2">Casual Tshirt</option>
                              <option value="3">Electronic</option>
                            </select>
                          </div>
                          <div class="col-12">
                            <label for="Collection" class="form-label">Collection</label>
                            <input type="text" class="form-control" id="Collection" placeholder="Collection">
                          </div>
                          <div class="col-12">
                            <label for="Tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="Tags" placeholder="Tags">
                          </div>
                          <div class="col-12">
                            <div class="d-flex align-items-center gap-2">
                              <a href="javascript:;" class="btn btn-sm btn-light border shadow-sm">Woman <i class="bi bi-x"></i></a>
                              <a href="javascript:;" class="btn btn-sm btn-light border shadow-sm">Fashion <i class="bi bi-x"></i></a>
                              <a href="javascript:;" class="btn btn-sm btn-light border shadow-sm">Furniture <i class="bi bi-x"></i></a>
                            </div>
                          </div>
                          <div class="col-12">
                            <label for="Vendor" class="form-label">Vendor</label>
                            <input type="text" class="form-control" id="Vendor" placeholder="Vendor">
                          </div>
                        </div><!--end row-->
                     </div>
                </div>

                <div class="card">
                  <div class="card-body">
                    <h5 class="mb-3">Variants</h5>
                    <div class="row g-3">
                      <div class="col-12">
                        <label for="Brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" id="Brand" placeholder="Brand">
                       </div>
                      <div class="col-12">
                        <label for="SKU" class="form-label">SKU</label>
                        <input type="text" class="form-control" id="SKU" placeholder="SKU">
                       </div>
                       <div class="col-12">
                        <label for="Color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="Color" placeholder="Color">
                       </div>
                       <div class="col-12">
                        <label for="Size" class="form-label">Size</label>
                        <input type="text" class="form-control" id="Size" placeholder="Size">
                       </div>
                        <div class="col-12">
                          <div class="d-grid">
                            <button type="button" class="btn btn-primary">Add Variants</button>
                          </div>
                        </div>
                      </div>
                  </div>
                 </div>

              </div>                
          
         </div><!--end row-->
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
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart">
    <div class="offcanvas-header border-bottom h-70">
      <h5 class="mb-0" id="offcanvasRightLabel">8 New Orders</h5>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body p-0">
      <div class="order-list">
        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/01.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">White Men Shoes</h5>
            <p class="mb-0 order-price">$289</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/02.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Red Airpods</h5>
            <p class="mb-0 order-price">$149</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/03.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Men Polo Tshirt</h5>
            <p class="mb-0 order-price">$139</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/04.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Blue Jeans Casual</h5>
            <p class="mb-0 order-price">$485</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/05.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Fancy Shirts</h5>
            <p class="mb-0 order-price">$758</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/06.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Home Sofa Set </h5>
            <p class="mb-0 order-price">$546</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/07.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Black iPhone</h5>
            <p class="mb-0 order-price">$1049</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/08.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Goldan Watch</h5>
            <p class="mb-0 order-price">$689</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>
      </div>
    </div>
    <div class="offcanvas-footer h-70 p-3 border-top">
      <div class="d-grid">
        <button type="button" class="btn btn-grd btn-grd-primary" data-bs-dismiss="offcanvas">View Products</button>
      </div>
    </div>
  </div>
  <!--end cart-->


  <!--start switcher-->
  <button class="btn btn-grd btn-grd-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
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

  <!--bootstrap js-->
  <script src="assets/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="assets/js/jquery.min.js"></script>
  <!--plugins-->
  <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="assets/plugins/metismenu/metisMenu.min.js"></script>
  <script src="assets/plugins/fancy-file-uploader/jquery.ui.widget.js"></script>
	<script src="assets/plugins/fancy-file-uploader/jquery.fileupload.js"></script>
	<script src="assets/plugins/fancy-file-uploader/jquery.iframe-transport.js"></script>
	<script src="assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js"></script>
  <script>
		$('#fancy-file-upload').FancyFileUpload({
			params: {
				action: 'fileuploader'
			},
			maxfilesize: 1000000
		});
	</script>
  <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="assets/js/main.js"></script>


</body>

</html>