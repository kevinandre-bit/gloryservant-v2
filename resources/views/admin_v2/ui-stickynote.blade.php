 ob_start(); 

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content ">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Sticky Note</h3>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">

                <!-- Sticky -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title">Sticky Note </h5>
                            <a class="btn btn-primary float-sm-end m-l-10" id="add_new"
                                href="javascript:void(0);">Add New Note</a>
                        </div>
                        <div class="card-body pb-1">
                            <div class="sticky-note" id="board"></div>
                        </div>
                    </div>
                </div>
                <!-- /Sticky -->

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