<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Myfarmer;
use Illuminate\Support\Facades\DB;
use App\Models\Village;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\FarmerVillage;
use Exception;

class ApiController extends Controller
{
    public function loginAitAction(Request $request)
    {
        // Set memory limit
        ini_set('memory_limit', '500M');

        // Default values and validation
        $validated = $request->validate([
            'LoginID' => 'required|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'device_id' => 'required|string',
            'device_type' => 'nullable|string',
            'fire_base_id' => 'nullable|string',
            'ver' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = new User();
            $userData = $user->getAITDataByLoginId($validated['LoginID']);

            if (!$userData) {
                throw new \Exception('Invalid login id.');
            }

            // Role validation
            if (in_array($userData['Role'], ['franchise', 'subfranchise'])) {
                throw new \Exception('Invalid login id.');
            }

            // Device authorization
            if (!empty($userData['device_id']) && $userData['device_id'] != $validated['device_id']) {
                throw new \Exception('You are not authorized for this device.');
            }

            // Account status check
            if (strtolower($userData['StaffStatus']) != 'ac') {
                throw new \Exception('Your account is not active.');
            }

            // Admin deactivation check
            if ($userData['App_Access_Status'] == '2') {
                throw new \Exception('This account deactivated by admin.');
            }

            // Update user data
            DB::table('logi_users')
                ->where('LoginID', $userData['LoginID'])
                ->update([
                    'latitude' => $validated['latitude'] ?? '0.0',
                    'longitude' => $validated['longitude'] ?? '0.0',
                    'device_id' => $validated['device_id'],
                    'device_type' => $validated['device_type'] ?? 'android',
                    'device_token' => Str::random(32),
                    'gps_status' => 'on',
                    'Attendance_time' => now(),
                    'ver' => $validated['ver'] ?? '',
                    'fire_base_id' => $validated['fire_base_id'] ?? ''
                ]);

            // Get all required data
            $productDetails = $user->getAITProductDetails($userData['stateID'], $userData['id']);
            $updatedUserData = $user->getAITDataByLoginId($validated['LoginID']);
            $farmerVillages = $user->getFarmerVillage($userData['id']);
            $brandList = $user->getBrandList();

            // Get manual URLs
            $manual = DB::table('tbl_menual')
                ->where('type', 'manual')
                ->orderByDesc('id')
                ->first();

            $userManual = DB::table('tbl_menual')
                ->where('type', 'user_manual')
                ->orderByDesc('id')
                ->first();

            DB::commit();

            return response()->json([
                'error_code' => '0',
                'response_string' => 'login success.',
                'Get_all_user_data' => $updatedUserData,
                'Get_all_farmer_village' => $farmerVillages,
                'Get_all_product_detail' => $productDetails,
                'brand_list' => $brandList,
                'manual_url' => $manual ? url("uploads/manual/{$manual->attachment}") : null,
                'user_manual_url' => $userManual ? url("uploads/manual/{$userManual->attachment}") : null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 1,
                'response_string' => $e->getMessage()
            ], 400);
        }
    }

    //Famer Registration
    public function farmerRegister(Request $request)
    {
        $params = $request->all();

        $appKey             = $params['appkey'] ?? "";
        $loginID            = $params['LoginID'] ?? "AIT0023653";
        $deviceID           = $params['device_id'] ?? "";
        $farmerName         = $params['farmer_name'] ?? "";
        $mobileNo           = $params['mobile_no'] ?? "";
        $farmerState        = $params['farmer_state'] ?? "";
        $farmerVillage      = $params['farmer_village'] ?? "";
        $villageName        = $params['village_name'] ?? "";
        $longitude          = $params['longitude'] ?? "";
        $latitude           = $params['latitude'] ?? "";
        $whatsappMobileNo   = $params['ait_et_whatsapp'] ?? "";
        $landlineNo         = $params['landline_no'] ?? null;

        $errorCode = 1;
        $success = false;

        DB::beginTransaction();

        try {
            // Validation for required fields
            if (!$loginID || !$deviceID) {
                throw new \Exception("Required parameter missing.");
            }

            // Validate User
            $user = User::where('LoginID', $loginID)->first();

            if (!$user || $appKey != $user->device_token) {
                throw new \Exception("Invalid appkey.");
            }

            if ($user->LoginID != $loginID) {
                throw new \Exception("Invalid login id.");
            }

            if ($user->DeviceID && $user->DeviceID != $deviceID) {
                $errorCode = 3;
                throw new \Exception("You are not authorized for this device.");
            }

            if ($user->App_Access_Status == 2) {
                $errorCode = 2;
                throw new \Exception("This account is deactivated by admin.");
            }

            // Generate Farmer Code
            $lastFarmer = Myfarmer::latest('id')->first();
            $farmerCode = $lastFarmer ? 'FAR0000' . ($lastFarmer->id + 1) : 'FAR00001';

            // Handle village creation
            if ($farmerVillage == '0003') {
                $village = FarmerVillage::create([
                    'village' => $villageName,
                    'ait_id'  => $user->id,
                ]);
                $villageId = $village->id;
            } else {
                $villageId = $farmerVillage;
            }

            // Prepare farmer data
            $farmerData = [
                'FarmerName'         => $farmerName,
                'FarmerCode'         => $farmerCode,
                'MobileNo'           => $mobileNo,
                'stateId'            => $farmerState,
                'villageId'          => $villageId,
                'longitude'          => $longitude,
                'latitude'           => $latitude,
                'ait_id'             => $user->id,
                'RegisterDate'       => now(),
                'whatsapp_mobile_no' => $whatsappMobileNo,
                'landline_no'        => $landlineNo,
            ];

            // Handle farmer image upload
            if ($request->hasFile('farmer_img')) {
                $image = $request->file('farmer_img');
                $imageName = 'farmer-' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/farmer_img', $imageName, 'public');
                $farmerData['farmer_img'] = $imageName;
            }

            // Create farmer entry
            $farmer = Myfarmer::create($farmerData);
            $formattedServerIDForFarmer = 'AI#' . $farmer->id;

            DB::commit();

            $success = true;

        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();
        }

        // Return response
        if ($success) {
            $response = [
                'error_code' => 0,
                'response_string' => 'Farmer Register Successfully.',
                'serverGeneratedId' => $formattedServerIDForFarmer,
                'FarmerCode' => $farmerCode,
            ];

            if ($farmerVillage == '0003') {
                $response['villageId'] = $villageId;
                $response['villageName'] = $villageName;
            }

            return response()->json($response);
        }

        return response()->json([
            'error_code' => $errorCode,
            'response_string' => $error ?? 'Unknown error occurred.',
        ]);
    }


    //Update Farmer Update
    public function updateFarmerRecord(Request $request)
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'appkey' => 'required|string',
            'LoginID' => 'required|string',
            'device_id' => 'required|string',
            'Id' => 'required|numeric',
            'farmer_name' => 'required|string',
            'mobile_no' => 'required|string',
            'farmer_state' => 'required',
            'farmer_village' => 'required',
            'village_name' => 'required_if:farmer_village,0003',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'ait_et_whatsapp' => 'nullable|string',
            'landline_no' => 'nullable|string',
            'farmer_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 1,
                'response_string' => $validator->errors()->first()
            ], 400);
        }

        $errorCode = 1;
        $success = false;

        DB::beginTransaction();

        try {
            // Validate User
            $user = User::where('LoginID', $request->LoginID)->first();

            if (!$user || $request->appkey != $user->device_token) {
                throw new \Exception("Invalid appkey.");
            }

            if ($user->App_Access_Status == 2) {
                $errorCode = 2;
                throw new \Exception("This account deactivated by admin.");
            }

            // Find farmer
            $farmer = Myfarmer::find($request->Id);
            if (!$farmer) {
                throw new \Exception("Farmer not found.");
            }

            // Handle village
            $villageId = $request->farmer_village;
            if ($request->farmer_village == '0003') {
                $village = FarmerVillage::create([
                    'village' => $request->village_name,
                    'ait_id' => $user->id,
                ]);
                $villageId = $village->id;
            }

            // Prepare farmer data
            $farmerData = [
                'FarmerName' => $request->farmer_name,
                'MobileNo' => $request->mobile_no,
                'stateId' => $request->farmer_state,
                'villageId' => $villageId,
                'longitude' => $request->longitude ?? '',
                'latitude' => $request->latitude ?? '',
                'whatsapp_mobile_no' => $request->ait_et_whatsapp ?? '',
                'landline_no' => $request->landline_no,
            ];

            // Handle image upload
            if ($request->hasFile('farmer_img')) {
                // Delete old image if exists
                if ($farmer->farmer_img) {
                    Storage::disk('public')->delete('uploads/farmer_img/' . $farmer->farmer_img);
                }

                $image = $request->file('farmer_img');
                $imageName = 'farmer-' . time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('uploads/farmer_img', $imageName, 'public');
                $farmerData['farmer_img'] = $imageName;
            }

            // Update farmer
            $farmer->update($farmerData);

            DB::commit();
            $success = true;

            $response = [
                'error_code' => 0,
                'response_string' => 'Farmer record updated successfully.',
                'serverGeneratedId' => $farmer->id,
                'FarmerCode' => $farmer->FarmerCode,
            ];

            if ($request->farmer_village == '0003') {
                $response['villageId'] = $villageId;
                $response['villageName'] = $request->village_name;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => $errorCode,
                'response_string' => $e->getMessage()
            ], 400);
        }
    }
}