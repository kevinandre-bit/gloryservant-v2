<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin_v2 Routes
|--------------------------------------------------------------------------
| All the SmartHR HTML pages converted into Blade templates
| will be accessible under the /admin_v2 URL prefix.
| Example: http://127.0.0.1:8000/admin_v2/dashboard
|
*/

use App\Http\Controllers\AdminV2\DepartmentController;

Route::middleware(['auth','admin'])->prefix('admin_v2')->name('admin_v2.')->group(function () {
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
    Route::get('/departments/json', [DepartmentController::class, 'json'])->name('departments.json');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::delete('/departments', [DepartmentController::class, 'destroyMany'])->name('departments.destroy-many');
});

use App\Http\Controllers\AdminV2\CampusController;

Route::middleware(['auth','admin'])->prefix('admin_v2')->name('admin_v2.')->group(function () {
    Route::get('/campus', [CampusController::class, 'index'])->name('campus');
    Route::get('/campus/json', [CampusController::class, 'json'])->name('campus.json');
    Route::post('/campus', [CampusController::class, 'store'])->name('campus.store');
    Route::put('/campus/{campus}', [CampusController::class, 'update'])->name('campus.update');
    Route::delete('/campus/{campus}', [CampusController::class, 'destroy'])->name('campus.destroy');
    Route::delete('/campus', [CampusController::class, 'destroyMany'])->name('campus.destroy-many');
});

use App\Http\Controllers\AdminV2\MinistryController;

Route::middleware(['auth','admin'])->prefix('admin_v2')->name('admin_v2.')->group(function () {
    Route::get('/ministry', [MinistryController::class, 'index'])->name('ministry');
    Route::get('/ministry/json', [MinistryController::class, 'json'])->name('ministry.json');
    Route::post('/ministry', [MinistryController::class, 'store'])->name('ministry.store');
    Route::put('/ministry/{ministry}', [MinistryController::class, 'update'])->name('ministry.update');
    Route::delete('/ministry/{ministry}', [MinistryController::class, 'destroy'])->name('ministry.destroy');
    Route::delete('/ministry', [MinistryController::class, 'destroyMany'])->name('ministry.destroy-many');
});

// routes/web.php
use App\Http\Controllers\AdminV2\ScheduleTimingController;

Route::middleware(['auth','admin'])->prefix('admin_v2')->name('admin_v2.')->group(function () {
    Route::get('/schedule-timing',  [ScheduleTimingController::class,'index'])->name('schedule.index');
    Route::post('/schedule-timing', [ScheduleTimingController::class,'store'])->name('schedule.store');

    // JSON payload to prefill the modal
    Route::get('/schedule-timing/{schedule}/json', [ScheduleTimingController::class,'showJson'])
        ->name('schedule.show');

    // Update + Delete
    Route::put('/schedule-timing/{schedule}',    [ScheduleTimingController::class,'update'])->name('schedule.update');
    Route::delete('/schedule-timing/{schedule}', [ScheduleTimingController::class,'destroy'])->name('schedule.destroy');
});

use App\Http\Controllers\AdminV2\EmployeeController;

Route::middleware(['auth','admin'])->prefix('admin_v2')->name('admin_v2.')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::get('/employees/json', [EmployeeController::class, 'json'])->name('employees.json');

    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{person}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{person}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/employee-details/{person?}', fn() => view('admin_v2.employee-details'))->name('employee-details');
    Route::get('/employees-grid', fn() => view('admin_v2.employees-grid'))->name('employees-grid');
});


Route::middleware(['auth'])->prefix('admin_v2')->name('admin_v2.')->group(function () {

    $pages = [
        'activity',
        'add-invoices',
        'add-language',
        'ai-settings',
        'analytics',
        'api-keys',
        'appearance',
        'approval-settings',
        'aptitude-result',
        'asset-categories',
        'assets',
        'attendance-admin',
        'attendance-employee',
        'attendance-report',
        'authentication-settings',
        'backup',
        'ban-ip-address',
        'blog-2',
        'blog-categories',
        'blog-comments',
        'blog-tags',
        'blogs',
        'budget-expenses',
        'budget-revenues',
        'budgets',
        'bussiness-settings',
        'calendar',
        'call',  
        'call-history',
        'candidates-grid',
        'candidates-kanban',
        'candidates',
        'categories',
        'chart-apex',
        'chart-c3',
        'chart-flot',
        'chart-js',
        'chart-morris',
        'chart-peity',
        'chat',
        'cities',
        'clear-cache',
        'client-details',
        'clients-grid',
        'clients',
        'coming-soon',
        'companies-crm',
        'companies-grid',
        'companies',
        'company-details',
        'connected-apps',
        'contact-details',
        'contacts-grid',
        'contacts',
        'countries',
        'cronjob-schedule',
        'cronjob',
        'currencies',
        'custom-css',
        'custom-fields',
        'custom-js',
        'daily-report',
        'dashboard',
        'data-tables',
        'deals-dashboard',
        'deals-details',
        'deals-grid',
        'deals',
        'designations',
        'domain',
        'edit-invoices',
        'email-reply',
        'email-settings',
        'email-template',
        'email-verification-2',
        'email-verification-3',
        'email-verification',
        'email',
        'employee-dashboard',
        'employee-details',
        'employee-report',
        'employee-salary',
        'employees-grid',
        'error-404',
        'error-500',
        'estimates',
        'expenses-report',
        'expenses',
        'experience-level',
        'faq',
        'file-manager',
        'forgot-password-2',
        'forgot-password-3',
        'forgot-password',
        'form-basic-inputs',
        'form-checkbox-radios',
        'form-elements',
        'form-fileupload',
        'form-floating-labels',
        'form-grid-gutters',
        'form-horizontal',
        'form-input-groups',
        'form-mask',
        'form-pickers',
        'form-select',
        'form-select2',
        'form-validation',
        'form-vertical',
        'form-wizard',
        'gallery',
        'gdpr',
        'gdpr-cookies',
        'goal-tracking',
        'goal-type',
        'group-video-call',
        'holidays',
        'icon-bootstrap',
        'icon-feather',
        'icon-flag',
        'icon-fontawesome',
        'icon-ionic',
        'icon-material',
        'icon-pe7',
        'icon-remix',
        'icon-simpleline',
        'icon-tabler',
        'icon-themify',
        'icon-typicon',
        'icon-weather',
        'incoming-call',
        'interview-questions',
        'invoice-details',
        'invoice-report',
        'invoice-settings',
        'invoice',
        'invoices',
        'job-details',
        'job-grid-2',
        'job-grid',
        'job-list-2',
        'job-list',
        'kanban-view',
        'knowledgebase-details',
        'knowledgebase-view',
        'knowledgebase',
        'language-web',
        'language',
        'layout-box',
        'layout-dark',
        'layout-detached',
        'layout-horizontal-box',
        'layout-horizontal-fullwidth',
        'layout-horizontal-overlay',
        'layout-horizontal-sidemenu',
        'layout-horizontal-single',
        'layout-horizontal',
        'layout-hovered',
        'layout-modern',
        'layout-rtl',
        'layout-stacked',
        'layout-two-column',
        'layout-vertical-transparent',
        'layout-without-header',
        'leads-dashboard',
        'leads-details',
        'leads-grid',
        'leads',
        'leave-report',
        'leave-settings',
        'leave-type',
        'leaves-employee',
        'leaves',
        'localization-settings',
        'lock-screen',
        'login-2',
        'login-3',
        'login',
        'maintenance-mode',
        'manage-jobs',
        'maps-leaflet',
        'maps-vector',
        'notes',
        'notification-settings',
        'offer-approvals',
        'otp-settings',
        'outgoing-call',
        'overtime',
        'packages-grid',
        'packages',
        'pages',
        'payment-gateways',
        'payment-report',
        'payments',
        'payroll-deduction',
        'payroll-overtime',
        'payroll',
        'payslip-report',
        'payslip',
        'performance-appraisal',
        'performance-indicator',
        'performance-review',
        'permission',
        'pipeline',
        'plugin',
        'policy',
        'preferences',
        'prefixes',
        'pricing',
        'privacy-policy',
        'profile-settings',
        'profile',
        'project-details',
        'project-report',
        'projects-grid',
        'projects',
        'promotion',
        'provident-fund',
        'purchase-transaction',
        'refferals',
        'register-2',
        'register-3',
        'register',
        'reset-password-2',
        'reset-password-3',
        'reset-password',
        'resignation',
        'roles-permissions',
        'salary-settings',
        'search-result',
        'security-settings',
        'seo-settings',
        'shortlist-candidates',
        'sms-settings',
        'sms-template',
        'social-feed',
        'starter',
        'states',
        'storage-settings',
        'subscription',
        'success-2',
        'success-3',
        'success',
        'tables-basic',
        'task-board',
        'task-details',
        'task-report',
        'tasks',
        'tax-rates',
        'taxes',
        'termination',
        'terms-condition',
        'testimonials',
        'ticket-details',
        'tickets-grid',
        'tickets',
        'timeline',
        'timesheets',
        'todo-list',
        'todo',
        'trainers',
        'training-type',
        'training',
        'two-step-verification-2',
        'two-step-verification-3',
        'two-step-verification',
        'ui-accordion',
        'ui-alerts',
        'ui-avatar',
        'ui-badges',
        'ui-borders',
        'ui-breadcrumb',
        'ui-buttons-group',
        'ui-buttons',
        'ui-cards',
        'ui-carousel',
        'ui-clipboard',
        'ui-colors',
        'ui-counter',
        'ui-drag-drop',
        'ui-dropdowns',
        'ui-grid',
        'ui-images',
        'ui-lightbox',
        'ui-media',
        'ui-modals',
        'ui-nav-tabs',
        'ui-offcanvas',
        'ui-pagination',
        'ui-placeholders',
        'ui-popovers',
        'ui-progress',
        'ui-rangeslider',
        'ui-rating',
        'ui-ribbon',
        'ui-scrollbar',
        'ui-sortable',
        'ui-spinner',
        'ui-stickynote',
        'ui-sweetalerts',
        'ui-swiperjs',
        'ui-text-editor',
        'ui-timeline',
        'ui-toasts',
        'ui-tooltips',
        'ui-typography',
        'ui-video',
        'under-construction',
        'under-maintenance',
        'user-report',
        'users',
        'video-call',
        'voice-call',
    ];

    // Default landing page: /admin_v2 -> admin_v2.index
    Route::view('/', 'admin_v2.index')->name('index');

    foreach ($pages as $page) {
        Route::view("/{$page}", "admin_v2.{$page}")->name($page);
    }
});
