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
        $tehsils = DB::table('logi_area_mapping')
            ->join('logi_theshil_detail', 'logi_area_mapping.tehshil_id', '=', 'logi_theshil_detail.id')
            ->join('logi_users', 'logi_area_mapping.sub_fre_id', '=', 'logi_users.id')
            ->where('logi_area_mapping.district_id', $request->district_id)
            ->where('logi_area_mapping.status', 0)
            ->where(function ($query) use ($request) {
                if ($request->filled('sales_person')) {
                    $query->where('logi_users.LoginID', $request->sales_person);
                }
            })
            ->select(
                'logi_area_mapping.tehshil_id as id',
                'logi_theshil_detail.tehshil_name as name'
            )
            ->groupBy('logi_area_mapping.tehshil_id', 'logi_theshil_detail.tehshil_name')
            ->orderBy('logi_theshil_detail.tehshil_name')
            ->get();

        return response()->json($tehsils);
    }

    
//Ait Registraion new user generate the AIT data for the current user
    public function submitAITRegistration(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'mobile_no' => 'required',
                'whatsapp_no' => 'required',
                'address' => 'required',
                'state' => 'required',
                'district' => 'required',
                'tehsil' => 'required',
                'sales_person' => 'required',
                'brand' => 'required',
                'breed' => 'required',
                'monthly_ai' => 'required',
                'container_type' => 'required',
            ]);

            // Check for existing user
            $existingUser = DB::table('logi_users')
                ->where('MobileNo', $request->mobile_no)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mobile number already registered with login ID: ' . $existingUser->LoginID
                ], 422);
            }

            // Generate user data
            $lastId = DB::table('logi_users')->max('id') + 1;
            $loginId = 'AIT00' . $lastId;

            $userData = [
                'StaffName' => strtoupper($request->name),
                'Email' => $request->email,
                'MobileNo' => $request->mobile_no,
                'Address_Curr' => $request->address,
                'Address_Per' => $request->address,
                'Role' => 'AIT',
                'franchise_id' => $request->sales_person,
                'Password' => '123456',
                'under' => 'subfranchise',
                'Gender' => 'M',
                'mapped_state' => $request->state,
                'mapped_district' => $request->district,
                'mapped_theshil' => $request->tehsil,
                'mapped_block' => $request->tehsil,
                'mapped_village' => $request->tehsil,
                'brand' => $request->brand,
                'breed' => $request->breed,
                'monthly_ai' => $request->monthly_ai,
                'container_type' => $request->container_type,
                'stateID' => $request->state,
                'ait_et_whatsappphone' => $request->whatsapp_no,
                'LoginID' => $loginId,
                'rendom_no' => $lastId . '01001',
            ];

            DB::table('logi_users')->insert($userData);

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful!',
                'login_id' => $loginId
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
