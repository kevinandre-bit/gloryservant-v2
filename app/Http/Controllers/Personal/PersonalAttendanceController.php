<?php
/*
* Workday - A time clock application for employees
* Email: official.codefactor@gmail.com
* Version: 1.1
* Author: 
* Copyright 
*/
namespace App\Http\Controllers\Personal;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PersonalAttendanceController extends Controller
{
    public function index() 
    {
        $ref = \Auth::user()->reference;
        $a = table::attendance()->where('reference', $ref)->get();
        $tf = table::settings()->value("time_format");

        return view('personal.personal-attendance-view', compact('a', 'tf'));
    }

    public function getPA(Request $request) 
    {
		$ref = \Auth::user()->reference;
		$datefrom = $request->datefrom;
		$dateto = $request->dateto;
		
        if($datefrom == '' || $dateto == '' ) 
        {
             $data = table::attendance()
             ->select('date', 'timein', 'timeout', 'totalhours', 'status_timein', 'status_timeout')
             ->where('reference', $ref)
             ->get();
             
			return response()->json($data);

		} elseif ($datefrom !== '' AND $dateto !== '') {
            $data = table::attendance()
            ->select('date', 'timein', 'timeout', 'totalhours', 'status_timein', 'status_timeout')
            ->where('reference', $ref)
            ->whereBetween('date', [$datefrom, $dateto])
            ->get();

			return response()->json($data);
        }
	}
}

