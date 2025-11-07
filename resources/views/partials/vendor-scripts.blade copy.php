@php
    use Illuminate\Support\Str;

    // Current route name like: "src.index", "src.employees", etc.
    $route = Route::currentRouteName() ?? '';

    // Helpers
    $is = fn ($names) => Str::is((array) $names, $route);
    $in = fn ($prefixes) => collect((array) $prefixes)->contains(fn ($p) => Str::startsWith($route, $p));

    // “Lite pages” (auth/errors/landing) should skip heavy libs
    $isLitePage = $in(['auth.', 'errors.', 'landing.']);
@endphp

<!-- Core -->
<script src="{{ asset('assets3/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets3/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets3/js/feather.min.js') }}"></script>

@if(!$isLitePage)
    <!-- Slimscroll -->
    <script src="{{ asset('assets3/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Summernote -->
    <script src="{{ asset('assets3/plugins/summernote/summernote-lite.min.js') }}"></script>

    <!-- Daterangepicker (moment required) -->
    <script src="{{ asset('assets3/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Color Picker -->
    <script src="{{ asset('assets3/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets3/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets3/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Tagsinput -->
    <script src="{{ asset('assets3/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Datetimepicker & Select2 -->
    <script src="{{ asset('assets3/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Sticky Sidebar -->
    <script src="{{ asset('assets3/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets3/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endif

{{-- =========================
     Feature / Page specific
========================= --}}

{{-- Media player / carousel heavy pages --}}
@if($is([
    'src.add-invoices','src.edit-invoices','src.file-manager','src.group-video-call',
    'src.invoice-details','src.invoices','src.manage-jobs','src.maps-leaflet',
    'src.maps-vector','src.payslip','src.promotion','src.resignation','src.termination'
]))
    <script src="{{ asset('assets3/js/plyr-js.js') }}"></script>
    <script src="{{ asset('assets3/plugins/owlcarousel/owl.carousel.min.js') }}"></script>
@endif

{{-- Rangeslider pages --}}
@if($is(['src.job-grid-2','src.job-list-2','src.plugin','src.ui-rangeslider','src.ui-rating']))
    <script src="{{ asset('assets3/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/ion-rangeslider/js/custom-rangeslider.js') }}"></script>
@endif

{{-- FullCalendar --}}
@if($is(['src.calendar','src.incoming-call','src.outgoing-call','src.video-call','src.voice-call']))
    <script src="{{ asset('assets3/plugins/fullcalendar/index.global.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/fullcalendar/calendar-data.js') }}"></script>
@endif

{{-- Owl carousel on some pages --}}
@if($is(['src.email-reply','src.email','src.notes','src.plugin','src.project-details','src.social-feed','src.task-details']))
    <script src="{{ asset('assets3/js/owl.carousel.min.js') }}"></script>
@endif

{{-- Clipboard --}}
@if($is('src.ui-clipboard'))
    <script src="{{ asset('assets3/plugins/clipboard/clipboard.min.js') }}"></script>
@endif

{{-- Vector maps --}}
@if($is('src.maps-vector'))
    <script src="{{ asset('assets3/plugins/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ asset('assets3/js/us-merc-en.js') }}"></script>
    <script src="{{ asset('assets3/js/russia.js') }}"></script>
    <script src="{{ asset('assets3/js/spain.js') }}"></script>
    <script src="{{ asset('assets3/js/canada.js') }}"></script>
    <script src="{{ asset('assets3/js/jsvectormap.js') }}"></script>
    <script src="{{ asset('assets3/plugins/@simonwep/pickr/pickr.min.js') }}"></script>
@endif

{{-- Leaflet maps --}}
@if($is('src.maps-leaflet'))
    <script src="{{ asset('assets3/plugins/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('assets3/js/leaflet.js') }}"></script>
@endif

{{-- Drag & Drop --}}
@if($is('src.ui-drag-drop'))
    <script src="{{ asset('assets3/plugins/dragula/js/dragula.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/dragula/js/drag-drop.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/dragula/js/draggable-cards.js') }}"></script>
@endif

{{-- Sweetalerts --}}
@if($is(['src.ui-sweetalerts','src.ui-ribbon']))
    <script src="{{ asset('assets3/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/sweetalert/sweetalerts.min.js') }}"></script>
@endif

{{-- Stickynote / jQuery UI helpers --}}
@if($is(['src.ui-stickynote','src.kanban-view','src.task-board','src.deals-grid','src.leads-grid','src.candidates-kanban']))
    <script src="{{ asset('assets3/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets3/js/jquery.ui.touch-punch.min.js') }}"></script>
@endif

{{-- Stickynote core --}}
@if($is(['src.plugin','src.ui-stickynote']))
    <script src="{{ asset('assets3/plugins/stickynote/sticky.js') }}"></script>
@endif

{{-- Apexcharts (dashboards & reports) --}}
@if($is([
        'src.chart-apex','src.index','src.employee-dashboard','src.deals-dashboard','src.leads-dashboard',
        'src.file-manager','src.dashboard','src.companies','src.packages',
        'src.layout-horizontal','src.layout-detached','src.layout-modern','src.layout-horizontal-overlay',
        'src.layout-two-column','src.layout-hovered','src.layout-box','src.layout-horizontal-single',
        'src.layout-horizontal-box','src.layout-horizontal-fullwidth','src.layout-horizontal-sidemenu',
        'src.layout-vertical-transparent','src.layout-without-header','src.layout-rtl','src.layout-stacked','src.layout-dark',
        // any "*-report" page:
    ]) || Str::is('src.*-report', $route))
    <script src="{{ asset('assets3/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/apexchart/chart-data.js') }}"></script>
@endif

{{-- Chart.js (some dashboards) --}}
@if($is([
        'src.chart-js','src.index','src.deals-dashboard','src.dashboard','src.companies',
        'src.layout-horizontal','src.layout-detached','src.layout-modern','src.layout-horizontal-overlay',
        'src.layout-two-column','src.layout-hovered','src.layout-box','src.layout-horizontal-single',
        'src.layout-horizontal-box','src.layout-horizontal-fullwidth','src.layout-horizontal-sidemenu',
        'src.layout-vertical-transparent','src.layout-without-header','src.layout-rtl','src.layout-stacked','src.layout-dark',
        'src.analytics'
    ]))
    <script src="{{ asset('assets3/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/chartjs/chart-data.js') }}"></script>
@endif

{{-- Morris --}}
@if($is('src.chart-morris'))
    <script src="{{ asset('assets3/plugins/morris/raphael-min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/morris/chart-data.js') }}"></script>
@endif

{{-- Peity --}}
@if($is(['src.chart-peity','src.deals-dashboard','src.leads-dashboard','src.dashboard','src.companies','src.subscription','src.tickets-grid','src.tickets','src.task-report']))
    <script src="{{ asset('assets3/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/peity/chart-data.js') }}"></script>
@endif

{{-- Flot --}}
@if($is('src.chart-flot'))
    <script src="{{ asset('assets3/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets3/plugins/flot/jquery.flot.fillbetween.js') }}"></script>
    <script src="{{ asset('assets3/plugins/flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('assets3/plugins/flot/chart-data.js') }}"></script>
@endif

{{-- Rating --}}
@if($is('src.ui-rating'))
    <script src="{{ asset('assets3/plugins/rater-js/index.js') }}"></script>
    <script src="{{ asset('assets3/js/ratings.js') }}"></script>
@endif

{{-- Toasts --}}
@if($is('src.ui-toasts'))
    <script src="{{ asset('assets3/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/toastr/toastr.js') }}"></script>
@endif

{{-- Counter --}}
@if($is('src.ui-counter'))
    <script src="{{ asset('assets3/plugins/countup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/countup/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/countup/jquery.missofis-countdown.js') }}"></script>
    <script src="{{ asset('assets3/js/counter.js') }}"></script>
@endif

{{-- Lightbox --}}
@if($is('src.ui-lightbox'))
    <script src="{{ asset('assets3/plugins/lightbox/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/lightbox/lightbox.js') }}"></script>
@endif

{{-- Swiper --}}
@if($is('src.ui-swiperjs'))
    <script src="{{ asset('assets3/plugins/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/@simonwep/pickr/pickr.min.js') }}"></script>
    <script src="{{ asset('assets3/js/swiper.js') }}"></script>
@endif

{{-- Form wizard --}}
@if($is('src.form-wizard'))
    <script src="{{ asset('assets3/plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
    <script src="{{ asset('assets3/plugins/twitter-bootstrap-wizard/prettify.js') }}"></script>
    <script src="{{ asset('assets3/plugins/twitter-bootstrap-wizard/form-wizard.js') }}"></script>
@endif

{{-- Mask --}}
@if($is('src.form-mask'))
    <script src="{{ asset('assets3/js/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('assets3/js/mask.js') }}"></script>
@endif

{{-- Validation / OTP flows --}}
@if($is(['src.reset-password','src.reset-password-2','src.reset-password-3']))
    <script src="{{ asset('assets3/js/validation.js') }}"></script>
@endif
@if($is(['src.email-verification','src.email-verification-2','src.email-verification-3','src.two-step-verification','src.two-step-verification-2','src.two-step-verification-3']))
    <script src="{{ asset('assets3/js/otp.js') }}"></script>
@endif

{{-- File upload --}}
@if($is('src.form-fileupload'))
    <script src="{{ asset('assets3/plugins/fileupload/fileupload.min.js') }}"></script>
    <script src="{{ asset('assets3/js/file-upload.js') }}"></script>
@endif

{{-- Employee salary --}}
@if($is('src.employee-salary'))
    <script src="{{ asset('assets3/js/employee-salary.js') }}"></script>
@endif

{{-- Fancybox --}}
@if($is(['src.employee-salary','src.project-details','src.gallery','src.plugins','src.search-result','src.social-feed']))
    <script src="{{ asset('assets3/plugins/fancybox/jquery.fancybox.min.js') }}"></script>
@endif

{{-- Form pickers demo page --}}
@if($is('src.form-pickers'))
    <script src="{{ asset('assets3/plugins/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets3/plugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets3/plugins/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('assets3/plugins/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets3/js/forms-pickers.js') }}"></script>
@endif

{{-- Coming soon --}}
@if($is('src.coming-soon'))
    <script src="{{ asset('assets3/js/coming-soon.js') }}"></script>
@endif

{{-- Email --}}
@if($is(['src.email-reply','src.email','src.social-feed']))
    <script src="{{ asset('assets3/js/email.js') }}"></script>
@endif

{{-- Kanban --}}
@if($is(['src.candidates-kanban','src.kanban-view','src.deals-grid','src.leads-grid','src.task-board']))
    <script src="{{ asset('assets3/js/kanban.js') }}"></script>
@endif

{{-- Invoices --}}
@if($is(['src.add-invoices','src.edit-invoices']))
    <script src="{{ asset('assets3/js/invoice.js') }}"></script>
@endif

{{-- Projects --}}
@if($is('src.project-details'))
    <script src="{{ asset('assets3/js/projects.js') }}"></script>
@endif

{{-- Multiselect --}}
@if($is('src.leave-settings'))
    <script src="{{ asset('assets3/js/multiselect.min.js') }}"></script>
@endif

{{-- Popovers / Tooltips --}}
@if($is(['src.ui-popovers','src.ui-tooltips']))
    <script src="{{ asset('assets3/js/popover.js') }}"></script>
@endif

{{-- Comments on details pages --}}
@if($is(['src.company-details','src.contact-details','src.deals-details','src.leads-details']))
    <script src="{{ asset('assets3/js/add-comments.js') }}"></script>
@endif

{{-- File manager helpers --}}
@if($is(['src.file-manager','src.project-details','src.task-details']))
    <script src="{{ asset('assets3/js/file-manager.js') }}"></script>
@endif

{{-- Todos --}}
@if($is([
    'src.client-details','src.employee-dashboard','src.index','src.layout-box','src.layout-dark','src.layout-detached',
    'src.layout-horizontal-box','src.layout-horizontal-fullwidth','src.layout-horizontal-overlay','src.layout-horizontal-single',
    'src.layout-horizontal-sidemenu','src.layout-horizontal','src.layout-modern','src.layout-two-column','src.layout-hovered',
    'src.layout-vertical-transparent','src.layout-without-header','src.layout-rtl','src.layout-stacked',
    'src.project-details','src.task-details','src.tasks','src.todo-list','src.todo'
]))
    <script src="{{ asset('assets3/js/todo.js') }}"></script>
@endif

{{-- Theme color picker (skip lite pages) --}}
@if(!$isLitePage)
    <script src="{{ asset('assets3/js/theme-colorpicker.js') }}"></script>
@endif

<!-- App -->
<script src="{{ asset('assets3/js/script.js') }}"></script>

{{-- Allow pages to add more scripts --}}
@stack('scripts')