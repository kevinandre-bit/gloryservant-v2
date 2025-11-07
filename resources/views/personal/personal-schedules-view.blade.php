@extends('layouts.personal')

    @section('meta')
        <title>My Schedules | Workday Time Clock</title>
        <meta name="description" content="Workday my schedules, view my schedule records, view present and previous schedules.">
    @endsection

    @section('styles')
        <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
    @endsection

    @section('content')
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <h2 class="page-title">{{ __("My Schedules") }}</h2>
            </div>    
        </div>

        <div class="row">
            <div class="col-md-12">
            <div class="box box-success">
                <div class="box-body reportstable">
                    <form action="" method="get" accept-charset="utf-8" class="ui small form form-filter" id="filterform">
                        @csrf
                        <div class="inline two fields">
                            <div class="three wide field">
                                <label>{{ __("Date Range") }}</label>
                                <input id="datefrom" type="text" name="" value="" placeholder="Start Date" class="airdatepicker">
                                <i class="ui icon calendar alternate outline calendar-icon"></i>
                            </div>

                            <div class="two wide field">
                                <input id="dateto" type="text" name="" value="" placeholder="End Date" class="airdatepicker">
                                <i class="ui icon calendar alternate outline calendar-icon"></i>
                            </div>
                            <button id="btnfilter" class="ui button positive small"><i class="ui icon filter alternate"></i> {{ __("Filter") }}</button>
                        </div>
                    </form>

                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" data-order='[[ 6, "desc" ]]'>
                        <thead>
                            <tr>
                                <th>{{ __("Start Time") }}</th>
                                <th>{{ __("Off Time") }}</th>
                                <th>{{ __("Total Hours") }}</th>
                                <th>{{ __("Rest Days") }}</th>
                                <th>{{ __("From (Date)") }}</th>
                                <th>{{ __("To (Date)") }}</th>
                                <th>{{ __("Status") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($s)
                            @foreach ($s as $sched)
                            <tr>
                                <td>
                                    @php
                                        if($tf == 1) {
                                            echo e($sched->intime);
                                        } else {
                                            echo e(date("H:i", strtotime($sched->intime)));     
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if($tf == 1) {
                                            echo e($sched->outime);
                                        } else {
                                            echo e(date("H:i", strtotime($sched->outime))); 
                                        }
                                    @endphp
                                </td>
                                <td>{{ $sched->hours }} hours</td>
                                <td>{{ $sched->restday }}</td>
                                <td>
                                    @php 
                                        echo e(date('l, F j, Y',strtotime($sched->datefrom)));
                                    @endphp 
                                </td>
                                <td>
                                    @php 
                                        echo e(date('l, F j, Y',strtotime($sched->dateto)));
                                    @endphp
                                </td>
                                <td>
                                    @if($sched->archive == '0') 
                                        <span class="green">Present Schedule</span>
                                    @else
                                        <span class="teal">Past Schedule</span>
                                    @endif
                                </td>
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
    <script src="{{ asset('/assets/vendor/momentjs/moment.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/momentjs/moment-timezone-with-data.js') }}"></script>

    <script src="/assets3/personal-schedules-view.js" defer></script>
    @endsection 
