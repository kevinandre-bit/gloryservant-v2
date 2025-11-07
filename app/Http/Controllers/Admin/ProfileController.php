<?php

namespace App\Http\Controllers\Admin;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    public function view($id, Request $request)
{ 
    // make sure it's numeric
    $id = (int) $id;

    if (permission::permitted('employees-view') == 'fail') {
        return redirect()->route('denied');
    }

    // tbl_people has 'id' (not 'reference')
    $p = table::people()->where('id', $id)->first();
    if (!$p) {
        abort(404, 'Employee not found'); 
    }

    $u = table::users()->where('reference', $id)->first();
    $c = table::campusdata()->where('reference', $id)->first();
    $i = table::people()->where('id', $id)->value('avatar');

    $leavetype  = table::leavetypes()->get();
    $leavegroup = table::leavegroup()->get();

    $e_id = $p->id ? Crypt::encryptString($p->id) : 0;

    $campus_details = table::campusdata()->where('reference', $id)->first();
    $person_details  = table::people()->where('id', $id)->first();

    $campus    = table::campus()->get();
    $department    = table::department()->get();
    $ministry = table::ministry()->get();
    $jobtitle   = table::jobtitle()->get();

    return view('admin.profile-view', compact(
        'p','c','i','leavetype','leavegroup','u','e_id',
        'campus_details', 'person_details','campus','department', 'ministry'
    ));
}

   	public function clear(Request $request)
{

    if (permission::permitted('employees-delete') == 'fail') {
        return redirect()->route('denied');
    }

    $id = (int) $request->id;

    // 1) Verify using the same helper you use everywhere else
    $person = table::people()->where('id', $id)->first();

    if (!$person) {
        // Fallback probe: if your actual table name differs, uncomment one of these to test quickly.
        // $person = DB::table('people')->where('id', $id)->first();
        // $person = DB::table('tbl_people')->where('id', $id)->first();

        if (!$person) {
            // Optional: \Log::warning("Delete attempted for missing employee id={$id}");
            return back()->with('error', __("Employee not found or already deleted."));
        }
    }
    try {
        $counts = ['attendance'=>0,'schedules'=>0,'leaves'=>0,'users'=>0,'campusdata'=>0,'people'=>0];

        DB::transaction(function () use ($id, &$counts) {
            // 2) Delete children FIRST (adjust column names if yours differ)
            $counts['attendance'] = table::attendance()->where('reference', $id)->delete();
            $counts['schedules']  = table::schedules()->where('reference', $id)->delete();
            $counts['leaves']     = table::leaves()->where('reference', $id)->delete();
            $counts['users']      = table::users()->where('reference', $id)->delete();
            $counts['campusdata'] = table::campusdata()->where('reference', $id)->delete();

            // 3) Delete the parent LAST
            $counts['people']     = table::people()->where('id', $id)->delete();
        });

        if ($counts['people'] > 0) {
            // Optional: \Log::info('Delete counts', $counts);
            return redirect('employees')->with('success', __("Employee information has been deleted!"));
        }

        // Optional: \Log::warning('Delete had no effect', $counts);
        return back()->with('error', __("Nothing was deleted. Check table/column names and DB connection."));
    } catch (\Throwable $e) {
        // \Log::error('Delete failed', ['id'=>$id, 'err'=>$e->getMessage()]);
        return back()->with('error', __("Delete failed. Please try again."));
    }
}

   	public function archive($id, Request $request)
    {
		if (permission::permitted('employees-archive')=='fail'){ return redirect()->route('denied'); }

		$id = $request->id;
		table::people()->where('id', $id)->update(['employmentstatus' => 'Archived']);
		table::users()->where('reference', $id)->update(['status' => '0']);

    	return redirect('employees')->with('success', trans("Employee information has been archived!"));
   	}

	public function editPerson($id)
    {
		if (permission::permitted('employees-edit')=='fail'){ return redirect()->route('denied'); }

		$campus_details = table::campusdata()->where('id', $id)->first();
		$person_details = table::people()->where('id', $id)->first();
		$campus = table::campus()->get();
		$ministry = table::ministry()->get();
		$jobtitle = table::jobtitle()->get();
		$leavegroup = table::leavegroup()->get();
		$e_id = ($person_details->id == null) ? 0 : Crypt::encryptString($person_details->id) ;

        return view('admin.edits.edit-personal-info', compact('campus_details', 'person_details', 'campus', 'ministry', 'jobtitle', 'leavegroup', 'e_id'));
    }

    public function updatePerson(Request $request)
    {
		if (permission::permitted('employees-edit')=='fail'){ return redirect()->route('denied'); }

		$v = $request->validate([
			'id' => 'required|max:200',
			'lastname' => 'required|alpha_dash_space|max:155',
			'firstname' => 'required|alpha_dash_space|max:155',
			// 'mi' => 'required|alpha_dash_space|max:155',
			// 'age' => 'required|digits_between:0,199|max:3',
			// 'gender' => 'required|alpha|max:155',
			'emailaddress' => 'required|email|max:155',
			// 'civilstatus' => 'required|alpha|max:155',
			// 'temperament' => 'required|digits_between:0,299|max:3',
			// 'love_language' => 'required|digits_between:0,999|max:3',
			// 'mobileno' => 'required|max:155',
			// 'birthday' => 'required|date|max:155',
			// 'nationalid' => 'required|max:155',
			// 'birthplace' => 'required|max:255',
			// 'homeaddress' => 'required|max:255',
			// 'campus' => 'required|alpha_dash_space|max:100',
			// 'ministry' => 'required|alpha_dash_space|max:100',
			// 'jobposition' => 'required|alpha_dash_space|max:100',
			// 'corporate_email' => 'required|email|max:155',
			// 'leaveprivilege' => 'required|max:155',
			'idno' => 'required|max:155',
			// 'employmenttype' => 'required|alpha_dash_space|max:155',
			'employmentstatus' => 'required|alpha_dash_space|max:155',
			// 'startdate' => 'required|date|max:155',
			// 'dateregularized' => 'required|date|max:155'
		]);

		$id = Crypt::decryptString($request->id);
		$lastname = mb_strtoupper($request->lastname);
		$firstname = mb_strtoupper($request->firstname);
		$mi = mb_strtoupper($request->mi);
		$age = $request->age;
		$gender = mb_strtoupper($request->gender);
		$emailaddress =  mb_strtolower($request->emailaddress);
		$civilstatus = mb_strtoupper($request->civilstatus);
		$temperament = $request->temperament;
		$love_language = $request->love_language;
		$mobileno = $request->mobileno;
		$birthday = date("Y-m-d", strtotime($request->birthday));
		$nationalid = mb_strtoupper($request->nationalid);
		$birthplace = mb_strtoupper($request->birthplace);
		$homeaddress = mb_strtoupper($request->homeaddress);
		$department = mb_strtoupper($request->department);
		$campus = mb_strtoupper($request->campus);
		$ministry = mb_strtoupper($request->ministry);
		$jobposition = mb_strtoupper($request->jobposition);
		$corporate_email = mb_strtolower($request->corporate_email);
		$leaveprivilege = $request->leaveprivilege;
		$idno = mb_strtoupper($request->idno);
		$employmenttype = $request->employmenttype;
		$employmentstatus = $request->employmentstatus;
		$startdate = date("Y-m-d", strtotime($request->startdate));
		$dateregularized = date("Y-m-d", strtotime($request->dateregularized));

        // Validate and handle avatar upload (images only, small size)
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $file = $request->file('image');
        if ($file) {
            $name = 'avatar_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('assets2/faces');
            if (!is_dir($destinationPath)) { @mkdir($destinationPath, 0755, true); }
            $file->move($destinationPath, $name);
        } else {
            $name = table::people()->where('id', $id)->value('avatar');
        }
		
		table::people()->where('id', $id)->update([
			'lastname' => $lastname,
			'firstname' => $firstname,
			'mi' => $mi,
			'age' => $age,
			'gender' => $gender,
			'emailaddress' => $emailaddress,
			'civilstatus' => $civilstatus,
			'temperament' => $temperament,
			'love_language' => $love_language,
			'mobileno' => $mobileno,
			'birthday' => $birthday,
			'birthplace' => $birthplace,
			'nationalid' => $nationalid,
			'homeaddress' => $homeaddress,
			'employmenttype' => $employmenttype,
			'employmentstatus' => $employmentstatus,
			'avatar' => $name,
		]);

		table::campusdata()->where('reference', $id)->update([
			'campus' => $campus,
			'department' => $department,
			'ministry' => $ministry,
			'jobposition' => $jobposition,
			'corporate_email' => $corporate_email,
			'leaveprivilege' => $leaveprivilege,
			'idno' => $idno,
			'startdate' => $startdate,
			'dateregularized' => $dateregularized,
		]);
		
    	// on success
		return redirect('profile-view-'.$id)
		       ->with('success', 'Employee information has been updated!');

		// on custom error
		return redirect('profile-view-'.$id)
		       ->with('error', 'Something went wrong while updating the profile.');
	}

	public function viewProfile(Request $request) 
	{
		$id = \Auth::user()->id;
		$myuser = table::users()->where('id', $id)->first();
		$myrole = table::roles()->where('id', $myuser->role_id)->value('role_name');

		return view('admin.update-profile', compact('myuser', 'myrole'));
	}

	public function viewPassword() 
	{
		return view('admin.update-password');
	}

	public function updateUser(Request $request) 
	{

		$v = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:100',
		]);
		
		$id = \Auth::id();
		$name = mb_strtoupper($request->name);
		$email = mb_strtolower($request->email);

		if($id == null) 
        {
            return redirect('personal/update-user')->with('error', trans("Whoops! Please fill the form completely."));
		}
		
		table::users()->where('id', $id)->update([
			'name' => $name,
			'email' => $email,
		]);

		return redirect('update-profile')->with('success', trans("Updated!"));
	}

	public function updatePassword(Request $request) 
	{

		$v = $request->validate([
            'currentpassword' => 'required|max:100',
            'newpassword' => 'required|min:8|max:100',
            'confirmpassword' => 'required|min:8|max:100',
		]);

		$id = \Auth::id();
		$p = \Auth::user()->password;
		$c_password = $request->currentpassword;
		$n_password = $request->newpassword;
		$c_p_password = $request->confirmpassword;

		if($id == null) 
        {
            return redirect('personal/update-user')->with('error', trans("Whoops! Please fill the form completely."));
		}

		if($n_password != $c_p_password) 
		{
			return redirect('update-password')->with('error', trans("New password does not match!"));
		}

		if(Hash::check($c_password, $p)) 
		{
			table::users()->where('id', $id)->update([
				'password' => Hash::make($n_password),
			]);

			return redirect('update-password')->with('success', trans("Updated!"));
		} else {
			return redirect('update-password')->with('error', trans("Oops! current password does not match."));
		}
	}


} 
