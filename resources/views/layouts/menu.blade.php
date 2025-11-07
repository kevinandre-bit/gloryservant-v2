		<!-- Header -->
		<div class="header">
			<div class="main-header">

				<div class="header-left">
					<a href="{{ route('admin_v2.index') }}" class="logo">
						<img src="/assets3/img/logo.svg" alt="Logo">
					</a>
					<a href="{{ route('admin_v2.index') }}" class="dark-logo">
						<img src="/assets3/img/logo-white.svg" alt="Logo">
					</a>
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
								<!--<div class="dropdown-menu dropdown-lg dropdown-menu-start">
									<div class="card mb-0 border-0 shadow-none">
										<div class="card-header">
											<h4>CRM</h4>
										</div>
										<div class="card-body pb-1">
											<div class="row">
												<div class="col-sm-6">
													<a href="{{ route('admin_v2.contacts') }}"
														class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
														<span class="d-flex align-items-center me-3">
															<i class="ti ti-user-shield text-default me-2"></i>Contacts
														</span>
														<i class="ti ti-arrow-right"></i>
													</a>
													<a href="{{ route('admin_v2.deals-grid') }}"
														class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
														<span class="d-flex align-items-center me-3">
															<i class="ti ti-heart-handshake text-default me-2"></i>Deals
														</span>
														<i class="ti ti-arrow-right"></i>
													</a>
													<a href="{{ route('admin_v2.pipeline') }}"
														class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
														<span class="d-flex align-items-center me-3">
															<i
																class="ti ti-timeline-event-text text-default me-2"></i>Pipeline
														</span>
														<i class="ti ti-arrow-right"></i>
													</a>
												</div>
												<div class="col-sm-6">
													<a href="{{ route('admin_v2.companies-grid') }}"
														class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
														<span class="d-flex align-items-center me-3">
															<i class="ti ti-building text-default me-2"></i>Companies
														</span>
														<i class="ti ti-arrow-right"></i>
													</a>
													<a href="{{ route('admin_v2.leads-grid') }}"
														class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
														<span class="d-flex align-items-center me-3">
															<i class="ti ti-user-check text-default me-2"></i>Leads
														</span>
														<i class="ti ti-arrow-right"></i>
													</a>
													<a href="{{ route('admin_v2.activity') }}"
														class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
														<span class="d-flex align-items-center me-3">
															<i class="ti ti-activity text-default me-2"></i>Activities
														</span>
														<i class="ti ti-arrow-right"></i>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>-->
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
											<a href="#">
												<i class="ti ti-smart-home"></i><span>Dashboard</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.index') }}">Admin Dashboard</a></li>
												<li><a href="{{ route('admin_v2.employee-dashboard') }}">Employee Dashboard</a></li>
												<li><a href="{{ route('admin_v2.deals-dashboard') }}">Deals Dashboard</a></li>
												<li><a href="{{ route('admin_v2.leads-dashboard') }}">Leads Dashboard</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<i class="ti ti-user-star"></i><span>Super Admin</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.dashboard') }}">Dashboard</a></li>
												<li><a href="{{ route('admin_v2.companies') }}">Companies</a></li>
												<li><a href="{{ route('admin_v2.subscription') }}">Subscriptions</a></li>
												<li><a href="{{ route('admin_v2.packages') }}">Packages</a></li>
												<li><a href="{{ route('admin_v2.domain') }}">Domain</a></li>
												<li><a href="{{ route('admin_v2.purchase-transaction') }}">Purchase Transaction</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<i class="ti ti-layout-grid-add"></i><span>Applications</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.chat') }}">Chat</a></li>
												<li class="submenu submenu-two">
													<a href="{{ route('admin_v2.call') }}">Calls<span
															class="menu-arrow inside-submenu"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.voice-call') }}">Voice Call</a></li>
														<li><a href="{{ route('admin_v2.video-call') }}">Video Call</a></li>
														<li><a href="{{ route('admin_v2.outgoing-call') }}">Outgoing Call</a></li>
														<li><a href="{{ route('admin_v2.incoming-call') }}">Incoming Call</a></li>
														<li><a href="{{ route('admin_v2.call-history') }}">Call History</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.calendar') }}">Calendar</a></li>
												<li><a href="{{ route('admin_v2.email') }}">Email</a></li>
												<li><a href="{{ route('admin_v2.todo') }}">To Do</a></li>
												<li><a href="{{ route('admin_v2.notes') }}">Notes</a></li>
												<li><a href="{{ route('admin_v2.social-feed') }}">Social Feed</a></li>
												<li><a href="{{ route('admin_v2.file-manager') }}">File Manager</a></li>
												<li><a href="{{ route('admin_v2.kanban-view') }}">Kanban</a></li>
												<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<i class="ti ti-layout-board-split"></i><span>Layouts</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.layout-horizontal') }}">
														<span>Horizontal</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-detached') }}">
														<span>Detached</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-modern') }}">
														<span>Modern</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-two-column') }}">
														<span>Two Column </span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-hovered') }}">
														<span>Hovered</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-box') }}">
														<span>Boxed</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-horizontal-single') }}">
														<span>Horizontal Single</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-horizontal-overlay') }}">
														<span>Horizontal Overlay</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-horizontal-box') }}">
														<span>Horizontal Box</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-horizontal-sidemenu') }}">
														<span>Menu Aside</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-vertical-transparent') }}">
														<span>Transparent</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-without-header') }}">
														<span>Without Header</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-rtl') }}">
														<span>RTL</span>
													</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.layout-dark') }}">
														<span>Dark</span>
													</a>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#" class="active" >
												<i class="ti ti-user-star"></i><span>Projects</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.clients-grid') }}"><span>Clients</span>
													</a>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Projects</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.projects-grid') }}">Projects</a></li>
														<li><a href="{{ route('admin_v2.tasks') }}">Tasks</a></li>
														<li><a href="{{ route('admin_v2.task-board') }}">Task Board</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="{{ route('admin_v2.call') }}">Crm<span class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.contacts-grid') }}" ><span>Contacts</span></a></li>
														<li><a href="{{ route('admin_v2.companies-grid') }}"><span>Companies</span></a>
														</li>
														<li><a href="{{ route('admin_v2.deals-grid') }}"><span>Deals</span></a></li>
														<li><a href="{{ route('admin_v2.leads-grid') }}"><span>Leads</span></a></li>
														<li><a href="{{ route('admin_v2.pipeline') }}"><span>Pipeline</span></a></li>
														<li><a href="{{ route('admin_v2.analytics') }}"><span>Analytics</span></a></li>
														<li><a href="{{ route('admin_v2.activity') }}"><span>Activities</span></a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);" class="active"><span>Employees</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.employees') }}" class="active">Employee Lists</a></li>
														<li><a href="{{ route('admin_v2.employees-grid') }}">Employee Grid</a></li>
														<li><a href="{{ route('admin_v2.employee-details') }}">Employee Details</a></li>
														<li><a href="{{ route('admin_v2.departments') }}">Departments</a></li>
														<li><a href="{{ route('admin_v2.ministry') }}">Ministry</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Tickets</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.tickets') }}">Tickets</a></li>
														<li><a href="{{ route('admin_v2.ticket-details') }}">Ticket Details</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.holidays') }}"><span>Holidays</span></a></li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Attendance</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li class="submenu">
															<a href="javascript:void(0);">Leaves<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.leaves') }}">Leaves (Admin)</a></li>
																<li><a href="{{ route('admin_v2.leaves-employee') }}">Leave (Employee)</a>
																</li>
																<li><a href="{{ route('admin_v2.leave-settings') }}">Leave Settings</a>
																</li>
															</ul>
														</li>
														<li><a href="{{ route('admin_v2.attendance-admin') }}">Attendance (Admin)</a></li>
														<li><a href="{{ route('admin_v2.attendance-employee') }}">Attendance (Employee)</a>
														</li>
														<li><a href="{{ route('admin_v2.timesheets') }}">Timesheets</a></li>
														<li><a href="{{ route('admin_v2.schedule.index') }}">Shift & Schedule</a></li>
														<li><a href="{{ route('admin_v2.overtime') }}">Overtime</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Performance</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.performance-indicator') }}">Performance
																Indicator</a></li>
														<li><a href="{{ route('admin_v2.performance-review') }}">Performance Review</a>
														</li>
														<li><a href="{{ route('admin_v2.performance-appraisal') }}">Performance
																Appraisal</a></li>
														<li><a href="{{ route('admin_v2.goal-tracking') }}">Goal List</a></li>
														<li><a href="{{ route('admin_v2.goal-type') }}">Goal Type</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Training</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.training') }}">Training List</a></li>
														<li><a href="{{ route('admin_v2.trainers') }}">Trainers</a></li>
														<li><a href="{{ route('admin_v2.training-type') }}">Training Type</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.promotion') }}"><span>Promotion</span></a></li>
												<li><a href="{{ route('admin_v2.resignation') }}"><span>Resignation</span></a></li>
												<li><a href="{{ route('admin_v2.termination') }}"><span>Termination</span></a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<i class="ti ti-user-star"></i><span>Administration</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Sales</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.estimates') }}">Estimates</a></li>
														<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
														<li><a href="{{ route('admin_v2.payments') }}">Payments</a></li>
														<li><a href="{{ route('admin_v2.expenses') }}">Expenses</a></li>
														<li><a href="{{ route('admin_v2.provident-fund') }}">Provident Fund</a></li>
														<li><a href="{{ route('admin_v2.taxes') }}">Taxes</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Accounting</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.categories') }}">Categories</a></li>
														<li><a href="{{ route('admin_v2.budgets') }}">Budgets</a></li>
														<li><a href="{{ route('admin_v2.budget-expenses') }}">Budget Expenses</a></li>
														<li><a href="{{ route('admin_v2.budget-revenues') }}">Budget Revenues</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Payroll</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.employee-salary') }}">Employee Salary</a></li>
														<li><a href="{{ route('admin_v2.payslip') }}">Payslip</a></li>
														<li><a href="{{ route('admin_v2.payroll') }}">Payroll Items</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Assets</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.assets') }}">Assets</a></li>
														<li><a href="{{ route('admin_v2.asset-categories') }}">Asset Categories</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Help & Supports</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.knowledgebase') }}">Knowledge Base</a></li>
														<li><a href="{{ route('admin_v2.activity') }}">Activities</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>User Management</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.users') }}">Users</a></li>
														<li><a href="{{ route('admin_v2.roles-permissions') }}">Roles & Permissions</a>
														</li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Reports</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li><a href="{{ route('admin_v2.expenses-report') }}">Expense Report</a></li>
														<li><a href="{{ route('admin_v2.invoice-report') }}">Invoice Report</a></li>
														<li><a href="{{ route('admin_v2.payment-report') }}">Payment Report</a></li>
														<li><a href="{{ route('admin_v2.project-report') }}">Project Report</a></li>
														<li><a href="{{ route('admin_v2.task-report') }}">Task Report</a></li>
														<li><a href="{{ route('admin_v2.user-report') }}">User Report</a></li>
														<li><a href="{{ route('admin_v2.employee-report') }}">Employee Report</a></li>
														<li><a href="{{ route('admin_v2.payslip-report') }}">Payslip Report</a></li>
														<li><a href="{{ route('admin_v2.attendance-report') }}">Attendance Report</a></li>
														<li><a href="{{ route('admin_v2.leave-report') }}">Leave Report</a></li>
														<li><a href="{{ route('admin_v2.daily-report') }}">Daily Report</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Settings</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li class="submenu">
															<a href="javascript:void(0);">General Settings<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.profile-settings') }}">Profile</a></li>
																<li><a href="{{ route('admin_v2.security-settings') }}">Security</a></li>
																<li><a
																		href="{{ route('admin_v2.notification-settings') }}">Notifications</a>
																</li>
																<li><a href="{{ route('admin_v2.connected-apps') }}">Connected Apps</a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">Website Settings<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.bussiness-settings') }}">Business
																		Settings</a></li>
																<li><a href="{{ route('admin_v2.seo-settings') }}">SEO Settings</a></li>
																<li><a
																		href="{{ route('admin_v2.localization-settings') }}">Localization</a>
																</li>
																<li><a href="{{ route('admin_v2.prefixes') }}">Prefixes</a></li>
																<li><a href="{{ route('admin_v2.preferences') }}">Preferences</a></li>
																<li><a href="{{ route('admin_v2.performance-appraisal') }}">Appearance</a>
																</li>
																<li><a href="{{ route('admin_v2.language') }}">Language</a></li>
																<li><a
																		href="{{ route('admin_v2.authentication-settings') }}">Authentication</a>
																</li>
																<li><a href="{{ route('admin_v2.ai-settings') }}">AI Settings</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">App Settings<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.salary-settings') }}">Salary Settings</a>
																</li>
																<li><a href="{{ route('admin_v2.approval-settings') }}">Approval
																		Settings</a></li>
																<li><a href="{{ route('admin_v2.invoice-settings') }}">Invoice Settings</a>
																</li>
																<li><a href="{{ route('admin_v2.leave-type') }}">Leave Type</a></li>
																<li><a href="{{ route('admin_v2.custom-fields') }}">Custom Fields</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">System Settings<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.email-settings') }}">Email Settings</a>
																</li>
																<li><a href="{{ route('admin_v2.email-template') }}">Email Templates</a>
																</li>
																<li><a href="{{ route('admin_v2.sms-settings') }}">SMS Settings</a></li>
																<li><a href="{{ route('admin_v2.sms-template') }}">SMS Templates</a></li>
																<li><a href="{{ route('admin_v2.otp-settings') }}">OTP</a></li>
																<li><a href="{{ route('admin_v2.gdpr') }}">GDPR Cookies</a></li>
																<li><a href="{{ route('admin_v2.maintenance-mode') }}">Maintenance Mode</a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">Financial Settings<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.payment-gateways') }}">Payment Gateways</a>
																</li>
																<li><a href="{{ route('admin_v2.tax-rates') }}">Tax Rate</a></li>
																<li><a href="{{ route('admin_v2.currencies') }}">Currencies</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">Other Settings<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.custom-css') }}">Custom CSS</a></li>
																<li><a href="{{ route('admin_v2.custom-js') }}">Custom JS</a></li>
																<li><a href="{{ route('admin_v2.cronjob') }}">Cronjob</a></li>
																<li><a href="{{ route('admin_v2.storage-settings') }}">Storage</a></li>
																<li><a href="{{ route('admin_v2.ban-ip-address') }}">Ban IP Address</a>
																</li>
																<li><a href="{{ route('admin_v2.backup') }}">Backup</a></li>
																<li><a href="{{ route('admin_v2.clear-cache') }}">Clear Cache</a></li>
															</ul>
														</li>
													</ul>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="#">
												<i class="ti ti-page-break"></i><span>Pages</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.starter') }}"><span>Starter</span></a></li>
												<li><a href="{{ route('admin_v2.profile') }}"><span>Profile</span></a></li>
												<li><a href="{{ route('admin_v2.gallery') }}"><span>Gallery</span></a></li>
												<li><a href="{{ route('admin_v2.search-result') }}"><span>Search Results</span></a></li>
												<li><a href="{{ route('admin_v2.timeline') }}"><span>Timeline</span></a></li>
												<li><a href="{{ route('admin_v2.pricing') }}"><span>Pricing</span></a></li>
												<li><a href="{{ route('admin_v2.coming-soon') }}"><span>Coming Soon</span></a></li>
												<li><a href="{{ route('admin_v2.under-maintenance') }}"><span>Under Maintenance</span></a>
												</li>
												<li><a href="{{ route('admin_v2.under-construction') }}"><span>Under
															Construction</span></a></li>
												<li><a href="{{ route('admin_v2.api-keys') }}"><span>API Keys</span></a></li>
												<li><a href="{{ route('admin_v2.privacy-policy') }}"><span>Privacy Policy</span></a></li>
												<li><a href="{{ route('admin_v2.terms-condition') }}"><span>Terms & Conditions</span></a>
												</li>
												<li class="submenu">
													<a href="#"><span>Content</span> <span
															class="menu-arrow"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.pages') }}">Pages</a></li>
														<li class="submenu">
															<a href="javascript:void(0);">Blogs<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.blogs') }}">All Blogs</a></li>
																<li><a href="{{ route('admin_v2.blog-categories') }}">Categories</a></li>
																<li><a href="{{ route('admin_v2.blog-comments') }}">Comments</a></li>
																<li><a href="{{ route('admin_v2.blog-tags') }}">Tags</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">Locations<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.countries') }}">Countries</a></li>
																<li><a href="{{ route('admin_v2.states') }}">States</a></li>
																<li><a href="{{ route('admin_v2.cities') }}">Cities</a></li>
															</ul>
														</li>
														<li><a href="{{ route('admin_v2.testimonials') }}">Testimonials</a></li>
														<li><a href="{{ route('admin_v2.faq') }}">FAQ’S</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="#">
														<span>Authentication</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li class="submenu">
															<a href="javascript:void(0);" class="">Login<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.login') }}">Cover</a></li>
																<li><a href="{{ route('admin_v2.login-2') }}">Illustration</a></li>
																<li><a href="{{ route('admin_v2.login-3') }}">Basic</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);" class="">Register<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.register') }}">Cover</a></li>
																<li><a href="{{ route('admin_v2.register-2') }}">Illustration</a></li>
																<li><a href="{{ route('admin_v2.register-3') }}">Basic</a></li>
															</ul>
														</li>
														<li class="submenu"><a href="javascript:void(0);">Forgot
																Password<span class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.forgot-password') }}">Cover</a></li>
																<li><a href="{{ route('admin_v2.forgot-password-2') }}">Illustration</a>
																</li>
																<li><a href="{{ route('admin_v2.forgot-password-3') }}">Basic</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">Reset Password<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.reset-password') }}">Cover</a></li>
																<li><a href="{{ route('admin_v2.reset-password-2') }}">Illustration</a>
																</li>
																<li><a href="{{ route('admin_v2.reset-password-3') }}">Basic</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">Email Verification<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.email-verification') }}">Cover</a></li>
																<li><a href="{{ route('admin_v2.email-verification-2') }}">Illustration</a>
																</li>
																<li><a href="{{ route('admin_v2.email-verification-3') }}">Basic</a></li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">2 Step Verification<span
																	class="menu-arrow"></span></a>
															<ul>
																<li><a href="{{ route('admin_v2.two-step-verification') }}">Cover</a></li>
																<li><a
																		href="{{ route('admin_v2.two-step-verification-2') }}">Illustration</a>
																</li>
																<li><a href="{{ route('admin_v2.two-step-verification-3') }}">Basic</a>
																</li>
															</ul>
														</li>
														<li><a href="{{ route('admin_v2.lock-screen') }}">Lock Screen</a></li>
														<li><a href="{{ route('admin_v2.error-404') }}">404 Error</a></li>
														<li><a href="{{ route('admin_v2.error-500') }}">500 Error</a></li>
													</ul>
												</li>
												<li class="submenu">
													<a href="#">
														<span>UI Interface</span>
														<span class="menu-arrow"></span>
													</a>
													<ul>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-hierarchy-2"></i>
																<span>Base UI</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.ui-alerts') }}">Alerts</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-accordion') }}">Accordion</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-avatar') }}">Avatar</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-badges') }}">Badges</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-borders') }}">Border</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-buttons') }}">Buttons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-buttons-group') }}">Button Group</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-breadcrumb') }}">Breadcrumb</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-cards') }}">Card</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-carousel') }}">Carousel</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-colors') }}">Colors</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-dropdowns') }}">Dropdowns</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-grid') }}">Grid</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-images') }}">Images</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-lightbox') }}">Lightbox</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-media') }}">Media</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-modals') }}">Modals</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-offcanvas') }}">Offcanvas</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-pagination') }}">Pagination</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-popovers') }}">Popovers</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-progress') }}">Progress</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-placeholders') }}">Placeholders</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-spinner') }}">Spinner</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-sweetalerts') }}">Sweet Alerts</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-nav-tabs') }}">Tabs</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-toasts') }}">Toasts</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-tooltips') }}">Tooltips</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-typography') }}">Typography</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-video') }}">Video</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-sortable') }}">Sortable</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-swiperjs') }}">Swiperjs</a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-hierarchy-3"></i>
																<span>Advanced UI</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.ui-ribbon') }}">Ribbon</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-clipboard') }}">Clipboard</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-drag-drop') }}">Drag & Drop</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-rangeslider') }}">Range Slider</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-rating') }}">Rating</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-text-editor') }}">Text Editor</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-counter') }}">Counter</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-scrollbar') }}">Scrollbar</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-stickynote') }}">Sticky Note</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.ui-timeline') }}">Timeline</a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-input-search"></i>
																<span>Forms</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li class="submenu submenu-two">
																	<a href="javascript:void(0);">Form Elements <span
																			class="menu-arrow inside-submenu"></span>
																	</a>
																	<ul>
																		<li>
																			<a href="{{ route('admin_v2.form-basic-inputs') }}">Basic
																				Inputs</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-checkbox-radios') }}">Checkbox
																				& Radios</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-input-groups') }}">Input
																				Groups</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-grid-gutters') }}">Grid &
																				Gutters</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-select') }}">Form Select</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-mask') }}">Input Masks</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-fileupload') }}">File
																				Uploads</a>
																		</li>
																	</ul>
																</li>
																<li class="submenu submenu-two">
																	<a href="javascript:void(0);">Layouts <span
																			class="menu-arrow inside-submenu"></span>
																	</a>
																	<ul>
																		<li>
																			<a href="{{ route('admin_v2.form-horizontal') }}">Horizontal
																				Form</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-vertical') }}">Vertical
																				Form</a>
																		</li>
																		<li>
																			<a href="{{ route('admin_v2.form-floating-labels') }}">Floating
																				Labels</a>
																		</li>
																	</ul>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-validation') }}">Form Validation</a>
																</li>

																<li>
																	<a href="{{ route('admin_v2.form-select2') }}">Select2</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-wizard') }}">Form Wizard</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.form-pickers') }}">Form Pickers</a>
																</li>

															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-table-plus"></i>
																<span>Tables</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.tables-basic') }}">Basic Tables </a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.data-tables') }}">Data Table </a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-chart-line"></i>
																<span>Charts</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.chart-apex') }}">Apex Charts</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.chart-c3') }}">Chart C3</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.chart-js') }}">Chart Js</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.chart-morris') }}">Morris Charts</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.chart-flot') }}">Flot Charts</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.chart-peity') }}">Peity Charts</a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-icons"></i>
																<span>Icons</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.icon-fontawesome') }}">Fontawesome
																		Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-tabler') }}">Tabler Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-bootstrap') }}">Bootstrap Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-remix') }}">Remix Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-feather') }}">Feather Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-ionic') }}">Ionic Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-material') }}">Material Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-pe7') }}">Pe7 Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-simpleline') }}">Simpleline Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-themify') }}">Themify Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-weather') }}">Weather Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-typicon') }}">Typicon Icons</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.icon-flag') }}">Flag Icons</a>
																</li>
															</ul>
														</li>
														<li class="submenu">
															<a href="javascript:void(0);">
																<i class="ti ti-table-plus"></i>
																<span>Maps</span>
																<span class="menu-arrow"></span>
															</a>
															<ul>
																<li>
																	<a href="{{ route('admin_v2.maps-vector') }}">Vector</a>
																</li>
																<li>
																	<a href="{{ route('admin_v2.maps-leaflet') }}">Leaflet</a>
																</li>
															</ul>
														</li>
													</ul>
												</li>
												<li><a href="#">Documentation</a></li>
												<li><a href="#">Change Log</a></li>
												<li class="submenu">
													<a href="javascript:void(0);"><span>Multi Level</span><span
															class="menu-arrow"></span></a>
													<ul>
														<li><a href="javascript:void(0);">Multilevel 1</a></li>
														<li class="submenu submenu-two">
															<a href="javascript:void(0);">Multilevel 2<span
																	class="menu-arrow inside-submenu"></span></a>
															<ul>
																<li><a href="javascript:void(0);">Multilevel 2.1</a>
																</li>
																<li class="submenu submenu-two submenu-three">
																	<a href="javascript:void(0);">Multilevel 2.2<span
																			class="menu-arrow inside-submenu inside-submenu-two"></span></a>
																	<ul>
																		<li><a href="javascript:void(0);">Multilevel
																				2.2.1</a></li>
																		<li><a href="javascript:void(0);">Multilevel
																				2.2.2</a></li>
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
												<span class="avatar avatar-md bg-transparent-dark me-2"><i
														class="ti ti-calendar text-gray-9"></i></span>Calendar
											</a>
											<a href="{{ route('admin_v2.todo') }}" class="d-block py-2">
												<span class="avatar avatar-md bg-transparent-dark me-2"><i
														class="ti ti-subtask text-gray-9"></i></span>To Do
											</a>
											<a href="{{ route('admin_v2.notes') }}" class="d-block py-2">
												<span class="avatar avatar-md bg-transparent-dark me-2"><i
														class="ti ti-notes text-gray-9"></i></span>Notes
											</a>
											<a href="{{ route('admin_v2.file-manager') }}" class="d-block py-2">
												<span class="avatar avatar-md bg-transparent-dark me-2"><i
														class="ti ti-folder text-gray-9"></i></span>File Manager
											</a>
											<a href="{{ route('admin_v2.kanban-view') }}" class="d-block py-2">
												<span class="avatar avatar-md bg-transparent-dark me-2"><i
														class="ti ti-layout-kanban text-gray-9"></i></span>Kanban
											</a>
											<a href="{{ route('admin_v2.invoices') }}" class="d-block py-2 pb-0">
												<span class="avatar avatar-md bg-transparent-dark me-2"><i
														class="ti ti-file-invoice text-gray-9"></i></span>Invoices
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="me-1">
								<a href="{{ route('admin_v2.chat') }}" class="btn btn-menubar position-relative">
									<i class="ti ti-brand-hipchat"></i>
									<span
										class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
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
									<div
										class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
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
															<img src="/assets3/img/profiles/avatar-27.jpg" alt="Profile">
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
															<img src="/assets3/img/profiles/avatar-23.jpg" alt="Profile">
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
															<img src="/assets3/img/profiles/avatar-25.jpg" alt="Profile">
														</span>
														<div class="flex-grow-1">
															<p class="mb-1">New student record <span class="text-dark fw-semibold"> George</span> 
																is created by <span class="text-dark fw-semibold">Teressa</span>
															</p>
															<span>2 hrs ago</span>
														</div>
													</div>
												</a>
											</div>
											<div class="border-0 mb-3 pb-0">
												<a href="{{ route('admin_v2.activity') }}">
													<div class="d-flex">
														<span class="avatar avatar-lg me-2 flex-shrink-0">
															<img src="/assets3/img/profiles/avatar-01.jpg" alt="Profile">
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
								<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
									<span class="avatar avatar-sm online">
										<img src="/assets3/img/profiles/avatar-12.jpg" alt="Img" class="img-fluid rounded-circle">
									</span>
								</a>
								<div class="dropdown-menu shadow-none">
									<div class="card mb-0">
										<div class="card-header">
											<div class="d-flex align-items-center">
												<span class="avatar avatar-lg me-2 avatar-rounded">
													<img src="/assets3/img/profiles/avatar-12.jpg" alt="img">
												</span>
												<div>
													<h5 class="mb-0">Kevin Larry</h5>
													<p class="fs-12 fw-medium mb-0">warren@example.com</p>
												</div>
											</div>
										</div>
										<div class="card-body">
											<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
												href="{{ route('admin_v2.profile') }}">
												<i class="ti ti-user-circle me-1"></i>My Profile
											</a>
											<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
												href="{{ route('admin_v2.bussiness-settings') }}">
												<i class="ti ti-settings me-1"></i>Settings
											</a>
											
											<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
												href="{{ route('admin_v2.profile-settings') }}">
												<i class="ti ti-circle-arrow-up me-1"></i>My Account
											</a>
											<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
												href="{{ route('admin_v2.knowledgebase') }}">
												<i class="ti ti-question-mark me-1"></i>Knowledge Base
											</a>
										</div>
										<div class="card-footer py-1">
											<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('admin_v2.login') }}"><i class="ti ti-login me-2"></i>Logout</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Mobile Menu -->
				<div class="dropdown mobile-user-menu">
					<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-ellipsis-v"></i>
					</a>
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

		<!-- Sidebar -->
		<div class="sidebar" id="sidebar">
			<!-- Logo -->
			<div class="sidebar-logo">
				<a href="{{ route('admin_v2.index') }}" class="logo logo-normal">
					<img src="/assets3/img/logo.svg" alt="Logo">
				</a>
				<a href="{{ route('admin_v2.index') }}" class="logo-small">
					<img src="/assets3/img/logo-small.svg" alt="Logo">
				</a>
				<a href="{{ route('admin_v2.index') }}" class="dark-logo">
					<img src="/assets3/img/logo-white.svg" alt="Logo">
				</a>
			</div>
			<!-- /Logo -->
			<div class="modern-profile p-3 pb-0">
				<div class="text-center rounded bg-light p-3 mb-4 user-profile">
					<div class="avatar avatar-lg online mb-3">
						<img src="/assets3/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
					</div>
					<h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
					<p class="fs-10">System Admin</p>
				</div>
				<div class="sidebar-nav mb-3">
					<ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
						role="tablist">
						<li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
						<li class="nav-item"><a class="nav-link border-0" href="{{ route('admin_v2.chat') }}">Chats</a></li>
						<li class="nav-item"><a class="nav-link border-0" href="{{ route('admin_v2.email') }}">Inbox</a></li>
					</ul>
				</div>
			</div>
			<div class="sidebar-header p-3 pb-0 pt-2">
				<div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
					<div class="avatar avatar-md onlin">
						<img src="/assets3/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
					</div>
					<div class="text-start sidebar-profile-info ms-2">
						<h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
						<p class="fs-10">System Admin</p>
					</div>
				</div>
				<div class="input-group input-group-flat d-inline-flex mb-4">
					<span class="input-icon-addon">
						<i class="ti ti-search"></i>
					</span>
					<input type="text" class="form-control" placeholder="Search in HRMS">
					<span class="input-group-text">
						<kbd>CTRL + / </kbd>
					</span>
				</div>
				<div class="d-flex align-items-center justify-content-between menu-item mb-3">
					<div class="me-3">
						<a href="{{ route('admin_v2.calendar') }}" class="btn btn-menubar">
							<i class="ti ti-layout-grid-remove"></i>
						</a>
					</div>
					<div class="me-3">
						<a href="{{ route('admin_v2.chat') }}" class="btn btn-menubar position-relative">
							<i class="ti ti-brand-hipchat"></i>
							<span
								class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
						</a>
					</div>
					<div class="me-3 notification-item">
						<a href="{{ route('admin_v2.activity') }}" class="btn btn-menubar position-relative me-1">
							<i class="ti ti-bell"></i>
							<span class="notification-status-dot"></span>
						</a>
					</div>
					<div class="me-0">
						<a href="{{ route('admin_v2.email') }}" class="btn btn-menubar">
							<i class="ti ti-message"></i>
						</a>
					</div>
				</div>
			</div>
			<div class="sidebar-inner slimscroll">
				<div id="sidebar-menu" class="sidebar-menu">
					<ul>
						<li class="menu-title"><span>MAIN MENU</span></li>
						<li>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-smart-home"></i><span>Dashboard</span><span
											class="badge badge-danger fs-10 fw-medium text-white p-1">Hot</span><span
											class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.index') }}">Admin Dashboard</a></li>
										<li><a href="{{ route('admin_v2.employee-dashboard') }}">Employee Dashboard</a></li>
										<li><a href="{{ route('admin_v2.deals-dashboard') }}">Deals Dashboard</a></li>
										<li><a href="{{ route('admin_v2.leads-dashboard') }}">Leads Dashboard</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-layout-grid-add"></i><span>Applications</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.chat') }}">Chat</a></li>
										<li class="submenu submenu-two">
											<a href="{{ route('admin_v2.call') }}">Calls<span class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.voice-call') }}">Voice Call</a></li>
												<li><a href="{{ route('admin_v2.video-call') }}">Video Call</a></li>
												<li><a href="{{ route('admin_v2.outgoing-call') }}">Outgoing Call</a></li>
												<li><a href="{{ route('admin_v2.incoming-call') }}">Incoming Call</a></li>
												<li><a href="{{ route('admin_v2.call-history') }}">Call History</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.calendar') }}">Calendar</a></li>
										<li><a href="{{ route('admin_v2.email') }}">Email</a></li>
										<li><a href="{{ route('admin_v2.todo') }}">To Do</a></li>
										<li><a href="{{ route('admin_v2.notes') }}">Notes</a></li>
										<li><a href="{{ route('admin_v2.social-feed') }}">Social Feed</a></li>
										<li><a href="{{ route('admin_v2.file-manager') }}">File Manager</a></li>
										<li><a href="{{ route('admin_v2.kanban-view') }}">Kanban</a></li>
										<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#">
										<i class="ti ti-user-star"></i><span>Super Admin</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.dashboard') }}">Dashboard</a></li>
										<li><a href="{{ route('admin_v2.companies') }}">Companies</a></li>
										<li><a href="{{ route('admin_v2.subscription') }}">Subscriptions</a></li>
										<li><a href="{{ route('admin_v2.packages') }}">Packages</a></li>
										<li><a href="{{ route('admin_v2.domain') }}">Domain</a></li>
										<li><a href="{{ route('admin_v2.purchase-transaction') }}">Purchase Transaction</a></li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>LAYOUT</span></li>
						<li>
							<ul>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal') }}">
										<i class="ti ti-layout-navbar"></i><span>Horizontal</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-detached') }}">
										<i class="ti ti-details"></i><span>Detached</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-modern') }}">
										<i class="ti ti-layout-board-split"></i><span>Modern</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-two-column') }}">
										<i class="ti ti-columns-2"></i><span>Two Column </span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-hovered') }}">
										<i class="ti ti-column-insert-left"></i><span>Hovered</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-box') }}">
										<i class="ti ti-layout-align-middle"></i><span>Boxed</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-single') }}">
										<i class="ti ti-layout-navbar-inactive"></i><span>Horizontal Single</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-overlay') }}">
										<i class="ti ti-layout-collage"></i><span>Horizontal Overlay</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-box') }}">
										<i class="ti ti-layout-board"></i><span>Horizontal Box</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-sidemenu') }}">
										<i class="ti ti-table"></i><span>Menu Aside</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-vertical-transparent') }}">
										<i class="ti ti-layout"></i><span>Transparent</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-without-header') }}">
										<i class="ti ti-layout-sidebar"></i><span>Without Header</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-rtl') }}">
										<i class="ti ti-text-direction-rtl"></i><span>RTL</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-dark') }}">
										<i class="ti ti-moon"></i><span>Dark</span>
									</a>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>PROJECTS</span></li>
						<li>
							<ul>
								<li>
									<a href="{{ route('admin_v2.clients-grid') }}">
										<i class="ti ti-users-group"></i><span>Clients</span>
									</a>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-box"></i><span>Projects</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.projects-grid') }}">Projects</a></li>
										<li><a href="{{ route('admin_v2.tasks') }}">Tasks</a></li>
										<li><a href="{{ route('admin_v2.task-board') }}">Task Board</a></li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>CRM</span></li>
						<li>
							<ul>
								<li>
									<a href="{{ route('admin_v2.contacts-grid') }}">
										<i class="ti ti-user-shield"></i><span>Contacts</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.companies-grid') }}">
										<i class="ti ti-building"></i><span>Companies</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.deals-grid') }}">
										<i class="ti ti-heart-handshake"></i><span>Deals</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.leads-grid') }}">
										<i class="ti ti-user-check"></i><span>Leads</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.pipeline') }}">
										<i class="ti ti-timeline-event-text"></i><span>Pipeline</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.analytics') }}">
										<i class="ti ti-graph"></i><span>Analytics</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.activity') }}">
										<i class="ti ti-activity"></i><span>Activities</span>
									</a>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>HRM</span></li>
						<li>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);"  class="active subdrop">
										<i class="ti ti-users"></i><span>Employees</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.employees') }}" class="active">Employee Lists</a></li>
										<li><a href="{{ route('admin_v2.employees-grid') }}">Employee Grid</a></li>
										<li><a href="{{ route('admin_v2.employee-details') }}">Employee Details</a></li>
										<li><a href="{{ route('admin_v2.departments') }}">Departments</a></li>
										<li><a href="{{ route('admin_v2.campus') }}">Campus</a></li>
										<li><a href="{{ route('admin_v2.ministry') }}">Ministry</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-ticket"></i><span>Tickets</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.tickets') }}">Tickets</a></li>
										<li><a href="{{ route('admin_v2.ticket-details') }}">Ticket Details</a></li>
									</ul>
								</li>
								<li>
									<a href="{{ route('admin_v2.holidays') }}">
										<i class="ti ti-calendar-event"></i><span>Holidays</span>
									</a>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-file-time"></i><span>Attendance</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Leaves<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.leaves') }}">Leaves (Admin)</a></li>
												<li><a href="{{ route('admin_v2.leaves-employee') }}">Leave (Employee)</a></li>
												<li><a href="{{ route('admin_v2.leave-settings') }}">Leave Settings</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.attendance-admin') }}">Attendance (Admin)</a></li>
										<li><a href="{{ route('admin_v2.attendance-employee') }}">Attendance (Employee)</a></li>
										<li><a href="{{ route('admin_v2.timesheets') }}">Timesheets</a></li>
										<li><a href="{{ route('admin_v2.schedule.index') }}">Shift & Schedule</a></li>
										<li><a href="{{ route('admin_v2.overtime') }}">Overtime</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-school"></i><span>Performance</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.performance-indicator') }}">Performance Indicator</a></li>
										<li><a href="{{ route('admin_v2.performance-review') }}">Performance Review</a></li>
										<li><a href="{{ route('admin_v2.performance-appraisal') }}">Performance Appraisal</a></li>
										<li><a href="{{ route('admin_v2.goal-tracking') }}">Goal List</a></li>
										<li><a href="{{ route('admin_v2.goal-type') }}">Goal Type</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-edit"></i><span>Training</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.training') }}">Training List</a></li>
										<li><a href="{{ route('admin_v2.trainers') }}">Trainers</a></li>
										<li><a href="{{ route('admin_v2.training-type') }}">Training Type</a></li>
									</ul>
								</li>
								<li>
									<a href="{{ route('admin_v2.promotion') }}">
										<i class="ti ti-speakerphone"></i><span>Promotion</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.resignation') }}">
										<i class="ti ti-external-link"></i><span>Resignation</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.termination') }}">
										<i class="ti ti-circle-x"></i><span>Termination</span>
									</a>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>RECRUITMENT</span></li>
						<li>
							<ul>
								<li>
									<a href="{{ route('admin_v2.job-grid') }}">
										<i class="ti ti-timeline"></i><span>Jobs</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.candidates-grid') }}">
										<i class="ti ti-user-shield"></i><span>Candidates</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.refferals') }}">
										<i class="ti ti-ux-circle"></i><span>Referrals</span>
									</a>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>FINANCE & ACCOUNTS</span></li>
						<li>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-shopping-cart-dollar"></i><span>Sales</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.estimates') }}">Estimates</a></li>
										<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
										<li><a href="{{ route('admin_v2.payments') }}">Payments</a></li>
										<li><a href="{{ route('admin_v2.expenses') }}">Expenses</a></li>
										<li><a href="{{ route('admin_v2.provident-fund') }}">Provident Fund</a></li>
										<li><a href="{{ route('admin_v2.taxes') }}">Taxes</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-file-dollar"></i><span>Accounting</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.categories') }}">Categories</a></li>
										<li><a href="{{ route('admin_v2.budgets') }}">Budgets</a></li>
										<li><a href="{{ route('admin_v2.budget-expenses') }}">Budget Expenses</a></li>
										<li><a href="{{ route('admin_v2.budget-revenues') }}">Budget Revenues</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-cash"></i><span>Payroll</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.employee-salary') }}">Employee Salary</a></li>
										<li><a href="{{ route('admin_v2.payslip') }}">Payslip</a></li>
										<li><a href="{{ route('admin_v2.payroll') }}">Payroll Items</a></li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>ADMINISTRATION</span></li>
						<li>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-cash"></i><span>Assets</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.assets') }}">Assets</a></li>
										<li><a href="{{ route('admin_v2.asset-categories') }}">Asset Categories</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-headset"></i><span>Help & Supports</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.knowledgebase') }}">Knowledge Base</a></li>
										<li><a href="{{ route('admin_v2.activity') }}">Activities</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-user-star"></i><span>User Management</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.users') }}">Users</a></li>
										<li><a href="{{ route('admin_v2.roles-permissions') }}">Roles & Permissions</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-user-star"></i><span>Reports</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.expenses-report') }}">Expense Report</a></li>
										<li><a href="{{ route('admin_v2.invoice-report') }}">Invoice Report</a></li>
										<li><a href="{{ route('admin_v2.payment-report') }}">Payment Report</a></li>
										<li><a href="{{ route('admin_v2.project-report') }}">Project Report</a></li>
										<li><a href="{{ route('admin_v2.task-report') }}">Task Report</a></li>
										<li><a href="{{ route('admin_v2.user-report') }}">User Report</a></li>
										<li><a href="{{ route('admin_v2.employee-report') }}">Employee Report</a></li>
										<li><a href="{{ route('admin_v2.payslip-report') }}">Payslip Report</a></li>
										<li><a href="{{ route('admin_v2.attendance-report') }}">Attendance Report</a></li>
										<li><a href="{{ route('admin_v2.leave-report') }}">Leave Report</a></li>
										<li><a href="{{ route('admin_v2.daily-report') }}">Daily Report</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-settings"></i><span>Settings</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">General Settings<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.profile-settings') }}">Profile</a></li>
												<li><a href="{{ route('admin_v2.security-settings') }}">Security</a></li>
												<li><a href="{{ route('admin_v2.notification-settings') }}">Notifications</a></li>
												<li><a href="{{ route('admin_v2.connected-apps') }}">Connected Apps</a></li>
											</ul>
										</li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Website Settings<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.bussiness-settings') }}">Business Settings</a></li>
												<li><a href="{{ route('admin_v2.seo-settings') }}">SEO Settings</a></li>
												<li><a href="{{ route('admin_v2.localization-settings') }}">Localization</a></li>
												<li><a href="{{ route('admin_v2.prefixes') }}">Prefixes</a></li>
												<li><a href="{{ route('admin_v2.preferences') }}">Preferences</a></li>
												<li><a href="{{ route('admin_v2.performance-appraisal') }}">Appearance</a></li>
												<li><a href="{{ route('admin_v2.language') }}">Language</a></li>
												<li><a href="{{ route('admin_v2.authentication-settings') }}">Authentication</a></li>
												<li><a href="{{ route('admin_v2.ai-settings') }}">AI Settings</a></li>
											</ul>
										</li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">App Settings<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.salary-settings') }}">Salary Settings</a></li>
												<li><a href="{{ route('admin_v2.approval-settings') }}">Approval Settings</a></li>
												<li><a href="{{ route('admin_v2.invoice-settings') }}">Invoice Settings</a></li>
												<li><a href="{{ route('admin_v2.leave-type') }}">Leave Type</a></li>
												<li><a href="{{ route('admin_v2.custom-fields') }}">Custom Fields</a></li>
											</ul>
										</li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">System Settings<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.email-settings') }}">Email Settings</a></li>
												<li><a href="{{ route('admin_v2.email-template') }}">Email Templates</a></li>
												<li><a href="{{ route('admin_v2.sms-settings') }}">SMS Settings</a></li>
												<li><a href="{{ route('admin_v2.sms-template') }}">SMS Templates</a></li>
												<li><a href="{{ route('admin_v2.otp-settings') }}">OTP</a></li>
												<li><a href="{{ route('admin_v2.gdpr') }}">GDPR Cookies</a></li>
												<li><a href="{{ route('admin_v2.maintenance-mode') }}">Maintenance Mode</a></li>
											</ul>
										</li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Financial Settings<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.payment-gateways') }}">Payment Gateways</a></li>
												<li><a href="{{ route('admin_v2.tax-rates') }}">Tax Rate</a></li>
												<li><a href="{{ route('admin_v2.currencies') }}">Currencies</a></li>
											</ul>
										</li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Other Settings<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.custom-css') }}">Custom CSS</a></li>
												<li><a href="{{ route('admin_v2.custom-js') }}">Custom JS</a></li>
												<li><a href="{{ route('admin_v2.cronjob') }}">Cronjob</a></li>
												<li><a href="{{ route('admin_v2.storage-settings') }}">Storage</a></li>
												<li><a href="{{ route('admin_v2.ban-ip-address') }}">Ban IP Address</a></li>
												<li><a href="{{ route('admin_v2.backup') }}">Backup</a></li>
												<li><a href="{{ route('admin_v2.clear-cache') }}">Clear Cache</a></li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>CONTENT</span></li>
						<li>
							<ul>
								<li>
									<a href="{{ route('admin_v2.pages') }}">
										<i class="ti ti-box-multiple"></i><span>Pages</span>
									</a>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-brand-blogger"></i><span>Blogs</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.blogs') }}">All Blogs</a></li>
										<li><a href="{{ route('admin_v2.blog-categories') }}">Categories</a></li>
										<li><a href="{{ route('admin_v2.blog-comments') }}">Comments</a></li>
										<li><a href="{{ route('admin_v2.blog-tags') }}">Blog Tags</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-map-pin-check"></i><span>Locations</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.countries') }}">Countries</a></li>
										<li><a href="{{ route('admin_v2.states') }}">States</a></li>
										<li><a href="{{ route('admin_v2.cities') }}">Cities</a></li>
									</ul>
								</li>
								<li>
									<a href="{{ route('admin_v2.testimonials') }}">
										<i class="ti ti-message-2"></i><span>Testimonials</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.faq') }}">
										<i class="ti ti-question-mark"></i><span>FAQ’S</span>
									</a>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>PAGES</span></li>
						<li>
							<ul>
								<li>
									<a href="{{ route('admin_v2.starter') }}">
										<i class="ti ti-layout-sidebar"></i><span>Starter</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.profile') }}">
										<i class="ti ti-user-circle"></i><span>Profile</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.gallery') }}">
										<i class="ti ti-photo"></i><span>Gallery</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.search-result') }}">
										<i class="ti ti-list-search"></i><span>Search Results</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.timeline') }}">
										<i class="ti ti-timeline"></i><span>Timeline</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.pricing') }}">
										<i class="ti ti-file-dollar"></i><span>Pricing</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.coming-soon') }}">
										<i class="ti ti-progress-bolt"></i><span>Coming Soon</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.under-maintenance') }}">
										<i class="ti ti-alert-octagon"></i><span>Under Maintenance</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.under-construction') }}">
										<i class="ti ti-barrier-block"></i><span>Under Construction</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.api-keys') }}">
										<i class="ti ti-api"></i><span>API Keys</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.privacy-policy') }}">
										<i class="ti ti-file-description"></i><span>Privacy Policy</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.terms-condition') }}">
										<i class="ti ti-file-check"></i><span>Terms & Conditions</span>
									</a>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>AUTHENTICATION</span></li>
						<li>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-login"></i><span>Login</span><span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.login') }}">Cover</a></li>
										<li><a href="{{ route('admin_v2.login-2') }}">Illustration</a></li>
										<li><a href="{{ route('admin_v2.login-3') }}">Basic</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-forms"></i><span>Register</span><span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.register') }}">Cover</a></li>
										<li><a href="{{ route('admin_v2.register-2') }}">Illustration</a></li>
										<li><a href="{{ route('admin_v2.register-3') }}">Basic</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-help-triangle"></i><span>Forgot Password</span><span
											class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.forgot-password') }}">Cover</a></li>
										<li><a href="{{ route('admin_v2.forgot-password-2') }}">Illustration</a></li>
										<li><a href="{{ route('admin_v2.forgot-password-3') }}">Basic</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-restore"></i><span>Reset Password</span><span
											class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.reset-password') }}">Cover</a></li>
										<li><a href="{{ route('admin_v2.reset-password-2') }}">Illustration</a></li>
										<li><a href="{{ route('admin_v2.reset-password-3') }}">Basic</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-mail-exclamation"></i><span>Email Verification</span><span
											class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.email-verification') }}">Cover</a></li>
										<li><a href="{{ route('admin_v2.email-verification-2') }}">Illustration</a></li>
										<li><a href="{{ route('admin_v2.email-verification-3') }}">Basic</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-password"></i><span>2 Step Verification</span><span
											class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.two-step-verification') }}">Cover</a></li>
										<li><a href="{{ route('admin_v2.two-step-verification-2') }}">Illustration</a></li>
										<li><a href="{{ route('admin_v2.two-step-verification-3') }}">Basic</a></li>
									</ul>
								</li>
								<li><a href="{{ route('admin_v2.lock-screen') }}"><i class="ti ti-lock-square"></i><span>Lock
											Screen</span></a></li>
								<li><a href="{{ route('admin_v2.error-404') }}"><i class="ti ti-error-404"></i><span>404 Error</span></a>
								</li>
								<li><a href="{{ route('admin_v2.error-500') }}"><i class="ti ti-server"></i><span>500 Error</span></a></li>
							</ul>
						</li>
						<li class="menu-title"><span>UI INTERFACE</span></li>
						<li>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-hierarchy-2"></i>
										<span>Base UI</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.ui-alerts') }}">Alerts</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-accordion') }}">Accordion</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-avatar') }}">Avatar</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-badges') }}">Badges</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-borders') }}">Border</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-buttons') }}">Buttons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-buttons-group') }}">Button Group</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-breadcrumb') }}">Breadcrumb</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-cards') }}">Card</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-carousel') }}">Carousel</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-colors') }}">Colors</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-dropdowns') }}">Dropdowns</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-grid') }}">Grid</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-images') }}">Images</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-lightbox') }}">Lightbox</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-media') }}">Media</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-modals') }}">Modals</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-offcanvas') }}">Offcanvas</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-pagination') }}">Pagination</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-popovers') }}">Popovers</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-progress') }}">Progress</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-placeholders') }}">Placeholders</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-spinner') }}">Spinner</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-sweetalerts') }}">Sweet Alerts</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-nav-tabs') }}">Tabs</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-toasts') }}">Toasts</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-tooltips') }}">Tooltips</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-typography') }}">Typography</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-video') }}">Video</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-sortable') }}">Sortable</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-swiperjs') }}">Swiperjs</a>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-hierarchy-3"></i>
										<span>Advanced UI</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.ui-ribbon') }}">Ribbon</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-clipboard') }}">Clipboard</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-drag-drop') }}">Drag & Drop</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-rangeslider') }}">Range Slider</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-rating') }}">Rating</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-text-editor') }}">Text Editor</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-counter') }}">Counter</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-scrollbar') }}">Scrollbar</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-stickynote') }}">Sticky Note</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.ui-timeline') }}">Timeline</a>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-input-search"></i>
										<span>Forms</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Form Elements <span
													class="menu-arrow inside-submenu"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.form-basic-inputs') }}">Basic Inputs</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-checkbox-radios') }}">Checkbox & Radios</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-input-groups') }}">Input Groups</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-grid-gutters') }}">Grid & Gutters</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-select') }}">Form Select</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-mask') }}">Input Masks</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-fileupload') }}">File Uploads</a>
												</li>
											</ul>
										</li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Layouts <span
													class="menu-arrow inside-submenu"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.form-horizontal') }}">Horizontal Form</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-vertical') }}">Vertical Form</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-floating-labels') }}">Floating Labels</a>
												</li>
											</ul>
										</li>
										<li>
											<a href="{{ route('admin_v2.form-validation') }}">Form Validation</a>
										</li>

										<li>
											<a href="{{ route('admin_v2.form-select2') }}">Select2</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.form-wizard') }}">Form Wizard</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.form-pickers') }}">Form Picker</a>
										</li>

									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-table-plus"></i>
										<span>Tables</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.tables-basic') }}">Basic Tables </a>
										</li>
										<li>
											<a href="{{ route('admin_v2.data-tables') }}">Data Table </a>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-chart-line"></i>
										<span>Charts</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.chart-apex') }}">Apex Charts</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.chart-c3') }}">Chart C3</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.chart-js') }}">Chart Js</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.chart-morris') }}">Morris Charts</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.chart-flot') }}">Flot Charts</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.chart-peity') }}">Peity Charts</a>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-icons"></i>
										<span>Icons</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.icon-fontawesome') }}">Fontawesome Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-tabler') }}">Tabler Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-bootstrap') }}">Bootstrap Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-remix') }}">Remix Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-feather') }}">Feather Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-ionic') }}">Ionic Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-material') }}">Material Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-pe7') }}">Pe7 Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-simpleline') }}">Simpleline Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-themify') }}">Themify Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-weather') }}">Weather Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-typicon') }}">Typicon Icons</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.icon-flag') }}">Flag Icons</a>
										</li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);">
										<i class="ti ti-table-plus"></i>
										<span>Maps</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li>
											<a href="{{ route('admin_v2.maps-vector') }}">Vector</a>
										</li>
										<li>
											<a href="{{ route('admin_v2.maps-leaflet') }}">Leaflet</a>
										</li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="menu-title"><span>Extras</span></li>
						<li>
							<ul>
								<li>
									<a href="javascript:void(0);"><i
											class="ti ti-file-text"></i><span>Documentation</span></a>
								</li>
								<li>
									<a href="javascript:void(0);"><i
											class="ti ti-exchange"></i><span>Changelog</span><span
											class="badge bg-pink badge-xs text-white fs-10 ms-s">v4.0.9</span></a>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><i class="ti ti-menu-2"></i><span>Multi
											Level</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="javascript:void(0);">Multilevel 1</a></li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Multilevel 2<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="javascript:void(0);">Multilevel 2.1</a></li>
												<li class="submenu submenu-two submenu-three">
													<a href="javascript:void(0);">Multilevel 2.2<span
															class="menu-arrow inside-submenu inside-submenu-two"></span></a>
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
		<!-- /Sidebar -->

		<!-- Horizontal Menu -->
		<div class="sidebar sidebar-horizontal" id="horizontal-menu">
			<div class="sidebar-menu">
				<div class="main-menu">
					<ul class="nav-menu">
						<li class="menu-title">
							<span>Main</span>
						</li>
						<li class="submenu">
							<a href="#">
								<i class="ti ti-smart-home"></i><span>Dashboard</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('admin_v2.index') }}">Admin Dashboard</a></li>
								<li><a href="{{ route('admin_v2.employee-dashboard') }}">Employee Dashboard</a></li>
								<li><a href="{{ route('admin_v2.deals-dashboard') }}">Deals Dashboard</a></li>
								<li><a href="{{ route('admin_v2.leads-dashboard') }}">Leads Dashboard</a></li>
							</ul>
						</li>
						<li class="submenu">
							<a href="#">
								<i class="ti ti-user-star"></i><span>Super Admin</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('admin_v2.dashboard') }}">Dashboard</a></li>
								<li><a href="{{ route('admin_v2.companies') }}">Companies</a></li>
								<li><a href="{{ route('admin_v2.subscription') }}">Subscriptions</a></li>
								<li><a href="{{ route('admin_v2.packages') }}">Packages</a></li>
								<li><a href="{{ route('admin_v2.domain') }}">Domain</a></li>
								<li><a href="{{ route('admin_v2.purchase-transaction') }}">Purchase Transaction</a></li>
							</ul>
						</li>
						<li class="submenu">
							<a href="#">
								<i class="ti ti-layout-grid-add"></i><span>Applications</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('admin_v2.chat') }}">Chat</a></li>
								<li class="submenu submenu-two">
									<a href="{{ route('admin_v2.call') }}">Calls<span class="menu-arrow inside-submenu"></span></a>
									<ul>
										<li><a href="{{ route('admin_v2.voice-call') }}">Voice Call</a></li>
										<li><a href="{{ route('admin_v2.video-call') }}">Video Call</a></li>
										<li><a href="{{ route('admin_v2.outgoing-call') }}">Outgoing Call</a></li>
										<li><a href="{{ route('admin_v2.incoming-call') }}">Incoming Call</a></li>
										<li><a href="{{ route('admin_v2.call-history') }}">Call History</a></li>
									</ul>
								</li>
								<li><a href="{{ route('admin_v2.calendar') }}">Calendar</a></li>
								<li><a href="{{ route('admin_v2.email') }}">Email</a></li>
								<li><a href="{{ route('admin_v2.todo') }}">To Do</a></li>
								<li><a href="{{ route('admin_v2.notes') }}">Notes</a></li>
								<li><a href="{{ route('admin_v2.social-feed') }}">Social Feed</a></li>
								<li><a href="{{ route('admin_v2.file-manager') }}">File Manager</a></li>
								<li><a href="{{ route('admin_v2.kanban-view') }}">Kanban</a></li>
								<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
							</ul>
						</li>
						<li class="submenu">
							<a href="#">
								<i class="ti ti-layout-board-split"></i><span>Layouts</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal') }}">
										<span>Horizontal</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-detached') }}">
										<span>Detached</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-modern') }}">
										<span>Modern</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-two-column') }}">
										<span>Two Column </span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-hovered') }}">
										<span>Hovered</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-box') }}">
										<span>Boxed</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-single') }}">
										<span>Horizontal Single</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-overlay') }}">
										<span>Horizontal Overlay</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-box') }}">
										<span>Horizontal Box</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-horizontal-sidemenu') }}">
										<span>Menu Aside</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-vertical-transparent') }}">
										<span>Transparent</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-without-header') }}">
										<span>Without Header</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-rtl') }}">
										<span>RTL</span>
									</a>
								</li>
								<li>
									<a href="{{ route('admin_v2.layout-dark') }}">
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
								<li>
									<a href="{{ route('admin_v2.clients-grid') }}"><span>Clients</span>
									</a>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Projects</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.projects-grid') }}">Projects</a></li>
										<li><a href="{{ route('admin_v2.tasks') }}">Tasks</a></li>
										<li><a href="{{ route('admin_v2.task-board') }}">Task Board</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="{{ route('admin_v2.call') }}" >Crm<span class="menu-arrow"></span></a>
									<ul>
										<li><a href="{{ route('admin_v2.contacts-grid') }}"><span>Contacts</span></a></li>
										<li><a href="{{ route('admin_v2.companies-grid') }}"><span>Companies</span></a></li>
										<li><a href="{{ route('admin_v2.deals-grid') }}"><span>Deals</span></a></li>
										<li><a href="{{ route('admin_v2.leads-grid') }}"><span>Leads</span></a></li>
										<li><a href="{{ route('admin_v2.pipeline') }}"><span>Pipeline</span></a></li>
										<li><a href="{{ route('admin_v2.analytics') }}"><span>Analytics</span></a></li>
										<li><a href="{{ route('admin_v2.activity') }}"><span>Activities</span></a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);" class="active subdrop"><span>Employees</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.employees') }}" class="active">Employee Lists</a></li>
										<li><a href="{{ route('admin_v2.employees-grid') }}">Employee Grid</a></li>
										<li><a href="{{ route('admin_v2.employee-details') }}">Employee Details</a></li>
										<li><a href="{{ route('admin_v2.departments') }}">Departments</a></li>
										<li><a href="{{ route('admin_v2.campus') }}">Campus</a></li>
										<li><a href="{{ route('admin_v2.ministry') }}">Ministry</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Tickets</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.tickets') }}">Tickets</a></li>
										<li><a href="{{ route('admin_v2.ticket-details') }}">Ticket Details</a></li>
									</ul>
								</li>
								<li><a href="{{ route('admin_v2.holidays') }}"><span>Holidays</span></a></li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Attendance</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu">
											<a href="javascript:void(0);">Leaves<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.leaves') }}">Leaves (Admin)</a></li>
												<li><a href="{{ route('admin_v2.leaves-employee') }}">Leave (Employee)</a></li>
												<li><a href="{{ route('admin_v2.leave-settings') }}">Leave Settings</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.attendance-admin') }}">Attendance (Admin)</a></li>
										<li><a href="{{ route('admin_v2.attendance-employee') }}">Attendance (Employee)</a></li>
										<li><a href="{{ route('admin_v2.timesheets') }}">Timesheets</a></li>
										<li><a href="{{ route('admin_v2.schedule.index') }}">Shift & Schedule</a></li>
										<li><a href="{{ route('admin_v2.overtime') }}">Overtime</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Performance</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.performance-indicator') }}">Performance Indicator</a></li>
										<li><a href="{{ route('admin_v2.performance-review') }}">Performance Review</a></li>
										<li><a href="{{ route('admin_v2.performance-appraisal') }}">Performance Appraisal</a></li>
										<li><a href="{{ route('admin_v2.goal-tracking') }}">Goal List</a></li>
										<li><a href="{{ route('admin_v2.goal-type') }}">Goal Type</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Training</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.training') }}">Training List</a></li>
										<li><a href="{{ route('admin_v2.trainers') }}">Trainers</a></li>
										<li><a href="{{ route('admin_v2.training-type') }}">Training Type</a></li>
									</ul>
								</li>
								<li><a href="{{ route('admin_v2.promotion') }}"><span>Promotion</span></a></li>
								<li><a href="{{ route('admin_v2.resignation') }}"><span>Resignation</span></a></li>
								<li><a href="{{ route('admin_v2.termination') }}"><span>Termination</span></a></li>
							</ul>
						</li>
						<li class="submenu">
							<a href="#">
								<i class="ti ti-user-star"></i><span>Administration</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Sales</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.estimates') }}">Estimates</a></li>
										<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
										<li><a href="{{ route('admin_v2.payments') }}">Payments</a></li>
										<li><a href="{{ route('admin_v2.expenses') }}">Expenses</a></li>
										<li><a href="{{ route('admin_v2.provident-fund') }}">Provident Fund</a></li>
										<li><a href="{{ route('admin_v2.taxes') }}">Taxes</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Accounting</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.categories') }}">Categories</a></li>
										<li><a href="{{ route('admin_v2.budgets') }}">Budgets</a></li>
										<li><a href="{{ route('admin_v2.budget-expenses') }}">Budget Expenses</a></li>
										<li><a href="{{ route('admin_v2.budget-revenues') }}">Budget Revenues</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Payroll</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.employee-salary') }}">Employee Salary</a></li>
										<li><a href="{{ route('admin_v2.payslip') }}">Payslip</a></li>
										<li><a href="{{ route('admin_v2.payroll') }}">Payroll Items</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Assets</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.assets') }}">Assets</a></li>
										<li><a href="{{ route('admin_v2.asset-categories') }}">Asset Categories</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Help & Supports</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.knowledgebase') }}">Knowledge Base</a></li>
										<li><a href="{{ route('admin_v2.activity') }}">Activities</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>User Management</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.users') }}">Users</a></li>
										<li><a href="{{ route('admin_v2.roles-permissions') }}">Roles & Permissions</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Reports</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="{{ route('admin_v2.expenses-report') }}">Expense Report</a></li>
										<li><a href="{{ route('admin_v2.invoice-report') }}">Invoice Report</a></li>
										<li><a href="{{ route('admin_v2.payment-report') }}">Payment Report</a></li>
										<li><a href="{{ route('admin_v2.project-report') }}">Project Report</a></li>
										<li><a href="{{ route('admin_v2.task-report') }}">Task Report</a></li>
										<li><a href="{{ route('admin_v2.user-report') }}">User Report</a></li>
										<li><a href="{{ route('admin_v2.employee-report') }}">Employee Report</a></li>
										<li><a href="{{ route('admin_v2.payslip-report') }}">Payslip Report</a></li>
										<li><a href="{{ route('admin_v2.attendance-report') }}">Attendance Report</a></li>
										<li><a href="{{ route('admin_v2.leave-report') }}">Leave Report</a></li>
										<li><a href="{{ route('admin_v2.daily-report') }}">Daily Report</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Settings</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu">
											<a href="javascript:void(0);">General Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.profile-settings') }}">Profile</a></li>
												<li><a href="{{ route('admin_v2.security-settings') }}">Security</a></li>
												<li><a href="{{ route('admin_v2.notification-settings') }}">Notifications</a></li>
												<li><a href="{{ route('admin_v2.connected-apps') }}">Connected Apps</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Website Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.bussiness-settings') }}">Business Settings</a></li>
												<li><a href="{{ route('admin_v2.seo-settings') }}">SEO Settings</a></li>
												<li><a href="{{ route('admin_v2.localization-settings') }}">Localization</a></li>
												<li><a href="{{ route('admin_v2.prefixes') }}">Prefixes</a></li>
												<li><a href="{{ route('admin_v2.preferences') }}">Preferences</a></li>
												<li><a href="{{ route('admin_v2.performance-appraisal') }}">Appearance</a></li>
												<li><a href="{{ route('admin_v2.language') }}">Language</a></li>
												<li><a href="{{ route('admin_v2.authentication-settings') }}">Authentication</a></li>
												<li><a href="{{ route('admin_v2.ai-settings') }}">AI Settings</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">App Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.salary-settings') }}">Salary Settings</a></li>
												<li><a href="{{ route('admin_v2.approval-settings') }}">Approval Settings</a></li>
												<li><a href="{{ route('admin_v2.invoice-settings') }}">Invoice Settings</a></li>
												<li><a href="{{ route('admin_v2.leave-type') }}">Leave Type</a></li>
												<li><a href="{{ route('admin_v2.custom-fields') }}">Custom Fields</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">System Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.email-settings') }}">Email Settings</a></li>
												<li><a href="{{ route('admin_v2.email-template') }}">Email Templates</a></li>
												<li><a href="{{ route('admin_v2.sms-settings') }}">SMS Settings</a></li>
												<li><a href="{{ route('admin_v2.sms-template') }}">SMS Templates</a></li>
												<li><a href="{{ route('admin_v2.otp-settings') }}">OTP</a></li>
												<li><a href="{{ route('admin_v2.gdpr') }}">GDPR Cookies</a></li>
												<li><a href="{{ route('admin_v2.maintenance-mode') }}">Maintenance Mode</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Financial Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.payment-gateways') }}">Payment Gateways</a></li>
												<li><a href="{{ route('admin_v2.tax-rates') }}">Tax Rate</a></li>
												<li><a href="{{ route('admin_v2.currencies') }}">Currencies</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Other Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.custom-css') }}">Custom CSS</a></li>
												<li><a href="{{ route('admin_v2.custom-js') }}">Custom JS</a></li>
												<li><a href="{{ route('admin_v2.cronjob') }}">Cronjob</a></li>
												<li><a href="{{ route('admin_v2.storage-settings') }}">Storage</a></li>
												<li><a href="{{ route('admin_v2.ban-ip-address') }}">Ban IP Address</a></li>
												<li><a href="{{ route('admin_v2.backup') }}">Backup</a></li>
												<li><a href="{{ route('admin_v2.clear-cache') }}">Clear Cache</a></li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="submenu">
							<a href="#">
								<i class="ti ti-page-break"></i><span>Pages</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('admin_v2.starter') }}"><span>Starter</span></a></li>
								<li><a href="{{ route('admin_v2.profile') }}"><span>Profile</span></a></li>
								<li><a href="{{ route('admin_v2.gallery') }}"><span>Gallery</span></a></li>
								<li><a href="{{ route('admin_v2.search-result') }}"><span>Search Results</span></a></li>
								<li><a href="{{ route('admin_v2.timeline') }}"><span>Timeline</span></a></li>
								<li><a href="{{ route('admin_v2.pricing') }}"><span>Pricing</span></a></li>
								<li><a href="{{ route('admin_v2.coming-soon') }}"><span>Coming Soon</span></a></li>
								<li><a href="{{ route('admin_v2.under-maintenance') }}"><span>Under Maintenance</span></a></li>
								<li><a href="{{ route('admin_v2.under-construction') }}"><span>Under Construction</span></a></li>
								<li><a href="{{ route('admin_v2.api-keys') }}"><span>API Keys</span></a></li>
								<li><a href="{{ route('admin_v2.privacy-policy') }}"><span>Privacy Policy</span></a></li>
								<li><a href="{{ route('admin_v2.terms-condition') }}"><span>Terms & Conditions</span></a></li>
								<li class="submenu">
									<a href="#"><span>Content</span> <span class="menu-arrow"></span></a>
									<ul>
										<li><a href="{{ route('admin_v2.pages') }}">Pages</a></li>
										<li class="submenu">
											<a href="javascript:void(0);">Blogs<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.blogs') }}">All Blogs</a></li>
												<li><a href="{{ route('admin_v2.blog-categories') }}">Categories</a></li>
												<li><a href="{{ route('admin_v2.blog-comments') }}">Comments</a></li>
												<li><a href="{{ route('admin_v2.blog-tags') }}">Tags</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Locations<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.countries') }}">Countries</a></li>
												<li><a href="{{ route('admin_v2.states') }}">States</a></li>
												<li><a href="{{ route('admin_v2.cities') }}">Cities</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.testimonials') }}">Testimonials</a></li>
										<li><a href="{{ route('admin_v2.faq') }}">FAQ’S</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#">
										<span>Authentication</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu">
											<a href="javascript:void(0);" class="">Login<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.login') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.login-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.login-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);" class="">Register<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.register') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.register-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.register-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu"><a href="javascript:void(0);">Forgot Password<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.forgot-password') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.forgot-password-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.forgot-password-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Reset Password<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.reset-password') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.reset-password-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.reset-password-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Email Verification<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.email-verification') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.email-verification-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.email-verification-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">2 Step Verification<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.two-step-verification') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.two-step-verification-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.two-step-verification-3') }}">Basic</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.lock-screen') }}">Lock Screen</a></li>
										<li><a href="{{ route('admin_v2.error-404') }}">404 Error</a></li>
										<li><a href="{{ route('admin_v2.error-500') }}">500 Error</a></li>
									</ul>
								</li>
								<li class="submenu">
									<a href="#">
										<span>UI Interface</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-hierarchy-2"></i>
												<span>Base UI</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.ui-alerts') }}">Alerts</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-accordion') }}">Accordion</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-avatar') }}">Avatar</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-badges') }}">Badges</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-borders') }}">Border</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-buttons') }}">Buttons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-buttons-group') }}">Button Group</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-breadcrumb') }}">Breadcrumb</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-cards') }}">Card</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-carousel') }}">Carousel</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-colors') }}">Colors</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-dropdowns') }}">Dropdowns</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-grid') }}">Grid</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-images') }}">Images</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-lightbox') }}">Lightbox</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-media') }}">Media</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-modals') }}">Modals</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-offcanvas') }}">Offcanvas</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-pagination') }}">Pagination</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-popovers') }}">Popovers</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-progress') }}">Progress</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-placeholders') }}">Placeholders</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-spinner') }}">Spinner</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-sweetalerts') }}">Sweet Alerts</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-nav-tabs') }}">Tabs</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-toasts') }}">Toasts</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-tooltips') }}">Tooltips</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-typography') }}">Typography</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-video') }}">Video</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-sortable') }}">Sortable</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-swiperjs') }}">Swiperjs</a>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-hierarchy-3"></i>
												<span>Advanced UI</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.ui-ribbon') }}">Ribbon</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-clipboard') }}">Clipboard</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-drag-drop') }}">Drag & Drop</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-rangeslider') }}">Range Slider</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-rating') }}">Rating</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-text-editor') }}">Text Editor</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-counter') }}">Counter</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-scrollbar') }}">Scrollbar</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-stickynote') }}">Sticky Note</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.ui-timeline') }}">Timeline</a>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-input-search"></i>
												<span>Forms</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu submenu-two">
													<a href="javascript:void(0);">Form Elements <span
															class="menu-arrow inside-submenu"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.form-basic-inputs') }}">Basic Inputs</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-checkbox-radios') }}">Checkbox & Radios</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-input-groups') }}">Input Groups</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-grid-gutters') }}">Grid & Gutters</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-select') }}">Form Select</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-mask') }}">Input Masks</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-fileupload') }}">File Uploads</a>
														</li>
													</ul>
												</li>
												<li class="submenu submenu-two">
													<a href="javascript:void(0);">Layouts <span
															class="menu-arrow inside-submenu"></span>
													</a>
													<ul>
														<li>
															<a href="{{ route('admin_v2.form-horizontal') }}">Horizontal Form</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-vertical') }}">Vertical Form</a>
														</li>
														<li>
															<a href="{{ route('admin_v2.form-floating-labels') }}">Floating Labels</a>
														</li>
													</ul>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-validation') }}">Form Validation</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-select2') }}">Select2</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-wizard') }}">Form Wizard</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.form-pickers') }}">Form Pickers</a>
												</li>

											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-table-plus"></i>
												<span>Tables</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.tables-basic') }}">Basic Tables </a>
												</li>
												<li>
													<a href="{{ route('admin_v2.data-tables') }}">Data Table </a>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-chart-line"></i>
												<span>Charts</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.chart-apex') }}">Apex Charts</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.chart-c3') }}">Chart C3</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.chart-js') }}">Chart Js</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.chart-morris') }}">Morris Charts</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.chart-flot') }}">Flot Charts</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.chart-peity') }}">Peity Charts</a>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-icons"></i>
												<span>Icons</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.icon-fontawesome') }}">Fontawesome Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-tabler') }}">Tabler Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-bootstrap') }}">Bootstrap Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-remix') }}">Remix Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-feather') }}">Feather Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-ionic') }}">Ionic Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-material') }}">Material Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-pe7') }}">Pe7 Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-simpleline') }}">Simpleline Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-themify') }}">Themify Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-weather') }}">Weather Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-typicon') }}">Typicon Icons</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.icon-flag') }}">Flag Icons</a>
												</li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-table-plus"></i>
												<span>Maps</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.maps-vector') }}">Vector</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.maps-leaflet') }}">Leaflet</a>
												</li>
											</ul>
										</li>
									</ul>
								</li>
								<li><a href="#">Documentation</a></li>
								<li><a href="#">Change Log</a></li>
								<li class="submenu">
									<a href="javascript:void(0);"><span>Multi Level</span><span
											class="menu-arrow"></span></a>
									<ul>
										<li><a href="javascript:void(0);">Multilevel 1</a></li>
										<li class="submenu submenu-two">
											<a href="javascript:void(0);">Multilevel 2<span
													class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="javascript:void(0);">Multilevel 2.1</a></li>
												<li class="submenu submenu-two submenu-three">
													<a href="javascript:void(0);">Multilevel 2.2<span
															class="menu-arrow inside-submenu inside-submenu-two"></span></a>
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
					<div class="d-xl-flex align-items-center d-none">
						<a href="#" class="me-3 avatar avatar-sm">
							<img src="/assets3/img/profiles/avatar-07.jpg" alt="profile" class="rounded-circle">
						</a>
						<a href="#" class="btn btn-icon btn-sm rounded-circle mode-toggle">
							<i class="ti ti-sun"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<!-- /Horizontal Menu -->

		<!-- Two Col Sidebar -->
		<div class="two-col-sidebar" id="two-col-sidebar">
			<div class="sidebar sidebar-twocol">
				<div class="twocol-mini">
					<a href="{{ route('admin_v2.index') }}" class="logo-small">
						<img src="/assets3/img/logo-small.svg" alt="Logo">
					</a>
					<div class="sidebar-left slimscroll">
						<div class="nav flex-column align-items-center nav-pills" id="sidebar-tabs" role="tablist"
							aria-orientation="vertical">
							<a href="#" class="nav-link " title="Dashboard" data-bs-toggle="tab"
								data-bs-target="#dashboard">
								<i class="ti ti-smart-home"></i>
							</a>
							<a href="#" class="nav-link " title="Apps" data-bs-toggle="tab"
								data-bs-target="#application">
								<i class="ti ti-layout-grid-add"></i>
							</a>
							<a href="#" class="nav-link " title="Super Admin" data-bs-toggle="tab"
								data-bs-target="#super-admin">
								<i class="ti ti-user-star"></i>
							</a>
							<a href="#" class="nav-link " title="Layout" data-bs-toggle="tab"
								data-bs-target="#layout">
								<i class="ti ti-layout-board-split"></i>
							</a>
							<a href="#" class="nav-link " title="Projects" data-bs-toggle="tab"
								data-bs-target="#projects">
								<i class="ti ti-users-group"></i>
							</a>
							<a href="#" class="nav-link " title="Crm" data-bs-toggle="tab" data-bs-target="#crm">
								<i class="ti ti-user-shield"></i>
							</a>
							<a href="#" class="nav-link active" title="Hrm" data-bs-toggle="tab" data-bs-target="#hrm">
								<i class="ti ti-user"></i>
							</a>
							<a href="#" class="nav-link" title="Finance" data-bs-toggle="tab" data-bs-target="#finance">
								<i class="ti ti-shopping-cart-dollar"></i>
							</a>
							<a href="#" class="nav-link" title="Administration" data-bs-toggle="tab"
								data-bs-target="#administration">
								<i class="ti ti-cash"></i>
							</a>
							<a href="#" class="nav-link" title="Content" data-bs-toggle="tab" data-bs-target="#content">
								<i class="ti ti-license"></i>
							</a>
							<a href="#" class="nav-link" title="Pages" data-bs-toggle="tab" data-bs-target="#pages">
								<i class="ti ti-page-break"></i>
							</a>
							<a href="#" class="nav-link" title="Authentication" data-bs-toggle="tab"
								data-bs-target="#authentication">
								<i class="ti ti-lock-check"></i>
							</a>
							<a href="#" class="nav-link " title="UI Elements" data-bs-toggle="tab"
								data-bs-target="#ui-elements">
								<i class="ti ti-ux-circle"></i>
							</a>
							<a href="#" class="nav-link" title="Extras" data-bs-toggle="tab" data-bs-target="#extras">
								<i class="ti ti-vector-triangle"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="sidebar-right">
					<div class="sidebar-logo mb-4">
						<a href="{{ route('admin_v2.index') }}" class="logo logo-normal">
							<img src="/assets3/img/logo.svg" alt="Logo">
						</a>
						<a href="{{ route('admin_v2.index') }}" class="dark-logo">
							<img src="/assets3/img/logo-white.svg" alt="Logo">
						</a>
					</div>
					<div class="sidebar-scroll">
						<h6 class="mb-3">Welcome to SmartHR</h6>
						<div class="text-center rounded bg-light p-3 mb-4">
							<div class="avatar avatar-lg online mb-3">
								<img src="/assets3/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
							</div>
							<h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
							<p class="fs-10">System Admin</p>
						</div>
						<div class="tab-content" id="v-pills-tabContent">
							<div class="tab-pane fade" id="dashboard">
								<ul>
									<li class="menu-title"><span>MAIN MENU</span></li>
									<li><a href="{{ route('admin_v2.index') }}">Admin Dashboard</a></li>
									<li><a href="{{ route('admin_v2.employee-dashboard') }}">Employee Dashboard</a></li>
									<li><a href="{{ route('admin_v2.deals-dashboard') }}">Deals Dashboard</a></li>
									<li><a href="{{ route('admin_v2.leads-dashboard') }}">Leads Dashboard</a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="application">
								<ul>
									<li class="menu-title"><span>APPLICATION</span></li>
									<li><a href="{{ route('admin_v2.chat') }}">Chat</a></li>
									<li><a href="{{ route('admin_v2.voice-call') }}">Voice Call</a></li>
									<li><a href="{{ route('admin_v2.video-call') }}">Video Call</a></li>
									<li><a href="{{ route('admin_v2.outgoing-call') }}">Outgoing Call</a></li>
									<li><a href="{{ route('admin_v2.incoming-call') }}">Incoming Call</a></li>
									<li><a href="{{ route('admin_v2.call-history') }}">Call History</a></li>
									<li><a href="{{ route('admin_v2.calendar') }}">Calendar</a></li>
									<li><a href="{{ route('admin_v2.email') }}">Email</a></li>
									<li><a href="{{ route('admin_v2.todo') }}">To Do</a></li>
									<li><a href="{{ route('admin_v2.notes') }}">Notes</a></li>
									<li><a href="{{ route('admin_v2.social-feed') }}">Social Feed</a></li>
									<li><a href="{{ route('admin_v2.file-manager') }}">File Manager</a></li>
									<li><a href="{{ route('admin_v2.kanban-view') }}">Kanban</a></li>
									<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="super-admin">
								<ul>
									<li class="menu-title"><span>SUPER ADMIN</span></li>
									<li><a href="{{ route('admin_v2.dashboard') }}">Dashboard</a></li>
									<li><a href="{{ route('admin_v2.companies') }}">Companies</a></li>
									<li><a href="{{ route('admin_v2.subscription') }}">Subscriptions</a></li>
									<li><a href="{{ route('admin_v2.packages') }}">Packages</a></li>
									<li><a href="{{ route('admin_v2.domain') }}">Domain</a></li>
									<li><a href="{{ route('admin_v2.purchase-transaction') }}">Purchase Transaction</a></li>
								</ul>
							</div>
							<div class="tab-pane fade " id="layout">
								<ul>
									<li class="menu-title"><span>LAYOUT</span></li>
									<li><a href="{{ route('admin_v2.layout-horizontal') }}"><span>Horizontal</span></a></li>
									<li><a href="{{ route('admin_v2.layout-detached') }}"><span>Detached</span></a></li>
									<li><a href="{{ route('admin_v2.layout-modern') }}"><span>Modern</span></a></li>
									<li><a href="{{ route('admin_v2.layout-two-column') }}"><span>Two Column </span></a></li>
									<li><a href="{{ route('admin_v2.layout-hovered') }}"><span>Hovered</span></a></li>
									<li><a href="{{ route('admin_v2.layout-box') }}"><span>Boxed</span></a></li>
									<li><a href="{{ route('admin_v2.layout-horizontal-single') }}"><span>Horizontal Single</span></a></li>
									<li><a href="{{ route('admin_v2.layout-horizontal-overlay') }}"><span>Horizontal Overlay</span></a>
									</li>
									<li><a href="{{ route('admin_v2.layout-horizontal-box') }}"><span>Horizontal Box</span></a></li>
									<li><a href="{{ route('admin_v2.layout-horizontal-sidemenu') }}"><span>Menu Aside</span></a></li>
									<li><a href="{{ route('admin_v2.layout-vertical-transparent') }}"><span>Transparent</span></a></li>
									<li><a href="{{ route('admin_v2.layout-without-header') }}"><span>Without Header</span></a></li>
									<li><a href="{{ route('admin_v2.layout-rtl') }}"><span>RTL</span></a></li>
									<li><a href="{{ route('admin_v2.layout-dark') }}"><span>Dark</span></a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="projects">
								<ul>
									<li class="menu-title"><span>PROJECTS</span></li>
									<li><a href="{{ route('admin_v2.clients-grid') }}" >Clients</a></li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Projects</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.projects-grid') }}">Projects</a></li>
											<li><a href="{{ route('admin_v2.tasks') }}">Tasks</a></li>
											<li><a href="{{ route('admin_v2.task-board') }}">Task Board</a></li>
										</ul>
									</li>
								</ul>
							</div>
							<div class="tab-pane fade" id="crm">
								<ul>
									<li class="menu-title"><span>CRM</span></li>
									<li><a href="{{ route('admin_v2.contacts-grid') }}"><span>Contacts</span></a></li>
									<li><a href="{{ route('admin_v2.companies-grid') }}"><span>Companies</span></a></li>
									<li><a href="{{ route('admin_v2.deals-grid') }}"><span>Deals</span></a></li>
									<li><a href="{{ route('admin_v2.leads-grid') }}"><span>Leads</span></a></li>
									<li><a href="{{ route('admin_v2.pipeline') }}"><span>Pipeline</span></a></li>
									<li><a href="{{ route('admin_v2.analytics') }}"><span>Analytics</span></a></li>
									<li><a href="{{ route('admin_v2.activity') }}"><span>Activities</span></a></li>
								</ul>
							</div>
							<div class="tab-pane fade show active" id="hrm">
								<ul>
									<li class="menu-title"><span>HRM</span></li>
									<li class="submenu">
										<a href="javascript:void(0);"  class="active subdrop"><span>Employees</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.employees') }}"  class="active">Employee Lists</a></li>
											<li><a href="{{ route('admin_v2.employees-grid') }}">Employee Grid</a></li>
											<li><a href="{{ route('admin_v2.employee-details') }}">Employee Details</a></li>
											<li><a href="{{ route('admin_v2.departments') }}">Departments</a></li>
											<li><a href="{{ route('admin_v2.ministry') }}">Ministry</a></li>
											<li><a href="{{ route('admin_v2.policy') }}">Policies</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Tickets</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.tickets') }}">Tickets</a></li>
											<li><a href="{{ route('admin_v2.ticket-details') }}">Ticket Details</a></li>
										</ul>
									</li>
									<li><a href="{{ route('admin_v2.holidays') }}"><span>Holidays</span></a></li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Attendance</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li class="submenu submenu-two">
												<a href="javascript:void(0);">Leaves<span
														class="menu-arrow inside-submenu"></span></a>
												<ul>
													<li><a href="{{ route('admin_v2.leaves') }}">Leaves (Admin)</a></li>
													<li><a href="{{ route('admin_v2.leaves-employee') }}">Leave (Employee)</a></li>
													<li><a href="{{ route('admin_v2.leave-settings') }}">Leave Settings</a></li>
												</ul>
											</li>
											<li><a href="{{ route('admin_v2.attendance-admin') }}">Attendance (Admin)</a></li>
											<li><a href="{{ route('admin_v2.attendance-employee') }}">Attendance (Employee)</a></li>
											<li><a href="{{ route('admin_v2.timesheets') }}">Timesheets</a></li>
											<li><a href="{{ route('admin_v2.schedule.index') }}">Shift & Schedule</a></li>
											<li><a href="{{ route('admin_v2.overtime') }}">Overtime</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Performance</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.performance-indicator') }}">Performance Indicator</a></li>
											<li><a href="{{ route('admin_v2.performance-review') }}">Performance Review</a></li>
											<li><a href="{{ route('admin_v2.performance-appraisal') }}">Performance Appraisal</a></li>
											<li><a href="{{ route('admin_v2.goal-tracking') }}">Goal List</a></li>
											<li><a href="{{ route('admin_v2.goal-type') }}">Goal Type</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Training</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.training') }}">Training List</a></li>
											<li><a href="{{ route('admin_v2.trainers') }}">Trainers</a></li>
											<li><a href="{{ route('admin_v2.training-type') }}">Training Type</a></li>
										</ul>
									</li>
									<li><a href="{{ route('admin_v2.promotion') }}"><span>Promotion</span></a></li>
									<li><a href="{{ route('admin_v2.resignation') }}"><span>Resignation</span></a></li>
									<li><a href="{{ route('admin_v2.termination') }}"><span>Termination</span></a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="finance">
								<ul>
									<li class="menu-title"><span>FINANCE & ACCOUNTS</span></li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Sales</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.estimates') }}">Estimates</a></li>
											<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
											<li><a href="{{ route('admin_v2.payments') }}">Payments</a></li>
											<li><a href="{{ route('admin_v2.expenses') }}">Expenses</a></li>
											<li><a href="{{ route('admin_v2.provident-fund') }}">Provident Fund</a></li>
											<li><a href="{{ route('admin_v2.taxes') }}">Taxes</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Accounting</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.categories') }}">Categories</a></li>
											<li><a href="{{ route('admin_v2.budgets') }}">Budgets</a></li>
											<li><a href="{{ route('admin_v2.budget-expenses') }}">Budget Expenses</a></li>
											<li><a href="{{ route('admin_v2.budget-revenues') }}">Budget Revenues</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Payroll</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.employee-salary') }}">Employee Salary</a></li>
											<li><a href="{{ route('admin_v2.payslip') }}">Payslip</a></li>
											<li><a href="{{ route('admin_v2.payroll') }}">Payroll Items</a></li>
										</ul>
									</li>
								</ul>
							</div>
							<div class="tab-pane fade" id="administration">
								<ul>
									<li class="menu-title"><span>ADMINISTRATION</span></li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Assets</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.assets') }}">Assets</a></li>
											<li><a href="{{ route('admin_v2.asset-categories') }}">Asset Categories</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Help & Supports</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.knowledgebase') }}">Knowledge Base</a></li>
											<li><a href="{{ route('admin_v2.activity') }}">Activities</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>User Management</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.users') }}">Users</a></li>
											<li><a href="{{ route('admin_v2.roles-permissions') }}">Roles & Permissions</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Reports</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.expenses-report') }}">Expense Report</a></li>
											<li><a href="{{ route('admin_v2.invoice-report') }}">Invoice Report</a></li>
											<li><a href="{{ route('admin_v2.payment-report') }}">Payment Report</a></li>
											<li><a href="{{ route('admin_v2.project-report') }}">Project Report</a></li>
											<li><a href="{{ route('admin_v2.task-report') }}">Task Report</a></li>
											<li><a href="{{ route('admin_v2.user-report') }}">User Report</a></li>
											<li><a href="{{ route('admin_v2.employee-report') }}">Employee Report</a></li>
											<li><a href="{{ route('admin_v2.payslip-report') }}">Payslip Report</a></li>
											<li><a href="{{ route('admin_v2.attendance-report') }}">Attendance Report</a></li>
											<li><a href="{{ route('admin_v2.leave-report') }}">Leave Report</a></li>
											<li><a href="{{ route('admin_v2.daily-report') }}">Daily Report</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											General Settings
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.profile-settings') }}">Profile</a></li>
											<li><a href="{{ route('admin_v2.security-settings') }}">Security</a></li>
											<li><a href="{{ route('admin_v2.notification-settings') }}">Notifications</a></li>
											<li><a href="{{ route('admin_v2.connected-apps') }}">Connected Apps</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Website Settings
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.bussiness-settings') }}">Business Settings</a></li>
											<li><a href="{{ route('admin_v2.seo-settings') }}">SEO Settings</a></li>
											<li><a href="{{ route('admin_v2.localization-settings') }}">Localization</a></li>
											<li><a href="{{ route('admin_v2.prefixes') }}">Prefixes</a></li>
											<li><a href="{{ route('admin_v2.preferences') }}">Preferences</a></li>
											<li><a href="{{ route('admin_v2.performance-appraisal') }}">Appearance</a></li>
											<li><a href="{{ route('admin_v2.language') }}">Language</a></li>
											<li><a href="{{ route('admin_v2.authentication-settings') }}">Authentication</a></li>
											<li><a href="{{ route('admin_v2.ai-settings') }}">AI Settings</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">App Settings<span class="menu-arrow"></span></a>
										<ul>
											<li><a href="{{ route('admin_v2.salary-settings') }}">Salary Settings</a></li>
											<li><a href="{{ route('admin_v2.approval-settings') }}">Approval Settings</a></li>
											<li><a href="{{ route('admin_v2.invoice-settings') }}">Invoice Settings</a></li>
											<li><a href="{{ route('admin_v2.leave-type') }}">Leave Type</a></li>
											<li><a href="{{ route('admin_v2.custom-fields') }}">Custom Fields</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											System Settings
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.email-settings') }}">Email Settings</a></li>
											<li><a href="{{ route('admin_v2.email-template') }}">Email Templates</a></li>
											<li><a href="{{ route('admin_v2.sms-settings') }}">SMS Settings</a></li>
											<li><a href="{{ route('admin_v2.sms-template') }}">SMS Templates</a></li>
											<li><a href="{{ route('admin_v2.otp-settings') }}">OTP</a></li>
											<li><a href="{{ route('admin_v2.gdpr') }}">GDPR Cookies</a></li>
											<li><a href="{{ route('admin_v2.maintenance-mode') }}">Maintenance Mode</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Financial Settings
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.payment-gateways') }}">Payment Gateways</a></li>
											<li><a href="{{ route('admin_v2.tax-rates') }}">Tax Rate</a></li>
											<li><a href="{{ route('admin_v2.currencies') }}">Currencies</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">Other Settings<span class="menu-arrow"></span></a>
										<ul>
											<li><a href="{{ route('admin_v2.custom-css') }}">Custom CSS</a></li>
											<li><a href="{{ route('admin_v2.custom-js') }}">Custom JS</a></li>
											<li><a href="{{ route('admin_v2.cronjob') }}">Cronjob</a></li>
											<li><a href="{{ route('admin_v2.storage-settings') }}">Storage</a></li>
											<li><a href="{{ route('admin_v2.ban-ip-address') }}">Ban IP Address</a></li>
											<li><a href="{{ route('admin_v2.backup') }}">Backup</a></li>
											<li><a href="{{ route('admin_v2.clear-cache') }}">Clear Cache</a></li>
										</ul>
									</li>
								</ul>
							</div>
							<div class="tab-pane fade" id="content">
								<ul>
									<li class="menu-title"><span>CONTENT</span></li>
									<li><a href="{{ route('admin_v2.pages') }}">Pages</a></li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Blogs
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.blogs') }}">All Blogs</a></li>
											<li><a href="{{ route('admin_v2.blog-categories') }}">Categories</a></li>
											<li><a href="{{ route('admin_v2.blog-comments') }}">Comments</a></li>
											<li><a href="{{ route('admin_v2.blog-tags') }}">Blog Tags</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Locations
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.countries') }}">Countries</a></li>
											<li><a href="{{ route('admin_v2.states') }}">States</a></li>
											<li><a href="{{ route('admin_v2.cities') }}">Cities</a></li>
										</ul>
									</li>
									<li><a href="{{ route('admin_v2.testimonials') }}">Testimonials</a></li>
									<li><a href="{{ route('admin_v2.faq') }}">FAQ’S</a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="pages">
								<ul>
									<li class="menu-title"><span>PAGES</span></li>
									<li><a href="{{ route('admin_v2.starter') }}"><span>Starter</span></a></li>
									<li><a href="{{ route('admin_v2.profile') }}"><span>Profile</span></a></li>
									<li><a href="{{ route('admin_v2.gallery') }}"><span>Gallery</span></a></li>
									<li><a href="{{ route('admin_v2.search-result') }}"><span>Search Results</span></a></li>
									<li><a href="{{ route('admin_v2.timeline') }}"><span>Timeline</span></a></li>
									<li><a href="{{ route('admin_v2.pricing') }}"><span>Pricing</span></a></li>
									<li><a href="{{ route('admin_v2.coming-soon') }}"><span>Coming Soon</span></a></li>
									<li><a href="{{ route('admin_v2.under-maintenance') }}"><span>Under Maintenance</span></a></li>
									<li><a href="{{ route('admin_v2.under-construction') }}"><span>Under Construction</span></a></li>
									<li><a href="{{ route('admin_v2.api-keys') }}"><span>API Keys</span></a></li>
									<li><a href="{{ route('admin_v2.privacy-policy') }}"><span>Privacy Policy</span></a></li>
									<li><a href="{{ route('admin_v2.terms-condition') }}"><span>Terms & Conditions</span></a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="authentication">
								<ul>
									<li class="menu-title"><span>AUTHENTICATION</span></li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Login<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.login') }}">Cover</a></li>
											<li><a href="{{ route('admin_v2.login-2') }}">Illustration</a></li>
											<li><a href="{{ route('admin_v2.login-3') }}">Basic</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Register<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.register') }}">Cover</a></li>
											<li><a href="{{ route('admin_v2.register-2') }}">Illustration</a></li>
											<li><a href="{{ route('admin_v2.register-3') }}">Basic</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Forgot Password<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.forgot-password') }}">Cover</a></li>
											<li><a href="{{ route('admin_v2.forgot-password-2') }}">Illustration</a></li>
											<li><a href="{{ route('admin_v2.forgot-password-3') }}">Basic</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Reset Password<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.reset-password') }}">Cover</a></li>
											<li><a href="{{ route('admin_v2.reset-password-2') }}">Illustration</a></li>
											<li><a href="{{ route('admin_v2.reset-password-3') }}">Basic</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											Email Verification<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.email-verification') }}">Cover</a></li>
											<li><a href="{{ route('admin_v2.email-verification-2') }}">Illustration</a></li>
											<li><a href="{{ route('admin_v2.email-verification-3') }}">Basic</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											2 Step Verification<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.two-step-verification') }}">Cover</a></li>
											<li><a href="{{ route('admin_v2.two-step-verification-2') }}">Illustration</a></li>
											<li><a href="{{ route('admin_v2.two-step-verification-3') }}">Basic</a></li>
										</ul>
									</li>
									<li><a href="{{ route('admin_v2.lock-screen') }}">Lock Screen</a></li>
									<li><a href="{{ route('admin_v2.error-404') }}">404 Error</a></li>
									<li><a href="{{ route('admin_v2.error-500') }}">500 Error</a></li>
								</ul>
							</div>
							<div class="tab-pane fade" id="ui-elements">
								<ul>
									<li class="menu-title"><span>UI INTERFACE</span></li>
									<li class="submenu">
										<a href="javascript:void(0);">Base UI<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="{{ route('admin_v2.ui-alerts') }}">Alerts</a></li>
											<li><a href="{{ route('admin_v2.ui-accordion') }}">Accordion</a></li>
											<li><a href="{{ route('admin_v2.ui-avatar') }}">Avatar</a></li>
											<li><a href="{{ route('admin_v2.ui-badges') }}">Badges</a></li>
											<li><a href="{{ route('admin_v2.ui-borders') }}">Border</a></li>
											<li><a href="{{ route('admin_v2.ui-buttons') }}">Buttons</a></li>
											<li><a href="{{ route('admin_v2.ui-buttons-group') }}">Button Group</a></li>
											<li><a href="{{ route('admin_v2.ui-breadcrumb') }}">Breadcrumb</a></li>
											<li><a href="{{ route('admin_v2.ui-cards') }}">Card</a></li>
											<li><a href="{{ route('admin_v2.ui-carousel') }}">Carousel</a></li>
											<li><a href="{{ route('admin_v2.ui-colors') }}">Colors</a></li>
											<li><a href="{{ route('admin_v2.ui-dropdowns') }}">Dropdowns</a></li>
											<li><a href="{{ route('admin_v2.ui-grid') }}">Grid</a></li>
											<li><a href="{{ route('admin_v2.ui-images') }}">Images</a></li>
											<li><a href="{{ route('admin_v2.ui-lightbox') }}">Lightbox</a></li>
											<li><a href="{{ route('admin_v2.ui-media') }}">Media</a></li>
											<li><a href="{{ route('admin_v2.ui-modals') }}">Modals</a></li>
											<li><a href="{{ route('admin_v2.ui-offcanvas') }}">Offcanvas</a></li>
											<li><a href="{{ route('admin_v2.ui-pagination') }}">Pagination</a></li>
											<li><a href="{{ route('admin_v2.ui-popovers') }}">Popovers</a></li>
											<li><a href="{{ route('admin_v2.ui-progress') }}">Progress</a></li>
											<li><a href="{{ route('admin_v2.ui-placeholders') }}">Placeholders</a></li>
											<li><a href="{{ route('admin_v2.ui-spinner') }}">Spinner</a></li>
											<li><a href="{{ route('admin_v2.ui-sweetalerts') }}">Sweet Alerts</a></li>
											<li><a href="{{ route('admin_v2.ui-nav-tabs') }}">Tabs</a></li>
											<li><a href="{{ route('admin_v2.ui-toasts') }}">Toasts</a></li>
											<li><a href="{{ route('admin_v2.ui-tooltips') }}">Tooltips</a></li>
											<li><a href="{{ route('admin_v2.ui-typography') }}">Typography</a></li>
											<li><a href="{{ route('admin_v2.ui-video') }}">Video</a></li>
											<li><a href="{{ route('admin_v2.ui-sortable') }}">Sortable</a></li>
											<li><a href="{{ route('admin_v2.ui-swiperjs') }}">Swiperjs</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"> Advanced UI <span class="menu-arrow"></span> </a>
										<ul>
											<li><a href="{{ route('admin_v2.ui-ribbon') }}">Ribbon</a></li>
											<li><a href="{{ route('admin_v2.ui-clipboard') }}">Clipboard</a></li>
											<li><a href="{{ route('admin_v2.ui-drag-drop') }}">Drag & Drop</a></li>
											<li><a href="{{ route('admin_v2.ui-rangeslider') }}">Range Slider</a></li>
											<li><a href="{{ route('admin_v2.ui-rating') }}">Rating</a></li>
											<li><a href="{{ route('admin_v2.ui-text-editor') }}">Text Editor</a></li>
											<li><a href="{{ route('admin_v2.ui-counter') }}">Counter</a></li>
											<li><a href="{{ route('admin_v2.ui-scrollbar') }}">Scrollbar</a></li>
											<li><a href="{{ route('admin_v2.ui-stickynote') }}">Sticky Note</a></li>
											<li><a href="{{ route('admin_v2.ui-timeline') }}">Timeline</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);"> Forms <span class="menu-arrow"></span>
										</a>
										<ul>
											<li class="submenu submenu-two">
												<a href="javascript:void(0);">Form Elements<span
														class="menu-arrow inside-submenu"></span></a>
												<ul>
													<li><a href="{{ route('admin_v2.form-basic-inputs') }}">Basic Inputs</a></li>
													<li><a href="{{ route('admin_v2.form-checkbox-radios') }}">Checkbox & Radios</a></li>
													<li><a href="{{ route('admin_v2.form-input-groups') }}">Input Groups</a></li>
													<li><a href="{{ route('admin_v2.form-grid-gutters') }}">Grid & Gutters</a></li>
													<li><a href="{{ route('admin_v2.form-select') }}">Form Select</a></li>
													<li><a href="{{ route('admin_v2.form-mask') }}">Input Masks</a></li>
													<li><a href="{{ route('admin_v2.form-fileupload') }}">File Uploads</a></li>
												</ul>
											</li>
											<li class="submenu submenu-two">
												<a href="javascript:void(0);">Layouts<span
														class="menu-arrow inside-submenu"></span></a>
												<ul>
													<li><a href="{{ route('admin_v2.form-horizontal') }}">Horizontal Form</a></li>
													<li><a href="{{ route('admin_v2.form-vertical') }}">Vertical Form</a></li>
													<li><a href="{{ route('admin_v2.form-floating-labels') }}">Floating Labels</a></li>
												</ul>
											</li>
											<li><a href="{{ route('admin_v2.form-validation') }}">Form Validation</a></li>
											<li><a href="{{ route('admin_v2.form-select2') }}">Select2</a></li>
											<li><a href="{{ route('admin_v2.form-wizard') }}">Form Wizard</a></li>
											<li><a href="{{ route('admin_v2.form-pickers') }}">Form Picker</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">Tables <span class="menu-arrow"></span></a>
										<ul>
											<li><a href="{{ route('admin_v2.tables-basic') }}">Basic Tables </a></li>
											<li><a href="{{ route('admin_v2.data-tables') }}">Data Table </a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">Charts<span class="menu-arrow"></span> </a>
										<ul>
											<li><a href="{{ route('admin_v2.chart-apex') }}">Apex Charts</a></li>
											<li><a href="{{ route('admin_v2.chart-c3') }}">Chart C3</a></li>
											<li><a href="{{ route('admin_v2.chart-js') }}">Chart Js</a></li>
											<li><a href="{{ route('admin_v2.chart-morris') }}">Morris Charts</a></li>
											<li><a href="{{ route('admin_v2.chart-flot') }}">Flot Charts</a></li>
											<li><a href="{{ route('admin_v2.chart-peity') }}">Peity Charts</a></li>
										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">Icons<span class="menu-arrow"></span> </a>
										<ul>
											<li><a href="{{ route('admin_v2.icon-fontawesome') }}">Fontawesome Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-tabler') }}">Tabler Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-bootstrap') }}">Bootstrap Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-remix') }}">Remix Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-feather') }}">Feather Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-ionic') }}">Ionic Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-material') }}">Material Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-pe7') }}">Pe7 Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-simpleline') }}">Simpleline Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-themify') }}">Themify Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-weather') }}">Weather Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-typicon') }}">Typicon Icons</a></li>
											<li><a href="{{ route('admin_v2.icon-flag') }}">Flag Icons</a></li>

										</ul>
									</li>
									<li class="submenu">
										<a href="javascript:void(0);">
											<i class="ti ti-table-plus"></i>
											<span>Maps</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li>
												<a href="{{ route('admin_v2.maps-vector') }}">Vector</a>
											</li>
											<li>
												<a href="{{ route('admin_v2.maps-leaflet') }}">Leaflet</a>
											</li>
										</ul>
									</li>
								</ul>
							</div>
							<div class="tab-pane fade" id="extras">
								<ul>
									<li class="menu-title"><span>EXTRAS</span></li>
									<li><a href="#">Documentation</a></li>
									<li><a href="#">Change Log</a></li>
									<li class="submenu">
										<a href="javascript:void(0);"><span>Multi Level</span><span
												class="menu-arrow"></span></a>
										<ul>
											<li><a href="javascript:void(0);">Multilevel 1</a></li>
											<li class="submenu submenu-two">
												<a href="javascript:void(0);">Multilevel 2<span
														class="menu-arrow inside-submenu"></span></a>
												<ul>
													<li><a href="javascript:void(0);">Multilevel 2.1</a></li>
													<li class="submenu submenu-two submenu-three">
														<a href="javascript:void(0);">Multilevel 2.2<span
																class="menu-arrow inside-submenu inside-submenu-two"></span></a>
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
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Two Col Sidebar -->

		<!-- Stacked Sidebar -->
		<div class="stacked-sidebar" id="stacked-sidebar">
			<div class="sidebar sidebar-stacked" style="display: flex !important;">
				<div class="stacked-mini">
					<a href="{{ route('admin_v2.index') }}" class="logo-small">
						<img src="/assets3/img/logo-small.svg" alt="Logo">
					</a>
					<div class="sidebar-left slimscroll">
						<div class="d-flex align-items-center flex-column">
							<div class="mb-1 notification-item">
								<a href="#" class="btn btn-menubar position-relative">
									<i class="ti ti-bell"></i>
									<span class="notification-status-dot"></span>
								</a>
							</div>
							<div class="mb-1">
								<a href="#" class="btn btn-menubar btnFullscreen">
									<i class="ti ti-maximize"></i>
								</a>
							</div>
							<div class="mb-1">
								<a href="{{ route('admin_v2.calendar') }}" class="btn btn-menubar">
									<i class="ti ti-layout-grid-remove"></i>
								</a>
							</div>
							<div class="mb-1">
								<a href="{{ route('admin_v2.chat') }}" class="btn btn-menubar position-relative">
									<i class="ti ti-brand-hipchat"></i>
									<span
										class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
								</a>
							</div>
							<div class="mb-1">
								<a href="{{ route('admin_v2.email') }}" class="btn btn-menubar">
									<i class="ti ti-mail"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="sidebar-right d-flex justify-content-between flex-column">
					<div class="sidebar-scroll">
						<h6 class="mb-3">Welcome to SmartHR</h6>
						<div class="sidebar-profile text-center rounded bg-light p-3 mb-4">
							<div class="avatar avatar-lg online mb-3">
								<img src="/assets3/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
							</div>
							<h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
							<p class="fs-10">System Admin</p>
						</div>
						<div class="stack-menu">
							<div class="nav flex-column align-items-center nav-pills" role="tablist"
								aria-orientation="vertical">
								<div class="row g-2">
									<div class="col-6">
										<a href="#menu-dashboard" role="tab" class="nav-link " title="Dashboard"
											data-bs-toggle="tab" data-bs-target="#menu-dashboard" aria-selected="true">
											<span><i class="ti ti-smart-home"></i></span>
											<p>Dashboard</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-application" role="tab" class="nav-link " title="Apps"
											data-bs-toggle="tab" data-bs-target="#menu-application"
											aria-selected="false">
											<span><i class="ti ti-layout-grid-add"></i></span>
											<p>Applications</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-superadmin" role="tab" class="nav-link " title="Apps"
											data-bs-toggle="tab" data-bs-target="#menu-superadmin"
											aria-selected="false">
											<span><i class="ti ti-user-star"></i></span>
											<p>Super Admin</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-layout" role="tab" class="nav-link " title="Layout"
											data-bs-toggle="tab" data-bs-target="#menu-layout" aria-selected="false">
											<span><i class="ti ti-layout-board-split"></i></span>
											<p>Layouts</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-project" role="tab" class="nav-link " title="Projects"
											data-bs-toggle="tab" data-bs-target="#menu-project" aria-selected="false">
											<span><i class="ti ti-folder"></i></span>
											<p>Projects</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-crm" role="tab" class="nav-link " title="CRM" data-bs-toggle="tab"
											data-bs-target="#menu-crm" aria-selected="false">
											<span><i class="ti ti-user-shield"></i></span>
											<p>Crm</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-hrm" role="tab" class="nav-link active" title="HRM" data-bs-toggle="tab"
											data-bs-target="#menu-hrm" aria-selected="false">
											<span><i class="ti ti-users"></i></span>
											<p>Hrm</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-finance" role="tab" class="nav-link" title="Finance & Accounts"
											data-bs-toggle="tab" data-bs-target="#menu-finance" aria-selected="false">
											<span><i class="ti ti-shopping-cart-dollar"></i></span>
											<p>Finance & Accounts</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-administration" role="tab" class="nav-link"
											title="Administration" data-bs-toggle="tab"
											data-bs-target="#menu-administration" aria-selected="false">
											<span><i class="ti ti-cash"></i></span>
											<p>Administration</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-content" role="tab" class="nav-link" title="Content"
											data-bs-toggle="tab" data-bs-target="#menu-content" aria-selected="false">
											<span><i class="ti ti-license"></i></span>
											<p>Contents</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-pages" role="tab" class="nav-link" title="Pages"
											data-bs-toggle="tab" data-bs-target="#menu-pages" aria-selected="false">
											<span><i class="ti ti-page-break"></i></span>
											<p>Pages</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-authentication" role="tab" class="nav-link"
											title="Authentication" data-bs-toggle="tab"
											data-bs-target="#menu-authentication" aria-selected="false">
											<span><i class="ti ti-lock-check"></i></span>
											<p>Authentication</p>
										</a>
									</div>
									<div class="col-6">
										<a href="#menu-ui-elements" role="tab" class="nav-link" title="UI Elements"
											data-bs-toggle="tab" data-bs-target="#menu-ui-elements"
											aria-selected="false">
											<span><i class="ti ti-ux-circle"></i></span>
											<p>Basic UI</p>
										</a>
									</div>
								</div>
							</div>
							<div class="tab-content">
								<div class="tab-pane fade" id="menu-dashboard">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.index') }}">Admin Dashboard</a></li>
										<li><a href="{{ route('admin_v2.employee-dashboard') }}">Employee Dashboard</a></li>
										<li><a href="{{ route('admin_v2.deals-dashboard') }}">Deals Dashboard</a></li>
										<li><a href="{{ route('admin_v2.leads-dashboard') }}">Leads Dashboard</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-superadmin">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.dashboard') }}">Dashboard</a></li>
										<li><a href="{{ route('admin_v2.companies') }}">Companies</a></li>
										<li><a href="{{ route('admin_v2.subscription') }}">Subscriptions</a></li>
										<li><a href="{{ route('admin_v2.packages') }}">Packages</a></li>
										<li><a href="{{ route('admin_v2.domain') }}">Domain</a></li>
										<li><a href="{{ route('admin_v2.purchase-transaction') }}">Purchase Transaction</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-application">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.chat') }}">Chat</a></li>
										<li class="submenu submenu-two">
											<a href="{{ route('admin_v2.call') }}">Calls<span class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.voice-call') }}">Voice Call</a></li>
												<li><a href="{{ route('admin_v2.video-call') }}">Video Call</a></li>
												<li><a href="{{ route('admin_v2.outgoing-call') }}">Outgoing Call</a></li>
												<li><a href="{{ route('admin_v2.incoming-call') }}">Incoming Call</a></li>
												<li><a href="{{ route('admin_v2.call-history') }}">Call History</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.calendar') }}">Calendar</a></li>
										<li><a href="{{ route('admin_v2.email') }}">Email</a></li>
										<li><a href="{{ route('admin_v2.todo') }}">To Do</a></li>
										<li><a href="{{ route('admin_v2.notes') }}">Notes</a></li>
										<li><a href="{{ route('admin_v2.social-feed') }}">Social Feed</a></li>
										<li><a href="{{ route('admin_v2.file-manager') }}">File Manager</a></li>
										<li><a href="{{ route('admin_v2.kanban-view') }}">Kanban</a></li>
										<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-layout">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.layout-horizontal') }}">Horizontal</a></li>
										<li><a href="{{ route('admin_v2.layout-detached') }}">Detached</a></li>
										<li><a href="{{ route('admin_v2.layout-modern') }}">Modern</a></li>
										<li><a href="{{ route('admin_v2.layout-two-column') }}">Two Column</a></li>
										<li><a href="{{ route('admin_v2.layout-hovered') }}">Hovered</a></li>
										<li><a href="{{ route('admin_v2.layout-box') }}">Boxed</a></li>
										<li><a href="{{ route('admin_v2.layout-horizontal-single') }}">Horizontal Single</a></li>
										<li><a href="{{ route('admin_v2.layout-horizontal-overlay') }}">Horizontal Overlay</a></li>
										<li><a href="{{ route('admin_v2.layout-horizontal-box') }}">Horizontal Box</a></li>
										<li><a href="{{ route('admin_v2.layout-horizontal-sidemenu') }}">Menu Aside</a></li>
										<li><a href="{{ route('admin_v2.layout-vertical-transparent') }}">Transparent</a></li>
										<li><a href="{{ route('admin_v2.layout-without-header') }}">Without Header</a></li>
										<li><a href="{{ route('admin_v2.layout-rtl') }}">RTL</a></li>
										<li><a href="{{ route('admin_v2.layout-dark') }}">Dark</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-project">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.clients-grid') }}"><span>Clients</span></a></li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Projects</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.projects-grid') }}">Projects</a></li>
												<li><a href="{{ route('admin_v2.tasks') }}">Tasks</a></li>
												<li><a href="{{ route('admin_v2.task-board') }}">Task Board</a></li>
											</ul>
										</li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-crm">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.contacts-grid') }}"><span>Contacts</span></a></li>
										<li><a href="{{ route('admin_v2.companies-grid') }}"><span>Companies</span></a></li>
										<li><a href="{{ route('admin_v2.deals-grid') }}"><span>Deals</span></a></li>
										<li><a href="{{ route('admin_v2.leads-grid') }}"><span>Leads</span></a></li>
										<li><a href="{{ route('admin_v2.pipeline') }}"><span>Pipeline</span></a></li>
										<li><a href="{{ route('admin_v2.analytics') }}"><span>Analytics</span></a></li>
										<li><a href="{{ route('admin_v2.activity') }}"><span>Activities</span></a></li>
									</ul>
								</div>
								<div class="tab-pane fade show active" id="menu-hrm">
									<ul class="stack-submenu">
										<li class="submenu">
											<a href="javascript:void(0);" class="active"><span>Employees</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.employees') }}" class="active">Employee Lists</a></li>
												<li><a href="{{ route('admin_v2.employees-grid') }}">Employee Grid</a></li>
												<li><a href="{{ route('admin_v2.employee-details') }}">Employee Details</a></li>
												<li><a href="{{ route('admin_v2.departments') }}">Departments</a></li>
												<li><a href="{{ route('admin_v2.ministry') }}">ministry</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Tickets</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.tickets') }}">Tickets</a></li>
												<li><a href="{{ route('admin_v2.ticket-details') }}">Ticket Details</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.holidays') }}"><span>Holidays</span></a></li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Attendance</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li class="submenu submenu-two">
													<a href="javascript:void(0);">Leaves<span
															class="menu-arrow inside-submenu"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.leaves') }}">Leaves (Admin)</a></li>
														<li><a href="{{ route('admin_v2.leaves-employee') }}">Leave (Employee)</a></li>
														<li><a href="{{ route('admin_v2.leave-settings') }}">Leave Settings</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.attendance-admin') }}">Attendance (Admin)</a></li>
												<li><a href="{{ route('admin_v2.attendance-employee') }}">Attendance (Employee)</a></li>
												<li><a href="{{ route('admin_v2.timesheets') }}">Timesheets</a></li>
												<li><a href="{{ route('admin_v2.schedule.index') }}">Shift & Schedule</a></li>
												<li><a href="{{ route('admin_v2.overtime') }}">Overtime</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Performance</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.performance-indicator') }}">Performance Indicator</a></li>
												<li><a href="{{ route('admin_v2.performance-review') }}">Performance Review</a></li>
												<li><a href="{{ route('admin_v2.performance-appraisal') }}">Performance Appraisal</a></li>
												<li><a href="{{ route('admin_v2.goal-tracking') }}">Goal List</a></li>
												<li><a href="{{ route('admin_v2.goal-type') }}">Goal Type</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Training</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.training') }}">Training List</a></li>
												<li><a href="{{ route('admin_v2.trainers') }}">Trainers</a></li>
												<li><a href="{{ route('admin_v2.training-type') }}">Training Type</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.promotion') }}"><span>Promotion</span></a></li>
										<li><a href="{{ route('admin_v2.resignation') }}"><span>Resignation</span></a></li>
										<li><a href="{{ route('admin_v2.termination') }}"><span>Termination</span></a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-finance">
									<ul class="stack-submenu">
										<li class="submenu">
											<a href="javascript:void(0);"><span>Sales</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.estimates') }}">Estimates</a></li>
												<li><a href="{{ route('admin_v2.invoices') }}">Invoices</a></li>
												<li><a href="{{ route('admin_v2.payments') }}">Payments</a></li>
												<li><a href="{{ route('admin_v2.expenses') }}">Expenses</a></li>
												<li><a href="{{ route('admin_v2.provident-fund') }}">Provident Fund</a></li>
												<li><a href="{{ route('admin_v2.taxes') }}">Taxes</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Accounting</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.categories') }}">Categories</a></li>
												<li><a href="{{ route('admin_v2.budgets') }}">Budgets</a></li>
												<li><a href="{{ route('admin_v2.budget-expenses') }}">Budget Expenses</a></li>
												<li><a href="{{ route('admin_v2.budget-revenues') }}">Budget Revenues</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Payroll</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.employee-salary') }}">Employee Salary</a></li>
												<li><a href="{{ route('admin_v2.payslip') }}">Payslip</a></li>
												<li><a href="{{ route('admin_v2.payroll') }}">Payroll Items</a></li>
											</ul>
										</li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-administration">
									<ul class="stack-submenu">
										<li class="submenu">
											<a href="javascript:void(0);"><span>Assets</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.assets') }}">Assets</a></li>
												<li><a href="{{ route('admin_v2.asset-categories') }}">Asset Categories</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Help & Supports</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.knowledgebase') }}">Knowledge Base</a></li>
												<li><a href="{{ route('admin_v2.activity') }}">Activities</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>User Management</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.users') }}">Users</a></li>
												<li><a href="{{ route('admin_v2.roles-permissions') }}">Roles & Permissions</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"><span>Reports</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.expenses-report') }}">Expense Report</a></li>
												<li><a href="{{ route('admin_v2.invoice-report') }}">Invoice Report</a></li>
												<li><a href="{{ route('admin_v2.payment-report') }}">Payment Report</a></li>
												<li><a href="{{ route('admin_v2.project-report') }}">Project Report</a></li>
												<li><a href="{{ route('admin_v2.task-report') }}">Task Report</a></li>
												<li><a href="{{ route('admin_v2.user-report') }}">User Report</a></li>
												<li><a href="{{ route('admin_v2.employee-report') }}">Employee Report</a></li>
												<li><a href="{{ route('admin_v2.payslip-report') }}">Payslip Report</a></li>
												<li><a href="{{ route('admin_v2.attendance-report') }}">Attendance Report</a></li>
												<li><a href="{{ route('admin_v2.leave-report') }}">Leave Report</a></li>
												<li><a href="{{ route('admin_v2.daily-report') }}">Daily Report</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												General Settings
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.profile-settings') }}">Profile</a></li>
												<li><a href="{{ route('admin_v2.security-settings') }}">Security</a></li>
												<li><a href="{{ route('admin_v2.notification-settings') }}">Notifications</a></li>
												<li><a href="{{ route('admin_v2.connected-apps') }}">Connected Apps</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												Website Settings
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.bussiness-settings') }}">Business Settings</a></li>
												<li><a href="{{ route('admin_v2.seo-settings') }}">SEO Settings</a></li>
												<li><a href="{{ route('admin_v2.localization-settings') }}">Localization</a></li>
												<li><a href="{{ route('admin_v2.prefixes') }}">Prefixes</a></li>
												<li><a href="{{ route('admin_v2.preferences') }}">Preferences</a></li>
												<li><a href="{{ route('admin_v2.performance-appraisal') }}">Appearance</a></li>
												<li><a href="{{ route('admin_v2.language') }}">Language</a></li>
												<li><a href="{{ route('admin_v2.authentication-settings') }}">Authentication</a></li>
												<li><a href="{{ route('admin_v2.ai-settings') }}">AI Settings</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">App Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.salary-settings') }}">Salary Settings</a></li>
												<li><a href="{{ route('admin_v2.approval-settings') }}">Approval Settings</a></li>
												<li><a href="{{ route('admin_v2.invoice-settings') }}">Invoice Settings</a></li>
												<li><a href="{{ route('admin_v2.leave-type') }}">Leave Type</a></li>
												<li><a href="{{ route('admin_v2.custom-fields') }}">Custom Fields</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												System Settings
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.email-settings') }}">Email Settings</a></li>
												<li><a href="{{ route('admin_v2.email-template') }}">Email Templates</a></li>
												<li><a href="{{ route('admin_v2.sms-settings') }}">SMS Settings</a></li>
												<li><a href="{{ route('admin_v2.sms-template') }}">SMS Templates</a></li>
												<li><a href="{{ route('admin_v2.otp-settings') }}">OTP</a></li>
												<li><a href="{{ route('admin_v2.gdpr') }}">GDPR Cookies</a></li>
												<li><a href="{{ route('admin_v2.maintenance-mode') }}">Maintenance Mode</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												Financial Settings
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li><a href="{{ route('admin_v2.payment-gateways') }}">Payment Gateways</a></li>
												<li><a href="{{ route('admin_v2.tax-rates') }}">Tax Rate</a></li>
												<li><a href="{{ route('admin_v2.currencies') }}">Currencies</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Other Settings<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.custom-css') }}">Custom CSS</a></li>
												<li><a href="{{ route('admin_v2.custom-js') }}">Custom JS</a></li>
												<li><a href="{{ route('admin_v2.cronjob') }}">Cronjob</a></li>
												<li><a href="{{ route('admin_v2.storage-settings') }}">Storage</a></li>
												<li><a href="{{ route('admin_v2.ban-ip-address') }}">Ban IP Address</a></li>
												<li><a href="{{ route('admin_v2.backup') }}">Backup</a></li>
												<li><a href="{{ route('admin_v2.clear-cache') }}">Clear Cache</a></li>
											</ul>
										</li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-content">
									<ul class="stack-submenu">
										<li class="submenu">
											<a href="javascript:void(0);">Blogs<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.blogs') }}">All Blogs</a></li>
												<li><a href="{{ route('admin_v2.blog-categories') }}">Categories</a></li>
												<li><a href="{{ route('admin_v2.blog-comments') }}">Comments</a></li>
												<li><a href="{{ route('admin_v2.blog-tags') }}">Tags</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Locations<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.countries') }}">Countries</a></li>
												<li><a href="{{ route('admin_v2.states') }}">States</a></li>
												<li><a href="{{ route('admin_v2.cities') }}">Cities</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.testimonials') }}">Testimonials</a></li>
										<li><a href="{{ route('admin_v2.faq') }}">FAQ’S</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-pages">
									<ul class="stack-submenu">
										<li><a href="{{ route('admin_v2.starter') }}">Starter</a></li>
										<li><a href="{{ route('admin_v2.profile') }}">Profile</a></li>
										<li><a href="{{ route('admin_v2.profile-settings') }}">Profile Settings</a></li>
										<li><a href="{{ route('admin_v2.gallery') }}">Gallery</a></li>
										<li><a href="{{ route('admin_v2.search-result') }}">Search Results</a></li>
										<li><a href="{{ route('admin_v2.timeline') }}">Timeline</a></li>
										<li><a href="{{ route('admin_v2.pricing') }}">Pricing</a></li>
										<li><a href="{{ route('admin_v2.coming-soon') }}">Coming Soon</a></li>
										<li><a href="{{ route('admin_v2.under-maintenance') }}">Under Maintenance</a></li>
										<li><a href="{{ route('admin_v2.under-construction') }}">Under Construction</a></li>
										<li><a href="{{ route('admin_v2.api-keys') }}">API Keys</a></li>
										<li><a href="{{ route('admin_v2.privacy-policy') }}">Privacy Policy</a></li>
										<li><a href="{{ route('admin_v2.terms-condition') }}">Terms & Conditions</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-authentication">
									<ul class="stack-submenu">
										<li class="submenu">
											<a href="javascript:void(0);" class="">Login<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.login') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.login-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.login-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);" class="">Register<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.register') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.register-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.register-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Reset Password<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.reset-password') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.reset-password-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.reset-password-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Email Verification<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.email-verification') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.email-verification-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.email-verification-3') }}">Basic</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">2 Step Verification<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.two-step-verification') }}">Cover</a></li>
												<li><a href="{{ route('admin_v2.two-step-verification-2') }}">Illustration</a></li>
												<li><a href="{{ route('admin_v2.two-step-verification-3') }}">Basic</a></li>
											</ul>
										</li>
										<li><a href="{{ route('admin_v2.lock-screen') }}">Lock Screen</a></li>
										<li><a href="{{ route('admin_v2.error-404') }}">404 Error</a></li>
										<li><a href="{{ route('admin_v2.error-500') }}">500 Error</a></li>
									</ul>
								</div>
								<div class="tab-pane fade" id="menu-ui-elements">
									<ul class="stack-submenu">
										<li class="submenu">
											<a href="javascript:void(0);">Base UI<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.ui-alerts') }}">Alerts</a></li>
												<li><a href="{{ route('admin_v2.ui-accordion') }}">Accordion</a></li>
												<li><a href="{{ route('admin_v2.ui-avatar') }}">Avatar</a></li>
												<li><a href="{{ route('admin_v2.ui-badges') }}">Badges</a></li>
												<li><a href="{{ route('admin_v2.ui-borders') }}">Border</a></li>
												<li><a href="{{ route('admin_v2.ui-buttons') }}">Buttons</a></li>
												<li><a href="{{ route('admin_v2.ui-buttons-group') }}">Button Group</a></li>
												<li><a href="{{ route('admin_v2.ui-breadcrumb') }}">Breadcrumb</a></li>
												<li><a href="{{ route('admin_v2.ui-cards') }}">Card</a></li>
												<li><a href="{{ route('admin_v2.ui-carousel') }}">Carousel</a></li>
												<li><a href="{{ route('admin_v2.ui-colors') }}">Colors</a></li>
												<li><a href="{{ route('admin_v2.ui-dropdowns') }}">Dropdowns</a></li>
												<li><a href="{{ route('admin_v2.ui-grid') }}">Grid</a></li>
												<li><a href="{{ route('admin_v2.ui-images') }}">Images</a></li>
												<li><a href="{{ route('admin_v2.ui-lightbox') }}">Lightbox</a></li>
												<li><a href="{{ route('admin_v2.ui-media') }}">Media</a></li>
												<li><a href="{{ route('admin_v2.ui-modals') }}">Modals</a></li>
												<li><a href="{{ route('admin_v2.ui-offcanvas') }}">Offcanvas</a></li>
												<li><a href="{{ route('admin_v2.ui-pagination') }}">Pagination</a></li>
												<li><a href="{{ route('admin_v2.ui-popovers') }}">Popovers</a></li>
												<li><a href="{{ route('admin_v2.ui-progress') }}">Progress</a></li>
												<li><a href="{{ route('admin_v2.ui-placeholders') }}">Placeholders</a></li>
												<li><a href="{{ route('admin_v2.ui-spinner') }}">Spinner</a></li>
												<li><a href="{{ route('admin_v2.ui-sweetalerts') }}">Sweet Alerts</a></li>
												<li><a href="{{ route('admin_v2.ui-nav-tabs') }}">Tabs</a></li>
												<li><a href="{{ route('admin_v2.ui-toasts') }}">Toasts</a></li>
												<li><a href="{{ route('admin_v2.ui-tooltips') }}">Tooltips</a></li>
												<li><a href="{{ route('admin_v2.ui-typography') }}">Typography</a></li>
												<li><a href="{{ route('admin_v2.ui-video') }}">Video</a></li>
												<li><a href="{{ route('admin_v2.ui-sortable') }}">Sortable</a></li>
												<li><a href="{{ route('admin_v2.ui-swiperjs') }}">Swiperjs</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);"> Advanced UI<span
													class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.ui-ribbon') }}">Ribbon</a></li>
												<li><a href="{{ route('admin_v2.ui-clipboard') }}">Clipboard</a></li>
												<li><a href="{{ route('admin_v2.ui-drag-drop') }}">Drag & Drop</a></li>
												<li><a href="{{ route('admin_v2.ui-rangeslider') }}">Range Slider</a></li>
												<li><a href="{{ route('admin_v2.ui-rating') }}">Rating</a></li>
												<li><a href="{{ route('admin_v2.ui-text-editor') }}">Text Editor</a></li>
												<li><a href="{{ route('admin_v2.ui-counter') }}">Counter</a></li>
												<li><a href="{{ route('admin_v2.ui-scrollbar') }}">Scrollbar</a></li>
												<li><a href="{{ route('admin_v2.ui-stickynote') }}">Sticky Note</a></li>
												<li><a href="{{ route('admin_v2.ui-timeline') }}">Timeline</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Forms<span class="menu-arrow"></span> </a>
											<ul>
												<li class="submenu submenu-two">
													<a href="javascript:void(0);">Form Elements<span
															class="menu-arrow inside-submenu"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.form-basic-inputs') }}">Basic Inputs</a></li>
														<li><a href="{{ route('admin_v2.form-checkbox-radios') }}">Checkbox & Radios</a>
														</li>
														<li><a href="{{ route('admin_v2.form-input-groups') }}">Input Groups</a></li>
														<li><a href="{{ route('admin_v2.form-grid-gutters') }}">Grid & Gutters</a></li>
														<li><a href="{{ route('admin_v2.form-select') }}">Form Select</a></li>
														<li><a href="{{ route('admin_v2.form-mask') }}">Input Masks</a></li>
														<li><a href="{{ route('admin_v2.form-fileupload') }}">File Uploads</a></li>

													</ul>
												</li>
												<li class="submenu submenu-two">
													<a href="javascript:void(0);">Layouts<span
															class="menu-arrow inside-submenu"></span></a>
													<ul>
														<li><a href="{{ route('admin_v2.form-horizontal') }}">Horizontal Form</a></li>
														<li><a href="{{ route('admin_v2.form-vertical') }}">Vertical Form</a></li>
														<li><a href="{{ route('admin_v2.form-floating-labels') }}">Floating Labels</a></li>
													</ul>
												</li>
												<li><a href="{{ route('admin_v2.form-validation') }}">Form Validation</a></li>
												<li><a href="{{ route('admin_v2.form-select2') }}">Select2</a></li>
												<li><a href="{{ route('admin_v2.form-wizard') }}">Form Wizard</a></li>
												<li><a href="{{ route('admin_v2.form-pickers') }}">Form Picker</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Tables<span class="menu-arrow"></span></a>
											<ul>
												<li><a href="{{ route('admin_v2.tables-basic') }}">Basic Tables </a></li>
												<li><a href="{{ route('admin_v2.data-tables') }}">Data Table </a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Charts<span class="menu-arrow"></span> </a>
											<ul>
												<li><a href="{{ route('admin_v2.chart-apex') }}">Apex Charts</a></li>
												<li><a href="{{ route('admin_v2.chart-c3') }}">Chart C3</a></li>
												<li><a href="{{ route('admin_v2.chart-js') }}">Chart Js</a></li>
												<li><a href="{{ route('admin_v2.chart-morris') }}">Morris Charts</a></li>
												<li><a href="{{ route('admin_v2.chart-flot') }}">Flot Charts</a></li>
												<li><a href="{{ route('admin_v2.chart-peity') }}">Peity Charts</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">Icons<span class="menu-arrow"></span> </a>
											<ul>
												<li><a href="{{ route('admin_v2.icon-fontawesome') }}">Fontawesome Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-tabler') }}">Tabler Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-bootstrap') }}">Bootstrap Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-remix') }}">Remix Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-feather') }}">Feather Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-ionic') }}">Ionic Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-material') }}">Material Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-pe7') }}">Pe7 Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-simpleline') }}">Simpleline Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-themify') }}">Themify Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-weather') }}">Weather Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-typicon') }}">Typicon Icons</a></li>
												<li><a href="{{ route('admin_v2.icon-flag') }}">Flag Icons</a></li>
											</ul>
										</li>
										<li class="submenu">
											<a href="javascript:void(0);">
												<i class="ti ti-table-plus"></i>
												<span>Maps</span>
												<span class="menu-arrow"></span>
											</a>
											<ul>
												<li>
													<a href="{{ route('admin_v2.maps-vector') }}">Vector</a>
												</li>
												<li>
													<a href="{{ route('admin_v2.maps-leaflet') }}">Leaflet</a>
												</li>
											</ul>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="p-3">
						<a href="javascript:void(0);" class="d-flex align-items-center fs-12 mb-3">Documentation</a>
						<a href="javascript:void(0);" class="d-flex align-items-center fs-12">Change Log<span
								class="badge bg-pink badge-xs text-white fs-10 ms-2">v4.0.9</span></a>
					</div>
				</div>
			</div>
		</div>
		<!-- /Stacked Sidebar -->


		@if (session('success'))
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-success">
                    <i class="feather-check-circle" style="font-size: 48px;"></i>
                </div>
                <h5 class="fw-bold mb-2">Success</h5>
                <p class="mb-3">{{ session('success') }}</p>
                <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endif
<script>
document.addEventListener('DOMContentLoaded', () => {
    const successModal = document.getElementById('successModal');
    if (successModal) {
        const modal = new bootstrap.Modal(successModal);
        modal.show();
    }
});
</script>