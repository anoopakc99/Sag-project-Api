<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
}