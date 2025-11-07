@extends('layouts.personal')

    @section('meta')
        <title>My Settings | Workday Time Clock</title>
        <meta name="description" content="Workday my settings">
    @endsection

    @section('styles')
        {{-- moved inline JS to /assets3/personal-settings-view.js --}}
    @endsection

    @section('content')
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-title">{{ __("Request Forms") }}</h2>
            </div>    
        </div>

        <div class="row">
            <div class="col-md-12">

            <div class="box box-success">
                <div class="box-body">
                    <div class="ui secondary blue pointing tabular menu">
                        <a class="item active" data-tab="website-support">{{ __("Website Support") }}</a>
                        <a class="item" data-tab="Graphic">{{ __("Request Graphics") }}</a>
                        <a class="item" data-tab="Video">{{ __("Request Video") }}</a>
                        <a class="item" data-tab="about">{{ __("Special Request") }}</a>
                    </div>
                    
                    
                    <div class="ui tab active" data-tab="website-support">
                        <div class="col-md-12">
                            <div class="asana-embed-container"><link rel="stylesheet" href="https://form.asana.com/static/asana-form-embed-style.css"/><iframe class="asana-embed-iframe" height="533" width = "800" src="https://form.asana.com/?k=igXWqRIDD3SIGfSUmaLTiQ&d=34642379201116&embed=true"></iframe><div class="asana-embed-footer"><a rel="nofollow noopener" target="_blank" class="asana-embed-footer-link" href="https://asana.com/?utm_source=embedded_form"><span class="asana-embed-footer-text">Form powered by</span><div class="asana-embed-footer-logo" role="img" aria-label="Logo of Asana"></div></a></div></div>
                        </div>
                    </div>
                    

                    <div class="ui tab" data-tab="Graphic">
                        <div class="col-md-12">
                            <div class="tab-content">
                                <div class="asana-embed-container"><link rel="stylesheet" href="https://form.asana.com/static/asana-form-embed-style.css"/><iframe class="asana-embed-iframe" height="533" width = "800" src="https://form.asana.com/?k=8y4roi6vbCbYSPd1l73jWA&d=34642379201116&embed=true"></iframe><div class="asana-embed-footer"><a rel="nofollow noopener" target="_blank" class="asana-embed-footer-link" href="https://asana.com/?utm_source=embedded_form"><span class="asana-embed-footer-text">Form powered by</span><div class="asana-embed-footer-logo" role="img" aria-label="Logo of Asana"></div></a></div></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ui tab" data-tab="Video">
                        <div class="col-md-12">
                            <div class="tab-content">
                                <div class="asana-embed-container"><link rel="stylesheet" href="https://form.asana.com/static/asana-form-embed-style.css"/><iframe class="asana-embed-iframe" height="533" width = "800" src="https://form.asana.com/?k=YBGvMD6rPApBhJjn2gEj-w&d=34642379201116&embed=true"></iframe><div class="asana-embed-footer"><a rel="nofollow noopener" target="_blank" class="asana-embed-footer-link" href="https://asana.com/?utm_source=embedded_form"><span class="asana-embed-footer-text">Form powered by</span><div class="asana-embed-footer-logo" role="img" aria-label="Logo of Asana"></div></a></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="ui tab" data-tab="about">
                        <div class="col-md-12">
                            <div class="tab-content">
                                <div class="asana-embed-container"><link rel="stylesheet" href="https://form.asana.com/static/asana-form-embed-style.css"/><iframe class="asana-embed-iframe" height="533" width = "800" src="https://form.asana.com/?k=zGNvbvatDUqU_0gXwL_UyA&d=34642379201116&embed=true"></iframe><div class="asana-embed-footer"><a rel="nofollow noopener" target="_blank" class="asana-embed-footer-link" href="https://asana.com/?utm_source=embedded_form"><span class="asana-embed-footer-text">Form powered by</span><div class="asana-embed-footer-logo" role="img" aria-label="Logo of Asana"></div></a></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    @endsection
    
    @section('scripts')
        <script src="/assets3/personal-settings-view.js" defer></script>
    @endsection 
