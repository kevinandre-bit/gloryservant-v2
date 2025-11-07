@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $route = request()->route();
    $currentRoute = '';

    if ($route) {
        if (method_exists($route, 'getName')) {
            $currentRoute = (string) $route->getName();
        }

        if ($currentRoute === '' && method_exists($route, 'uri')) {
            $currentRoute = (string) $route->uri();
        }
    }

    $normalizePage = function ($value) {
        $value = trim($value ?? '');
        $value = Str::of($value)
            ->replace('.php', '')
            ->replace('\\', '/')
            ->trim('/');

        return (string) $value;
    };

    // Use admin_v2.* route names now
    $routeNameFor = function ($value) use ($normalizePage) {
        $value = $normalizePage($value);

        if ($value === '') {
            return '';
        }

        return 'admin_v2.' . str_replace('/', '.', $value);
    };

    $linkFor = function ($value) use ($routeNameFor) {
        $routeName = $routeNameFor($value);

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        if ($routeName === '') {
            return route('admin_v2.index');
        }

        return '#';
    };

    // Determine current page for "active" classes (legacy .php checks kept)
    if ($currentRoute && Str::startsWith($currentRoute, 'admin_v2.')) {
        $page = Str::after($currentRoute, 'admin_v2.') . '.php';
    } else {
        $page = basename(request()->path()) ?: 'index.php';
    }
@endphp

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('admin_v2.index') }}" class="logo logo-normal">
            <img src="{{ asset('assets3/img/logo.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('admin_v2.index') }}" class="logo-small">
            <img src="{{ asset('assets3/img/logo-small.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('admin_v2.index') }}" class="dark-logo">
            <img src="{{ asset('assets3/img/logo-white.svg') }}" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->

    <div class="modern-profile p-3 pb-0">
        <div class="text-center rounded bg-light p-3 mb-4 user-profile">
            <div class="avatar avatar-lg online mb-3">
                <img src="{{ asset('assets3/img/profiles/avatar-02.jpg') }}" alt="Img" class="img-fluid rounded-circle">
            </div>
            <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
            <p class="fs-10">System Admin</p>
        </div>
        <div class="sidebar-nav mb-3">
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
                <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="{{ $linkFor('chat') }}">Chats</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="{{ $linkFor('email') }}">Inbox</a></li>
            </ul>
        </div>
    </div>

    <div class="sidebar-header p-3 pb-0 pt-2">
        <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
            <div class="avatar avatar-md onlin">
                <img src="{{ asset('assets3/img/profiles/avatar-02.jpg') }}" alt="Img" class="img-fluid rounded-circle">
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
                <a href="{{ $linkFor('index') }}" class="btn btn-menubar">
                    <i class="ti ti-layout-grid-remove"></i>
                </a>
            </div>
            <div class="me-3">
                <a href="{{ $linkFor('chat') }}" class="btn btn-menubar position-relative">
                    <i class="ti ti-brand-hipchat"></i>
                    <span class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
                </a>
            </div>
            <div class="me-3 notification-item">
                <a href="{{ $linkFor('notifications') }}" class="btn btn-menubar position-relative me-1">
                    <i class="ti ti-bell"></i>
                    <span class="notification-status-dot"></span>
                </a>
            </div>
            <div class="me-0">
                <a href="{{ $linkFor('email') }}" class="btn btn-menubar">
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
                            <a href="javascript:void(0);" class=" {{ ($page == 'index.php'||$page == 'employee-dashboard.php'||$page == 'deals-dashboard.php'||$page == 'leads-dashboard.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-smart-home"></i>
                                <span>Dashboard</span>
                                <span class="badge badge-danger fs-10 fw-medium text-white p-1">Hot</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('index') }}" class="{{ ($page == 'index.php') ? 'active' : '' }}">Admin Dashboard</a></li>
                                <li><a href="{{ $linkFor('employee-dashboard') }}" class="{{ ($page == 'employee-dashboard.php') ? 'active' : '' }}">Employee Dashboard</a></li>
                                <li><a href="{{ $linkFor('deals-dashboard') }}" class="{{ ($page == 'deals-dashboard.php') ? 'active' : '' }}">Deals Dashboard</a></li>
                                <li><a href="{{ $linkFor('leads-dashboard') }}" class="{{ ($page == 'leads-dashboard.php') ? 'active' : '' }}">Leads Dashboard</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);"  class=" {{ ($page == 'chat.php'||$page == 'call.php'||$page == 'voice-call.php'||$page == 'video-call.php'||$page == 'outgoing-call.php'||$page == 'incoming-call.php'||$page == 'call-history.php'||$page == 'calendar.php'||$page == 'email.php'||$page == 'todo.php'||$page == 'notes.php'||$page == 'social-feed.php'||$page == 'file-manager.php'||$page == 'kanban-view.php'||$page == 'invoices.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-layout-grid-add"></i><span>Applications</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('chat') }}" class="{{ ($page == 'chat.php') ? 'active' : '' }}">Chat</a></li>
                                <li class="submenu submenu-two">
                                    <a href="{{ $linkFor('call') }}" class="{{ ($page == 'call.php'||$page == 'voice-call.php'||$page == 'video-call.php'||$page == 'outgoing-call.php'||$page == 'incoming-call.php'||$page == 'call-history.php') ? 'active subdrop' : '' }}">Calls<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('voice-call') }}" class="{{ ($page == 'voice-call.php') ? 'active' : '' }}">Voice Call</a></li>
                                        <li><a href="{{ $linkFor('video-call') }}" class="{{ ($page == 'video-call.php') ? 'active' : '' }}">Video Call</a></li>
                                        <li><a href="{{ $linkFor('outgoing-call') }}" class="{{ ($page == 'outgoing-call.php') ? 'active' : '' }}">Outgoing Call</a></li>
                                        <li><a href="{{ $linkFor('incoming-call') }}" class="{{ ($page == 'incoming-call.php') ? 'active' : '' }}">Incoming Call</a></li>
                                        <li><a href="{{ $linkFor('call-history') }}" class="{{ ($page == 'call-history.php') ? 'active' : '' }}">Call History</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ $linkFor('calendar') }}" class="{{ ($page == 'calendar.php') ? 'active' : '' }}">Calendar</a></li>
                                <li><a href="{{ $linkFor('email') }}" class="{{ ($page == 'email.php') ? 'active' : '' }}">Email</a></li>
                                <li><a href="{{ $linkFor('todo') }}" class="{{ ($page == 'todo.php') ? 'active' : '' }}">To Do</a></li>
                                <li><a href="{{ $linkFor('notes') }}" class="{{ ($page == 'notes.php') ? 'active' : '' }}">Notes</a></li>
                                <li><a href="{{ $linkFor('social-feed') }}" class="{{ ($page == 'social-feed.php') ? 'active' : '' }}">Social Feed</a></li>
                                <li><a href="{{ $linkFor('file-manager') }}" class="{{ ($page == 'file-manager.php') ? 'active' : '' }}">File Manager</a></li>
                                <li><a href="{{ $linkFor('kanban-view') }}" class="{{ ($page == 'kanban-view.php') ? 'active' : '' }}">Kanban</a></li>
                                <li><a href="{{ $linkFor('invoices') }}" class="{{ ($page == 'invoices.php') ? 'active' : '' }}">Invoices</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="#" class=" {{ ($page == 'dashboard.php'||$page == 'companies.php'||$page == 'subscription.php'||$page == 'packages.php'||$page == 'domain.php'||$page == 'purchase-transaction.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-user-star"></i><span>Super Admin</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('dashboard') }}" class="{{ ($page == 'dashboard.php') ? 'active' : '' }}">Dashboard</a></li>
                                <li><a href="{{ $linkFor('companies') }}" class="{{ ($page == 'companies.php') ? 'active' : '' }}">Companies</a></li>
                                <li><a href="{{ $linkFor('subscription') }}" class="{{ ($page == 'subscription.php') ? 'active' : '' }}">Subscriptions</a></li>
                                <li><a href="{{ $linkFor('packages') }}" class="{{ ($page == 'packages.php') ? 'active' : '' }}">Packages</a></li>
                                <li><a href="{{ $linkFor('domain') }}" class="{{ ($page == 'domain.php') ? 'active' : '' }}">Domain</a></li>
                                <li><a href="{{ $linkFor('purchase-transaction') }}" class="{{ ($page == 'purchase-transaction.php') ? 'active' : '' }}">Purchase Transaction</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>PROJECTS</span></li>
                <li>
                    <ul>
                        <li class="{{ ($page == 'clients-grid.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('clients-grid') }}">
                                <i class="ti ti-users-group"></i><span>Clients</span>
                            </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'projects-grid.php'||$page == 'tasks.php'||$page == 'task-board.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-box"></i><span>Projects</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('projects-grid') }}" class="{{ ($page == 'projects-grid.php') ? 'active' : '' }}">Projects</a></li>
                                <li><a href="{{ $linkFor('tasks') }}" class="{{ ($page == 'tasks.php') ? 'active' : '' }}">Tasks</a></li>
                                <li><a href="{{ $linkFor('task-board') }}" class="{{ ($page == 'task-board.php') ? 'active' : '' }}">Task Board</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>CRM</span></li>
                <li>
                    <ul>
                        <li class="{{ ($page == 'contacts-grid.php'||$page == 'contacts.php'||$page == 'contact-details.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('contacts-grid') }}">
                                <i class="ti ti-user-shield"></i><span>Contacts</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'companies-grid.php'||$page == 'companies-crm.php'||$page == 'company-details.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('companies-grid') }}">
                                <i class="ti ti-building"></i><span>Companies</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'deals-grid.php'||$page == 'deals-details.php'||$page == 'deals.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('deals-grid') }}">
                                <i class="ti ti-heart-handshake"></i><span>Deals</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'leads-grid.php'||$page == 'leads-details.php'||$page == 'leads.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('leads-grid') }}">
                                <i class="ti ti-user-check"></i><span>Leads</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'pipeline.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('pipeline') }}">
                                <i class="ti ti-timeline-event-text"></i><span>Pipeline</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'analytics.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('analytics') }}">
                                <i class="ti ti-graph"></i><span>Analytics</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'activity.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('activity') }}">
                                <i class="ti ti-activity"></i><span>Activities</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>CREATIVE TEAM</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ Str::startsWith($currentRoute, 'admin.creative') ? 'active subdrop' : '' }}">
                                <i class="ti ti-palette"></i><span>Creative Workload</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('admin.creative.index') }}" class="{{ $currentRoute === 'admin.creative.index' ? 'active' : '' }}">Dashboard</a></li>
                                <li><a href="{{ route('admin.creative.requests.index') }}" class="{{ Str::startsWith($currentRoute, 'admin.creative.requests') ? 'active' : '' }}">Requests</a></li>
                                <li><a href="{{ route('admin.creative.reports.index') }}" class="{{ Str::startsWith($currentRoute, 'admin.creative.reports') ? 'active' : '' }}">Reports</a></li>
                                <li><a href="{{ route('admin.creative.insights.index') }}" class="{{ Str::startsWith($currentRoute, 'admin.creative.insights') ? 'active' : '' }}">Advanced Insights</a></li>
                                <li><a href="{{ route('admin.creative.insights.realtime') }}" class="{{ $currentRoute === 'admin.creative.insights.realtime' ? 'active' : '' }}">Real-time Monitor</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>HRM</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'employees.php'||$page == 'employees-grid.php'||$page == 'employee-details.php'||$page == 'departments.php'||$page == 'designations.php'||$page == 'policy.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-users"></i><span>Employees</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('employees') }}" class="{{ ($page == 'employees.php') ? 'active' : '' }}">Employee Lists</a></li>
                                <li><a href="{{ $linkFor('employees-grid') }}" class="{{ ($page == 'employees-grid.php') ? 'active' : '' }}">Employee Grid</a></li>
                                <li><a href="{{ $linkFor('employee-details') }}" class="{{ ($page == 'employee-details.php') ? 'active' : '' }}">Employee Details</a></li>
                                <li><a href="{{ $linkFor('departments') }}" class="{{ ($page == 'departments.php') ? 'active' : '' }}">Departments</a></li>
                                <li><a href="{{ $linkFor('designations') }}" class="{{ ($page == 'designations.php') ? 'active' : '' }}">Designations</a></li>
                                <li><a href="{{ $linkFor('policy') }}" class="{{ ($page == 'policy.php') ? 'active' : '' }}">Policies</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'tickets.php'||$page == 'ticket-details.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-ticket"></i><span>Tickets</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('tickets') }}" class="{{ ($page == 'tickets.php') ? 'active' : '' }}">Tickets</a></li>
                                <li><a href="{{ $linkFor('ticket-details') }}" class="{{ ($page == 'ticket-details.php') ? 'active' : '' }}">Ticket Details</a></li>
                            </ul>
                        </li>
                        <li class="{{ ($page == 'holidays.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('holidays') }}">
                                <i class="ti ti-calendar-event"></i><span>Holidays</span>
                            </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'leaves.php'||$page == 'leaves-employee.php'||$page == 'leave-settings.php'||$page == 'attendance-admin.php'||$page == 'attendance-employee.php'||$page == 'timesheets.php'||$page == 'schedule-timing.php'||$page == 'overtime.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-file-time"></i><span>Attendance</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class=" {{ ($page == 'leaves.php'||$page == 'leaves-employee.php'||$page == 'leave-settings.php') ? 'active subdrop' : '' }}">Leaves<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('leaves') }}" class="{{ ($page == 'leaves.php') ? 'active' : '' }}">Leaves (Admin)</a></li>
                                        <li><a href="{{ $linkFor('leaves-employee') }}" class="{{ ($page == 'leaves-employee.php') ? 'active' : '' }}">Leave (Employee)</a></li>
                                        <li><a href="{{ $linkFor('leave-settings') }}" class="{{ ($page == 'leave-settings.php') ? 'active' : '' }}">Leave Settings</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ $linkFor('attendance-admin') }}" class="{{ ($page == 'attendance-admin.php') ? 'active' : '' }}">Attendance (Admin)</a></li>
                                <li><a href="{{ $linkFor('attendance-employee') }}" class="{{ ($page == 'attendance-employee.php') ? 'active' : '' }}">Attendance (Employee)</a></li>
                                <li><a href="{{ $linkFor('timesheets') }}" class="{{ ($page == 'timesheets.php') ? 'active' : '' }}">Timesheets</a></li>
                                <li><a href="{{ route('admin_v2.schedule.index') }}" class="{{ ($page == 'schedule-timing.php') ? 'active' : '' }}">Shift & Schedule</a></li>
                                <li><a href="{{ $linkFor('overtime') }}" class="{{ ($page == 'overtime.php') ? 'active' : '' }}">Overtime</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'performance-indicator.php'||$page == 'performance-review.php'||$page == 'performance-appraisal.php'||$page == 'goal-tracking.php'||$page == 'goal-type.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-school"></i><span>Performance</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('performance-indicator') }}" class="{{ ($page == 'performance-indicator.php') ? 'active' : '' }}">Performance Indicator</a></li>
                                <li><a href="{{ $linkFor('performance-review') }}" class="{{ ($page == 'performance-review.php') ? 'active' : '' }}">Performance Review</a></li>
                                <li><a href="{{ $linkFor('performance-appraisal') }}" class="{{ ($page == 'performance-appraisal.php') ? 'active' : '' }}">Performance Appraisal</a></li>
                                <li><a href="{{ $linkFor('goal-tracking') }}" class="{{ ($page == 'goal-tracking.php') ? 'active' : '' }}">Goal List</a></li>
                                <li><a href="{{ $linkFor('goal-type') }}" class="{{ ($page == 'goal-type.php') ? 'active' : '' }}">Goal Type</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'training.php'||$page == 'trainers.php'||$page == 'training-type.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-edit"></i><span>Training</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('training') }}" class="{{ ($page == 'training.php') ? 'active' : '' }}">Training List</a></li>
                                <li><a href="{{ $linkFor('trainers') }}" class="{{ ($page == 'trainers.php') ? 'active' : '' }}">Trainers</a></li>
                                <li><a href="{{ $linkFor('training-type') }}" class="{{ ($page == 'training-type.php') ? 'active' : '' }}">Training Type</a></li>
                            </ul>
                        </li>
                        <li class="{{ ($page == 'promotion.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('promotion') }}">
                                <i class="ti ti-speakerphone"></i><span>Promotion</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'resignation.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('resignation') }}">
                                <i class="ti ti-external-link"></i><span>Resignation</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'termination.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('termination') }}">
                                <i class="ti ti-circle-x"></i><span>Termination</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>RECRUITMENT</span></li>
                <li>
                    <ul>
                        <li class="{{ ($page == 'job-grid.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('job-grid') }}">
                                <i class="ti ti-timeline"></i><span>Jobs</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'candidates-grid.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('candidates-grid') }}">
                                <i class="ti ti-user-shield"></i><span>Candidates</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'refferals.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('refferals') }}">
                                <i class="ti ti-ux-circle"></i><span>Referrals</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>FINANCE & ACCOUNTS</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'estimates.php'||$page == 'invoices.php'||$page == 'payments.php'||$page == 'expenses.php'||$page == 'provident-fund.php'||$page == 'taxes.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-shopping-cart-dollar"></i><span>Sales</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('estimates') }}" class="{{ ($page == 'estimates.php') ? 'active' : '' }}">Estimates</a></li>
                                <li><a href="{{ $linkFor('invoices') }}" class="{{ ($page == 'invoices.php') ? 'active' : '' }}">Invoices</a></li>
                                <li><a href="{{ $linkFor('payments') }}" class="{{ ($page == 'payments.php') ? 'active' : '' }}">Payments</a></li>
                                <li><a href="{{ $linkFor('expenses') }}" class="{{ ($page == 'expenses.php') ? 'active' : '' }}">Expenses</a></li>
                                <li><a href="{{ $linkFor('provident-fund') }}" class="{{ ($page == 'provident-fund.php') ? 'active' : '' }}">Provident Fund</a></li>
                                <li><a href="{{ $linkFor('taxes') }}" class="{{ ($page == 'taxes.php') ? 'active' : '' }}">Taxes</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'categories.php'||$page == 'budgets.php'||$page == 'budget-expenses.php'||$page == 'budget-revenues.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-file-dollar"></i><span>Accounting</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('categories') }}" class="{{ ($page == 'categories.php') ? 'active' : '' }}">Categories</a></li>
                                <li><a href="{{ $linkFor('budgets') }}" class="{{ ($page == 'budgets.php') ? 'active' : '' }}">Budgets</a></li>
                                <li><a href="{{ $linkFor('budget-expenses') }}" class="{{ ($page == 'budget-expenses.php') ? 'active' : '' }}">Budget Expenses</a></li>
                                <li><a href="{{ $linkFor('budget-revenues') }}" class="{{ ($page == 'budget-revenues.php') ? 'active' : '' }}">Budget Revenues</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class=" {{ ($page == 'employee-salary.php'||$page == 'payslip.php'||$page == 'payroll.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-cash"></i><span>Payroll</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('employee-salary') }}" class="{{ ($page == 'employee-salary.php') ? 'active' : '' }}">Employee Salary</a></li>
                                <li><a href="{{ $linkFor('payslip') }}" class="{{ ($page == 'payslip.php') ? 'active' : '' }}">Payslip</a></li>
                                <li><a href="{{ $linkFor('payroll') }}" class="{{ ($page == 'payroll.php') ? 'active' : '' }}">Payroll Items</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>ADMINISTRATION</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'assets.php'||$page == 'asset-categories.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-cash"></i><span>Assets</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('assets') }}" class="{{ ($page == 'assets.php') ? 'active' : '' }}">Assets</a></li>
                                <li><a href="{{ $linkFor('asset-categories') }}" class="{{ ($page == 'asset-categories.php') ? 'active' : '' }}">Asset Categories</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'knowledgebase.php'||$page == 'activity.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-headset"></i><span>Help & Supports</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('knowledgebase') }}" class="{{ ($page == 'knowledgebase.php') ? 'active' : '' }}">Knowledge Base</a></li>
                                <li><a href="{{ $linkFor('activity') }}" class="{{ ($page == 'activity.php') ? 'active' : '' }}">Activities</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'users.php'||$page == 'roles-permissions.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-user-star"></i><span>User Management</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('users') }}">Users</a></li>
                                <li><a href="{{ $linkFor('roles-permissions') }}" class="{{ ($page == 'roles-permissions.php') ? 'active' : '' }}">Roles & Permissions</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'expenses-report.php'||$page == 'invoice-report.php'||$page == 'payment-report.php'||$page == 'project-report.php'||$page == 'task-report.php'||$page == 'user-report.php'||$page == 'employee-report.php'||$page == 'payslip-report.php'||$page == 'attendance-report.php'||$page == 'leave-report.php'||$page == 'daily-report.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-user-star"></i><span>Reports</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('expenses-report') }}" class="{{ ($page == 'expenses-report.php') ? 'active' : '' }}">Expense Report</a></li>
                                <li><a href="{{ $linkFor('invoice-report') }}" class="{{ ($page == 'invoice-report.php') ? 'active' : '' }}">Invoice Report</a></li>
                                <li><a href="{{ $linkFor('payment-report') }}" class="{{ ($page == 'payment-report.php') ? 'active' : '' }}">Payment Report</a></li>
                                <li><a href="{{ $linkFor('project-report') }}" class="{{ ($page == 'project-report.php') ? 'active' : '' }}">Project Report</a></li>
                                <li><a href="{{ $linkFor('task-report') }}" class="{{ ($page == 'task-report.php') ? 'active' : '' }}">Task Report</a></li>
                                <li><a href="{{ $linkFor('user-report') }}" class="{{ ($page == 'user-report.php') ? 'active' : '' }}">User Report</a></li>
                                <li><a href="{{ $linkFor('employee-report') }}" class="{{ ($page == 'employee-report.php') ? 'active' : '' }}">Employee Report</a></li>
                                <li><a href="{{ $linkFor('payslip-report') }}" class="{{ ($page == 'payslip-report.php') ? 'active' : '' }}">Payslip Report</a></li>
                                <li><a href="{{ $linkFor('attendance-report') }}" class="{{ ($page == 'attendance-report.php') ? 'active' : '' }}">Attendance Report</a></li>
                                <li><a href="{{ $linkFor('leave-report') }}" class="{{ ($page == 'leave-report.php') ? 'active' : '' }}">Leave Report</a></li>
                                <li><a href="{{ $linkFor('daily-report') }}" class="{{ ($page == 'daily-report.php') ? 'active' : '' }}">Daily Report</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ (
                                $page == 'profile-settings.php'||
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
                                ) ? 'active subdrop' : '' }}">
                                <i class="ti ti-settings"></i><span>Settings</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'profile-settings.php'||$page == 'security-settings.php'||$page == 'notification-settings.php'||$page == 'connected-apps.php') ? 'active subdrop' : '' }}">General Settings<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('profile-settings') }}" class="{{ ($page == 'profile-settings.php') ? 'active' : '' }}">Profile</a></li>
                                        <li><a href="{{ $linkFor('security-settings') }}" class="{{ ($page == 'security-settings.php') ? 'active' : '' }}">Security</a></li>
                                        <li><a href="{{ $linkFor('notification-settings') }}" class="{{ ($page == 'notification-settings.php') ? 'active' : '' }}">Notifications</a></li>
                                        <li><a href="{{ $linkFor('connected-apps') }}" class="{{ ($page == 'connected-apps.php') ? 'active' : '' }}">Connected Apps</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'bussiness-settings.php'||$page == 'seo-settings.php'||$page == 'localization-settings.php'||$page == 'prefixes.php'||$page == 'preferences.php'||$page == 'performance-appraisal.php'||$page == 'language.php'||$page == 'authentication-settings.php'||$page == 'ai-settings.php') ? 'active subdrop' : '' }}">Website Settings<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('bussiness-settings') }}" class="{{ ($page == 'bussiness-settings.php') ? 'active' : '' }}">Business Settings</a></li>
                                        <li><a href="{{ $linkFor('seo-settings') }}" class="{{ ($page == 'seo-settings.php') ? 'active' : '' }}">SEO Settings</a></li>
                                        <li><a href="{{ $linkFor('localization-settings') }}" class="{{ ($page == 'localization-settings.php') ? 'active' : '' }}">Localization</a></li>
                                        <li><a href="{{ $linkFor('prefixes') }}" class="{{ ($page == 'prefixes.php') ? 'active' : '' }}">Prefixes</a></li>
                                        <li><a href="{{ $linkFor('preferences') }}" class="{{ ($page == 'preferences.php') ? 'active' : '' }}">Preferences</a></li>
                                        <li><a href="{{ $linkFor('performance-appraisal') }}" class="{{ ($page == 'performance-appraisal.php') ? 'active' : '' }}">Appearance</a></li>
                                        <li><a href="{{ $linkFor('language') }}" class="{{ ($page == 'language.php') ? 'active' : '' }}">Language</a></li>
                                        <li><a href="{{ $linkFor('authentication-settings') }}" class="{{ ($page == 'authentication-settings.php') ? 'active' : '' }}">Authentication</a></li>
                                        <li><a href="{{ $linkFor('ai-settings') }}" class="{{ ($page == 'ai-settings.php') ? 'active' : '' }}">AI Settings</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'salary-settings.php'||$page == 'approval-settings.php'||$page == 'invoice-settings.php'||$page == 'leave-type.php'||$page == 'custom-fields.php') ? 'active subdrop' : '' }}">App Settings<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('salary-settings') }}" class="{{ ($page == 'salary-settings.php') ? 'active' : '' }}">Salary Settings</a></li>
                                        <li><a href="{{ $linkFor('approval-settings') }}" class="{{ ($page == 'approval-settings.php') ? 'active' : '' }}">Approval Settings</a></li>
                                        <li><a href="{{ $linkFor('invoice-settings') }}" class="{{ ($page == 'invoice-settings.php') ? 'active' : '' }}">Invoice Settings</a></li>
                                        <li><a href="{{ $linkFor('leave-type') }}" class="{{ ($page == 'leave-type.php') ? 'active' : '' }}">Leave Type</a></li>
                                        <li><a href="{{ $linkFor('custom-fields') }}" class="{{ ($page == 'custom-fields.php') ? 'active' : '' }}">Custom Fields</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'email-settings.php'||$page == 'email-template.php'||$page == 'sms-settings.php'||$page == 'sms-template.php'||$page == 'otp-settings.php'||$page == 'gdpr.php'||$page == 'maintenance-mode.php') ? 'active subdrop' : '' }}">System Settings<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('email-settings') }}" class="{{ ($page == 'email-settings.php') ? 'active' : '' }}">Email Settings</a></li>
                                        <li><a href="{{ $linkFor('email-template') }}" class="{{ ($page == 'email-template.php') ? 'active' : '' }}">Email Templates</a></li>
                                        <li><a href="{{ $linkFor('sms-settings') }}" class="{{ ($page == 'sms-settings.php') ? 'active' : '' }}">SMS Settings</a></li>
                                        <li><a href="{{ $linkFor('sms-template') }}" class="{{ ($page == 'sms-template.php') ? 'active' : '' }}">SMS Templates</a></li>
                                        <li><a href="{{ $linkFor('otp-settings') }}" class="{{ ($page == 'otp-settings.php') ? 'active' : '' }}">OTP</a></li>
                                        <li><a href="{{ $linkFor('gdpr') }}" class="{{ ($page == 'gdpr.php') ? 'active' : '' }}">GDPR Cookies</a></li>
                                        <li><a href="{{ $linkFor('maintenance-mode') }}" class="{{ ($page == 'maintenance-mode.php') ? 'active' : '' }}">Maintenance Mode</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'payment-gateways.php'||$page == 'tax-rates.php'||$page == 'currencies.php') ? 'active subdrop' : '' }}">Financial Settings<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('payment-gateways') }}" class="{{ ($page == 'payment-gateways.php') ? 'active' : '' }}">Payment Gateways</a></li>
                                        <li><a href="{{ $linkFor('tax-rates') }}" class="{{ ($page == 'tax-rates.php') ? 'active' : '' }}">Tax Rate</a></li>
                                        <li><a href="{{ $linkFor('currencies') }}" class="{{ ($page == 'currencies.php') ? 'active' : '' }}">Currencies</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'custom-css.php'||$page == 'custom-js.php'||$page == 'cronjob.php'||$page == 'storage-settings.php'||$page == 'ban-ip-address.php'||$page == 'backup.php'||$page == 'clear-cache.php') ? 'active subdrop' : '' }}">Other Settings<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('custom-css') }}" class="{{ ($page == 'custom-css.php') ? 'active' : '' }}">Custom CSS</a></li>
                                        <li><a href="{{ $linkFor('custom-js') }}" class="{{ ($page == 'custom-js.php') ? 'active' : '' }}">Custom JS</a></li>
                                        <li><a href="{{ $linkFor('cronjob') }}" class="{{ ($page == 'cronjob.php') ? 'active' : '' }}">Cronjob</a></li>
                                        <li><a href="{{ $linkFor('storage-settings') }}" class="{{ ($page == 'storage-settings.php') ? 'active' : '' }}">Storage</a></li>
                                        <li><a href="{{ $linkFor('ban-ip-address') }}" class="{{ ($page == 'ban-ip-address.php') ? 'active' : '' }}">Ban IP Address</a></li>
                                        <li><a href="{{ $linkFor('backup') }}" class="{{ ($page == 'backup.php') ? 'active' : '' }}">Backup</a></li>
                                        <li><a href="{{ $linkFor('clear-cache') }}" class="{{ ($page == 'clear-cache.php') ? 'active' : '' }}">Clear Cache</a></li>
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
                            <a href="{{ $linkFor('pages') }}">
                                <i class="ti ti-box-multiple"></i><span>Pages</span>
                            </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'blogs.php'||$page == 'blog-categories.php'||$page == 'blog-comments.php'||$page == 'blog-tags.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-brand-blogger"></i><span>Blogs</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('blogs') }}" class="{{ ($page == 'blogs.php') ? 'active' : '' }}">All Blogs</a></li>
                                <li><a href="{{ $linkFor('blog-categories') }}" class="{{ ($page == 'blog-categories.php') ? 'active' : '' }}">Categories</a></li>
                                <li><a href="{{ $linkFor('blog-comments') }}" class="{{ ($page == 'blog-comments.php') ? 'active' : '' }}">Comments</a></li>
                                <li><a href="{{ $linkFor('blog-tags') }}" class="{{ ($page == 'blog-tags.php') ? 'active' : '' }}">Blog Tags</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'countries.php'||$page == 'states.php'||$page == 'cities.php') ? 'active subdrop' : '' }}">
                                <i class="ti ti-map-pin-check"></i><span>Locations</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('countries') }}" class="{{ ($page == 'countries.php') ? 'active' : '' }}">Countries</a></li>
                                <li><a href="{{ $linkFor('states') }}" class="{{ ($page == 'states.php') ? 'active' : '' }}">States</a></li>
                                <li><a href="{{ $linkFor('cities') }}" class="{{ ($page == 'cities.php') ? 'active' : '' }}">Cities</a></li>
                            </ul>
                        </li>
                        <li class="{{ ($page == 'testimonials.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('testimonials') }}">
                                <i class="ti ti-message-2"></i><span>Testimonials</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'faq.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('faq') }}">
                                <i class="ti ti-question-mark"></i><span>FAQ’S</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>PAGES</span></li>
                <li>
                    <ul>
                        <li class="{{ ($page == 'starter.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('starter') }}">
                                <i class="ti ti-layout-sidebar"></i><span>Starter</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'profile.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('profile') }}">
                                <i class="ti ti-user-circle"></i><span>Profile</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'gallery.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('gallery') }}">
                                <i class="ti ti-photo"></i><span>Gallery</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'search-result.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('search-result') }}">
                                <i class="ti ti-list-search"></i><span>Search Results</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'timeline.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('timeline') }}">
                                <i class="ti ti-timeline"></i><span>Timeline</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'pricing.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('pricing') }}">
                                <i class="ti ti-file-dollar"></i><span>Pricing</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'coming-soon.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('coming-soon') }}">
                                <i class="ti ti-progress-bolt"></i><span>Coming Soon</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'under-maintenance.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('under-maintenance') }}">
                                <i class="ti ti-alert-octagon"></i><span>Under Maintenance</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'under-construction.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('under-construction') }}">
                                <i class="ti ti-barrier-block"></i><span>Under Construction</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'api-keys.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('api-keys') }}">
                                <i class="ti ti-api"></i><span>API Keys</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'privacy-policy.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('privacy-policy') }}">
                                <i class="ti ti-file-description"></i><span>Privacy Policy</span>
                            </a>
                        </li>
                        <li class="{{ ($page == 'terms-condition.php') ? 'active' : '' }}">
                            <a href="{{ $linkFor('terms-condition') }}">
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
                                <li><a href="{{ $linkFor('login-2') }}">Cover</a></li>
                                <li><a href="{{ $linkFor('login-3') }}">Illustration</a></li>
                                <li><a href="{{ $linkFor('login') }}">Basic</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-forms"></i><span>Register</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('register-2') }}">Cover</a></li>
                                <li><a href="{{ $linkFor('register-3') }}">Illustration</a></li>
                                <li><a href="{{ $linkFor('register') }}">Basic</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-help-triangle"></i><span>Forgot Password</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('forgot-password-2') }}">Cover</a></li>
                                <li><a href="{{ $linkFor('forgot-password-3') }}">Illustration</a></li>
                                <li><a href="{{ $linkFor('forgot-password') }}">Basic</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-restore"></i><span>Reset Password</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('reset-password-2') }}">Cover</a></li>
                                <li><a href="{{ $linkFor('reset-password-3') }}">Illustration</a></li>
                                <li><a href="{{ $linkFor('reset-password') }}">Basic</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-mail-exclamation"></i><span>Email Verification</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('email-verification-2') }}">Cover</a></li>
                                <li><a href="{{ $linkFor('email-verification-3') }}">Illustration</a></li>
                                <li><a href="{{ $linkFor('email-verification') }}">Basic</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-password"></i><span>2 Step Verification</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('two-step-verification-2') }}">Cover</a></li>
                                <li><a href="{{ $linkFor('two-step-verification-3') }}">Illustration</a></li>
                                <li><a href="{{ $linkFor('two-step-verification') }}">Basic</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ $linkFor('lock-screen') }}"><i class="ti ti-lock-square"></i><span>Lock Screen</span></a></li>
                        <li><a href="{{ $linkFor('error-404') }}"><i class="ti ti-error-404"></i><span>404 Error</span></a></li>
                        <li><a href="{{ $linkFor('error-500') }}"><i class="ti ti-server"></i><span>500 Error</span></a></li>
                    </ul>
                </li>

                <li class="menu-title"><span>UI INTERFACE</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'ui-alerts.php' || $page == 'ui-accordion.php' || $page == 'ui-avatar.php' || $page == 'ui-badges.php' || $page == 'ui-borders.php'
                                || $page == 'ui-buttons.php' || $page == 'ui-buttons-group.php' || $page == 'ui-breadcrumb.php' || $page == 'ui-cards.php' || $page == 'ui-carousel.php'
                                || $page == 'ui-colors.php' || $page == 'ui-dropdowns.php' || $page == 'ui-grid.php' || $page == 'ui-images.php' || $page == 'ui-lightbox.php'
                                || $page == 'ui-media.php' || $page == 'ui-modals.php' || $page == 'ui-offcanvas.php' || $page == 'ui-pagination.php' || $page == 'ui-popovers.php'
                                || $page == 'ui-progress.php' || $page == 'ui-placeholders.php'  || $page == 'ui-spinner.php'
                                || $page == 'ui-sweetalerts.php' || $page == 'ui-nav-tabs.php' || $page == 'ui-toasts.php' || $page == 'ui-tooltips.php'
                                || $page == 'ui-typography.php' || $page == 'ui-video.php'
                                || $page == 'ui-sortable.php' || $page == 'ui-swiperjs.php'
                                ) ? 'subdrop active' : '' }}">
                                <i class="ti ti-hierarchy-2"></i>
                                <span>Base UI</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('ui-alerts') }}" class="{{ ($page == 'ui-alerts.php') ? 'active' : '' }}">Alerts</a></li>
                                <li><a href="{{ $linkFor('ui-accordion') }}" class="{{ ($page == 'ui-accordion.php') ? 'active' : '' }}">Accordion</a></li>
                                <li><a href="{{ $linkFor('ui-avatar') }}" class="{{ ($page == 'ui-avatar.php') ? 'active' : '' }}">Avatar</a></li>
                                <li><a href="{{ $linkFor('ui-badges') }}" class="{{ ($page == 'ui-badges.php') ? 'active' : '' }}">Badges</a></li>
                                <li><a href="{{ $linkFor('ui-borders') }}" class="{{ ($page == 'ui-borders.php') ? 'active' : '' }}">Border</a></li>
                                <li><a href="{{ $linkFor('ui-buttons') }}" class="{{ ($page == 'ui-buttons.php') ? 'active' : '' }}">Buttons</a></li>
                                <li><a href="{{ $linkFor('ui-buttons-group') }}" class="{{ ($page == 'ui-buttons-group.php') ? 'active' : '' }}">Button Group</a></li>
                                <li><a href="{{ $linkFor('ui-breadcrumb') }}" class="{{ ($page == 'ui-breadcrumb.php') ? 'active' : '' }}">Breadcrumb</a></li>
                                <li><a href="{{ $linkFor('ui-cards') }}" class="{{ ($page == 'ui-cards.php') ? 'active' : '' }}">Card</a></li>
                                <li><a href="{{ $linkFor('ui-carousel') }}" class="{{ ($page == 'ui-carousel.php') ? 'active' : '' }}">Carousel</a></li>
                                <li><a href="{{ $linkFor('ui-colors') }}" class="{{ ($page == 'ui-colors.php') ? 'active' : '' }}">Colors</a></li>
                                <li><a href="{{ $linkFor('ui-dropdowns') }}" class="{{ ($page == 'ui-dropdowns.php') ? 'active' : '' }}">Dropdowns</a></li>
                                <li><a href="{{ $linkFor('ui-grid') }}" class="{{ ($page == 'ui-grid.php') ? 'active' : '' }}">Grid</a></li>
                                <li><a href="{{ $linkFor('ui-images') }}" class="{{ ($page == 'ui-images.php') ? 'active' : '' }}">Images</a></li>
                                <li><a href="{{ $linkFor('ui-lightbox') }}" class="{{ ($page == 'ui-lightbox.php') ? 'active' : '' }}">Lightbox</a></li>
                                <li><a href="{{ $linkFor('ui-media') }}" class="{{ ($page == 'ui-media.php') ? 'active' : '' }}">Media</a></li>
                                <li><a href="{{ $linkFor('ui-modals') }}" class="{{ ($page == 'ui-modals.php') ? 'active' : '' }}">Modals</a></li>
                                <li><a href="{{ $linkFor('ui-offcanvas') }}" class="{{ ($page == 'ui-offcanvas.php') ? 'active' : '' }}">Offcanvas</a></li>
                                <li><a href="{{ $linkFor('ui-pagination') }}" class="{{ ($page == 'ui-pagination.php') ? 'active' : '' }}">Pagination</a></li>
                                <li><a href="{{ $linkFor('ui-popovers') }}" class="{{ ($page == 'ui-popovers.php') ? 'active' : '' }}">Popovers</a></li>
                                <li><a href="{{ $linkFor('ui-progress') }}" class="{{ ($page == 'ui-progress.php') ? 'active' : '' }}">Progress</a></li>
                                <li><a href="{{ $linkFor('ui-placeholders') }}" class="{{ ($page == 'ui-placeholders.php') ? 'active' : '' }}">Placeholders</a></li>
                                <li><a href="{{ $linkFor('ui-spinner') }}" class="{{ ($page == 'ui-spinner.php') ? 'active' : '' }}">Spinner</a></li>
                                <li><a href="{{ $linkFor('ui-sweetalerts') }}" class="{{ ($page == 'ui-sweetalerts.php') ? 'active' : '' }}">Sweet Alerts</a></li>
                                <li><a href="{{ $linkFor('ui-nav-tabs') }}" class="{{ ($page == 'ui-nav-tabs.php') ? 'active' : '' }}">Tabs</a></li>
                                <li><a href="{{ $linkFor('ui-toasts') }}" class="{{ ($page == 'ui-toasts.php') ? 'active' : '' }}">Toasts</a></li>
                                <li><a href="{{ $linkFor('ui-tooltips') }}" class="{{ ($page == 'ui-tooltips.php') ? 'active' : '' }}">Tooltips</a></li>
                                <li><a href="{{ $linkFor('ui-typography') }}" class="{{ ($page == 'ui-typography.php') ? 'active' : '' }}">Typography</a></li>
                                <li><a href="{{ $linkFor('ui-video') }}" class="{{ ($page == 'ui-video.php') ? 'active' : '' }}">Video</a></li>
                                <li><a href="{{ $linkFor('ui-sortable') }}" class="{{ ($page == 'ui-sortable.php') ? 'active' : '' }}">Sortable</a></li>
                                <li><a href="{{ $linkFor('ui-swiperjs') }}" class="{{ ($page == 'ui-swiperjs.php') ? 'active' : '' }}">Swiperjs</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'ui-ribbon.php' || $page == 'ui-clipboard.php' || $page == 'ui-drag-drop.php' || $page == 'ui-rangeslider.php' || $page == 'ui-rating.php' || $page == 'ui-text-editor.php' || $page == 'ui-counter.php' || $page == 'ui-scrollbar.php' || $page == 'ui-stickynote.php' || $page == 'ui-timeline.php') ? 'subdrop active' : '' }}">
                                <i class="ti ti-hierarchy-3"></i>
                                <span>Advanced UI</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('ui-ribbon') }}" class="{{ ($page == 'ui-ribbon.php') ? 'active' : '' }}">Ribbon</a></li>
                                <li><a href="{{ $linkFor('ui-clipboard') }}" class="{{ ($page == 'ui-clipboard.php') ? 'active' : '' }}">Clipboard</a></li>
                                <li><a href="{{ $linkFor('ui-drag-drop') }}" class="{{ ($page == 'ui-drag-drop.php') ? 'active' : '' }}">Drag & Drop</a></li>
                                <li><a href="{{ $linkFor('ui-rangeslider') }}" class="{{ ($page == 'ui-rangeslider.php') ? 'active' : '' }}">Range Slider</a></li>
                                <li><a href="{{ $linkFor('ui-rating') }}" class="{{ ($page == 'ui-rating.php') ? 'active' : '' }}">Rating</a></li>
                                <li><a href="{{ $linkFor('ui-text-editor') }}" class="{{ ($page == 'ui-text-editor.php') ? 'active' : '' }}">Text Editor</a></li>
                                <li><a href="{{ $linkFor('ui-counter') }}" class="{{ ($page == 'ui-counter.php') ? 'active' : '' }}">Counter</a></li>
                                <li><a href="{{ $linkFor('ui-scrollbar') }}" class="{{ ($page == 'ui-scrollbar.php') ? 'active' : '' }}">Scrollbar</a></li>
                                <li><a href="{{ $linkFor('ui-stickynote') }}" class="{{ ($page == 'ui-stickynote.php') ? 'active' : '' }}">Sticky Note</a></li>
                                <li><a href="{{ $linkFor('ui-timeline') }}" class="{{ ($page == 'ui-timeline.php') ? 'active' : '' }}">Timeline</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'form-basic-inputs.php' || $page == 'form-checkbox-radios.php' || $page == 'form-input-groups.php' || $page == 'form-grid-gutters.php' || $page == 'form-select.php' || $page == 'form-mask.php' || $page == 'form-fileupload.php' || $page == 'form-horizontal.php' || $page == 'form-vertical.php' || $page == 'form-floating-labels.php' || $page == 'form-validation.php' || $page == 'form-wizard.php' || $page == 'form-select2.php' || $page == 'form-pickers.php') ? 'subdrop active' : '' }}">
                                <i class="ti ti-input-search"></i>
                                <span>Forms</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'form-basic-inputs.php' || $page == 'form-checkbox-radios.php' || $page == 'form-input-groups.php' || $page == 'form-grid-gutters.php' || $page == 'form-select.php' || $page == 'form-mask.php' || $page == 'form-fileupload.php') ? 'subdrop active' : '' }}">Form Elements <span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('form-basic-inputs') }}" class="{{ ($page == 'form-basic-inputs.php') ? 'active' : '' }}">Basic Inputs</a></li>
                                        <li><a href="{{ $linkFor('form-checkbox-radios') }}" class="{{ ($page == 'form-checkbox-radios.php') ? 'active' : '' }}">Checkbox & Radios</a></li>
                                        <li><a href="{{ $linkFor('form-input-groups') }}" class="{{ ($page == 'form-input-groups.php') ? 'active' : '' }}">Input Groups</a></li>
                                        <li><a href="{{ $linkFor('form-grid-gutters') }}" class="{{ ($page == 'form-grid-gutters.php') ? 'active' : '' }}">Grid & Gutters</a></li>
                                        <li><a href="{{ $linkFor('form-select') }}" class="{{ ($page == 'form-select.php') ? 'active' : '' }}">Form Select</a></li>
                                        <li><a href="{{ $linkFor('form-mask') }}" class="{{ ($page == 'form-mask.php') ? 'active' : '' }}">Input Masks</a></li>
                                        <li><a href="{{ $linkFor('form-fileupload') }}" class="{{ ($page == 'form-fileupload.php') ? 'active' : '' }}">File Uploads</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);" class="{{ ($page == 'form-horizontal.php' || $page == 'form-vertical.php' || $page == 'form-floating-labels.php') ? 'subdrop active' : '' }}">Layouts <span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ $linkFor('form-horizontal') }}" class="{{ ($page == 'form-horizontal.php') ? 'active' : '' }}">Horizontal Form</a></li>
                                        <li><a href="{{ $linkFor('form-vertical') }}" class="{{ ($page == 'form-vertical.php') ? 'active' : '' }}">Vertical Form</a></li>
                                        <li><a href="{{ $linkFor('form-floating-labels') }}" class="{{ ($page == 'form-floating-labels.php') ? 'active' : '' }}">Floating Labels</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ $linkFor('form-validation') }}" class="{{ ($page == 'form-validation.php') ? 'active' : '' }}">Form Validation</a></li>
                                <li><a href="{{ $linkFor('form-select2') }}" class="{{ ($page == 'form-select2.php') ? 'active' : '' }}">Select2</a></li>
                                <li><a href="{{ $linkFor('form-wizard') }}" class="{{ ($page == 'form-wizard.php') ? 'active' : '' }}">Form Wizard</a></li>
                                <li><a href="{{ $linkFor('form-pickers') }}" class="{{ ($page == 'form-pickers.php') ? 'active' : '' }}">Form Picker</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'tables-basic.php' || $page == 'data-tables.php') ? 'subdrop active' : '' }}">
                                <i class="ti ti-table-plus"></i>
                                <span>Tables</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('tables-basic') }}" class="{{ ($page == 'tables-basic.php') ? 'active' : '' }}">Basic Tables</a></li>
                                <li><a href="{{ $linkFor('data-tables') }}" class="{{ ($page == 'data-tables.php') ? 'active' : '' }}">Data Table</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'chart-apex.php' || $page == 'chart-js.php' || $page == 'chart-morris.php' || $page == 'chart-flot.php' || $page == 'chart-peity.php' || $page == 'chart-c3.php') ? 'subdrop active' : '' }}">
                                <i class="ti ti-chart-line"></i>
                                <span>Charts</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('chart-apex') }}" class="{{ ($page == 'chart-apex.php') ? 'active' : '' }}">Apex Charts</a></li>
                                <li><a href="{{ $linkFor('chart-c3') }}" class="{{ ($page == 'chart-c3.php') ? 'active' : '' }}">Chart C3</a></li>
                                <li><a href="{{ $linkFor('chart-js') }}" class="{{ ($page == 'chart-js.php') ? 'active' : '' }}">Chart Js</a></li>
                                <li><a href="{{ $linkFor('chart-morris') }}" class="{{ ($page == 'chart-morris.php') ? 'active' : '' }}">Morris Charts</a></li>
                                <li><a href="{{ $linkFor('chart-flot') }}" class="{{ ($page == 'chart-flot.php') ? 'active' : '' }}">Flot Charts</a></li>
                                <li><a href="{{ $linkFor('chart-peity') }}" class="{{ ($page == 'chart-peity.php') ? 'active' : '' }}">Peity Charts</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'icon-fontawesome.php' || $page == 'icon-feather.php' || $page == 'icon-ionic.php' || $page == 'icon-material.php' || $page == 'icon-pe7.php' || $page == 'icon-simpleline.php' || $page == 'icon-themify.php' || $page == 'icon-weather.php' || $page == 'icon-typicon.php' || $page == 'icon-flag.php' || $page == 'icon-tabler.php' || $page == 'icon-bootstrap.php' || $page == 'icon-remix.php') ? 'subdrop active' : '' }}">
                                <i class="ti ti-icons"></i>
                                <span>Icons</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('icon-fontawesome') }}" class="{{ ($page == 'icon-fontawesome.php') ? 'active' : '' }}">Fontawesome Icons</a></li>
                                <li><a href="{{ $linkFor('icon-tabler') }}" class="{{ ($page == 'icon-tabler.php') ? 'active' : '' }}">Tabler Icons</a></li>
                                <li><a href="{{ $linkFor('icon-bootstrap') }}" class="{{ ($page == 'icon-bootstrap.php') ? 'active' : '' }}">Bootstrap Icons</a></li>
                                <li><a href="{{ $linkFor('icon-remix') }}" class="{{ ($page == 'icon-remix.php') ? 'active' : '' }}">Remix Icons</a></li>
                                <li><a href="{{ $linkFor('icon-feather') }}" class="{{ ($page == 'icon-feather.php') ? 'active' : '' }}">Feather Icons</a></li>
                                <li><a href="{{ $linkFor('icon-ionic') }}" class="{{ ($page == 'icon-ionic.php') ? 'active' : '' }}">Ionic Icons</a></li>
                                <li><a href="{{ $linkFor('icon-material') }}" class="{{ ($page == 'icon-material.php') ? 'active' : '' }}">Material Icons</a></li>
                                <li><a href="{{ $linkFor('icon-pe7') }}" class="{{ ($page == 'icon-pe7.php') ? 'active' : '' }}">Pe7 Icons</a></li>
                                <li><a href="{{ $linkFor('icon-simpleline') }}" class="{{ ($page == 'icon-simpleline.php') ? 'active' : '' }}">Simpleline Icons</a></li>
                                <li><a href="{{ $linkFor('icon-themify') }}" class="{{ ($page == 'icon-themify.php') ? 'active' : '' }}">Themify Icons</a></li>
                                <li><a href="{{ $linkFor('icon-weather') }}" class="{{ ($page == 'icon-weather.php') ? 'active' : '' }}">Weather Icons</a></li>
                                <li><a href="{{ $linkFor('icon-typicon') }}" class="{{ ($page == 'icon-typicon.php') ? 'active' : '' }}">Typicon Icons</a></li>
                                <li><a href="{{ $linkFor('icon-flag') }}" class="{{ ($page == 'icon-flag.php') ? 'active' : '' }}">Flag Icons</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($page == 'maps-vector.php' || $page == 'maps-leaflet.php') ? 'subdrop active' : '' }}">
                                <i class="ti ti-table-plus"></i>
                                <span>Maps</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ $linkFor('maps-vector') }}" class="{{ ($page == 'maps-vector.php') ? 'active' : '' }}">Vector</a></li>
                                <li><a href="{{ $linkFor('maps-leaflet') }}" class="{{ ($page == 'maps-leaflet.php') ? 'active' : '' }}">Leaflet</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>Extras</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="javascript:void(0);"><i class="ti ti-file-text"></i><span>Documentation</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0);"><i class="ti ti-exchange"></i><span>Changelog</span><span class="badge bg-pink badge-xs text-white fs-10 ms-s">v4.0.9</span></a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-menu-2"></i><span>Multi Level</span><span class="menu-arrow"></span></a>
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
<!-- /Sidebar -->
