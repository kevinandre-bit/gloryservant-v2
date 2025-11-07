 @php
  // define your role groups
  $globalRoles = ['ADMIN','MANAGER','WEB OPERATOR'];
  $campusOnly = ['CAMPUS POC'];
  $deptOnly    = ['HP TEAM','OVERSEER'];
@endphp
@php
  // Whenever “Communication” is chosen, we’ll expand it to this array
  $communicationGroup = [
    'ADMIN',
    'Graphic Design',
    'Video Editing',
    'SEO',
    'Volunteer Care',
    'Social Media',
    'Audio Editing',
    'Radio_TV',
  ];
@endphp
 <!--start header-->
  @include ('layouts/admin_v2')
  <!--end top header-->

 <!--start header-->
  @include ('admin/nav')
  <!--end top header-->

    <!--start sidebar-->
  @include ('admin/sidebar')
  <!--end top sidebar-->

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Leves</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Request</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<div class="btn-group">
							<button type="button" class="btn btn-primary">Settings</button>
							<button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">{{ __("Return") }}</a>
								<a class="dropdown-item" href="javascript:;">{{ __("Raw data") }}</a>
							</div>
						</div> 
					</div>
				</div>
				<!--end breadcrumb-->
      
				<div class="">

    
				<hr>
        <!-- Devotion table -->
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
                  <tr>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Leave From') }}</th>
                                <th>{{ __('Leave To') }}</th>
                                <th>{{ __('Return Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($leaves)
                                @foreach ($leaves as $data)
                                <tr>
                                    <td>{{ $data->employee }}</td>
                                    <td>{{ $data->type }}</td>
                                    <td>@php echo e(date('D, M d, Y', strtotime($data->leavefrom))) @endphp</td>
                                    <td>@php echo e(date('D, M d, Y', strtotime($data->leaveto))) @endphp</td>
                                    <td>@php echo e(date('M d, Y', strtotime($data->returndate))) @endphp</td>
                                    <td><span class="">{{ $data->status }}</span></td>
                                    <td class="align-right">
                                        <a href="{{ url('leaves/edit/'.$data->id) }}" class="ui circular basic icon button tiny" ><i  data-feather="edit"></i></a>
                                        <a href="{{ url('leaves/delete/'.$data->id) }}" class="ui circular basic icon button tiny"><i  data-feather="trash"></i></a>
                                    
                                        @isset($data->comment)
                                            @if($data->comment != null)
                                                <span class="" data-tooltip='{{ $data->comment }}' data-variation='wide' data-position='top right'><i class="fadeIn animated bx bx-comment-detail"></i></span>
                                            @endif
                                        @endisset
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
  </main>
  <!--end main wrapper-->


    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->



     <!--start footer-->
     <footer class="page-footer">
      <p class="mb-0">Copyright © 2025. All right reserved.</p>
    </footer>
    <!--top footer-->

  <!--start cart-->
  
  <!--end cart-->


  <!--start switcher-->
  <button class="btn btn-grd btn-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
  </button>
  
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Theme Customizer</h5>
        <p class="mb-0">Customize your theme</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Blue</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>
        </div><!--end row-->

      </div>
    </div>
  </div>
  <!--start switcher-->

  <!--bootstrap js-->
  <script src="/assets2/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="/assets2/js/jquery.min.js"></script>
  <!--plugins-->
  <script src="/assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="{{ asset('assets3/js/feather.min.js') }}"></script>
  <script>
		feather.replace()
	</script>
  <script src="/assets2/plugins/metismenu/metisMenu.min.js"></script>
  <script src="/assets2/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="/assets2/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#example').DataTable();
		  } );
	</script>
	<script>
		$(document).ready(function() {
			var table = $('#example2').DataTable( {
				pageLength: 15,
				lengthChange: false,
				buttons: [ 'copy', 'excel', 'pdf', 'print']
			} );
		 
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		} );
	</script>
  <script src="/assets2/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="/assets2/js/main.js"></script>

  
  <script type="text/javascript">
    $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: false,ordering: true});
    $('.airdatepicker').datepicker({ language: 'en', dateFormat: 'yyyy-mm-dd' });

    $('.ui.dropdown.getid').dropdown({ onChange: function(value, text, $selectedItem) {
        $('select[name="employee"] option').each(function() {
            if($(this).val()==value) {var id = $(this).attr('data-id');$('input[name="emp_id"]').val(id);};
        });
    }});

    $('#btnfilter').click(function(event) {
        event.preventDefault();
        var emp_id = $('input[name="emp_id"]').val();
        var date_from = $('#datefrom').val();
        var date_to = $('#dateto').val();
        var url = $("#_url").val();

        $.ajax({
            url: url + '/get/employee-leaves/',type: 'get',dataType: 'json',data: {id: emp_id, datefrom: date_from, dateto: date_to},headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                showdata(response);
                function showdata(jsonresponse) {
                    var leaves = jsonresponse;
                    var tbody = $('#dataTables-example tbody');
                    
                    // clear data and destroy datatable
                    $('#dataTables-example').DataTable().destroy();
                    tbody.children('tr').remove();

                    // append table row data
                    for (var i = 0; i < leaves.length; i++) {
                        tbody.append("<tr>"+ "<td>"+leaves[i].employee+"</td>" + "<td>"+leaves[i].type+"</td>" + "<td>"+leaves[i].leavefrom+"</td>" + "<td>"+leaves[i].leaveto+"</td>" + "<td>"+leaves[i].reason+"</td>" + "<td>"+leaves[i].status+"</td>" + "</tr>");
                    }

                    // initialize datatable
                    $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: false,ordering: true});
                }
            }
        })
    });
    </script>

</body>

</html>
