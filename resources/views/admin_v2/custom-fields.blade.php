 ob_start(); 

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Settings</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Administration
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Settings</li>
                        </ol>
                    </nav>
                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <ul class="nav nav-tabs nav-tabs-solid bg-transparent border-bottom mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="profile-settings.php"><i class="ti ti-settings me-2"></i>General Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bussiness-settings.php"><i class="ti ti-world-cog me-2"></i>Website Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="salary-settings.php"><i class="ti ti-device-ipad-horizontal-cog me-2"></i>App Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="email-settings.php"><i class="ti ti-server-cog me-2"></i>System Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payment-gateways.php"><i class="ti ti-settings-dollar me-2"></i>Financial Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="custom-css.php"><i class="ti ti-settings-2 me-2"></i>Other Settings</a>
                </li>
            </ul>
            <div class="row">
                <div class="col-xl-3 theiaStickySidebar">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column list-group settings-list">
                                <a href="salary-settings.php" class="d-inline-flex align-items-center rounded py-2 px-3">Salary Settings</a>
                                <a href="approval-settings.php" class="d-inline-flex align-items-center rounded py-2 px-3">Approval Settings</a>
                                <a href="invoice-settings.php" class="d-inline-flex align-items-center rounded py-2 px-3">Invoice Settings</a>
                                <a href="leave-type.php" class="d-inline-flex align-items-center rounded py-2 px-3">Leave Type</a>
                                <a href="custom-fields.php" class="d-inline-flex align-items-center rounded active py-2 px-3"><i class="ti ti-arrow-badge-right me-2"></i>Custom Fields</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Custom Fields</h4>
                                <a href="#" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#new-field"><i class="ti ti-circle-plus me-2"></i>Add Fields</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort">
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input" type="checkbox" id="select-all">
                                                </div>
                                            </th>
                                            <th class="fw-semibold">Module</th>
                                            <th class="fw-semibold">Label</th>
                                            <th class="fw-semibold">Type</th>
                                            <th class="fw-semibold">Default Value</th>
                                            <th class="fw-semibold">Required/Disable</th>
                                            <th class="fw-semibold">Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="no-sort">
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input" type="checkbox">
                                                </div>
                                            </th>
                                            <th>Employee</th>
                                            <th class="text-gray fw-normal">Preferred Language</th>
                                            <th class="text-gray fw-normal">Select</th>
                                            <th class="text-gray fw-normal">English</th>
                                            <th class="text-gray fw-normal">
                                                Required
                                            </th>
                                            <th class="d-flex">
                                                <span class="badge badge-success badge-sm d-flex align-items-center"><i class="ti ti-point-filled"></i>Active</span>
                                            </th>
                                            <th>
                                                <div class="dropdown">
                                                    <a href="javascript:void(0);" class="text-gray" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit-field"><i class="ti ti-edit me-2"></i>Edit</a>
                                                        <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-trash me-2"></i>Delete</a>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="no-sort">
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input" type="checkbox">
                                                </div>
                                            </th>
                                            <th>Projects</th>
                                            <th class="text-gray fw-normal">Project Type</th>
                                            <th class="text-gray fw-normal">Select</th>
                                            <th class="text-gray fw-normal">Internal</th>
                                            <th class="text-gray fw-normal">
                                                Required
                                            </th>
                                            <th class="d-flex">
                                                <span class="badge badge-success badge-sm d-flex align-items-center"><i class="ti ti-point-filled"></i>Active</span>
                                            </th>
                                            <th>
                                                <div class="dropdown">
                                                    <a href="javascript:void(0);" class="text-gray" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit-field"><i class="ti ti-edit me-2"></i>Edit</a>
                                                        <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-trash me-2"></i>Delete</a>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="no-sort">
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input" type="checkbox">
                                                </div>
                                            </th>
                                            <th>Tasks</th>
                                            <th class="text-gray fw-normal">Task Type</th>
                                            <th class="text-gray fw-normal">Select</th>
                                            <th class="text-gray fw-normal">Design</th>
                                            <th class="text-gray fw-normal">
                                                Required
                                            </th>
                                            <th class="d-flex">
                                                <span class="badge badge-success badge-sm d-flex align-items-center"><i class="ti ti-point-filled"></i>Active</span>
                                            </th>
                                            <th>
                                                <div class="dropdown">
                                                    <a href="javascript:void(0);" class="text-gray" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit-field"><i class="ti ti-edit me-2"></i>Edit</a>
                                                        <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-trash me-2"></i>Delete</a>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content -->   

        @include('partials.footer')

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@php
$content = ob_get_clean();
@endphp
@include('partials.main')   