@include('partials.session')

<!DOCTYPE html>
@include('partials.theme-settings')

<head>

    @include('partials.title-meta')

    @include('partials.head-css')

</head>

@include('partials.body')

    @include('partials.loader')

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

        @include('partials.menu')
        
        <?= $content ?>

        @include('partials.modal-popup')

    </div>
    <!-- End Main Wrapper -->

    @include('partials.vendor-scripts')

</body>
</html>
