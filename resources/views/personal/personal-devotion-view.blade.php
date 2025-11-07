@extends('layouts.personal')

@section('meta')
    <title>My Devotions | Glory Servant</title>
    <meta name="description" content="Workday my devotions, view all my devotions">
@endsection

@section('styles')
    <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
@include('personal.modals.modal-post-devotion')

<div class="container-fluid">
    <div class="row">
            <h2 class="page-title">{{ __("My Devotions") }}
            <button class="ui positive button mini offsettop5 btn-add float-right"><i class="ui icon write"></i>{{ __("Post Devotion") }}</button>
            </h2>
        </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-body reportstable">
                    <form action="" method="get" accept-charset="utf-8" class="ui small form form-filter" id="filterform">
                        @csrf
                        <input type="hidden" id="_url" value="{{ url('/') }}">
                        <div class="inline two fields">
                            <div class="three wide field">
                                <label>{{ __("Date Range") }}</label>
                                <input id="datefrom" type="text" name="datefrom" value="" placeholder="Start Date" class="airdatepicker">
                                <i class="ui icon calendar alternate outline calendar-icon"></i>
                            </div>
                            <div class="two wide field">
                                <input id="dateto" type="text" name="dateto" value="" placeholder="End Date" class="airdatepicker">
                                <input type="hidden" id="_url" value="{{ url('') }}">
                                <i class="ui icon calendar alternate outline calendar-icon"></i>
                            </div>
                            <button id="btnfilter" class="ui button positive small"><i class="ui icon filter alternate"></i> {{ __("Filter") }}</button>
                        </div>
                    </form>

                    <table width="100%" class="table table-bordered table-hover" id="dataTables-example" data-order='[[ 0, "desc" ]]'>
                        <thead>
                            <tr>
                                <th>{{ __("Devotion date") }}</th>
                                <th>{{ __("Devotion Text") }}</th>
                                <th>{{ __("Date & time Posted") }}</th>
                                <th>{{ __("Status") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($devotions)
                                @foreach ($devotions as $devotion)
                                    <tr>
                                        <td>{{ $devotion->devotion_date }}</td>
                                        <td>{{ $devotion->devotion_text }}</td>
                                        <td>{{ $devotion->created_at }}</td>
                                        <td>{{ $devotion->status }}</td>
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('/assets/vendor/air-datepicker/dist/js/datepicker.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/air-datepicker/dist/js/i18n/datepicker.en.js') }}"></script>


<script nonce="{{ $cspNonce ?? '' }}">
    // Initialize DataTable
    $('#dataTables-example').DataTable({
        responsive: true,
        pageLength: 15,
        lengthChange: false,
        searching: false,
        ordering: true
    });

    // Initialize datepicker
    $('.airdatepicker').datepicker({
        language: 'en',
        dateFormat: 'yyyy-mm-dd'
    });

    // Handle filter form submit
    $('#filterform').submit(function(event) {
        event.preventDefault();

        var date_from = $('#datefrom').val();
        var date_to = $('#dateto').val();
        var url = $('#_url').val(); // base URL

        console.log("Submitting filter:", date_from, date_to);

        $.ajax({
            url: url + '/get/personal/devotion', // ✅ fixed trailing slash
            type: 'get',
            dataType: 'json',
            data: {
                datefrom: date_from,
                dateto: date_to
            },
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log("AJAX success:", response);

                var devotions = response;
                var tbody = $('#dataTables-example tbody');

                // Destroy DataTable before modifying DOM
                $('#dataTables-example').DataTable().destroy();
                tbody.empty();

                // Append new data
                for (var i = 0; i < devotions.length; i++) {
                    tbody.append("<tr>" +
                        "<td>" + devotions[i].devotion_date + "</td>" +
                        "<td>" + devotions[i].devotion_text + "</td>" +
                        "<td>" + devotions[i].status + "</td>" +
                        "</tr>");
                }

                // Reinitialize DataTable
                $('#dataTables-example').DataTable({
                    responsive: true,
                    pageLength: 15,
                    lengthChange: false,
                    searching: false,
                    ordering: true
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                console.log("Response Text:", xhr.responseText);
            }
        });
    });
</script>

@endsection
