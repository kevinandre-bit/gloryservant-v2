<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use DB;
use App\Classes\table;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function create()
    {
        $users = table::people()->get();
        $campuses = table::campus()->get();
    	$ministries = table::ministry()->get();
        $roles = table::users_roles()->get();

        return view('admin.send-alert', compact('users', 'campuses', 'ministries', 'roles'));
    }
}