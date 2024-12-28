<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AitRegistrationController extends Controller
{
    public function showForm()
    {
        // $states = DB::table('state')->where('status', 1)->get();
        // return view('ait.add-user', compact('states'));
        $states = DB::table('logi_state_detail')
        ->where('status', 0)
        ->orderBy('state_name')
        ->get();
        return view('ait.add-user', compact('states'));
    }

    public function getDistricts(Request $request)
    {
        $districts = DB::table('logi_district_detail')
            ->select('id', 'district_name')
            ->where('state_id', $request->state_id)
            ->where('duplicate_entry', 0)
            ->orderBy('district_name', 'asc')
            ->get();

        return response()->json($districts);
    }

   
    //this function work the sales person record and return
    public function getSalesPersons(Request $request)
    {
        $salesPersons = DB::table('logi_area_mapping')
            ->join('logi_users', 'logi_area_mapping.sub_fre_id', '=', 'logi_users.id')
            ->select(
                DB::raw('MIN(logi_area_mapping.id) as id'),
                'logi_users.StaffName',
                'logi_users.LoginID',
                DB::raw("CONCAT(logi_users.StaffName, '(', logi_users.LoginID, ')') as name")
            )
            ->where('logi_area_mapping.district_id', $request->district_id)
            ->where('logi_area_mapping.status', 0)
            ->groupBy('logi_users.StaffName', 'logi_users.LoginID')
            ->get();
    
        $formattedSalesPersons = $salesPersons->map(function($person) {
            return [
                'id' => $person->id,
                'name' => $person->StaffName . '(' . $person->LoginID . ')'
            ];
        });
    
        return response()->json($formattedSalesPersons);
    }
    
    

    public function getTehsils(Request $request)
    {
        $tehsils = DB::table('tehsils')
            ->where('district_id', $request->district_id)
            ->where('status', 1)
            ->distinct()
            ->get(['id', 'name']);

        return response()->json($tehsils);
    }

    public function submitForm(Request $request)
    {
        $loginId = 'AIT' . rand(100000, 999999); // Example login ID generation
        DB::table('logi_users')->insert($request->all());
        return response()->json(['login_id' => $loginId]);
    }
}
