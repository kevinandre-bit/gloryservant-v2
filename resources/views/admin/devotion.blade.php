@extends('layouts.default')

@section('meta')
    <title>Devotions | Glory Servant</title>
    <meta name="description" content="View all employee devotion submissions with filters.">
@endsection

@section('styles')
    <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
    <style>.datepicker {z-index: 9999}</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <h2 class="page-title">{{ __('Devotions') }}</h2>
    </div>

    {{-- Filter form --}}
    <div class="row">
        <div class="box box-success">
            <div class="box-body reportstable">
                <form method="GET" action="{{ url('devotion') }}" class="ui small form form-filter" id="filterform">
                    <div class="inline three fields">
                        <div class="two wide field">
                            <input id="datefrom" type="text" name="datefrom" placeholder="Start Date" class="airdatepicker" value="{{ request('datefrom') }}">
                            <i class="ui icon calendar alternate outline calendar-icon"></i>
                        </div>
                        <div class="two wide field">
                            <input id="dateto" type="text" name="dateto" placeholder="End Date" class="airdatepicker" value="{{ request('dateto') }}">
                            <i class="ui icon calendar alternate outline calendar-icon"></i>
                        </div>
                        <button id="btnfilter" class="ui icon button positive small inline-button">
                            <i class="ui icon filter alternate"></i> {{ __("Filter") }}
                        </button>
                    </div>
                </form>

                {{-- Devotions Table --}}
                <table width="100%" class="table table-striped table-hover" id="dataTables-example">
                    <thead>
                            <tr>
                                <th>{{ __("Devotion date") }}</th>
                                <th>{{ __("Volunteer") }}</th>
                                <th>{{ __("Devotion Text") }}</th>
                                <th>{{ __("Date & time Posted") }}</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach($devotions as $devotion)
                        <tr>
                            <tr>
                                <td>{{ $devotion->devotion_date }}</td>
                                <td>{{ $devotion->employee }}</td>
                                <td><div  style ="
    max-width: 650px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;" title="{{ $devotion->devotion_text }}">
                                    {{ $devotion->devotion_text }}
                                </div></td>
                                <td>{{ $devotion->created_at }}</td>
                            </tr>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('/assets/vendor/air-datepicker/dist/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/air-datepicker/dist/js/i18n/datepicker.en.js') }}"></script>
    <script>
        $('#dataTables-example').DataTable({
            responsive: true,
            pageLength: 15,
            lengthChange: false,
            searching: false,
            ordering: true
        });

        $('.airdatepicker').datepicker({
            language: 'en',
            dateFormat: 'yyyy-mm-dd'
        });
    </script>
@endsection
