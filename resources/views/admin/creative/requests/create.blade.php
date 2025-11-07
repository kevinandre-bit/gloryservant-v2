@extends('layouts.crw_layout')

@section('meta')
    <title>New Creative Request</title>
@endsection

@section('content')
<main class="main-wrapper">
    <div class="main-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Creative Workload</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.requests.index') }}">All Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Request</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <!-- Stepper UI -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Submit a New Creative Request</h5>
                    </div>
                    <div class="card-body">
                        <form id="creative-request-form" action="{{ route('admin.creative.requests.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Stepper navigation -->
                            <ul class="nav nav-pills mb-4" id="stepper-nav">
                                <li class="nav-item"><a class="nav-link active" href="#" data-step="0">1. Approval</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="1">2. Project Type</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="2">3. Requester Info</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="3">4. Details</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="4">5. Content</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="5">6. Uploads</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="6">7. Logos</a></li>
                                <li class="nav-item"><a class="nav-link disabled" href="#" data-step="7">8. Size</a></li>
                            </ul>
                            <!-- Step 1: Approval -->
                            <div class="step step-0">
                                <div class="mb-3">
                                    <label for="approved" class="form-label">Is the request approved by the campus administrator? <span class="text-danger">*</span></label>
                                    <select class="form-select" id="approved" name="approved" required>
                                        <option value="">Choose...</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                            </div>
                            <!-- Step 2: Project Type -->
                            <div class="step step-1 d-none">
                                <div class="mb-3">
                                    <label class="form-label">Type of Project <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="project_type[]" id="project_type_new" value="new">
                                        <label class="form-check-label" for="project_type_new">New Graphic</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="project_type[]" id="project_type_revision" value="revision">
                                        <label class="form-check-label" for="project_type_revision">Revision of Previous</label>
                                    </div>
                                    <div class="invalid-feedback d-none" id="project-type-error">Please select at least one project type.</div>
                                </div>
                            </div>
                            <!-- Step 3: Requester Info -->
                            <div class="step step-2 d-none">
                                <div class="mb-3">
                                    <label for="requester_name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="requester_name" name="requester_name" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="ministry" class="form-label">Ministry <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ministry" name="ministry" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Campus <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-6 col-md-4">
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Miami" id="campus_miami"><label class="form-check-label" for="campus_miami">Miami</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Boston" id="campus_boston"><label class="form-check-label" for="campus_boston">Boston</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="New York" id="campus_ny"><label class="form-check-label" for="campus_ny">New York</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Orlando" id="campus_orlando"><label class="form-check-label" for="campus_orlando">Orlando</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Santiago" id="campus_santiago"><label class="form-check-label" for="campus_santiago">Santiago</label></div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Cap Haitian" id="campus_caphaitian"><label class="form-check-label" for="campus_caphaitian">Cap Haitian</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Port au Prince" id="campus_pap"><label class="form-check-label" for="campus_pap">Port au Prince</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Montreal" id="campus_montreal"><label class="form-check-label" for="campus_montreal">Montreal</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="West Palm Beach" id="campus_wpb"><label class="form-check-label" for="campus_wpb">West Palm Beach</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Other" id="campus_other"><label class="form-check-label" for="campus_other">Other</label></div>
                                            <input type="text" class="form-control mt-2 d-none" id="campus_other_text" name="campus_other_text" placeholder="Indicate Other">
                                        </div>
                                    </div>
                                    <div class="invalid-feedback d-none" id="campus-error">Please select at least one campus.</div>
                                </div>
                            </div>
                            <!-- Step 4: Graphic Request Details -->
                            <div class="step step-3 d-none">
                                <div class="mb-3">
                                    <label for="tg_email" class="form-label">TG Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="tg_email" name="tg_email" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="date_requested" class="form-label">Date Requested <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_requested" name="date_requested" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="digital_printing" class="form-label">Digital Graphic / Printing Needs <span class="text-danger">*</span></label>
                                    <select class="form-select" id="digital_printing" name="digital_printing" required>
                                        <option value="">Choose...</option>
                                        <option value="digital">Digital Graphic</option>
                                        <option value="printing">Printing</option>
                                    </select>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="projected_service" class="form-label">Will this need to be projected in service? <span class="text-danger">*</span></label>
                                    <select class="form-select" id="projected_service" name="projected_service" required>
                                        <option value="">Choose...</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="graphic_title" class="form-label">Title of Graphic <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="graphic_title" name="graphic_title" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="graphic_description" class="form-label">Description of Graphic <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="graphic_description" name="graphic_description" rows="3" required></textarea>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="graphic_text" class="form-label">What text needs to be included on the graphic? <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="graphic_text" name="graphic_text" rows="2" required></textarea>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                            </div>
                            <!-- Step 5: Graphic Content Details -->
                            <div class="step step-4 d-none">
                                <div class="mb-3">
                                    <label for="slogans" class="form-label">Important slogans or subtitles needed on graphic</label>
                                    <input type="text" class="form-control" id="slogans" name="slogans">
                                </div>
                                <div class="mb-3">
                                    <label for="important_dates" class="form-label">Important dates needed on graphic</label>
                                    <input type="text" class="form-control" id="important_dates" name="important_dates">
                                </div>
                                <div class="mb-3">
                                    <label for="important_times" class="form-label">Important times needed on graphic</label>
                                    <input type="text" class="form-control" id="important_times" name="important_times">
                                </div>
                                <div class="mb-3">
                                    <label for="important_email" class="form-label">Important email needed on graphic</label>
                                    <input type="text" class="form-control" id="important_email" name="important_email">
                                </div>
                                <div class="mb-3">
                                    <label for="important_locations" class="form-label">Important locations needed on graphic</label>
                                    <input type="text" class="form-control" id="important_locations" name="important_locations">
                                </div>
                            </div>
                            <!-- Step 6: File Uploads -->
                            <div class="step step-5 d-none">
                                <div class="mb-3">
                                    <label for="upload_text_doc" class="form-label">Upload Important Text Document</label>
                                    <input type="file" class="form-control" id="upload_text_doc" name="upload_text_doc">
                                </div>
                                <div class="mb-3">
                                    <label for="upload_images" class="form-label">Upload important images needed to be added</label>
                                    <input type="file" class="form-control" id="upload_images" name="upload_images[]" multiple>
                                </div>
                                <div class="mb-3">
                                    <label for="upload_samples" class="form-label">Samples / Examples of your idea(s)</label>
                                    <input type="file" class="form-control" id="upload_samples" name="upload_samples[]" multiple>
                                </div>
                            </div>
                            <!-- Step 7: Logo Requirements -->
                            <div class="step step-6 d-none">
                                <label class="form-label">Logos Needed</label>
                                <div class="row">
                                    <div class="col-6 col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="TG English" id="logo_tg_en"><label class="form-check-label" for="logo_tg_en">Tabernacle of Glory English</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="TG Spanish" id="logo_tg_es"><label class="form-check-label" for="logo_tg_es">Tabernacle of Glory Spanish</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="TG French" id="logo_tg_fr"><label class="form-check-label" for="logo_tg_fr">Tabernacle of Glory French</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="40 Days English" id="logo_40_en"><label class="form-check-label" for="logo_40_en">40 Days English</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="40 Days Spanish" id="logo_40_es"><label class="form-check-label" for="logo_40_es">40 Days Spanish</label></div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="40 Days French" id="logo_40_fr"><label class="form-check-label" for="logo_40_fr">40 Days French</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="Shekinah" id="logo_shekinah"><label class="form-check-label" for="logo_shekinah">Shekinah</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="Social Media" id="logo_social"><label class="form-check-label" for="logo_social">Social Media</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="GT" id="logo_gt"><label class="form-check-label" for="logo_gt">GT</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="Other" id="logo_other"><label class="form-check-label" for="logo_other">Other</label></div>
                                        <input type="text" class="form-control mt-2 d-none" id="logo_other_text" name="logo_other_text" placeholder="Indicate Other">
                                    </div>
                                </div>
                            </div>
                            <!-- Step 8: Graphic Size -->
                            <div class="step step-7 d-none">
                                <label class="form-label">Size of Graphic</label>
                                <div class="row">
                                    <div class="col-6 col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="Screen: 1920x1080" id="size_screen"><label class="form-check-label" for="size_screen">Screen: 1920x1080</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="Social Square (1:1): 1080x1080" id="size_square"><label class="form-check-label" for="size_square">Social Square (1:1): 1080x1080</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="Backdrop / Step & Repeat" id="size_backdrop"><label class="form-check-label" for="size_backdrop">Backdrop / Step & Repeat</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="Pamphlet" id="size_pamphlet"><label class="form-check-label" for="size_pamphlet">Pamphlet</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="Flyer 8.5 x 11" id="size_flyer"><label class="form-check-label" for="size_flyer">Flyer 8.5 x 11</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="Other" id="size_other"><label class="form-check-label" for="size_other">Other</label></div>
                                        <input type="text" class="form-control mt-2 d-none" id="size_other_text" name="size_other_text" placeholder="Describe other size(s) needed">
                                    </div>
                                </div>
                            </div>
                            <!-- Stepper navigation buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" id="stepper-prev" disabled>Previous</button>
                                <button type="button" class="btn btn-primary" id="stepper-next">Next</button>
                                <button type="submit" class="btn btn-success d-none" id="stepper-submit">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Stepper JS logic -->
<script>
    const steps = document.querySelectorAll('.step');
    const navLinks = document.querySelectorAll('#stepper-nav .nav-link');
    let currentStep = 0;
    function showStep(idx) {
        steps.forEach((step, i) => {
            step.classList.toggle('d-none', i !== idx);
        });
        navLinks.forEach((link, i) => {
            link.classList.toggle('active', i === idx);
            link.classList.toggle('disabled', i > idx);
        });
        document.getElementById('stepper-prev').disabled = idx === 0;
        document.getElementById('stepper-next').classList.toggle('d-none', idx === steps.length - 1);
        document.getElementById('stepper-submit').classList.toggle('d-none', idx !== steps.length - 1);
    }
    // Dynamic show/hide for "Other" campus, logo, and size fields
    document.getElementById('campus_other').addEventListener('change', function() {
        document.getElementById('campus_other_text').classList.toggle('d-none', !this.checked);
    });
    document.getElementById('logo_other').addEventListener('change', function() {
        document.getElementById('logo_other_text').classList.toggle('d-none', !this.checked);
    });
    document.getElementById('size_other').addEventListener('change', function() {
        document.getElementById('size_other_text').classList.toggle('d-none', !this.checked);
    });
    // Step 3: Requester Info required check
    function validateStep3() {
        let valid = true;
        const name = document.getElementById('requester_name');
        const ministry = document.getElementById('ministry');
        const campus = document.querySelectorAll('input[name="campus[]"]:checked');
        if (!name.value) { name.classList.add('is-invalid'); valid = false; } else { name.classList.remove('is-invalid'); }
        if (!ministry.value) { ministry.classList.add('is-invalid'); valid = false; } else { ministry.classList.remove('is-invalid'); }
        if (campus.length === 0) { document.getElementById('campus-error').classList.remove('d-none'); valid = false; } else { document.getElementById('campus-error').classList.add('d-none'); }
        return valid;
    }
    // Step 4: Details required check
    function validateStep4() {
        let valid = true;
        ['tg_email','date_requested','digital_printing','projected_service','graphic_title','graphic_description','graphic_text'].forEach(id => {
            const el = document.getElementById(id);
            if (!el.value) { el.classList.add('is-invalid'); valid = false; } else { el.classList.remove('is-invalid'); }
        });
        return valid;
    }
    document.getElementById('stepper-next').onclick = function() {
        // Step 1: Approval required check
        if (currentStep === 0) {
            const approved = document.getElementById('approved').value;
            if (!approved) {
                document.getElementById('approved').classList.add('is-invalid');
                return;
            }
            document.getElementById('approved').classList.remove('is-invalid');
            if (approved === 'no') {
                alert('You cannot proceed unless the request is approved.');
                return;
            }
        }
        // Step 2: Project Type required check
        if (currentStep === 1) {
            const checked = document.querySelectorAll('input[name="project_type[]"]:checked');
            const error = document.getElementById('project-type-error');
            if (checked.length === 0) {
                error.classList.remove('d-none');
                return;
            } else {
                error.classList.add('d-none');
            }
        }
        if (currentStep === 2 && !validateStep3()) return;
        if (currentStep === 3 && !validateStep4()) return;
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    };
    document.getElementById('stepper-prev').onclick = function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    };
    showStep(currentStep);
</script>
@endsection