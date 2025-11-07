@php
    $segments = request()->segments();
    $lastSegment = last($segments);

    if ($lastSegment === 'admin_v2') {
        $lastSegment = null;
    }

    $page = $lastSegment ? $lastSegment . '.php' : 'index.php';
@endphp

<!-- Header -->
<div class="header">
	<div class="main-header">

		<div class="header-left">
			<a href="{{ route('admin_v2.index') }}" class="logo">
				<img src="{{ asset('assets3/img/logo.svg') }}" alt="Logo">
			</a>
			<?php if($page !== 'layout-horizontal-box.php') {?>
				<a href="{{ route('admin_v2.index') }}" class="dark-logo">
					<img src="{{ asset('assets3/img/logo-white.svg') }}" alt="Logo">
				</a>
			<?php } ?>
		</div>

		<a id="mobile_btn" class="mobile_btn" href="#sidebar">
			<span class="bar-icon">
				<span></span>
				<span></span>
				<span></span>
			</span>
		</a>

		<div class="header-user">
			<div class="nav user-menu nav-list">

				<div class="me-auto d-flex align-items-center" id="header-search">
					<a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
						<i class="ti ti-arrow-bar-to-left"></i>
					</a>
					<!-- Search -->
					<div class="input-group input-group-flat d-inline-flex me-1">
						<span class="input-icon-addon">
							<i class="ti ti-search"></i>
						</span>
						<input type="text" class="form-control" placeholder="Search in HRMS">
						<span class="input-group-text">
							<kbd>CTRL + / </kbd>
						</span>
					</div>
					<!-- /Search -->
					<div class="dropdown crm-dropdown">
						<a href="#" class="btn btn-menubar me-1" data-bs-toggle="dropdown">
							<i class="ti ti-layout-grid"></i>
						</a>
						<div class="dropdown-menu dropdown-lg dropdown-menu-start">
							<div class="card mb-0 border-0 shadow-none">
								<div class="card-header">
									<h4>CRM</h4>
								</div>
								<div class="card-body pb-1">		
									<div class="row">
										<div class="col-sm-6">							
											<a href="{{ route('admin_v2.contacts') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
												<span class="d-flex align-items-center me-3">
													<i class="ti ti-user-shield text-default me-2"></i>Contacts
												</span>
												<i class="ti ti-arrow-right"></i>
											</a>							
											<a href="{{ route('admin_v2.deals-grid') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
												<span class="d-flex align-items-center me-3">
													<i class="ti ti-heart-handshake text-default me-2"></i>Deals
												</span>
												<i class="ti ti-arrow-right"></i>
											</a>								
											<a href="{{ route('admin_v2.pipeline') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
												<span class="d-flex align-items-center me-3">
													<i class="ti ti-timeline-event-text text-default me-2"></i>Pipeline
												</span>
												<i class="ti ti-arrow-right"></i>
											</a>		
										</div>
										<div class="col-sm-6">							
											<a href="{{ route('admin_v2.companies-grid') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
												<span class="d-flex align-items-center me-3">
													<i class="ti ti-building text-default me-2"></i>Companies
												</span>
												<i class="ti ti-arrow-right"></i>
											</a>								
											<a href="{{ route('admin_v2.leads-grid') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
												<span class="d-flex align-items-center me-3">
													<i class="ti ti-user-check text-default me-2"></i>Leads
												</span>
												<i class="ti ti-arrow-right"></i>
											</a>								
											<a href="{{ route('admin_v2.activity') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
												<span class="d-flex align-items-center me-3">
													<i class="ti ti-activity text-default me-2"></i>Activities
												</span>
												<i class="ti ti-arrow-right"></i>
											</a>		
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<a href="{{ route('admin_v2.profile-settings') }}" class="btn btn-menubar">
						<i class="ti ti-settings-cog"></i>
					</a>	
				</div>

				<!-- Horizontal Single -->
				<div class="sidebar sidebar-horizontal" id="horizontal-single">
					<div class="sidebar-menu">
						<div class="main-menu">
							<ul class="nav-menu">
								<li class="menu-title">
									<span>Main</span>
								</li>
								<li class="submenu">
									<a href="#" class=" <?php echo ($page == 'index.php'||$page == 'employee-dashboard.php'||$page == 'deals-dashboard.php'||$page == 'leads-dashboard.php') ? 'active subdrop' : ''; ?>">
										<i class="ti ti-smart-home"></i><span>Dashboard</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.index') }}" class="<?php echo ($page == 'index.php') ? 'active' : ''; ?>">Admin Dashboard</a></li>
										<li><a href="{{ route('admin_v2.employee-dashboard') }}" class="<?php echo ($page == 'employee-dashboard.php') ? 'active' : ''; ?>">Employee Dashboard</a></li>
										<li><a href="{{ route('admin_v2.deals-dashboard') }}" class="<?php echo ($page == 'deals-dashboard.php') ? 'active' : ''; ?>">Deals Dashboard</a></li>
										<li><a href="{{ route('admin_v2.leads-dashboard') }}" class="<?php echo ($page == 'leads-dashboard.php') ? 'active' : ''; ?>">Leads Dashboard</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#"class=" <?php echo ($page == 'dashboard.php'||$page == 'companies.php'||$page == 'subscription.php'||$page == 'packages.php'||$page == 'domain.php'||$page == 'purchase-transaction.php') ? 'active subdrop' : ''; ?>">
										<i class="ti ti-user-star"></i><span>Super Admin</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.dashboard') }}" class="<?php echo ($page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
										<li><a href="{{ route('admin_v2.companies') }}" class="<?php echo ($page == 'companies.php') ? 'active' : ''; ?>">Companies</a></li>
										<li><a href="{{ route('admin_v2.subscription') }}" class="<?php echo ($page == 'subscription.php') ? 'active' : ''; ?>">Subscriptions</a></li>
										<li><a href="{{ route('admin_v2.packages') }}" class="<?php echo ($page == 'packages.php') ? 'active' : ''; ?>">Packages</a></li>
										<li><a href="{{ route('admin_v2.domain') }}" class="<?php echo ($page == 'domain.php') ? 'active' : ''; ?>">Domain</a></li>
										<li><a href="{{ route('admin_v2.purchase-transaction') }}" class="<?php echo ($page == 'purchase-transaction.php') ? 'active' : ''; ?>">Purchase Transaction</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#"class=" <?php echo ($page == 'chat.php'||$page == 'call.php'||$page == 'voice-call.php'||$page == 'video-call.php'||$page == 'outgoing-call.php'||$page == 'incoming-call.php'||$page == 'call-history.php'||$page == 'calendar.php'
							||$page == 'email.php'||$page == 'todo.php'||$page == 'notes.php'||$page == 'social-feed.php'||$page == 'file-manager.php'||$page == 'kanban-view.php'||$page == 'invoices.php') ? 'active subdrop' : ''; ?>">
										<i class="ti ti-layout-grid-add"></i><span>Applications</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="<?php echo ($page == 'chat.php') ? 'active' : ''; ?>"><a href="{{ route('admin_v2.chat') }}" >Chat</a></li>
										<li class="submenu submenu-two">
											<a href="{{ route('admin_v2.call') }}" class="<?php echo ($page == 'call.php') ? 'active' : ''; ?>">Calls<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.voice-call') }}" class="<?php echo ($page == 'voice-call.php') ? 'active' : ''; ?>">Voice Call</a></li>
												<li><a href="{{ route('admin_v2.video-call') }}" class="<?php echo ($page == 'video-call.php') ? 'active' : ''; ?>">Video Call</a></li>
												<li><a href="{{ route('admin_v2.outgoing-call') }}" class="<?php echo ($page == 'outgoing-call.php') ? 'active' : ''; ?>">Outgoing Call</a></li>
												<li><a href="{{ route('admin_v2.incoming-call') }}" class="<?php echo ($page == 'incoming-call.php') ? 'active' : ''; ?>">Incoming Call</a></li>
												<li><a href="{{ route('admin_v2.call-history') }}" class="<?php echo ($page == 'call-history.php') ? 'active' : ''; ?>">Call History</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.calendar') }}" class="<?php echo ($page == 'calendar.php') ? 'active' : ''; ?>">Calendar</a></li>
										<li><a href="{{ route('admin_v2.email') }}" class="<?php echo ($page == 'email.php') ? 'active' : ''; ?>">Email</a></li>
										<li><a href="{{ route('admin_v2.todo') }}" class="<?php echo ($page == 'todo.php') ? 'active' : ''; ?>">To Do</a></li>
										<li><a href="{{ route('admin_v2.notes') }}" class="<?php echo ($page == 'notes.php') ? 'active' : ''; ?>">Notes</a></li>
										<li><a href="{{ route('admin_v2.file-manager') }}" class="<?php echo ($page == 'file-manager.php') ? 'active' : ''; ?>">File Manager</a></li>
										<li><a href="{{ route('admin_v2.kanban-view') }}" class="<?php echo ($page == 'kanban-view.php') ? 'active' : ''; ?>">Kanban</a></li>
										<li><a href="{{ route('admin_v2.invoices') }}" class="<?php echo ($page == 'invoices.php') ? 'active' : ''; ?>">Invoices</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#" class=" <?php echo ($page == 'layout-horizontal.php'||$page == 'layout-detached.php'||$page == 'layout-modern.php'||$page == 'layout-two-column.php'||$page == 'layout-hovered.php'||$page == 'layout-horizontal-single.php'||$page == 'layout-horizontal-overlay.php'||$page == 'layout-horizontal-box.php'||$page == 'layout-horizontal-sidemenu.php'||$page == 'layout-vertical-transparent.php'||$page == 'layout-without-header.php'||$page == 'layout-vertical-transparent.php'||$page == 'layout-rtl.php'||$page == 'layout-dark.php') ? 'active subdrop' : ''; ?>">
										<i class="ti ti-layout-board-split"></i><span>Layouts</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.layout-horizontal') }}" class="<?php echo ($page == 'layout-horizontal.php') ? 'active' : ''; ?>">
												<span>Horizontal</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-detached') }}" class="<?php echo ($page == 'layout-detached.php') ? 'active' : ''; ?>">
												<span>Detached</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-modern') }}" class="<?php echo ($page == 'layout-modern.php') ? 'active' : ''; ?>">
												<span>Modern</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-two-column') }}" class="<?php echo ($page == 'layout-two-column.php') ? 'active' : ''; ?>">
												<span>Two Column </span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-hovered') }}" class="<?php echo ($page == 'layout-hovered.php') ? 'active' : ''; ?>">
												<span>Hovered</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-box') }}" class="<?php echo ($page == 'layout-box.php') ? 'active' : ''; ?>">
												<span>Boxed</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-horizontal-single') }}" class="<?php echo ($page == 'layout-horizontal-single.php') ? 'active' : ''; ?>">
												<span>Horizontal Single</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-horizontal-overlay') }}" class="<?php echo ($page == 'layout-horizontal-overlay.php') ? 'active' : ''; ?>">
												<span>Horizontal Overlay</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-horizontal-box') }}" class="<?php echo ($page == 'layout-horizontal-box.php') ? 'active' : ''; ?>">
												<span>Horizontal Box</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-horizontal-sidemenu') }}" class="<?php echo ($page == 'layout-horizontal-sidemenu.php') ? 'active' : ''; ?>">
												<span>Menu Aside</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-vertical-transparent') }}" class="<?php echo ($page == 'layout-vertical-transparent.php') ? 'active' : ''; ?>">
												<span>Transparent</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-without-header') }}" class="<?php echo ($page == 'layout-without-header.php') ? 'active' : ''; ?>">
												<span>Without Header</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-rtl') }}" class="<?php echo ($page == 'layout-rtl.php') ? 'active' : ''; ?>">
												<span>RTL</span>
											</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.layout-dark') }}" class="<?php echo ($page == 'layout-dark.php') ? 'active' : ''; ?>">
												<span>Dark</span>
											</a>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#">
										<i class="ti ti-user-star"></i><span>Projects</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="<?php echo ($page == 'clients-grid.php') ? 'active' : ''; ?>">
											<a href="{{ route('admin_v2.clients-grid') }}"><span>Clients</span>
											</a>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);" class=" <?php echo ($page == 'projects-grid.php'||$page == 'tasks.php'||$page == 'task-board.php') ? 'active subdrop' : ''; ?>"><span>Projects</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.projects-grid') }}" class="<?php echo ($page == 'projects-grid.php') ? 'active' : ''; ?>">Projects</a></li>
												<li><a href="{{ route('admin_v2.tasks') }}" class="<?php echo ($page == 'tasks.php') ? 'active' : ''; ?>">Tasks</a></li>
												<li><a href="{{ route('admin_v2.task-board') }}" class="<?php echo ($page == 'task-board.php') ? 'active' : ''; ?>">Task Board</a></li>
											</ul>
										</li>		
										<li class="submenu">
											<a href="{{ route('admin_v2.call') }}" class="<?php echo ($page == 'call.php') ? 'active' : ''; ?>">Crm<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.contacts-grid') }}" class="<?php echo ($page == 'contacts-grid.php') ? 'active' : ''; ?>"><span>Contacts</span></a></li>
												<li><a href="{{ route('admin_v2.companies-grid') }}" class="<?php echo ($page == 'companies-grid.php') ? 'active' : ''; ?>"><span>Companies</span></a></li>
												<li><a href="{{ route('admin_v2.deals-grid') }}" class="<?php echo ($page == 'deals-grid.php') ? 'active' : ''; ?>"><span>Deals</span></a></li>
												<li><a href="{{ route('admin_v2.leads-grid') }}" class="<?php echo ($page == 'leads-grid.php') ? 'active' : ''; ?>" ><span>Leads</span></a></li>
												<li><a href="{{ route('admin_v2.pipeline') }}" class="<?php echo ($page == 'pipeline.php') ? 'active' : ''; ?>"><span>Pipeline</span></a></li>
												<li><a href="{{ route('admin_v2.analytics') }}" class="<?php echo ($page == 'analytics.php') ? 'active' : ''; ?>"><span>Analytics</span></a></li>
												<li><a href="{{ route('admin_v2.activity') }}" class="<?php echo ($page == 'activity.php') ? 'active' : ''; ?>"><span>Activities</span></a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class=" <?php echo ($page == 'employees.php'||$page == 'employees-grid.php'||$page == 'employee-details.php'||$page == 'departments.php'||$page == 'designations.php'||$page == 'policy.php') ? 'active subdrop' : ''; ?>"><span>Employees</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.employees') }}" class="<?php echo ($page == 'employees.php') ? 'active' : ''; ?>">Employee Lists</a></li>
												<li><a href="{{ route('admin_v2.employees-grid') }}" class="<?php echo ($page == 'employees-grid.php') ? 'active' : ''; ?>">Employee Grid</a></li>
												<li><a href="{{ route('admin_v2.employee-details') }}" class="<?php echo ($page == 'employee-details.php') ? 'active' : ''; ?>">Employee Details</a></li>
												<li><a href="{{ route('admin_v2.departments') }}" class="<?php echo ($page == 'departments.php') ? 'active' : ''; ?>">Departments</a></li>
												<li><a href="{{ route('admin_v2.designations') }}" class="<?php echo ($page == 'designations.php') ? 'active' : ''; ?>">Designations</a></li>
												<li><a href="{{ route('admin_v2.policy') }}" class="<?php echo ($page == 'policy.php') ? 'active' : ''; ?>">Policies</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class=" <?php echo ($page == 'tickets.php'||$page == 'ticket-details.php') ? 'active subdrop' : ''; ?>"><span>Tickets</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.tickets') }}" class="<?php echo ($page == 'tickets.php') ? 'active' : ''; ?>">Tickets</a></li>
												<li><a href="{{ route('admin_v2.ticket-details') }}" class="<?php echo ($page == 'ticket-details.php') ? 'active' : ''; ?>">Ticket Details</a></li>
											</ul>
										</li>
										<li class="<?php echo ($page == 'holidays.php') ? 'active' : ''; ?>"><a href="{{ route('admin_v2.holidays') }}"><span>Holidays</span></a></li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Attendance</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu">
													<a href="javascript:void(0);"class=" <?php echo ($page == 'leaves.php'||$page == 'leaves-employee.php'||$page == 'leave-settings.php') ? 'active subdrop' : ''; ?>">Leaves<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.leaves') }}" class="<?php echo ($page == 'leaves.php') ? 'active' : ''; ?>">Leaves (Admin)</a></li>
														<li><a href="{{ route('admin_v2.leaves-employee') }}" class="<?php echo ($page == 'leaves-employee.php') ? 'active' : ''; ?>">Leave (Employee)</a></li>
														<li><a href="{{ route('admin_v2.leave-settings') }}" class="<?php echo ($page == 'leave-settings.php') ? 'active' : ''; ?>">Leave Settings</a></li>												
													</ul>												
												</li>
												<li><a href="{{ route('admin_v2.attendance-admin') }}" class="<?php echo ($page == 'attendance-admin.php') ? 'active' : ''; ?>">Attendance (Admin)</a></li>
												<li><a href="{{ route('admin_v2.attendance-employee') }}" class="<?php echo ($page == 'attendance-employee.php') ? 'active' : ''; ?>">Attendance (Employee)</a></li>
												<li><a href="{{ route('admin_v2.timesheets') }}" class="<?php echo ($page == 'timesheets.php') ? 'active' : ''; ?>">Timesheets</a></li>
												<li><a href="{{ route('admin_v2.schedule.index') }}" class="<?php echo ($page == 'schedule-timing.php') ? 'active' : ''; ?>">Shift & Schedule</a></li>
												<li><a href="{{ route('admin_v2.overtime') }}" class="<?php echo ($page == 'overtime.php') ? 'active' : ''; ?>">Overtime</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class=" <?php echo ($page == 'performance-indicator.php'||$page == 'performance-review.php'||$page == 'performance-appraisal.php'||$page == 'goal-tracking.php'||$page == 'goal-type.php') ? 'active subdrop' : ''; ?>"><span>Performance</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.performance-indicator') }}" class="<?php echo ($page == 'performance-indicator.php') ? 'active' : ''; ?>">Performance Indicator</a></li>
												<li><a href="{{ route('admin_v2.performance-review') }}" class="<?php echo ($page == 'performance-review.php') ? 'active' : ''; ?>">Performance Review</a></li>
												<li><a href="{{ route('admin_v2.performance-appraisal') }}" class="<?php echo ($page == 'performance-appraisal.php') ? 'active' : ''; ?>">Performance Appraisal</a></li>
												<li><a href="{{ route('admin_v2.goal-tracking') }}" class="<?php echo ($page == 'goal-tracking.php') ? 'active' : ''; ?>">Goal List</a></li>
												<li><a href="{{ route('admin_v2.goal-type') }}" class="<?php echo ($page == 'goal-type.php') ? 'active' : ''; ?>">Goal Type</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class=" <?php echo ($page == 'training.php'||$page == 'trainers.php'||$page == 'training-type.php') ? 'active subdrop' : ''; ?>"><span>Training</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.training') }}" class="<?php echo ($page == 'training.php') ? 'active' : ''; ?>">Training List</a></li>
												<li><a href="{{ route('admin_v2.trainers') }}" class="<?php echo ($page == 'trainers.php') ? 'active' : ''; ?>">Trainers</a></li>
												<li><a href="{{ route('admin_v2.training-type') }}" class="<?php echo ($page == 'training-type.php') ? 'active' : ''; ?>">Training Type</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.promotion') }}" class="<?php echo ($page == 'promotion.php') ? 'active' : ''; ?>"><span>Promotion</span></a></li>
										<li><a href="{{ route('admin_v2.resignation') }}" class="<?php echo ($page == 'resignation.php') ? 'active' : ''; ?>"><span>Resignation</span></a></li>
										<li><a href="{{ route('admin_v2.termination') }}" class="<?php echo ($page == 'termination.php') ? 'active' : ''; ?>"><span>Termination</span></a></li>														
									</ul>
								</li>
								<li class="submenu">
									<a href="#">
										<i class="ti ti-user-star"></i><span>Administration</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu">
											<a href="javascript:void(0);"class=" <?php echo ($page == 'estimates.php'||$page == 'invoices.php'||$page == 'payments.php'||$page == 'expenses.php'||$page == 'provident-fund.php'||$page == 'taxes.php') ? 'active subdrop' : ''; ?>"><span>Sales</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.estimates') }}" class="<?php echo ($page == 'estimates.php') ? 'active' : ''; ?>">Estimates</a></li>
												<li><a href="{{ route('admin_v2.invoices') }}" class="<?php echo ($page == 'invoices.php') ? 'active' : ''; ?>">Invoices</a></li>
												<li><a href="{{ route('admin_v2.payments') }}" class="<?php echo ($page == 'payments.php') ? 'active' : ''; ?>">Payments</a></li>
												<li><a href="{{ route('admin_v2.expenses') }}" class="<?php echo ($page == 'expenses.php') ? 'active' : ''; ?>">Expenses</a></li>
												<li><a href="{{ route('admin_v2.provident-fund') }}" class="<?php echo ($page == 'provident-fund.php') ? 'active' : ''; ?>">Provident Fund</a></li>
												<li><a href="{{ route('admin_v2.taxes') }}" class="<?php echo ($page == 'taxes.php') ? 'active' : ''; ?>">Taxes</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class=" <?php echo ($page == 'categories.php'||$page == 'budgets.php'||$page == 'budget-expenses.php'||$page == 'budget-revenues.php') ? 'active subdrop' : ''; ?>"><span>Accounting</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.categories') }}" class="<?php echo ($page == 'categories.php') ? 'active' : ''; ?>">Categories</a></li>
												<li><a href="{{ route('admin_v2.budgets') }}" class="<?php echo ($page == 'budgets.php') ? 'active' : ''; ?>">Budgets</a></li>
												<li><a href="{{ route('admin_v2.budget-expenses') }}" class="<?php echo ($page == 'budget-expenses.php') ? 'active' : ''; ?>">Budget Expenses</a></li>
												<li><a href="{{ route('admin_v2.budget-revenues') }}" class="<?php echo ($page == 'budget-revenues.php') ? 'active' : ''; ?>">Budget Revenues</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class="<?php echo ($page == 'employee-salary.php'||$page == 'payslip.php'||$page == 'payroll.php') ? 'active subdrop' : ''; ?>"><span>Payroll</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.employee-salary') }}" class="<?php echo ($page == 'employee-salary.php') ? 'active' : ''; ?>">Employee Salary</a></li>
												<li><a href="{{ route('admin_v2.payslip') }}" class="<?php echo ($page == 'payslip.php') ? 'active' : ''; ?>">Payslip</a></li>
												<li><a href="{{ route('admin_v2.payroll') }}" class="<?php echo ($page == 'payroll.php') ? 'active' : ''; ?>">Payroll Items</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class="<?php echo ($page == 'assets-list.php'||$page == 'asset-categories.php') ? 'active subdrop' : ''; ?>"><span>Assets</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.assets') }}" class="<?php echo ($page == 'assets.php') ? 'active' : ''; ?>">Assets</a></li>
												<li><a href="{{ route('admin_v2.asset-categories') }}" class="<?php echo ($page == 'asset-categories.php') ? 'active' : ''; ?>">Asset Categories</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class="<?php echo ($page == 'knowledgebase.php'||$page == 'activity.php') ? 'active subdrop' : ''; ?>"><span>Help & Supports</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.knowledgebase') }}" class="<?php echo ($page == 'knowledgebase.php') ? 'active' : ''; ?>">Knowledge Base</a></li>
												<li><a href="{{ route('admin_v2.activity') }}" class="<?php echo ($page == 'activity.php') ? 'active' : ''; ?>">Activities</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class="<?php echo ($page == 'users.php'||$page == 'roles-permissions.php') ? 'active subdrop' : ''; ?>"><span>User Management</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.users') }}" class="<?php echo ($page == 'users.php') ? 'active' : ''; ?>">Users</a></li>
												<li><a href="{{ route('admin_v2.roles-permissions') }}" class="<?php echo ($page == 'roles-permissions.php') ? 'active' : ''; ?>">Roles & Permissions</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);" class="<?php echo ($page == 'expenses-report.php'||$page == 'invoice-report.php'||$page == 'payment-report.php'||$page == 'project-report.php'||$page == 'task-report.php'||$page == 'user-report.php'||$page == 'employee-report.php'||$page == 'payslip-report.php'||$page == 'attendance-report.php'||$page == 'leave-report.php'||$page == 'daily-report.php') ? 'active subdrop' : ''; ?>"><span>Reports</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.expenses-report') }}" class="<?php echo ($page == 'expenses-report.php') ? 'active' : ''; ?>">Expense Report</a></li>
												<li><a href="{{ route('admin_v2.invoice-report') }}" class="<?php echo ($page == 'invoice-report.php') ? 'active' : ''; ?>">Invoice Report</a></li>
												<li><a href="{{ route('admin_v2.payment-report') }}" class="<?php echo ($page == 'payment-report.php') ? 'active' : ''; ?>">Payment Report</a></li>
												<li><a href="{{ route('admin_v2.project-report') }}" class="<?php echo ($page == 'project-report.php') ? 'active' : ''; ?>">Project Report</a></li>
												<li><a href="{{ route('admin_v2.task-report') }}" class="<?php echo ($page == 'task-report.php') ? 'active' : ''; ?>">Task Report</a></li>
												<li><a href="{{ route('admin_v2.user-report') }}" class="<?php echo ($page == 'user-report.php') ? 'active' : ''; ?>">User Report</a></li>
												<li><a href="{{ route('admin_v2.employee-report') }}" class="<?php echo ($page == 'employee-report.php') ? 'active' : ''; ?>">Employee Report</a></li>
												<li><a href="{{ route('admin_v2.payslip-report') }}" class="<?php echo ($page == 'payslip-report.php') ? 'active' : ''; ?>">Payslip Report</a></li>
												<li><a href="{{ route('admin_v2.attendance-report') }}" class="<?php echo ($page == 'attendance-report.php') ? 'active' : ''; ?>">Attendance Report</a></li>
												<li><a href="{{ route('admin_v2.leave-report') }}" class="<?php echo ($page == 'leave-report.php') ? 'active' : ''; ?>">Leave Report</a></li>
												<li><a href="{{ route('admin_v2.daily-report') }}" class="<?php echo ($page == 'daily-report.php') ? 'active' : ''; ?>">Daily Report</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"class="<?php echo ($page == 'profile-settings.php'||
							$page == 'security-settings.php'||
							$page == 'notification-settings.php'||
							$page == 'project-report.php'||
							$page == 'connected-apps.php'||
							$page == 'bussiness-settings.php'||
							$page == 'seo-settings.php'||
							$page == 'localization-settings.php'||
							$page == 'prefixes.php'||
							$page == 'preferences.php'||
							$page == 'performance-appraisal.php'||
							$page == 'language.php'||
							$page == 'authentication-settings.php'||
							$page == 'ai-settings.php'||
							$page == 'salary-settings.php'||
							$page == 'approval-settings.php'||
							$page == 'invoice-settings.php'||
							$page == 'leave-type.php'||
							$page == 'custom-fields.php'||
							$page == 'email-settings.php'||
							$page == 'email-template.php'||
							$page == 'sms-settings.php'||
							$page == 'sms-template.php'||
							$page == 'otp-settings.php'||
							$page == 'gdpr.php'||
							$page == 'maintenance-mode.php'||
							$page == 'payment-gateways.php'||
							$page == 'tax-rates.php'||
							$page == 'currencies.php'||
							$page == 'custom-css.php'||
							$page == 'custom-js.php'||
							$page == 'cronjob.php'||
							$page == 'storage-settings.php'||
							$page == 'ban-ip-address.php'||
							$page == 'backup.php'||
							$page == 'clear-cache.php'
							) ? 'active subdrop' : ''; ?>"><span>Settings</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'profile-settings.php'||$page == 'security-settings.php'||$page == 'notification-settings.php'||$page == 'connected-apps.php') ? 'active subdrop' : ''; ?>">General Settings<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.profile-settings') }}" class="<?php echo ($page == 'profile-settings.php') ? 'active' : ''; ?>">Profile</a></li>
														<li><a href="{{ route('admin_v2.security-settings') }}" class="<?php echo ($page == 'security-settings.php') ? 'active' : ''; ?>">Security</a></li>
														<li><a href="{{ route('admin_v2.notification-settings') }}" class="<?php echo ($page == 'notification-settings.php') ? 'active' : ''; ?>">Notifications</a></li>
														<li><a href="{{ route('admin_v2.connected-apps') }}" class="<?php echo ($page == 'connected-apps.php') ? 'active' : ''; ?>">Connected Apps</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'bussiness-settings.php'||$page == 'seo-settings.php'||$page == 'localization-settings.php'||$page == 'prefixes.php'||$page == 'preferences.php'||$page == 'performance-appraisal.php'||$page == 'language.php'||$page == 'authentication-settings.php'||$page == 'ai-settings.php') ? 'active subdrop' : ''; ?>">Website Settings<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.bussiness-settings') }}" class="<?php echo ($page == 'bussiness-settings.php') ? 'active' : ''; ?>">Business Settings</a></li>
														<li><a href="{{ route('admin_v2.seo-settings') }}" class="<?php echo ($page == 'seo-settings.php') ? 'active' : ''; ?>">SEO Settings</a></li>
														<li><a href="{{ route('admin_v2.localization-settings') }}" class="<?php echo ($page == 'localization-settings.php') ? 'active' : ''; ?>">Localization</a></li>
														<li><a href="{{ route('admin_v2.prefixes') }}" class="<?php echo ($page == 'prefixes.php') ? 'active' : ''; ?>">Prefixes</a></li>
														<li><a href="{{ route('admin_v2.preferences') }}" class="<?php echo ($page == 'preferences.php') ? 'active' : ''; ?>">Preferences</a></li>
														<li><a href="{{ route('admin_v2.performance-appraisal') }}" class="<?php echo ($page == 'performance-appraisal.php') ? 'active' : ''; ?>">Appearance</a></li>
														<li><a href="{{ route('admin_v2.language') }}" class="<?php echo ($page == 'language.php') ? 'active' : ''; ?>">Language</a></li>
														<li><a href="{{ route('admin_v2.authentication-settings') }}" class="<?php echo ($page == 'authentication-settings.php') ? 'active' : ''; ?>">Authentication</a></li>
														<li><a href="{{ route('admin_v2.ai-settings') }}" class="<?php echo ($page == 'ai-settings.php') ? 'active' : ''; ?>">AI Settings</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'salary-settings.php'||$page == 'approval-settings.php'||$page == 'invoice-settings.php'||$page == 'leave-type.php'||$page == 'custom-fields.php') ? 'active subdrop' : ''; ?>">App Settings<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.salary-settings') }}" class="<?php echo ($page == 'salary-settings.php') ? 'active' : ''; ?>">Salary Settings</a></li>
														<li><a href="{{ route('admin_v2.approval-settings') }}" class="<?php echo ($page == 'approval-settings.php') ? 'active' : ''; ?>">Approval Settings</a></li>
														<li><a href="{{ route('admin_v2.invoice-settings') }}" class="<?php echo ($page == 'invoice-settings.php') ? 'active' : ''; ?>">Invoice Settings</a></li>
														<li><a href="{{ route('admin_v2.leave-type') }}" class="<?php echo ($page == 'leave-type.php') ? 'active' : ''; ?>">Leave Type</a></li>
														<li><a href="{{ route('admin_v2.custom-fields') }}" class="<?php echo ($page == 'custom-fields.php') ? 'active' : ''; ?>">Custom Fields</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'email-settings.php'||$page == 'email-template.php'||$page == 'sms-settings.php'||$page == 'sms-template.php'||$page == 'otp-settings.php'||$page == 'gdpr.php'||$page == 'maintenance-mode.php') ? 'active subdrop' : ''; ?>">System Settings<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.email-settings') }}" class="<?php echo ($page == 'email-settings.php') ? 'active' : ''; ?>">Email Settings</a></li>
														<li><a href="{{ route('admin_v2.email-template') }}" class="<?php echo ($page == 'email-template.php') ? 'active' : ''; ?>">Email Templates</a></li>
														<li><a href="{{ route('admin_v2.sms-settings') }}" class="<?php echo ($page == 'sms-settings.php') ? 'active' : ''; ?>">SMS Settings</a></li>
														<li><a href="{{ route('admin_v2.sms-template') }}" class="<?php echo ($page == 'sms-template.php') ? 'active' : ''; ?>">SMS Templates</a></li>
														<li><a href="{{ route('admin_v2.otp-settings') }}" class="<?php echo ($page == 'otp-settings.php') ? 'active' : ''; ?>">OTP</a></li>
														<li><a href="{{ route('admin_v2.gdpr') }}" class="<?php echo ($page == 'gdpr.php') ? 'active' : ''; ?>">GDPR Cookies</a></li>
														<li><a href="{{ route('admin_v2.maintenance-mode') }}" class="<?php echo ($page == 'maintenance-mode.php') ? 'active' : ''; ?>">Maintenance Mode</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'payment-gateways.php'||$page == 'tax-rates.php'||$page == 'currencies.php') ? 'active subdrop' : ''; ?>">Financial Settings<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.payment-gateways') }}" class="<?php echo ($page == 'payment-gateways.php') ? 'active' : ''; ?>">Payment Gateways</a></li>
														<li><a href="{{ route('admin_v2.tax-rates') }}" class="<?php echo ($page == 'tax-rates.php') ? 'active' : ''; ?>">Tax Rate</a></li>
														<li><a href="{{ route('admin_v2.currencies') }}" class="<?php echo ($page == 'currencies.php') ? 'active' : ''; ?>">Currencies</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'custom-css.php'||$page == 'custom-js.php'||$page == 'cronjob.php'||$page == 'storage-settings.php'||$page == 'ban-ip-address.php'||$page == 'backup.php'||$page == 'clear-cache.php') ? 'active subdrop' : ''; ?>">Other Settings<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.custom-css') }}" class="<?php echo ($page == 'custom-css.php') ? 'active' : ''; ?>">Custom CSS</a></li>
														<li><a href="{{ route('admin_v2.custom-js') }}" class="<?php echo ($page == 'custom-js.php') ? 'active' : ''; ?>">Custom JS</a></li>
														<li><a href="{{ route('admin_v2.cronjob') }}" class="<?php echo ($page == 'cronjob.php') ? 'active' : ''; ?>">Cronjob</a></li>
														<li><a href="{{ route('admin_v2.storage-settings') }}" class="<?php echo ($page == 'storage-settings.php') ? 'active' : ''; ?>">Storage</a></li>
														<li><a href="{{ route('admin_v2.ban-ip-address') }}" class="<?php echo ($page == 'ban-ip-address.php') ? 'active' : ''; ?>">Ban IP Address</a></li>
														<li><a href="{{ route('admin_v2.backup') }}" class="<?php echo ($page == 'backup.php') ? 'active' : ''; ?>">Backup</a></li>
														<li><a href="{{ route('admin_v2.clear-cache') }}" class="<?php echo ($page == 'clear-cache.php') ? 'active' : ''; ?>">Clear Cache</a></li>
													</ul>
												</li>
											</ul>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#"class="<?php echo ($page == 'starter.php'||$page == 'profile.php'||$page == 'gallery.php'||$page == 'search-result.php'||$page == 'timeline.php'||$page == 'pricing.php'
									||$page == 'coming-soon.php'
									||$page == 'under-maintenance.php'
									||$page == 'under-construction.php'
									||$page == 'api-keys.php'
									||$page == 'privacy-policy.php'
									||$page == 'terms-condition.php'
									||$page == 'pages.php'
									) ? 'active subdrop' : ''; ?>">
										<i class="ti ti-page-break"></i><span>Pages</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.starter') }}" class="<?php echo ($page == 'starter.php') ? 'active' : ''; ?>"><span>Starter</span></a></li>
										<li><a href="{{ route('admin_v2.profile') }}" class="<?php echo ($page == 'profile.php') ? 'active' : ''; ?>"><span>Profile</span></a></li>
										<li><a href="{{ route('admin_v2.gallery') }}" class="<?php echo ($page == 'gallery.php') ? 'active' : ''; ?>"><span>Gallery</span></a></li>
										<li><a href="{{ route('admin_v2.search-result') }}" class="<?php echo ($page == 'search-result.php') ? 'active' : ''; ?>"><span>Search Results</span></a></li>
										<li><a href="{{ route('admin_v2.timeline') }}" class="<?php echo ($page == 'timeline.php') ? 'active' : ''; ?>"><span>Timeline</span></a></li>
										<li><a href="{{ route('admin_v2.pricing') }}" class="<?php echo ($page == 'pricing.php') ? 'active' : ''; ?>"><span>Pricing</span></a></li>
										<li><a href="{{ route('admin_v2.coming-soon') }}" class="<?php echo ($page == 'coming-soon.php') ? 'active' : ''; ?>"><span>Coming Soon</span></a></li>
										<li><a href="{{ route('admin_v2.under-maintenance') }}" class="<?php echo ($page == 'under-maintenance.php') ? 'active' : ''; ?>"><span>Under Maintenance</span></a></li>
										<li><a href="{{ route('admin_v2.under-construction') }}" class="<?php echo ($page == 'under-construction.php') ? 'active' : ''; ?>"><span>Under Construction</span></a></li>
										<li><a href="{{ route('admin_v2.api-keys') }}" class="<?php echo ($page == 'api-keys.php') ? 'active' : ''; ?>"><span>API Keys</span></a></li>
										<li><a href="{{ route('admin_v2.privacy-policy') }}" class="<?php echo ($page == 'privacy-policy.php') ? 'active' : ''; ?>"><span>Privacy Policy</span></a></li>
										<li><a href="{{ route('admin_v2.terms-condition') }}" class="<?php echo ($page == 'terms-condition.php') ? 'active' : ''; ?>"><span>Terms & Conditions</span></a></li>
										<li class="submenu">
											<a href="#"class="<?php echo ($page == 'pages.php'||$page == 'blogs.php'||$page == 'blog-categories.php'||$page == 'blog-comments.php'||$page == 'blog-tags.php') ? 'active subdrop' : ''; ?>"><span>Content</span> <span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.pages') }}"class="<?php echo ($page == 'pages') ? 'active' : ''; ?>">Pages</a></li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'blogs.php'||$page == 'blog-categories.php'||$page == 'blog-comments.php'||$page == 'blog-tags.php') ? 'active subdrop' : ''; ?>">Blogs<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.blogs') }}" class="<?php echo ($page == 'blogs.php') ? 'active' : ''; ?>">All Blogs</a></li>
														<li><a href="{{ route('admin_v2.blog-categories') }}" class="<?php echo ($page == 'blog-categories.php') ? 'active' : ''; ?>">Categories</a></li>
														<li><a href="{{ route('admin_v2.blog-comments') }}" class="<?php echo ($page == 'blog-comments.php') ? 'active' : ''; ?>">Comments</a></li>
														<li><a href="{{ route('admin_v2.blog-tags') }}" class="<?php echo ($page == 'blog-tags.php') ? 'active' : ''; ?>">Tags</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'countries.php'||$page == 'states.php'||$page == 'cities.php') ? 'active subdrop' : ''; ?>">Locations<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.countries') }}" class="<?php echo ($page == 'countries.php') ? 'active' : ''; ?>">Countries</a></li>
														<li><a href="{{ route('admin_v2.states') }}" class="<?php echo ($page == 'states.php') ? 'active' : ''; ?>">States</a></li>
														<li><a href="{{ route('admin_v2.cities') }}" class="<?php echo ($page == 'cities.php') ? 'active' : ''; ?>">Cities</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.testimonials') }}" class="<?php echo ($page == 'testimonials.php') ? 'active' : ''; ?>">Testimonials</a></li>
												<li><a href="{{ route('admin_v2.faq') }}" class="<?php echo ($page == 'faq.php') ? 'active' : ''; ?>">FAQ’S</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<span>Authentication</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu">
													<a href="javascript:void(0);" class="">Login<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.login') }}" class="<?php echo ($page == 'login.php') ? 'active' : ''; ?>">Cover</a></li>
														<li><a href="{{ route('admin_v2.login-2') }}" class="<?php echo ($page == 'login-2.php') ? 'active' : ''; ?>">Illustration</a></li>
														<li><a href="{{ route('admin_v2.login-3') }}" class="<?php echo ($page == 'login-3.php') ? 'active' : ''; ?>">Basic</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);" class="">Register<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.register') }}" class="<?php echo ($page == 'register.php') ? 'active' : ''; ?>">Cover</a></li>
														<li><a href="{{ route('admin_v2.register-2') }}" class="<?php echo ($page == 'register-2.php') ? 'active' : ''; ?>">Illustration</a></li>
														<li><a href="{{ route('admin_v2.register-3') }}" class="<?php echo ($page == 'register-3.php') ? 'active' : ''; ?>">Basic</a></li>
													</ul>
												</li>
												<li class="submenu"><a href="javascript:void(0);">Forgot Password<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.forgot-password') }}" class="<?php echo ($page == 'forgot-password.php') ? 'active' : ''; ?>">Cover</a></li>
														<li><a href="{{ route('admin_v2.forgot-password-2') }}" class="<?php echo ($page == 'forgot-password-2.php') ? 'active' : ''; ?>">Illustration</a></li>
														<li><a href="{{ route('admin_v2.forgot-password-3') }}" class="<?php echo ($page == 'forgot-password-3.php') ? 'active' : ''; ?>">Basic</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);">Reset Password<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.reset-password') }}" class="<?php echo ($page == 'reset-password.php') ? 'active' : ''; ?>">Cover</a></li>
														<li><a href="{{ route('admin_v2.reset-password-2') }}" class="<?php echo ($page == 'reset-password-2.php') ? 'active' : ''; ?>">Illustration</a></li>
														<li><a href="{{ route('admin_v2.reset-password-3') }}" class="<?php echo ($page == 'reset-password-3.php') ? 'active' : ''; ?>">Basic</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);">Email Verification<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.email-verification') }}" class="<?php echo ($page == 'email-verification.php') ? 'active' : ''; ?>">Cover</a></li>
														<li><a href="{{ route('admin_v2.email-verification-2') }}" class="<?php echo ($page == 'email-verification-2.php') ? 'active' : ''; ?>">Illustration</a></li>
														<li><a href="{{ route('admin_v2.email-verification-3') }}" class="<?php echo ($page == 'email-verification-3.php') ? 'active' : ''; ?>">Basic</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);">2 Step Verification<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.two-step-verification') }}" class="<?php echo ($page == 'two-step-verification.php') ? 'active' : ''; ?>">Cover</a></li>
														<li><a href="{{ route('admin_v2.two-step-verification-2') }}" class="<?php echo ($page == 'two-step-verification-2.php') ? 'active' : ''; ?>">Illustration</a></li>
														<li><a href="{{ route('admin_v2.two-step-verification-3') }}" class="<?php echo ($page == 'two-step-verification-3.php') ? 'active' : ''; ?>">Basic</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.lock-screen') }}" class="<?php echo ($page == 'lock-screen.php') ? 'active' : ''; ?>">Lock Screen</a></li>
												<li><a href="{{ route('admin_v2.error-404') }}" class="<?php echo ($page == 'error-404.php') ? 'active' : ''; ?>">404 Error</a></li>
												<li><a href="{{ route('admin_v2.error-500') }}" class="<?php echo ($page == 'error-500.php') ? 'active' : ''; ?>">500 Error</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<span>UI Interface</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'ui-alerts.php' || $page == 'ui-accordion.php' || $page == 'ui-avatar.php' || $page == 'ui-badges.php' || $page == 'ui-borders.php'
																								|| $page == 'ui-buttons.php' || $page == 'ui-buttons-group.php' || $page == 'ui-breadcrumb.php' || $page == 'ui-cards.php' || $page == 'ui-carousel.php'
																								|| $page == 'ui-colors.php' || $page == 'ui-dropdowns.php' || $page == 'ui-grid.php' || $page == 'ui-images.php' || $page == 'ui-lightbox.php'
																								|| $page == 'ui-media.php' || $page == 'ui-modals.php' || $page == 'ui-offcanvas.php' || $page == 'ui-pagination.php' || $page == 'ui-popovers.php'
																								|| $page == 'ui-progress.php' || $page == 'ui-placeholders.php'  || $page == 'ui-spinner.php'
																								|| $page == 'ui-sweetalerts.php' || $page == 'ui-nav-tabs.php' || $page == 'ui-toasts.php' || $page == 'ui-tooltips.php'
																								|| $page == 'ui-typography.php' || $page == 'ui-video.php'
																						)
																								? 'subdrop active'
																								: ''; ?>">
														<i class="ti ti-hierarchy-2"></i>
														<span>Base UI</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.ui-alerts') }}" class="<?php echo ($page == 'ui-alerts.php') ? 'active' : ''; ?>">Alerts</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-accordion') }}" class="<?php echo ($page == 'ui-accordion.php') ? 'active' : ''; ?>">Accordion</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-avatar') }}" class="<?php echo ($page == 'ui-avatar.php') ? 'active' : ''; ?>">Avatar</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-badges') }}" class="<?php echo ($page == 'ui-badges.php') ? 'active' : ''; ?>">Badges</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-borders') }}" class="<?php echo ($page == 'ui-borders.php') ? 'active' : ''; ?>">Border</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-buttons') }}" class="<?php echo ($page == 'ui-buttons.php') ? 'active' : ''; ?>">Buttons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-buttons-group') }}" class="<?php echo ($page == 'ui-buttons-group.php') ? 'active' : ''; ?>">Button Group</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-breadcrumb') }}" class="<?php echo ($page == 'ui-breadcrumb.php') ? 'active' : ''; ?>">Breadcrumb</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-cards') }}" class="<?php echo ($page == 'ui-cards.php') ? 'active' : ''; ?>">Card</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-carousel') }}" class="<?php echo ($page == 'ui-carousel.php') ? 'active' : ''; ?>">Carousel</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-colors') }}" class="<?php echo ($page == 'ui-colors.php') ? 'active' : ''; ?>">Colors</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-dropdowns') }}" class="<?php echo ($page == 'ui-dropdowns.php') ? 'active' : ''; ?>">Dropdowns</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-grid') }}" class="<?php echo ($page == 'ui-grid.php') ? 'active' : ''; ?>">Grid</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-images') }}" class="<?php echo ($page == 'ui-images.php') ? 'active' : ''; ?>">Images</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-lightbox') }}" class="<?php echo ($page == 'ui-lightbox.php') ? 'active' : ''; ?>">Lightbox</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-media') }}" class="<?php echo ($page == 'ui-media.php') ? 'active' : ''; ?>">Media</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-modals') }}" class="<?php echo ($page == 'ui-modals.php') ? 'active' : ''; ?>">Modals</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-offcanvas') }}" class="<?php echo ($page == 'ui-offcanvas.php') ? 'active' : ''; ?>">Offcanvas</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-pagination') }}" class="<?php echo ($page == 'ui-pagination.php') ? 'active' : ''; ?>">Pagination</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-popovers') }}" class="<?php echo ($page == 'ui-popovers.php') ? 'active' : ''; ?>">Popovers</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-progress') }}" class="<?php echo ($page == 'ui-progress.php') ? 'active' : ''; ?>">Progress</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-placeholders') }}" class="<?php echo ($page == 'ui-placeholders.php') ? 'active' : ''; ?>">Placeholders</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-spinner') }}" class="<?php echo ($page == 'ui-spinner.php') ? 'active' : ''; ?>">Spinner</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-sweetalerts') }}" class="<?php echo ($page == 'ui-sweetalerts.php') ? 'active' : ''; ?>">Sweet Alerts</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-nav-tabs') }}" class="<?php echo ($page == 'ui-nav-tabs.php') ? 'active' : ''; ?>">Tabs</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-toasts') }}" class="<?php echo ($page == 'ui-toasts.php') ? 'active' : ''; ?>">Toasts</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-tooltips') }}" class="<?php echo ($page == 'ui-tooltips.php') ? 'active' : ''; ?>">Tooltips</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-typography') }}" class="<?php echo ($page == 'ui-typography.php') ? 'active' : ''; ?>">Typography</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-video') }}" class="<?php echo ($page == 'ui-video.php') ? 'active' : ''; ?>">Video</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-sortable') }}" class="<?php echo ($page == 'ui-sortable.php') ? 'active' : ''; ?>">Sortable</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-swiperjs') }}" class="<?php echo ($page == 'ui-swiperjs.php') ? 'active' : ''; ?>">Swiperjs</a>
														</li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'ui-ribbon.php' || $page == 'ui-clipboard.php' || $page == 'ui-drag-drop.php' || $page == 'ui-rangeslider.php' || $page == 'ui-rating.php' || $page == 'ui-text-editor.php' || $page == 'ui-counter.php' || $page == 'ui-scrollbar.php' || $page == 'ui-stickynote.php' || $page == 'ui-timeline.php') ? 'subdrop active' : ''; ?>">
														<i class="ti ti-hierarchy-3"></i>
														<span>Advanced UI</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.ui-ribbon') }}" class="<?php echo ($page == 'ui-ribbon.php') ? 'active' : ''; ?>">Ribbon</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-clipboard') }}" class="<?php echo ($page == 'ui-clipboard.php') ? 'active' : ''; ?>">Clipboard</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-drag-drop') }}" class="<?php echo ($page == 'ui-drag-drop.php') ? 'active' : ''; ?>">Drag & Drop</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-rangeslider') }}" class="<?php echo ($page == 'ui-rangeslider.php') ? 'active' : ''; ?>">Range Slider</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-rating') }}" class="<?php echo ($page == 'ui-rating.php') ? 'active' : ''; ?>">Rating</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-text-editor') }}" class="<?php echo ($page == 'ui-text-editor.php') ? 'active' : ''; ?>">Text Editor</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-counter') }}" class="<?php echo ($page == 'ui-counter.php') ? 'active' : ''; ?>">Counter</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-scrollbar') }}" class="<?php echo ($page == 'ui-scrollbar.php') ? 'active' : ''; ?>">Scrollbar</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-stickynote') }}" class="<?php echo ($page == 'ui-stickynote.php') ? 'active' : ''; ?>">Sticky Note</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.ui-timeline') }}" class="<?php echo ($page == 'ui-timeline.php') ? 'active' : ''; ?>">Timeline</a>
														</li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'form-basic-inputs.php' || $page == 'form-checkbox-radios.php' || $page == 'form-input-groups.php' || $page == 'form-grid-gutters.php' || $page == 'form-select.php' || $page == 'form-mask.php' || $page == 'form-fileupload.php' || $page == 'form-horizontal.php' || $page == 'form-vertical.php' || $page == 'form-floating-labels.php' || $page == 'form-validation.php' || $page == 'form-wizard.php' || $page == 'form-select2.php') ? 'subdrop active' : ''; ?>">
														<i class="ti ti-input-search"></i>
														<span>Forms</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li class="submenu submenu-two">
															<a href="javascript:void(0);" class="<?php echo ($page == 'form-basic-inputs.php' || $page == 'form-checkbox-radios.php' || $page == 'form-input-groups.php' || $page == 'form-grid-gutters.php' || $page == 'form-select.php' || $page == 'form-mask.php' || $page == 'form-fileupload.php' ) ? 'subdrop active' : ''; ?>">Form Elements <span class="menu-arrow inside-submenu"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.form-basic-inputs') }}" class="<?php echo ($page == 'form-basic-inputs.php') ? 'active' : ''; ?>">Basic Inputs</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-checkbox-radios') }}" class="<?php echo ($page == 'form-checkbox-radios.php') ? 'active' : ''; ?>">Checkbox & Radios</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-input-groups') }}" class="<?php echo ($page == 'form-input-groups.php') ? 'active' : ''; ?>">Input Groups</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-grid-gutters') }}" class="<?php echo ($page == 'form-grid-gutters.php') ? 'active' : ''; ?>">Grid & Gutters</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-select') }}" class="<?php echo ($page == 'form-select.php') ? 'active' : ''; ?>">Form Select</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-mask') }}" class="<?php echo ($page == 'form-mask.php') ? 'active' : ''; ?>">Input Masks</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-fileupload') }}" class="<?php echo ($page == 'form-fileupload.php') ? 'active' : ''; ?>">File Uploads</a>
																</li>
															</ul>
														</li>
														<li class="submenu submenu-two">
															<a href="javascript:void(0);"class="<?php echo ($page == 'form-horizontal.php' || $page == 'form-vertical.php' || $page == 'form-floating-labels.php') ? 'subdrop active' : ''; ?>">Layouts <span class="menu-arrow inside-submenu"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.form-horizontal') }}" class="<?php echo ($page == 'form-horizontal.php') ? 'active' : ''; ?>">Horizontal Form</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-vertical') }}" class="<?php echo ($page == 'form-vertical.php') ? 'active' : ''; ?>">Vertical Form</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-floating-labels') }}" class="<?php echo ($page == 'form-floating-labels.php') ? 'active' : ''; ?>">Floating Labels</a>
																</li>
															</ul>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-validation') }}" class="<?php echo ($page == 'form-validation.php') ? 'active' : ''; ?>">Form Validation</a>
														</li>
														
														<li>
															<a href="{{ route('admin_v2.form-select2') }}" class="<?php echo ($page == 'form-select2.php') ? 'active' : ''; ?>">Select2</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-wizard') }}" class="<?php echo ($page == 'form-wizard.php') ? 'active' : ''; ?>">Form Wizard</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-pickers') }}" class="<?php echo ($page == 'form-pickers.php') ? 'active' : ''; ?>">Form Pickers</a>
														</li>
														
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'tables-basic.php' || $page == 'data-tables.php') ? 'subdrop active' : ''; ?>">
														<i class="ti ti-table-plus"></i>
														<span>Tables</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.tables-basic') }}" class="<?php echo ($page == 'tables-basic.php') ? 'active' : ''; ?>">Basic Tables </a>
														</li>
														<li>
															<a href="{{ route('admin_v2.data-tables') }}" class="<?php echo ($page == 'data-tables.php') ? 'active' : ''; ?>">Data Table </a>
														</li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'chart-apex.php' || $page == 'chart-js.php' || $page == 'chart-morris.php' || $page == 'chart-flot.php' || $page == 'chart-peity.php' || $page == 'chart-c3.php') ? 'subdrop active' : ''; ?>">
														<i class="ti ti-chart-line"></i>
														<span>Charts</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.chart-apex') }}" class="<?php echo ($page == 'chart-apex.php') ? 'active' : ''; ?>">Apex Charts</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.chart-c3') }}" class="<?php echo ($page == 'chart-c3.php') ? 'active' : ''; ?>">Chart C3</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.chart-js') }}" class="<?php echo ($page == 'chart-js.php') ? 'active' : ''; ?>">Chart Js</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.chart-morris') }}" class="<?php echo ($page == 'chart-morris.php') ? 'active' : ''; ?>">Morris Charts</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.chart-flot') }}" class="<?php echo ($page == 'chart-flot.php') ? 'active' : ''; ?>">Flot Charts</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.chart-peity') }}" class="<?php echo ($page == 'chart-peity.php') ? 'active' : ''; ?>">Peity Charts</a>
														</li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'icon-fontawesome.php' || $page == 'icon-feather.php' || $page == 'icon-ionic.php' || $page == 'icon-material.php' || $page == 'icon-pe7.php' || $page == 'icon-simpleline.php' || $page == 'icon-themify.php' || $page == 'icon-weather.php' || $page == 'icon-typicon.php' || $page == 'icon-flag.php') ? 'subdrop active' : ''; ?>">
														<i class="ti ti-icons"></i>
														<span>Icons</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.icon-fontawesome') }}" class="<?php echo ($page == 'icon-fontawesome.php') ? 'active' : ''; ?>">Fontawesome Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-tabler') }}" class="<?php echo ($page == 'icon-tabler.php') ? 'active' : ''; ?>">Tabler Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-bootstrap') }}" class="<?php echo ($page == 'icon-bootstrap.php') ? 'active' : ''; ?>">Bootstrap Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-remix') }}" class="<?php echo ($page == 'icon-remix.php') ? 'active' : ''; ?>">Remix Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-feather') }}" class="<?php echo ($page == 'icon-feather.php') ? 'active' : ''; ?>">Feather Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-ionic') }}" class="<?php echo ($page == 'icon-ionic.php') ? 'active' : ''; ?>">Ionic Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-material') }}" class="<?php echo ($page == 'icon-material.php') ? 'active' : ''; ?>">Material Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-pe7') }}" class="<?php echo ($page == 'icon-pe7.php') ? 'active' : ''; ?>">Pe7 Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-simpleline') }}" class="<?php echo ($page == 'icon-simpleline.php') ? 'active' : ''; ?>">Simpleline Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-themify') }}" class="<?php echo ($page == 'icon-themify.php') ? 'active' : ''; ?>">Themify Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-weather') }}" class="<?php echo ($page == 'icon-weather.php') ? 'active' : ''; ?>">Weather Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-typicon') }}" class="<?php echo ($page == 'icon-typicon.php') ? 'active' : ''; ?>">Typicon Icons</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.icon-flag') }}" class="<?php echo ($page == 'icon-flag.php') ? 'active' : ''; ?>">Flag Icons</a>
														</li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"class="<?php echo ($page == 'maps-vector.php' || $page == 'maps-leaflet.php') ? 'subdrop active' : ''; ?>">
														<i class="ti ti-table-plus"></i>
														<span>Maps</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.maps-vector') }}" class="<?php echo ($page == 'maps-vector.php') ? 'active' : ''; ?>">Vector</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.maps-leaflet') }}" class="<?php echo ($page == 'maps-leaflet.php') ? 'active' : ''; ?>">Leaflet</a>
														</li>
													</ul>
												</li>
											</ul>
										</li>
										<li><a href="#">Documentation</a></li>
										<li><a href="#">Change Log</a></li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Multi Level</span><span class="menu-arrow"></span></a>
											<ul>
												<li><a href="javascript:void(0);">Multilevel 1</a></li>
												<li class="submenu submenu-two">
													<a href="javascript:void(0);">Multilevel 2<span class="menu-arrow inside-submenu"></span></a>
													<ul>
														<li><a href="javascript:void(0);">Multilevel 2.1</a></li>
														<li class="submenu submenu-two submenu-three">
															<a href="javascript:void(0);">Multilevel 2.2<span class="menu-arrow inside-submenu inside-submenu-two"></span></a>
															<ul>
																<li><a href="javascript:void(0);">Multilevel 2.2.1</a></li>
																<li><a href="javascript:void(0);">Multilevel 2.2.2</a></li>
															</ul>
														</li>
													</ul>
												</li>
												<li><a href="javascript:void(0);">Multilevel 3</a></li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Horizontal Single -->

				<div class="d-flex align-items-center">
					<div class="me-1">
						<a href="#" class="btn btn-menubar btnFullscreen">
							<i class="ti ti-maximize"></i>
						</a>
					</div>
					<div class="dropdown me-1">
						<a href="#" class="btn btn-menubar" data-bs-toggle="dropdown">
							<i class="ti ti-layout-grid-remove"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<div class="card mb-0 border-0 shadow-none">
								<div class="card-header">
									<h4>Applications</h4>
								</div>
								<div class="card-body">											
									<a href="{{ route('admin_v2.calendar') }}" class="d-block pb-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-calendar text-gray-9"></i></span>Calendar
									</a>										
									<a href="{{ route('admin_v2.todo') }}" class="d-block py-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-subtask text-gray-9"></i></span>To Do
									</a>										
									<a href="{{ route('admin_v2.notes') }}" class="d-block py-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-notes text-gray-9"></i></span>Notes
									</a>										
									<a href="{{ route('admin_v2.file-manager') }}" class="d-block py-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-folder text-gray-9"></i></span>File Manager
									</a>								
									<a href="{{ route('admin_v2.kanban-view') }}" class="d-block py-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-layout-kanban text-gray-9"></i></span>Kanban
									</a>								
									<a href="{{ route('admin_v2.invoices') }}" class="d-block py-2 pb-0">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-file-invoice text-gray-9"></i></span>Invoices
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="me-1">
						<a href="{{ route('admin_v2.chat') }}" class="btn btn-menubar position-relative">
							<i class="ti ti-brand-hipchat"></i>
							<span class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
						</a>
					</div>
					<div class="me-1">
						<a href="{{ route('admin_v2.email') }}" class="btn btn-menubar">
							<i class="ti ti-mail"></i>
						</a>
					</div>
					<div class="me-1 notification_item">
						<a href="#" class="btn btn-menubar position-relative me-1" id="notification_popup"
							data-bs-toggle="dropdown">
							<i class="ti ti-bell"></i>
							<span class="notification-status-dot"></span>
						</a>
						<div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
							<div class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
								<h4 class="notification-title">Notifications (2)</h4>
								<div class="d-flex align-items-center">
									<a href="#" class="text-primary fs-15 me-3 lh-1">Mark all as read</a>
									<div class="dropdown">
										<a href="javascript:void(0);" class="bg-white dropdown-toggle"
											data-bs-toggle="dropdown">
											<i class="ti ti-calendar-due me-1"></i>Today
										</a>
										<ul class="dropdown-menu mt-2 p-3">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">
													This Week
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">
													Last Week
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">
													Last Month
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="noti-content">
								<div class="d-flex flex-column">
									<div class="border-bottom mb-3 pb-3">
										<a href="{{ route('admin_v2.activity') }}">
											<div class="d-flex">
												<span class="avatar avatar-lg me-2 flex-shrink-0">
													<img src="{{ asset('assets3/img/profiles/avatar-27.jpg') }}" alt="Profile">
												</span>
												<div class="flex-grow-1">
													<p class="mb-1"><span
															class="text-dark fw-semibold">Shawn</span>
														performance in Math is below the threshold.</p>
													<span>Just Now</span>
												</div>
											</div>
										</a>
									</div>
									<div class="border-bottom mb-3 pb-3">
										<a href="{{ route('admin_v2.activity') }}" class="pb-0">
											<div class="d-flex">
												<span class="avatar avatar-lg me-2 flex-shrink-0">
													<img src="{{ asset('assets3/img/profiles/avatar-23.jpg') }}" alt="Profile">
												</span>
												<div class="flex-grow-1">
													<p class="mb-1"><span
															class="text-dark fw-semibold">Sylvia</span> added
														appointment on 02:00 PM</p>
													<span>10 mins ago</span>
													<div
														class="d-flex justify-content-start align-items-center mt-1">
														<span class="btn btn-light btn-sm me-2">Deny</span>
														<span class="btn btn-primary btn-sm">Approve</span>
													</div>
												</div>
											</div>
										</a>
									</div>
									<div class="border-bottom mb-3 pb-3">
										<a href="{{ route('admin_v2.activity') }}">
											<div class="d-flex">
												<span class="avatar avatar-lg me-2 flex-shrink-0">
													<img src="{{ asset('assets3/img/profiles/avatar-25.jpg') }}" alt="Profile">
												</span>
												<div class="flex-grow-1">
													<p class="mb-1">New student record <span class="text-dark fw-semibold"> George</span> is created by <span class="text-dark fw-semibold">Teressa</span></p>
													<span>2 hrs ago</span>
												</div>
											</div>
										</a>
									</div>
									<div class="border-0 mb-3 pb-0">
										<a href="{{ route('admin_v2.activity') }}">
											<div class="d-flex">
												<span class="avatar avatar-lg me-2 flex-shrink-0">
													<img src="{{ asset('assets3/img/profiles/avatar-01.jpg') }}" alt="Profile">
												</span>
												<div class="flex-grow-1">
													<p class="mb-1">A new teacher record for <span class="text-dark fw-semibold">Elisa</span> </p>
													<span>09:45 AM</span>
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
							<div class="d-flex p-0">
								<a href="#" class="btn btn-light w-100 me-2">Cancel</a>
								<a href="{{ route('admin_v2.activity') }}" class="btn btn-primary w-100">View All</a>
							</div>
						</div>
					</div>
					<div class="dropdown profile-dropdown">
						<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
							data-bs-toggle="dropdown">
							<span class="avatar avatar-sm online">
								<img src="{{ asset('assets3/img/profiles/avatar-12.jpg') }}" alt="Img" class="img-fluid rounded-circle">
							</span>
						</a>
						<div class="dropdown-menu shadow-none">
							<div class="card mb-0">
								<div class="card-header">
									<div class="d-flex align-items-center">
										<span class="avatar avatar-lg me-2 avatar-rounded">
											<img src="{{ asset('assets3/img/profiles/avatar-12.jpg') }}" alt="img">
										</span>
										<div>
											<h5 class="mb-0">Kevin Larry</h5>
											<p class="fs-12 fw-medium mb-0">warren@example.com</p>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.profile') }}">
										<i class="ti ti-user-circle me-1"></i>My Profile
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.bussiness-settings') }}">
										<i class="ti ti-settings me-1"></i>Settings
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.security-settings') }}">
										<i class="ti ti-status-change me-1"></i>Status
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.profile-settings') }}">
										<i class="ti ti-circle-arrow-up me-1"></i>My Account
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.knowledgebase') }}">
										<i class="ti ti-question-mark me-1"></i>Knowledge Base
									</a>
								</div>
								<div class="card-footer">
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.login') }}">
										<i class="ti ti-login me-2"></i>Logout
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Mobile Menu -->
		<div class="dropdown mobile-user-menu">
			<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
			<div class="dropdown-menu dropdown-menu-end">
				<a class="dropdown-item" href="{{ route('admin_v2.profile') }}">My Profile</a>
				<a class="dropdown-item" href="{{ route('admin_v2.profile-settings') }}">Settings</a>
				<a class="dropdown-item" href="{{ route('admin_v2.login') }}">Logout</a>
			</div>
		</div>
		<!-- /Mobile Menu -->
	</div>
</div>
<!-- /Header -->
