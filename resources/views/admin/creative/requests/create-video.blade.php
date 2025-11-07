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
                        <li class="breadcrumb-item active" aria-current="page">New Video Request</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Intercampus Video Creation Request Form</h5>
                <p class="text-muted mb-0">Submit a video request so the team can triage, schedule, and assign it.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.creative.requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="request_type" value="video">

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
                                    <input class="form-check-input" type="checkbox" name="project_type[]" value="new_video" id="new_video">
                                    <label class="form-check-label" for="new_video">New Video</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="project_type[]" value="revision" id="revision_video">
                                    <label class="form-check-label" for="revision_video">Revision of Previous</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Requester Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 3: Requester Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="requester_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ministry <span class="text-danger">*</span></label>
                                <input type="text" name="ministry" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">TG Email <span class="text-danger">*</span></label>
                                <input type="email" name="tg_email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Requested <span class="text-danger">*</span></label>
                            <input type="date" name="date_requested" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Campus <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Miami" id="v_miami"><label class="form-check-label" for="v_miami">Miami</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Boston" id="v_boston"><label class="form-check-label" for="v_boston">Boston</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="New York" id="v_newyork"><label class="form-check-label" for="v_newyork">New York</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Orlando" id="v_orlando"><label class="form-check-label" for="v_orlando">Orlando</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Santiago" id="v_santiago"><label class="form-check-label" for="v_santiago">Santiago</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Cap Haitian" id="v_caphaitian"><label class="form-check-label" for="v_caphaitian">Cap Haitian</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Port au Prince" id="v_portauprince"><label class="form-check-label" for="v_portauprince">Port au Prince</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Montreal" id="v_montreal"><label class="form-check-label" for="v_montreal">Montreal</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="West Palm Beach" id="v_westpalm"><label class="form-check-label" for="v_westpalm">West Palm Beach</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="campus[]" value="Other" id="v_campusother"><label class="form-check-label" for="v_campusother">Other</label></div>
                                </div>
                            </div>
                            <input type="text" name="campus_other" class="form-control mt-2" placeholder="If Other, indicate campus" style="display:none;" id="v_campusOtherText">
                        </div>
                    </div>

                    <!-- Section 4: Video Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 4: Video Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Title of Video <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description / Purpose <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Brief explanation of the video's purpose or event" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Primary Audience</label>
                                <input type="text" name="primary_audience" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expected Runtime (minutes:seconds)</label>
                                <input type="text" name="expected_runtime" class="form-control" placeholder="e.g., 2:30">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Platform(s)</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="YouTube" id="youtube"><label class="form-check-label" for="youtube">YouTube</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="Instagram" id="instagram"><label class="form-check-label" for="instagram">Instagram</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="Facebook" id="facebook"><label class="form-check-label" for="facebook">Facebook</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="Website" id="website"><label class="form-check-label" for="website">Website</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="Service Projection" id="service"><label class="form-check-label" for="service">Service Projection</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="TikTok" id="tiktok"><label class="form-check-label" for="tiktok">TikTok</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="target_platforms[]" value="Other" id="platformother"><label class="form-check-label" for="platformother">Other</label></div>
                                </div>
                            </div>
                            <input type="text" name="target_platforms_other" class="form-control mt-2" placeholder="If Other, describe" style="display:none;" id="platformOtherText">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orientation / Aspect Ratio</label>
                                <select name="aspect_ratio" class="form-select">
                                    <option value="">Choose one</option>
                                    <option value="horizontal_16_9">Horizontal (16:9)</option>
                                    <option value="vertical_9_16">Vertical (9:16)</option>
                                    <option value="square_1_1">Square (1:1)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Resolution</label>
                                <select name="resolution" class="form-select">
                                    <option value="">Choose one</option>
                                    <option value="1080p">1080p</option>
                                    <option value="4k">4K (2160p)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">On-screen Text or Key Information</label>
                            <textarea name="onscreen_text" class="form-control" rows="3" placeholder="Main names, verses, or phrases to appear on screen"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Call to Action / URL (if any)</label>
                                <input type="text" name="call_to_action" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Music or Style Preference</label>
                                <input type="text" name="music_style" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtitles or Captions Needed?</label>
                                <select name="subtitles_needed" class="form-select">
                                    <option value="">Choose one</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Languages (if applicable)</label>
                                <input type="text" name="languages" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Uploads & References -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 5: Uploads & References</h6>
                        <div class="mb-3">
                            <label class="form-label">Reference Links or Example Videos</label>
                            <textarea name="reference_links" class="form-control" rows="3" placeholder="URLs or descriptions"></textarea>
                        </div>
                    </div>

                    <!-- Section 6: Additional Notes -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Section 6: Additional Notes</h6>
                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" class="form-control" rows="3" placeholder="Optional additional information"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.creative.requests.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Video Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById('v_campusother').addEventListener('change', function() {
    document.getElementById('v_campusOtherText').style.display = this.checked ? 'block' : 'none';
});
document.getElementById('platformother').addEventListener('change', function() {
    document.getElementById('platformOtherText').style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection