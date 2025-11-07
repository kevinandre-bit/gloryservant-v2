@extends('layouts.personal')

    @section('meta')
        <title>My Attendances | Glory Servant</title>
        <meta name="description" content="Workday my attendance, view all my attendances, and clock-in/out">
    @endsection

    @section('styles')
        <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
    @endsection

    @section('content')
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-title">{{ __("My Attendances") }}</h2>
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

                    <table width="100%" class="table table-bordered table-hover" id="dataTables-example" data-order='[[ 0, "desc" ]]'>
                        <thead>
                            <tr>
                                <th>{{ __("Date") }}</th>
                                <th>{{ __("Time In") }}</th>
                                <th>{{ __("Time Out") }}</th>
                                <th>{{ __("Total Hours") }}</th>
                                <th>{{ __("Status (In/Out)") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($a)
                            @foreach ($a as $v)
                                <tr>
                                    <td>{{ $v->date }}</td>
                                    <td>
                                        @php
                                            if($v->timein != null) {
                                                if ($tf == 1) {
                                                    echo e(date('h:i:s A', strtotime($v->timein)));
                                                } else {
                                                    echo e(date('H:i:s', strtotime($v->timein)));
                                                }
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            if($v->timeout != null) {
                                                if ($tf == 1) {
                                                    echo e(date('h:i:s A', strtotime($v->timeout)));
                                                } else {
                                                    echo e(date('H:i:s', strtotime($v->timeout)));
                                                }
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                    @isset($v->totalhours)
                                        @if($v->totalhours != null) 
                                            @php
                                                if(stripos($v->totalhours, ".") === false) {
                                                    $h = $v->totalhours;
                                                } else {
                                                    $HM = explode('.', $v->totalhours); 
                                                    $h = $HM[0]; 
                                                    $m = $HM[1];
                                                }
                                            @endphp
                                        @endif
                                        @if($v->totalhours != null)
                                            @if(stripos($v->totalhours, ".") === false) 
                                                {{ $h }} hr
                                            @else 
                                                {{ $h }} hr {{ $m }} mins
                                            @endif
                                        @endif
                                    @endisset
                                    </td>
                                    <td>
                                        @if($v->status_timein != '' && $v->status_timeout != '') 
                                            <span class="@if($v->status_timein == 'Late In') orange @else blue @endif">{{ $v->status_timein }}</span> / 
                                            <span class="@if($v->status_timeout == 'Early Out') red @else green @endif">{{ $v->status_timeout }}</span> 
                                        @elseif($v->status_timein == 'Late In') 
                                            <span class="orange">{{ $v->status_timein }}</span>
                                        @else 
                                            <span class="blue">{{ $v->status_timein }}</span>
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
    {{-- inline JS moved to /assets3/personal-attendance-view.js --}}

    <script src="/assets3/personal-attendance-view.js" defer></script>
    <script src="/assets3/personal-attendance-view.js" defer></script>
    @endsection 
