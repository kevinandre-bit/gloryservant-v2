<?php

namespace App\Classes;

use DB;
use App\Classes\permission;

Class table {

	public static function people() 
	{
    	$people = DB::table('tbl_people');
		if (permission::hasFullAccess()) {
			return $people;
		}

		$scope = permission::getScope();
		$scopeData = permission::getScopeData();

		if ($scopeData && $scope !== 'all') {
			$people->whereExists(function($q) use ($scope, $scopeData) {
				$q->select(DB::raw(1))->from('tbl_campus_data')
				  ->whereColumn('tbl_campus_data.reference', 'tbl_people.id')
				  ->when($scopeData['campus'], fn($q, $v) => $q->where('tbl_campus_data.campus', $v))
				  ->when($scope === 'ministry' && $scopeData['ministry'], fn($q, $v) => $q->where('tbl_campus_data.ministry', $v))
				  ->when($scope === 'department' && $scopeData['department'], fn($q, $v) => $q->where('tbl_campus_data.department', $v));
			});
		}

    	return $people;
  	}

	public static function campusdata() 
	{
    	$campusdata = DB::table('tbl_campus_data');
		if (permission::hasFullAccess()) {
			return $campusdata;
		}

		$scope = permission::getScope();
		$scopeData = permission::getScopeData();

		if ($scopeData && $scope !== 'all') {
			if ($scopeData['campus']) {
				$campusdata->where('campus', $scopeData['campus']);
			}
			if ($scope === 'ministry' && $scopeData['ministry']) {
				$campusdata->where('ministry', $scopeData['ministry']);
			}
			if ($scope === 'department' && $scopeData['department']) {
				$campusdata->where('department', $scopeData['department']);
			}
		}

    	return $campusdata;
  	}

	public static function attendance() 
	{
    	$attendance = DB::table('tbl_people_attendance');
    	if (!permission::hasFullAccess()) {
			$scope = permission::getScope();
			$scopeData = permission::getScopeData();
			if ($scopeData && $scope !== 'all') {
				$attendance->whereExists(function($q) use ($scope, $scopeData) {
					$q->select(DB::raw(1))->from('tbl_campus_data')
					  ->whereColumn('tbl_campus_data.reference', 'tbl_people_attendance.reference')
					  ->when($scopeData['campus'], fn($q, $v) => $q->where('tbl_campus_data.campus', $v))
					  ->when($scope === 'ministry' && $scopeData['ministry'], fn($q, $v) => $q->where('tbl_campus_data.ministry', $v))
					  ->when($scope === 'department' && $scopeData['department'], fn($q, $v) => $q->where('tbl_campus_data.department', $v));
				});
			} else if (!$scopeData) {
				// If user has no scope data, they should see nothing
				$attendance->whereRaw('1=0');
			}
    	}
    	return $attendance;
  	}

	public static function leaves() 
	{
    	$leaves = DB::table('tbl_people_leaves');
    	if (!permission::hasFullAccess()) {
			$scope = permission::getScope();
			$scopeData = permission::getScopeData();
			if ($scopeData && $scope !== 'all') {
				$leaves->whereExists(function($q) use ($scope, $scopeData) {
					$q->select(DB::raw(1))->from('tbl_campus_data')
					  ->whereColumn('tbl_campus_data.reference', 'tbl_people_leaves.reference')
					  ->when($scopeData['campus'], fn($q, $v) => $q->where('tbl_campus_data.campus', $v))
					  ->when($scope === 'ministry' && $scopeData['ministry'], fn($q, $v) => $q->where('tbl_campus_data.ministry', $v))
					  ->when($scope === 'department' && $scopeData['department'], fn($q, $v) => $q->where('tbl_campus_data.department', $v));
				});
			} else if (!$scopeData) {
				// If user has no scope data, they should see nothing
				$leaves->whereRaw('1=0');
			}
    	}
    	return $leaves;
  	}

	public static function schedules() 
	{
    	$schedules = DB::table('tbl_people_schedules');
    	if (!permission::hasFullAccess()) {
			$scope = permission::getScope();
			$scopeData = permission::getScopeData();
			if ($scopeData && $scope !== 'all') {
				$schedules->whereExists(function($q) use ($scope, $scopeData) {
					$q->select(DB::raw(1))->from('tbl_campus_data')
					  ->whereColumn('tbl_campus_data.reference', 'tbl_people_schedules.reference')
					  ->when($scopeData['campus'], fn($q, $v) => $q->where('tbl_campus_data.campus', $v))
					  ->when($scope === 'ministry' && $scopeData['ministry'], fn($q, $v) => $q->where('tbl_campus_data.ministry', $v))
					  ->when($scope === 'department' && $scopeData['department'], fn($q, $v) => $q->where('tbl_campus_data.department', $v));
				});
			} else if (!$scopeData) {
				// If user has no scope data, they should see nothing
				$schedules->whereRaw('1=0');
			}
    	}
    	return $schedules;
  	}

  	public static function notifications() 
	{
    	$notifications = DB::table('notifications');
    	return $notifications;
  	}

  	public static function notification_targets() 
	{
    	$notification_targets = DB::table('notification_targets');
    	return $notification_targets;
  	}

	public static function reportviews() 
	{
    	$reportviews = DB::table('tbl_report_views');
    	return $reportviews;
  	}

	public static function permissions() 
	{
    	$permissions = DB::table('users_permissions');
    	return $permissions;
  	}

	public static function roles() 
	{
    	$roles = DB::table('users_roles');
    	return $roles;
  	}

	public static function users() 
	{
    	$users = DB::table('users')->select('id', 'reference', 'idno', 'name', 'email', 'role_id', 'acc_type', 'status');
    	return $users;
  	}
    public static function campusdata2() 
    {
        $campusdata2 = DB::table('tbl_campus_data')->select('id','reference','campus','ministry','jobposition','campusemail','idno','startdate','dateregularized','reason','leaveprivilege','work_type');
        return $campusdata2;
    }

	public static function campus() 
	{
    	$campus = DB::table('tbl_form_campus');
    	return $campus;
  	}

public static function peopleWithcampus()
{
    return DB::table('tbl_people')
        ->leftJoin('tbl_campus_data', 'tbl_people.id', '=', 'tbl_campus_data.reference')
        ->leftJoin('tbl_form_campus', 'tbl_campus_data.campus', '=', 'tbl_form_campus.id')
        ->select(
            'tbl_people.*',
            'tbl_campus_data.campus as campus_id',
            'tbl_form_campus.name as campus_name'
        );
}
	
	public static function radiostation() 
	{
    	$radiostation = DB::table('radio_station');
    	return $radiostation;
  	}

  	public static function radiopocs() 
	{
    	$radiopocs = DB::table('radio_pocs');
    	return $radiopocs;
  	}

  	public static function radiotechnician() 
	{
    	$radiotechnician = DB::table('radio_technician');
    	return $radiotechnician;
  	}

  	public static function htdepartements() 
	{
    	$htdepartements = DB::table('ht_departements');
    	return $htdepartements;
  	}

	public static function ministry() 
	{
    	$ministry = DB::table('tbl_form_ministry');
    	return $ministry;
  	}

  	public static function department() 
	{
    	$department = DB::table('tbl_form_department');
    	return $department;
  	}

	public static function jobtitle() 
	{
    	$jobtitle = DB::table('tbl_form_jobtitle');
    	return $jobtitle;
  	}

	public static function leavetypes() 
	{
    	$leavetypes = DB::table('tbl_form_leavetype');
    	return $leavetypes;
	}

	public static function leavegroup() 
	{
		$leavegroup = DB::table('tbl_form_leavegroup');
		return $leavegroup;
	}
	
	public static function meeting_attendance() 
	{
		$leavegroup = DB::table('meeting_attendance');
		return $leavegroup;
	}
	   
	public static function settings() 
	{
    	$settings = DB::table('settings');
    	return $settings;
  	}

	public static function creativeRequests() 
	{
    	$requests = DB::table('tbl_creative_requests');
    	if (!permission::hasFullAccess()) {
    		$scopeData = permission::getScopeData();
    		if ($scopeData) {
    			$requests->where('campus_id', $scopeData['campus']);
    		}
    	}
    	return $requests;
  	}

	public static function creativeTasks() 
	{
    	$tasks = DB::table('tbl_creative_tasks');
    	if (!permission::hasFullAccess()) {
    		$scopeData = permission::getScopeData();
    		if ($scopeData) {
    			$tasks->whereExists(function($q) use ($scopeData) {
    				$q->select(DB::raw(1))->from('tbl_creative_requests')
    				  ->whereColumn('tbl_creative_requests.id', 'tbl_creative_tasks.request_id')
    				  ->where('tbl_creative_requests.campus_id', $scopeData['campus']);
    			});
    		}
    	}
    	return $tasks;
  	}

}
