<?php
    // Current page name, safe for subfolders & query strings
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $page = basename($path);
    if ($page === '' || $page === false) { $page = 'index.php'; }

    // Helpers
    function active($page, $names) {
        return in_array($page, (array)$names, true) ? 'active' : '';
    }
    function subdrop($page, $names) {
        return in_array($page, (array)$names, true) ? 'active subdrop' : '';
    }
?>
<!-- Horizontal Menu -->
<div class="sidebar sidebar-horizontal" id="horizontal-menu">
    <div class="sidebar-menu">
        <div class="main-menu">
            <ul class="nav-menu">
                <li class="menu-title">
                    <span>Main</span>
                </li>

                <!-- Dashboard -->
                <li class="submenu">
                    <a href="#" class="<?php echo subdrop($page, ['index.php','employee-dashboard.php','deals-dashboard.php','leads-dashboard.php']); ?>">
                        <i class="ti ti-smart-home"></i><span>Dashboard</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="index.php" class="<?php echo active($page,'index.php'); ?>">Admin Dashboard</a></li>
                        <li><a href="employee-dashboard.php" class="<?php echo active($page,'employee-dashboard.php'); ?>">Employee Dashboard</a></li>
                        <li><a href="deals-dashboard.php" class="<?php echo active($page,'deals-dashboard.php'); ?>">Deals Dashboard</a></li>
                        <li><a href="leads-dashboard.php" class="<?php echo active($page,'leads-dashboard.php'); ?>">Leads Dashboard</a></li>
                    </ul>
                </li>

                <!-- Super Admin -->
                <li class="submenu">
                    <a href="#" class="<?php echo subdrop($page, ['dashboard.php','companies.php','subscription.php','packages.php','domain.php','purchase-transaction.php']); ?>">
                        <i class="ti ti-user-star"></i><span>Super Admin</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="dashboard.php" class="<?php echo active($page,'dashboard.php'); ?>">Dashboard</a></li>
                        <li><a href="companies.php" class="<?php echo active($page,'companies.php'); ?>">Companies</a></li>
                        <li><a href="subscription.php" class="<?php echo active($page,'subscription.php'); ?>">Subscriptions</a></li>
                        <li><a href="packages.php" class="<?php echo active($page,'packages.php'); ?>">Packages</a></li>
                        <li><a href="domain.php" class="<?php echo active($page,'domain.php'); ?>">Domain</a></li>
                        <li><a href="purchase-transaction.php" class="<?php echo active($page,'purchase-transaction.php'); ?>">Purchase Transaction</a></li>
                    </ul>
                </li>

                <!-- Applications -->
                <li class="submenu">
                    <a href="#" class="<?php echo subdrop($page, ['chat.php','call.php','voice-call.php','video-call.php','outgoing-call.php','incoming-call.php','call-history.php','calendar.php','email.php','todo.php','notes.php','social-feed.php','file-manager.php','kanban-view.php','invoices.php']); ?>">
                        <i class="ti ti-layout-grid-add"></i><span>Applications</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="chat.php" class="<?php echo active($page,'chat.php'); ?>">Chat</a></li>
                        <li class="submenu submenu-two">
                            <a href="call.php" class="<?php echo active($page,'call.php'); ?>">Calls<span class="menu-arrow inside-submenu"></span></a>
                            <ul>
                                <li><a href="voice-call.php" class="<?php echo active($page,'voice-call.php'); ?>">Voice Call</a></li>
                                <li><a href="video-call.php" class="<?php echo active($page,'video-call.php'); ?>">Video Call</a></li>
                                <li><a href="outgoing-call.php" class="<?php echo active($page,'outgoing-call.php'); ?>">Outgoing Call</a></li>
                                <li><a href="incoming-call.php" class="<?php echo active($page,'incoming-call.php'); ?>">Incoming Call</a></li>
                                <li><a href="call-history.php" class="<?php echo active($page,'call-history.php'); ?>">Call History</a></li>
                            </ul>
                        </li>
                        <li><a href="calendar.php" class="<?php echo active($page,'calendar.php'); ?>">Calendar</a></li>
                        <li><a href="email.php" class="<?php echo active($page,'email.php'); ?>">Email</a></li>
                        <li><a href="todo.php" class="<?php echo active($page,'todo.php'); ?>">To Do</a></li>
                        <li><a href="notes.php" class="<?php echo active($page,'notes.php'); ?>">Notes</a></li>
                        <li><a href="file-manager.php" class="<?php echo active($page,'file-manager.php'); ?>">File Manager</a></li>
                        <li><a href="kanban-view.php" class="<?php echo active($page,'kanban-view.php'); ?>">Kanban</a></li>
                        <li><a href="invoices.php" class="<?php echo active($page,'invoices.php'); ?>">Invoices</a></li>
                    </ul>
                </li>

                <!-- Layouts -->
                <li class="submenu">
                    <a href="#" class="<?php echo subdrop($page, ['layout-horizontal.php','layout-detached.php','layout-modern.php','layout-two-column.php','layout-hovered.php','layout-box.php','layout-horizontal-single.php','layout-horizontal-box.php','layout-horizontal-overlay.php','layout-horizontal-sidemenu.php','layout-vertical-transparent.php','layout-without-header.php','layout-rtl.php','layout-dark.php']); ?>">
                        <i class="ti ti-layout-board-split"></i><span>Layouts</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="<?php echo active($page,'layout-horizontal.php'); ?>">
                            <a href="layout-horizontal.php"><span>Horizontal</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-detached.php'); ?>">
                            <a href="layout-detached.php"><span>Detached</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-modern.php'); ?>">
                            <a href="layout-modern.php"><span>Modern</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-two-column.php'); ?>">
                            <a href="layout-two-column.php"><span>Two Column </span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-hovered.php'); ?>">
                            <a href="layout-hovered.php"><span>Hovered</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-box.php'); ?>">
                            <a href="layout-box.php"><span>Boxed</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-horizontal-single.php'); ?>">
                            <a href="layout-horizontal-single.php"><span>Horizontal Single</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-horizontal-overlay.php'); ?>">
                            <a href="layout-horizontal-overlay.php"><span>Horizontal Overlay</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-horizontal-box.php'); ?>">
                            <a href="layout-horizontal-box.php"><span>Horizontal Box</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-horizontal-sidemenu.php'); ?>">
                            <a href="layout-horizontal-sidemenu.php"><span>Menu Aside</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-vertical-transparent.php'); ?>">
                            <a href="layout-vertical-transparent.php"><span>Transparent</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-without-header.php'); ?>">
                            <a href="layout-without-header.php"><span>Without Header</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-rtl.php'); ?>">
                            <a href="layout-rtl.php"><span>RTL</span></a>
                        </li>
                        <li class="<?php echo active($page,'layout-dark.php'); ?>">
                            <a href="layout-dark.php"><span>Dark</span></a>
                        </li>
                    </ul>
                </li>

                <!-- Projects -->
                <li class="submenu">
                    <a href="#"><i class="ti ti-user-star"></i><span>Projects</span><span class="menu-arrow"></span></a>
                    <ul>
                        <li class="<?php echo active($page, ['clients-grid.php','clients.php']); ?>">
                            <a href="clients-grid.php"><span>Clients</span></a>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['projects-grid.php','tasks.php','task-board.php']); ?>"><span>Projects</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="projects-grid.php" class="<?php echo active($page,'projects-grid.php'); ?>">Projects</a></li>
                                <li><a href="tasks.php" class="<?php echo active($page,'tasks.php'); ?>">Tasks</a></li>
                                <li><a href="task-board.php" class="<?php echo active($page,'task-board.php'); ?>">Task Board</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="call.php" class="<?php echo active($page,'call.php'); ?>">Crm<span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="contacts-grid.php" class="<?php echo active($page, ['contacts-grid.php','contacts.php','contact-details.php']); ?>"><span>Contacts</span></a></li>
                                <li><a href="companies-grid.php" class="<?php echo active($page, ['companies-grid.php','companies-crm.php','company-details.php']); ?>"><span>Companies</span></a></li>
                                <li><a href="deals-grid.php" class="<?php echo active($page, ['deals-grid.php','deals-details.php','deals.php']); ?>"><span>Deals</span></a></li>
                                <li><a href="leads-grid.php" class="<?php echo active($page, ['leads-grid.php','leads-details.php','leads.php']); ?>"><span>Leads</span></a></li>
                                <li><a href="pipeline.php" class="<?php echo active($page,'pipeline.php'); ?>"><span>Pipeline</span></a></li>
                                <li><a href="analytics.php" class="<?php echo active($page,'analytics.php'); ?>"><span>Analytics</span></a></li>
                                <li><a href="activity.php" class="<?php echo active($page,'activity.php'); ?>"><span>Activities</span></a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['employees.php','employees-grid.php','employee-details.php','departments.php','designations.php','policy.php']); ?>"><span>Employees</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="employees.php" class="<?php echo active($page,'employees.php'); ?>">Employee Lists</a></li>
                                <li><a href="employees-grid.php" class="<?php echo active($page,'employees-grid.php'); ?>">Employee Grid</a></li>
                                <li><a href="employee-details.php" class="<?php echo active($page,'employee-details.php'); ?>">Employee Details</a></li>
                                <li><a href="departments.php" class="<?php echo active($page,'departments.php'); ?>">Departments</a></li>
                                <li><a href="designations.php" class="<?php echo active($page,'designations.php'); ?>">Designations</a></li>
                                <li><a href="policy.php" class="<?php echo active($page,'policy.php'); ?>">Policies</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['tickets.php','ticket-details.php']); ?>"><span>Tickets</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="tickets.php" class="<?php echo active($page,'tickets.php'); ?>">Tickets</a></li>
                                <li><a href="ticket-details.php" class="<?php echo active($page,'ticket-details.php'); ?>">Ticket Details</a></li>
                            </ul>
                        </li>

                        <li><a href="holidays.php" class="<?php echo active($page,'holidays.php'); ?>"><span>Holidays</span></a></li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['leaves.php','leaves-employee.php','leave-settings.php','attendance-admin.php','attendance-employee.php','timesheets.php','schedule-timing.php','overtime.php']); ?>"><span>Attendance</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['leaves.php','leaves-employee.php','leave-settings.php']); ?>">Leaves<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="leaves.php" class="<?php echo active($page,'leaves.php'); ?>">Leaves (Admin)</a></li>
                                        <li><a href="leaves-employee.php" class="<?php echo active($page,'leaves-employee.php'); ?>">Leave (Employee)</a></li>
                                        <li><a href="leave-settings.php" class="<?php echo active($page,'leave-settings.php'); ?>">Leave Settings</a></li>
                                    </ul>
                                </li>
                                <li><a href="attendance-admin.php" class="<?php echo active($page,'attendance-admin.php'); ?>">Attendance (Admin)</a></li>
                                <li><a href="attendance-employee.php" class="<?php echo active($page,'attendance-employee.php'); ?>">Attendance (Employee)</a></li>
                                <li><a href="timesheets.php" class="<?php echo active($page,'timesheets.php'); ?>">Timesheets</a></li>
                                <li><a href="schedule-timing.php" class="<?php echo active($page,'schedule-timing.php'); ?>">Shift & Schedule</a></li>
                                <li><a href="overtime.php" class="<?php echo active($page,'overtime.php'); ?>">Overtime</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['performance-indicator.php','performance-review.php','performance-appraisal.php','goal-tracking.php','goal-type.php']); ?>"><span>Performance</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="performance-indicator.php" class="<?php echo active($page,'performance-indicator.php'); ?>">Performance Indicator</a></li>
                                <li><a href="performance-review.php" class="<?php echo active($page,'performance-review.php'); ?>">Performance Review</a></li>
                                <li><a href="performance-appraisal.php" class="<?php echo active($page,'performance-appraisal.php'); ?>">Performance Appraisal</a></li>
                                <li><a href="goal-tracking.php" class="<?php echo active($page,'goal-tracking.php'); ?>">Goal List</a></li>
                                <li><a href="goal-type.php" class="<?php echo active($page,'goal-type.php'); ?>">Goal Type</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['training.php','trainers.php','training-type.php']); ?>"><span>Training</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="training.php" class="<?php echo active($page,'training.php'); ?>">Training List</a></li>
                                <li><a href="trainers.php" class="<?php echo active($page,'trainers.php'); ?>">Trainers</a></li>
                                <li><a href="training-type.php" class="<?php echo active($page,'training-type.php'); ?>">Training Type</a></li>
                            </ul>
                        </li>

                        <li><a href="promotion.php" class="<?php echo active($page,'promotion.php'); ?>"><span>Promotion</span></a></li>
                        <li><a href="resignation.php" class="<?php echo active($page,'resignation.php'); ?>"><span>Resignation</span></a></li>
                        <li><a href="termination.php" class="<?php echo active($page,'termination.php'); ?>"><span>Termination</span></a></li>
                    </ul>
                </li>

                <!-- Administration -->
                <li class="submenu">
                    <a href="#"><i class="ti ti-user-star"></i><span>Administration</span><span class="menu-arrow"></span></a>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['estimates.php','invoices.php','payments.php','expenses.php','provident-fund.php','taxes.php']); ?>"><span>Sales</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="estimates.php" class="<?php echo active($page,'estimates.php'); ?>">Estimates</a></li>
                                <li><a href="invoices.php" class="<?php echo active($page,'invoices.php'); ?>">Invoices</a></li>
                                <li><a href="payments.php" class="<?php echo active($page,'payments.php'); ?>">Payments</a></li>
                                <li><a href="expenses.php" class="<?php echo active($page,'expenses.php'); ?>">Expenses</a></li>
                                <li><a href="provident-fund.php" class="<?php echo active($page,'provident-fund.php'); ?>">Provident Fund</a></li>
                                <li><a href="taxes.php" class="<?php echo active($page,'taxes.php'); ?>">Taxes</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['categories.php','budgets.php','budget-expenses.php','budget-revenues.php']); ?>"><span>Accounting</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="categories.php" class="<?php echo active($page,'categories.php'); ?>">Categories</a></li>
                                <li><a href="budgets.php" class="<?php echo active($page,'budgets.php'); ?>">Budgets</a></li>
                                <li><a href="budget-expenses.php" class="<?php echo active($page,'budget-expenses.php'); ?>">Budget Expenses</a></li>
                                <li><a href="budget-revenues.php" class="<?php echo active($page,'budget-revenues.php'); ?>">Budget Revenues</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['employee-salary.php','payslip.php','payroll.php']); ?>"><span>Payroll</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="employee-salary.php" class="<?php echo active($page,'employee-salary.php'); ?>">Employee Salary</a></li>
                                <li><a href="payslip.php" class="<?php echo active($page,'payslip.php'); ?>">Payslip</a></li>
                                <li><a href="payroll.php" class="<?php echo active($page,'payroll.php'); ?>">Payroll Items</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['assets.php','asset-categories.php']); ?>"><span>Assets</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="assets.php" class="<?php echo active($page,'assets.php'); ?>">Assets</a></li>
                                <li><a href="asset-categories.php" class="<?php echo active($page,'asset-categories.php'); ?>">Asset Categories</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['knowledgebase.php','activity.php']); ?>"><span>Help & Supports</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="knowledgebase.php" class="<?php echo active($page,'knowledgebase.php'); ?>">Knowledge Base</a></li>
                                <li><a href="activity.php" class="<?php echo active($page,'activity.php'); ?>">Activities</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['users.php','roles-permissions.php']); ?>"><span>User Management</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="users.php" class="<?php echo active($page,'users.php'); ?>">Users</a></li>
                                <li><a href="roles-permissions.php" class="<?php echo active($page,'roles-permissions.php'); ?>">Roles & Permissions</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['expenses-report.php','invoice-report.php','payment-report.php','project-report.php','task-report.php','user-report.php','employee-report.php','payslip-report.php','attendance-report.php','leave-report.php','daily-report.php']); ?>"><span>Reports</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="expenses-report.php" class="<?php echo active($page,'expenses-report.php'); ?>">Expense Report</a></li>
                                <li><a href="invoice-report.php" class="<?php echo active($page,'invoice-report.php'); ?>">Invoice Report</a></li>
                                <li><a href="payment-report.php" class="<?php echo active($page,'payment-report.php'); ?>">Payment Report</a></li>
                                <li><a href="project-report.php" class="<?php echo active($page,'project-report.php'); ?>">Project Report</a></li>
                                <li><a href="task-report.php" class="<?php echo active($page,'task-report.php'); ?>">Task Report</a></li>
                                <li><a href="user-report.php" class="<?php echo active($page,'user-report.php'); ?>">User Report</a></li>
                                <li><a href="employee-report.php" class="<?php echo active($page,'employee-report.php'); ?>">Employee Report</a></li>
                                <li><a href="payslip-report.php" class="<?php echo active($page,'payslip-report.php'); ?>">Payslip Report</a></li>
                                <li><a href="attendance-report.php" class="<?php echo active($page,'attendance-report.php'); ?>">Attendance Report</a></li>
                                <li><a href="leave-report.php" class="<?php echo active($page,'leave-report.php'); ?>">Leave Report</a></li>
                                <li><a href="daily-report.php" class="<?php echo active($page,'daily-report.php'); ?>">Daily Report</a></li>
                            </ul>
                        </li>

                        <!-- Settings -->
                        <li class="submenu">
                            <a href="javascript:void(0);" class="<?php echo subdrop($page, [
                                'profile-settings.php','security-settings.php','notification-settings.php','connected-apps.php',
                                'bussiness-settings.php','seo-settings.php','localization-settings.php','prefixes.php','preferences.php','language.php','authentication-settings.php','ai-settings.php',
                                'salary-settings.php','approval-settings.php','invoice-settings.php','leave-type.php','custom-fields.php',
                                'email-settings.php','email-template.php','sms-settings.php','sms-template.php','otp-settings.php','gdpr.php','maintenance-mode.php',
                                'payment-gateways.php','tax-rates.php','currencies.php',
                                'custom-css.php','custom-js.php','cronjob.php','storage-settings.php','ban-ip-address.php','backup.php','clear-cache.php'
                            ]); ?>"><span>Settings</span><span class="menu-arrow"></span></a>

                            <ul>
                                <!-- General Settings -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['profile-settings.php','security-settings.php','notification-settings.php','connected-apps.php']); ?>">General Settings<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="profile-settings.php" class="<?php echo active($page,'profile-settings.php'); ?>">Profile</a></li>
                                        <li><a href="security-settings.php" class="<?php echo active($page,'security-settings.php'); ?>">Security</a></li>
                                        <li><a href="notification-settings.php" class="<?php echo active($page,'notification-settings.php'); ?>">Notifications</a></li>
                                        <li><a href="connected-apps.php" class="<?php echo active($page,'connected-apps.php'); ?>">Connected Apps</a></li>
                                    </ul>
                                </li>

                                <!-- Website Settings -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['bussiness-settings.php','seo-settings.php','localization-settings.php','prefixes.php','preferences.php','language.php','authentication-settings.php','ai-settings.php']); ?>">Website Settings<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="bussiness-settings.php" class="<?php echo active($page,'bussiness-settings.php'); ?>">Business Settings</a></li>
                                        <li><a href="seo-settings.php" class="<?php echo active($page,'seo-settings.php'); ?>">SEO Settings</a></li>
                                        <li><a href="localization-settings.php" class="<?php echo active($page,'localization-settings.php'); ?>">Localization</a></li>
                                        <li><a href="prefixes.php" class="<?php echo active($page,'prefixes.php'); ?>">Prefixes</a></li>
                                        <li><a href="preferences.php" class="<?php echo active($page,'preferences.php'); ?>">Preferences</a></li>
                                        <li><a href="language.php" class="<?php echo active($page,'language.php'); ?>">Language</a></li>
                                        <li><a href="authentication-settings.php" class="<?php echo active($page,'authentication-settings.php'); ?>">Authentication</a></li>
                                        <li><a href="ai-settings.php" class="<?php echo active($page,'ai-settings.php'); ?>">AI Settings</a></li>
                                    </ul>
                                </li>

                                <!-- App Settings -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['salary-settings.php','approval-settings.php','invoice-settings.php','leave-type.php','custom-fields.php']); ?>">App Settings<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="salary-settings.php" class="<?php echo active($page,'salary-settings.php'); ?>">Salary Settings</a></li>
                                        <li><a href="approval-settings.php" class="<?php echo active($page,'approval-settings.php'); ?>">Approval Settings</a></li>
                                        <li><a href="invoice-settings.php" class="<?php echo active($page,'invoice-settings.php'); ?>">Invoice Settings</a></li>
                                        <li><a href="leave-type.php" class="<?php echo active($page,'leave-type.php'); ?>">Leave Type</a></li>
                                        <li><a href="custom-fields.php" class="<?php echo active($page,'custom-fields.php'); ?>">Custom Fields</a></li>
                                    </ul>
                                </li>

                                <!-- System Settings -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['email-settings.php','email-template.php','sms-settings.php','sms-template.php','otp-settings.php','gdpr.php','maintenance-mode.php']); ?>">System Settings<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="email-settings.php" class="<?php echo active($page,'email-settings.php'); ?>">Email Settings</a></li>
                                        <li><a href="email-template.php" class="<?php echo active($page,'email-template.php'); ?>">Email Templates</a></li>
                                        <li><a href="sms-settings.php" class="<?php echo active($page,'sms-settings.php'); ?>">SMS Settings</a></li>
                                        <li><a href="sms-template.php" class="<?php echo active($page,'sms-template.php'); ?>">SMS Templates</a></li>
                                        <li><a href="otp-settings.php" class="<?php echo active($page,'otp-settings.php'); ?>">OTP</a></li>
                                        <li><a href="gdpr.php" class="<?php echo active($page,'gdpr.php'); ?>">GDPR Cookies</a></li>
                                        <li><a href="maintenance-mode.php" class="<?php echo active($page,'maintenance-mode.php'); ?>">Maintenance Mode</a></li>
                                    </ul>
                                </li>

                                <!-- Financial Settings -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['payment-gateways.php','tax-rates.php','currencies.php']); ?>">Financial Settings<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="payment-gateways.php" class="<?php echo active($page,'payment-gateways.php'); ?>">Payment Gateways</a></li>
                                        <li><a href="tax-rates.php" class="<?php echo active($page,'tax-rates.php'); ?>">Tax Rate</a></li>
                                        <li><a href="currencies.php" class="<?php echo active($page,'currencies.php'); ?>">Currencies</a></li>
                                    </ul>
                                </li>

                                <!-- Other Settings -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['custom-css.php','custom-js.php','cronjob.php','storage-settings.php','ban-ip-address.php','backup.php','clear-cache.php']); ?>">Other Settings<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="custom-css.php" class="<?php echo active($page,'custom-css.php'); ?>">Custom CSS</a></li>
                                        <li><a href="custom-js.php" class="<?php echo active($page,'custom-js.php'); ?>">Custom JS</a></li>
                                        <li><a href="cronjob.php" class="<?php echo active($page,'cronjob.php'); ?>">Cronjob</a></li>
                                        <li><a href="storage-settings.php" class="<?php echo active($page,'storage-settings.php'); ?>">Storage</a></li>
                                        <li><a href="ban-ip-address.php" class="<?php echo active($page,'ban-ip-address.php'); ?>">Ban IP Address</a></li>
                                        <li><a href="backup.php" class="<?php echo active($page,'backup.php'); ?>">Backup</a></li>
                                        <li><a href="clear-cache.php" class="<?php echo active($page,'clear-cache.php'); ?>">Clear Cache</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- Pages -->
                <li class="submenu">
                    <a href="#" class="<?php echo subdrop($page, ['starter.php','profile.php','gallery.php','search-result.php','timeline.php','pricing.php','coming-soon.php','under-maintenance.php','under-construction.php','api-keys.php','privacy-policy.php','terms-condition.php']); ?>">
                        <i class="ti ti-page-break"></i><span>Pages</span><span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="<?php echo active($page,'starter.php'); ?>"><a href="starter.php"><span>Starter</span></a></li>
                        <li class="<?php echo active($page,'profile.php'); ?>"><a href="profile.php"><span>Profile</span></a></li>
                        <li class="<?php echo active($page,'gallery.php'); ?>"><a href="gallery.php"><span>Gallery</span></a></li>
                        <li class="<?php echo active($page,'search-result.php'); ?>"><a href="search-result.php"><span>Search Results</span></a></li>
                        <li class="<?php echo active($page,'timeline.php'); ?>"><a href="timeline.php"><span>Timeline</span></a></li>
                        <li class="<?php echo active($page,'pricing.php'); ?>"><a href="pricing.php"><span>Pricing</span></a></li>
                        <li class="<?php echo active($page,'coming-soon.php'); ?>"><a href="coming-soon.php"><span>Coming Soon</span></a></li>
                        <li class="<?php echo active($page,'under-maintenance.php'); ?>"><a href="under-maintenance.php"><span>Under Maintenance</span></a></li>
                        <li class="<?php echo active($page,'under-construction.php'); ?>"><a href="under-construction.php"><span>Under Construction</span></a></li>
                        <li class="<?php echo active($page,'api-keys.php'); ?>"><a href="api-keys.php"><span>API Keys</span></a></li>
                        <li class="<?php echo active($page,'privacy-policy.php'); ?>"><a href="privacy-policy.php"><span>Privacy Policy</span></a></li>
                        <li class="<?php echo active($page,'terms-condition.php'); ?>"><a href="terms-condition.php"><span>Terms & Conditions</span></a></li>

                        <li class="submenu">
                            <a href="#"><span>Content</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="pages.php" class="<?php echo active($page,'pages.php'); ?>">Pages</a></li>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['blogs.php','blog-categories.php','blog-comments.php','blog-tags.php']); ?>">Blogs<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="blogs.php" class="<?php echo active($page,'blogs.php'); ?>">All Blogs</a></li>
                                        <li><a href="blog-categories.php" class="<?php echo active($page,'blog-categories.php'); ?>">Categories</a></li>
                                        <li><a href="blog-comments.php" class="<?php echo active($page,'blog-comments.php'); ?>">Comments</a></li>
                                        <li><a href="blog-tags.php" class="<?php echo active($page,'blog-tags.php'); ?>">Tags</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['countries.php','states.php','cities.php']); ?>">Locations<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="countries.php" class="<?php echo active($page,'countries.php'); ?>">Countries</a></li>
                                        <li><a href="states.php" class="<?php echo active($page,'states.php'); ?>">States</a></li>
                                        <li><a href="cities.php" class="<?php echo active($page,'cities.php'); ?>">Cities</a></li>
                                    </ul>
                                </li>
                                <li><a href="testimonials.php" class="<?php echo active($page,'testimonials.php'); ?>">Testimonials</a></li>
                                <li><a href="faq.php" class="<?php echo active($page,'faq.php'); ?>">FAQ’S</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="#"><span>Authentication</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="">Login<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="login.php" class="<?php echo active($page,'login.php'); ?>">Cover</a></li>
                                        <li><a href="login-2.php" class="<?php echo active($page,'login-2.php'); ?>">Illustration</a></li>
                                        <li><a href="login-3.php" class="<?php echo active($page,'login-3.php'); ?>">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="">Register<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="register.php" class="<?php echo active($page,'register.php'); ?>">Cover</a></li>
                                        <li><a href="register-2.php" class="<?php echo active($page,'register-2.php'); ?>">Illustration</a></li>
                                        <li><a href="register-3.php" class="<?php echo active($page,'register-3.php'); ?>">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">Forgot Password<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="forgot-password.php" class="<?php echo active($page,'forgot-password.php'); ?>">Cover</a></li>
                                        <li><a href="forgot-password-2.php" class="<?php echo active($page,'forgot-password-2.php'); ?>">Illustration</a></li>
                                        <li><a href="forgot-password-3.php" class="<?php echo active($page,'forgot-password-3.php'); ?>">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">Reset Password<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="reset-password.php" class="<?php echo active($page,'reset-password.php'); ?>">Cover</a></li>
                                        <li><a href="reset-password-2.php" class="<?php echo active($page,'reset-password-2.php'); ?>">Illustration</a></li>
                                        <li><a href="reset-password-3.php" class="<?php echo active($page,'reset-password-3.php'); ?>">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">Email Verification<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="email-verification.php" class="<?php echo active($page,'email-verification.php'); ?>">Cover</a></li>
                                        <li><a href="email-verification-2.php" class="<?php echo active($page,'email-verification-2.php'); ?>">Illustration</a></li>
                                        <li><a href="email-verification-3.php" class="<?php echo active($page,'email-verification-3.php'); ?>">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">2 Step Verification<span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="two-step-verification.php" class="<?php echo active($page,'two-step-verification.php'); ?>">Cover</a></li>
                                        <li><a href="two-step-verification-2.php" class="<?php echo active($page,'two-step-verification-2.php'); ?>">Illustration</a></li>
                                        <li><a href="two-step-verification-3.php" class="<?php echo active($page,'two-step-verification-3.php'); ?>">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="<?php echo active($page,'lock-screen.php'); ?>"><a href="lock-screen.php">Lock Screen</a></li>
                                <li class="<?php echo active($page,'error-404.php'); ?>"><a href="error-404.php">404 Error</a></li>
                                <li class="<?php echo active($page,'error-500.php'); ?>"><a href="error-500.php">500 Error</a></li>
                            </ul>
                        </li>

                        <!-- UI Interface -->
                        <li class="submenu">
                            <a href="#"><span>UI Interface</span><span class="menu-arrow"></span></a>
                            <ul>
                                <!-- Base UI -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, [
                                        'ui-alerts.php','ui-accordion.php','ui-avatar.php','ui-badges.php','ui-borders.php','ui-buttons.php','ui-buttons-group.php','ui-breadcrumb.php','ui-cards.php','ui-carousel.php',
                                        'ui-colors.php','ui-dropdowns.php','ui-grid.php','ui-images.php','ui-lightbox.php','ui-media.php','ui-modals.php','ui-offcanvas.php','ui-pagination.php','ui-popovers.php',
                                        'ui-progress.php','ui-placeholders.php','ui-spinner.php','ui-sweetalerts.php','ui-nav-tabs.php','ui-toasts.php','ui-tooltips.php','ui-typography.php','ui-video.php','ui-sortable.php','ui-swiperjs.php'
                                    ]); ?>">
                                        <i class="ti ti-hierarchy-2"></i>
                                        <span>Base UI</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="ui-alerts.php" class="<?php echo active($page,'ui-alerts.php'); ?>">Alerts</a></li>
                                        <li><a href="ui-accordion.php" class="<?php echo active($page,'ui-accordion.php'); ?>">Accordion</a></li>
                                        <li><a href="ui-avatar.php" class="<?php echo active($page,'ui-avatar.php'); ?>">Avatar</a></li>
                                        <li><a href="ui-badges.php" class="<?php echo active($page,'ui-badges.php'); ?>">Badges</a></li>
                                        <li><a href="ui-borders.php" class="<?php echo active($page,'ui-borders.php'); ?>">Border</a></li>
                                        <li><a href="ui-buttons.php" class="<?php echo active($page,'ui-buttons.php'); ?>">Buttons</a></li>
                                        <li><a href="ui-buttons-group.php" class="<?php echo active($page,'ui-buttons-group.php'); ?>">Button Group</a></li>
                                        <li><a href="ui-breadcrumb.php" class="<?php echo active($page,'ui-breadcrumb.php'); ?>">Breadcrumb</a></li>
                                        <li><a href="ui-cards.php" class="<?php echo active($page,'ui-cards.php'); ?>">Card</a></li>
                                        <li><a href="ui-carousel.php" class="<?php echo active($page,'ui-carousel.php'); ?>">Carousel</a></li>
                                        <li><a href="ui-colors.php" class="<?php echo active($page,'ui-colors.php'); ?>">Colors</a></li>
                                        <li><a href="ui-dropdowns.php" class="<?php echo active($page,'ui-dropdowns.php'); ?>">Dropdowns</a></li>
                                        <li><a href="ui-grid.php" class="<?php echo active($page,'ui-grid.php'); ?>">Grid</a></li>
                                        <li><a href="ui-images.php" class="<?php echo active($page,'ui-images.php'); ?>">Images</a></li>
                                        <li><a href="ui-lightbox.php" class="<?php echo active($page,'ui-lightbox.php'); ?>">Lightbox</a></li>
                                        <li><a href="ui-media.php" class="<?php echo active($page,'ui-media.php'); ?>">Media</a></li>
                                        <li><a href="ui-modals.php" class="<?php echo active($page,'ui-modals.php'); ?>">Modals</a></li>
                                        <li><a href="ui-offcanvas.php" class="<?php echo active($page,'ui-offcanvas.php'); ?>">Offcanvas</a></li>
                                        <li><a href="ui-pagination.php" class="<?php echo active($page,'ui-pagination.php'); ?>">Pagination</a></li>
                                        <li><a href="ui-popovers.php" class="<?php echo active($page,'ui-popovers.php'); ?>">Popovers</a></li>
                                        <li><a href="ui-progress.php" class="<?php echo active($page,'ui-progress.php'); ?>">Progress</a></li>
                                        <li><a href="ui-placeholders.php" class="<?php echo active($page,'ui-placeholders.php'); ?>">Placeholders</a></li>
                                        <li><a href="ui-spinner.php" class="<?php echo active($page,'ui-spinner.php'); ?>">Spinner</a></li>
                                        <li><a href="ui-sweetalerts.php" class="<?php echo active($page,'ui-sweetalerts.php'); ?>">Sweet Alerts</a></li>
                                        <li><a href="ui-nav-tabs.php" class="<?php echo active($page,'ui-nav-tabs.php'); ?>">Tabs</a></li>
                                        <li><a href="ui-toasts.php" class="<?php echo active($page,'ui-toasts.php'); ?>">Toasts</a></li>
                                        <li><a href="ui-tooltips.php" class="<?php echo active($page,'ui-tooltips.php'); ?>">Tooltips</a></li>
                                        <li><a href="ui-typography.php" class="<?php echo active($page,'ui-typography.php'); ?>">Typography</a></li>
                                        <li><a href="ui-video.php" class="<?php echo active($page,'ui-video.php'); ?>">Video</a></li>
                                        <li><a href="ui-sortable.php" class="<?php echo active($page,'ui-sortable.php'); ?>">Sortable</a></li>
                                        <li><a href="ui-swiperjs.php" class="<?php echo active($page,'ui-swiperjs.php'); ?>">Swiperjs</a></li>
                                    </ul>
                                </li>

                                <!-- Advanced UI -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['ui-ribbon.php','ui-clipboard.php','ui-drag-drop.php','ui-rangeslider.php','ui-rating.php','ui-text-editor.php','ui-counter.php','ui-scrollbar.php','ui-stickynote.php','ui-timeline.php']); ?>">
                                        <i class="ti ti-hierarchy-3"></i>
                                        <span>Advanced UI</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="ui-ribbon.php" class="<?php echo active($page,'ui-ribbon.php'); ?>">Ribbon</a></li>
                                        <li><a href="ui-clipboard.php" class="<?php echo active($page,'ui-clipboard.php'); ?>">Clipboard</a></li>
                                        <li><a href="ui-drag-drop.php" class="<?php echo active($page,'ui-drag-drop.php'); ?>">Drag & Drop</a></li>
                                        <li><a href="ui-rangeslider.php" class="<?php echo active($page,'ui-rangeslider.php'); ?>">Range Slider</a></li>
                                        <li><a href="ui-rating.php" class="<?php echo active($page,'ui-rating.php'); ?>">Rating</a></li>
                                        <li><a href="ui-text-editor.php" class="<?php echo active($page,'ui-text-editor.php'); ?>">Text Editor</a></li>
                                        <li><a href="ui-counter.php" class="<?php echo active($page,'ui-counter.php'); ?>">Counter</a></li>
                                        <li><a href="ui-scrollbar.php" class="<?php echo active($page,'ui-scrollbar.php'); ?>">Scrollbar</a></li>
                                        <li><a href="ui-stickynote.php" class="<?php echo active($page,'ui-stickynote.php'); ?>">Sticky Note</a></li>
                                        <li><a href="ui-timeline.php" class="<?php echo active($page,'ui-timeline.php'); ?>">Timeline</a></li>
                                    </ul>
                                </li>

                                <!-- Forms -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['form-basic-inputs.php','form-checkbox-radios.php','form-input-groups.php','form-grid-gutters.php','form-select.php','form-mask.php','form-fileupload.php','form-horizontal.php','form-vertical.php','form-floating-labels.php','form-validation.php','form-wizard.php','form-select2.php','form-pickers.php']); ?>">
                                        <i class="ti ti-input-search"></i>
                                        <span>Forms</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['form-basic-inputs.php','form-checkbox-radios.php','form-input-groups.php','form-grid-gutters.php','form-select.php','form-mask.php','form-fileupload.php']); ?>">
                                                Form Elements <span class="menu-arrow inside-submenu"></span>
                                            </a>
                                            <ul>
                                                <li><a href="form-basic-inputs.php" class="<?php echo active($page,'form-basic-inputs.php'); ?>">Basic Inputs</a></li>
                                                <li><a href="form-checkbox-radios.php" class="<?php echo active($page,'form-checkbox-radios.php'); ?>">Checkbox & Radios</a></li>
                                                <li><a href="form-input-groups.php" class="<?php echo active($page,'form-input-groups.php'); ?>">Input Groups</a></li>
                                                <li><a href="form-grid-gutters.php" class="<?php echo active($page,'form-grid-gutters.php'); ?>">Grid & Gutters</a></li>
                                                <li><a href="form-select.php" class="<?php echo active($page,'form-select.php'); ?>">Form Select</a></li>
                                                <li><a href="form-mask.php" class="<?php echo active($page,'form-mask.php'); ?>">Input Masks</a></li>
                                                <li><a href="form-fileupload.php" class="<?php echo active($page,'form-fileupload.php'); ?>">File Uploads</a></li>
                                            </ul>
                                        </li>

                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);" class="<?php echo subdrop($page, ['form-horizontal.php','form-vertical.php','form-floating-labels.php']); ?>">
                                                Layouts <span class="menu-arrow inside-submenu"></span>
                                            </a>
                                            <ul>
                                                <li><a href="form-horizontal.php" class="<?php echo active($page,'form-horizontal.php'); ?>">Horizontal Form</a></li>
                                                <li><a href="form-vertical.php" class="<?php echo active($page,'form-vertical.php'); ?>">Vertical Form</a></li>
                                                <li><a href="form-floating-labels.php" class="<?php echo active($page,'form-floating-labels.php'); ?>">Floating Labels</a></li>
                                            </ul>
                                        </li>

                                        <li><a href="form-validation.php" class="<?php echo active($page,'form-validation.php'); ?>">Form Validation</a></li>
                                        <li><a href="form-select2.php" class="<?php echo active($page,'form-select2.php'); ?>">Select2</a></li>
                                        <li><a href="form-wizard.php" class="<?php echo active($page,'form-wizard.php'); ?>">Form Wizard</a></li>
                                        <li><a href="form-pickers.php" class="<?php echo active($page,'form-pickers.php'); ?>">Form Pickers</a></li>
                                    </ul>
                                </li>

                                <!-- Tables -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['tables-basic.php','data-tables.php']); ?>">
                                        <i class="ti ti-table-plus"></i>
                                        <span>Tables</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="tables-basic.php" class="<?php echo active($page,'tables-basic.php'); ?>">Basic Tables</a></li>
                                        <li><a href="data-tables.php" class="<?php echo active($page,'data-tables.php'); ?>">Data Table</a></li>
                                    </ul>
                                </li>

                                <!-- Charts -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['chart-apex.php','chart-js.php','chart-morris.php','chart-flot.php','chart-peity.php','chart-c3.php']); ?>">
                                        <i class="ti ti-chart-line"></i>
                                        <span>Charts</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="chart-apex.php" class="<?php echo active($page,'chart-apex.php'); ?>">Apex Charts</a></li>
                                        <li><a href="chart-c3.php" class="<?php echo active($page,'chart-c3.php'); ?>">Chart C3</a></li>
                                        <li><a href="chart-js.php" class="<?php echo active($page,'chart-js.php'); ?>">Chart Js</a></li>
                                        <li><a href="chart-morris.php" class="<?php echo active($page,'chart-morris.php'); ?>">Morris Charts</a></li>
                                        <li><a href="chart-flot.php" class="<?php echo active($page,'chart-flot.php'); ?>">Flot Charts</a></li>
                                        <li><a href="chart-peity.php" class="<?php echo active($page,'chart-peity.php'); ?>">Peity Charts</a></li>
                                    </ul>
                                </li>

                                <!-- Icons -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['icon-fontawesome.php','icon-feather.php','icon-ionic.php','icon-material.php','icon-pe7.php','icon-simpleline.php','icon-themify.php','icon-weather.php','icon-typicon.php','icon-flag.php','icon-tabler.php','icon-bootstrap.php','icon-remix.php']); ?>">
                                        <i class="ti ti-icons"></i>
                                        <span>Icons</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="icon-fontawesome.php" class="<?php echo active($page,'icon-fontawesome.php'); ?>">Fontawesome Icons</a></li>
                                        <li><a href="icon-tabler.php" class="<?php echo active($page,'icon-tabler.php'); ?>">Tabler Icons</a></li>
                                        <li><a href="icon-bootstrap.php" class="<?php echo active($page,'icon-bootstrap.php'); ?>">Bootstrap Icons</a></li>
                                        <li><a href="icon-remix.php" class="<?php echo active($page,'icon-remix.php'); ?>">Remix Icons</a></li>
                                        <li><a href="icon-feather.php" class="<?php echo active($page,'icon-feather.php'); ?>">Feather Icons</a></li>
                                        <li><a href="icon-ionic.php" class="<?php echo active($page,'icon-ionic.php'); ?>">Ionic Icons</a></li>
                                        <li><a href="icon-material.php" class="<?php echo active($page,'icon-material.php'); ?>">Material Icons</a></li>
                                        <li><a href="icon-pe7.php" class="<?php echo active($page,'icon-pe7.php'); ?>">Pe7 Icons</a></li>
                                        <li><a href="icon-simpleline.php" class="<?php echo active($page,'icon-simpleline.php'); ?>">Simpleline Icons</a></li>
                                        <li><a href="icon-themify.php" class="<?php echo active($page,'icon-themify.php'); ?>">Themify Icons</a></li>
                                        <li><a href="icon-weather.php" class="<?php echo active($page,'icon-weather.php'); ?>">Weather Icons</a></li>
                                        <li><a href="icon-typicon.php" class="<?php echo active($page,'icon-typicon.php'); ?>">Typicon Icons</a></li>
                                        <li><a href="icon-flag.php" class="<?php echo active($page,'icon-flag.php'); ?>">Flag Icons</a></li>
                                    </ul>
                                </li>

                                <!-- Maps -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="<?php echo subdrop($page, ['maps-vector.php','maps-leaflet.php']); ?>">
                                        <i class="ti ti-table-plus"></i>
                                        <span>Maps</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="maps-vector.php" class="<?php echo active($page,'maps-vector.php'); ?>">Vector</a></li>
                                        <li><a href="maps-leaflet.php" class="<?php echo active($page,'maps-leaflet.php'); ?>">Leaflet</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Change Log</a></li>

                        <!-- Multi Level -->
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

            <div class="d-xl-flex align-items-center d-none">
                <a href="#" class="me-3 avatar avatar-sm">
                    <img src="{{ asset('assets3/img/profiles/avatar-07.jpg') }}" alt="profile" class="rounded-circle">
                </a>
                <a href="#" class="btn btn-icon btn-sm rounded-circle mode-toggle">
                    <i class="ti ti-sun"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- /Horizontal Menu -->