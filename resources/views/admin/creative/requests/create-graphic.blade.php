@extends('layouts.crw_layout')

@section('content')
<main class="main-wrapper">
    <div class="main-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Creative Workload</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.creative.requests.index') }}">Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Graphic Request</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Intercampus Graphic Request Form</h5>
                <p class="text-muted mb-0">Submit a creative request for graphics so the team can triage and assign it.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.creative.requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="request_type" value="graphic">

                    <!-- Section 1: Approval -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 1: Approval</h6>
                        <div class="mb-3">
                            <label class="form-label">Is the request approved by the campus administrator? <span class="text-danger">*</span></label>
                            <select name="admin_approved" class="form-select" required>
                                <option value="">Choose one</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 2: Project Type -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 2: Project Type</h6>
                        <div class="mb-3">
                            <label class="form-label">Type of Project <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="project_type[]" value="new_graphic" id="new_graphic">
                                    <label class="form-check-label" for="new_graphic">New Graphic</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="project_type[]" value="revision" id="revision">
                                    <label class="form-check-label" for="revision">Revision of Previous</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Requester Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 3: Requester Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="requester_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ministry <span class="text-danger">*</span></label>
                                <input type="text" name="ministry" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Campus <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Miami" id="miami"><label class="form-check-label" for="miami">Miami</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Boston" id="boston"><label class="form-check-label" for="boston">Boston</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="New York" id="newyork"><label class="form-check-label" for="newyork">New York</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Orlando" id="orlando"><label class="form-check-label" for="orlando">Orlando</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Santiago" id="santiago"><label class="form-check-label" for="santiago">Santiago</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Cap Haitian" id="caphaitian"><label class="form-check-label" for="caphaitian">Cap Haitian</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Port au Prince" id="portauprince"><label class="form-check-label" for="portauprince">Port au Prince</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Montreal" id="montreal"><label class="form-check-label" for="montreal">Montreal</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="West Palm Beach" id="westpalm"><label class="form-check-label" for="westpalm">West Palm Beach</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Other" id="campusother"><label class="form-check-label" for="campusother">Other</label></div>
                                </div>
                            </div>
                            <input type="text" name="campus_other" class="form-control mt-2" placeholder="Indicate Other Campus" style="display:none;" id="campusOtherText">
                        </div>
                    </div>

                    <!-- Section 4: Graphic Request Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 4: Graphic Request Details</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">TG Email <span class="text-danger">*</span></label>
                                <input type="email" name="tg_email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date Requested <span class="text-danger">*</span></label>
                                <input type="date" name="date_requested" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Digital Graphic / Printing Needs <span class="text-danger">*</span></label>
                                <select name="graphic_needs" class="form-select" required>
                                    <option value="">Choose one</option>
                                    <option value="digital_only">Digital Only</option>
                                    <option value="print_only">Print Only</option>
                                    <option value="both">Both Digital & Print</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Will this need to be projected in service? <span class="text-danger">*</span></label>
                                <select name="projected_in_service" class="form-select" required>
                                    <option value="">Choose one</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title of Graphic <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description of Graphic <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Explanation of the event, activity, or series" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">What text needs to be included on the graphic? <span class="text-danger">*</span></label>
                            <textarea name="graphic_text" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>

                    <!-- Section 5: Graphic Content Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 5: Graphic Content Details</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Important slogans or subtitles</label>
                                <input type="text" name="slogans" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Important dates</label>
                                <input type="text" name="important_dates" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Important times</label>
                                <input type="text" name="important_times" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Important email</label>
                                <input type="email" name="important_email" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Important locations</label>
                                <input type="text" name="important_locations" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: Logo Requirements -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 7: Logo Requirements</h6>
                        <div class="mb-3">
                            <label class="form-label">Logos Needed</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="tg_english" id="tg_english"><label class="form-check-label" for="tg_english">Tabernacle of Glory English</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="tg_spanish" id="tg_spanish"><label class="form-check-label" for="tg_spanish">Tabernacle of Glory Spanish</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="tg_french" id="tg_french"><label class="form-check-label" for="tg_french">Tabernacle of Glory French</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="40days_english" id="40days_english"><label class="form-check-label" for="40days_english">40 Days English</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="40days_spanish" id="40days_spanish"><label class="form-check-label" for="40days_spanish">40 Days Spanish</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="40days_french" id="40days_french"><label class="form-check-label" for="40days_french">40 Days French</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="shekinah" id="shekinah"><label class="form-check-label" for="shekinah">Shekinah</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="social_media" id="social_media"><label class="form-check-label" for="social_media">Social Media</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="gt" id="gt"><label class="form-check-label" for="gt">GT</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="logos[]" value="other" id="logoother"><label class="form-check-label" for="logoother">Other</label></div>
                                </div>
                            </div>
                            <input type="text" name="logos_other" class="form-control mt-2" placeholder="Indicate Other Logo" style="display:none;" id="logoOtherText">
                        </div>
                    </div>

                    <!-- Section 8: Graphic Size -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 8: Graphic Size</h6>
                        <div class="mb-3">
                            <label class="form-label">Size of Graphic</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="screen_1920x1080" id="screen"><label class="form-check-label" for="screen">Screen: 1920x1080</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="social_square_1080x1080" id="social_square"><label class="form-check-label" for="social_square">Social Square (1:1): 1080x1080</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="backdrop" id="backdrop"><label class="form-check-label" for="backdrop">Backdrop / Step & Repeat</label></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="pamphlet" id="pamphlet"><label class="form-check-label" for="pamphlet">Pamphlet</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="flyer_8.5x11" id="flyer"><label class="form-check-label" for="flyer">Flyer 8.5 x 11</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="graphic_size[]" value="other" id="sizeother"><label class="form-check-label" for="sizeother">Other</label></div>
                                </div>
                            </div>
                            <textarea name="graphic_size_other" class="form-control mt-2" placeholder="Describe other size(s) needed" style="display:none;" id="sizeOtherText"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.creative.requests.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Graphic Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById('campusother').addEventListener('change', function() {
    document.getElementById('campusOtherText').style.display = this.checked ? 'block' : 'none';
});
document.getElementById('logoother').addEventListener('change', function() {
    document.getElementById('logoOtherText').style.display = this.checked ? 'block' : 'none';
});
document.getElementById('sizeother').addEventListener('change', function() {
    document.getElementById('sizeOtherText').style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection