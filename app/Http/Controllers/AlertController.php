<?php
namespace App\Http\Controllers;

namespace App\Http\Controllers\admin;
use DB;
use App\Classes\table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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