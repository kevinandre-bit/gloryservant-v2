@extends('layouts.default')
@php use App\Classes\permission; @endphp
    @section('meta')
        <title>Reports | Glory Servant</title>
        <meta name="description" content="Workday reports, view reports, and export or download reports.">
    @endsection 

    @section('content')
    
    <div class="container-fluid">
        <div class="row">
            <h2 class="page-title">{{ __('Reports') }}</h2>
        </div>

        <div class="row">
    <div class="box box-success">
        <div class="box-body">
            <table width="100%" class="reports-table table table-striped table-hover" id="dataTables-example" data-order='[[ 0, "asc" ]]'>
                <thead>
                    <tr>
                        <th>{{ __('Report name') }}</th>
                        <th class="odd">{{ __('Last Viewed') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(permission::permitted('employee-list') === 'success')
                    <tr>
                        <td><a href="{{ url('reports/employee-list') }}"><i class="ui icon users"></i> {{ __('Volunteer List Report') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 4)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif
                    @if(permission::permitted('meeting-attendance') === 'success')
                    <tr>
                        <td>
                            <a href="{{ url('meeting-attendance') }}">
                                <i class="ui icon chart bar outline"></i> {{ __('Meeting Attendance Report') }}
                            </a>
                        </td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 2)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif
                    
                    @if(permission::permitted('devotions') === 'success')
                    <tr>
                        <td>
                            <a href="{{ url('admin/reports/devotions') }}">
                                <i class="icon book"></i> {{ __('Devotions Report') }}
                            </a>
                        </td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 1)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('employee-attendance') === 'success')
                    <tr>
                        <td><a href="{{ url('admin/reports/employee-attendance') }}"><i class="ui icon clock"></i> {{ __('Volunteer Attendance Report') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 3)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('employee-leaves') === 'success')
                    <tr>
                        <td><a href="{{ url('reports/employee-leaves') }}"><i class="ui icon calendar plus"></i> {{ __('Volunteer Leaves Report') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 6)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('employee-schedule') === 'success')
                    <tr>
                        <td><a href="{{ url('reports/employee-schedule') }}"><i class="ui icon calendar alternate outline"></i> {{ __('Volunteer Schedule Report') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 8)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('asana-portfolio') === 'success')
                    <tr>
                        <td><a href="{{ url('admin/asana-portfolio') }}"><i class="ui icon list pie"></i> {{ __("Checklist Monitoring") }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 6)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('employee-birthdays') === 'success')
                    <tr>
                        <td><a href="{{ url('reports/employee-birthdays') }}"><i class="ui icon birthday cake"></i> {{ __('Volunteer Birthdays') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 7)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('user-accounts') === 'success')
                    <tr>
                        <td><a href="{{ url('reports/user-accounts') }}"><i class="ui icon address book outline"></i> {{ __('User Accounts Report') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 5)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif

                    @if(permission::permitted('user-activity') === 'success')
                    <tr>
                        <td><a href="{{ url('report/user-activity') }}"><i class="icon globe"></i> {{ __('User activity Report') }}</a></td>
                        <td class="odd">
                            @isset($lastviews)
                                @foreach ($lastviews as $views)
                                    @if($views->report_id == 9)
                                        {{ $views->last_viewed }}
                                    @endif
                                @endforeach
                            @endisset
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>

    @endsection
    
    @section('scripts')
    <script type="text/javascript">
    $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: false,ordering: true});
    </script>
    @endsection 