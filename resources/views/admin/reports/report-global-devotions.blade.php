@extends('layouts.default')
@php use App\Classes\permission; @endphp
@php use Carbon\Carbon; @endphp

@section('meta')
    <title>Global Devotion Report | Glory Servant</title>
    <meta name="description" content="View all employee devotion submissions with filters.">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <h1 class="text-xl font-bold mb-4">{{ __('Devotions') }} <a href="{{ url('admin/reports/devotions') }}" class="ui basic blue button mini offsettop5 float-right"><i class="ui icon chevron left"></i>{{ __("Return") }}</a></h1>
    </div>

    <div class="row">
        <div class="box box-success">
            <div class="box-body">
                <table 
                    width="100%" 
                    class="table table-striped table-hover" 
                    id="dataTables-devotions" 
                    data-order='[[ 0, "desc" ]]'
                >
                    <thead>
                        <tr>
                            <th>{{ __('Devotion Date') }}</th>
                            <th>{{ __('Volunteer') }}</th>
                            <th>{{ __('Campus') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Devotion Text') }}</th>
                            <th>{{ __('Date & Time Posted') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- 
                            The controller should pass $devotions = DB::table('tbl_people_devotion')
                            ->leftJoin(...)->select( .... )->orderBy('devotion_date','desc')->get();
                            
                            Each $devotion row must have these properties:
                              • devotion_date
                              • employee     (i.e. “Lastname, Firstname”)
                              • company
                              • department
                              • devotion_text
                              • created_at
                        --}}

                        @foreach($devotions as $dev)
                            <tr>
                                <td>{{ $dev->devotion_date }}</td>
                                <td>{{ $dev->employee }}</td>
                                <td>{{ $dev->company }}</td>
                                <td>{{ $dev->department }}</td>
                                <td style="max-width:300px; white-space: nowrap; overflow:hidden; text-overflow: ellipsis;"
                                    title="{{ $dev->devotion_text }}"
                                >
                                    {{ $dev->devotion_text }}
                                </td>
                                <td>{{ $dev->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
  {{-- OPTIONAL: DataTables CSS (if using DataTables) --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endsection

@section('scripts')
  {{-- OPTIONAL: jQuery + DataTables JS (if using DataTables) --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

</script>

    {{-- 2) Initialize DataTable --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTables-devotions').DataTable({
                responsive: true,
                pageLength: 50,
                lengthChange: false,
                searching: true,   // enable the search box
                ordering:  true,   // enable column ordering
                autoWidth: false,
                language: {
                  search: "_INPUT_",
                  searchPlaceholder: "{{ __('Search devotions…') }}"
                }
            });
        });
    </script>
@endsection