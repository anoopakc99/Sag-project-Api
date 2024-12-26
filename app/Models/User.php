<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'logi_users';
    
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'Attendance_time' => 'datetime',
    ];

    protected $fillable = [
        'latitude',
        'longitude',
        'device_id',
        'device_type',
        'device_token',
        'gps_status',
        'Attendance_time',
        'ver',
        'fire_base_id',
    ];

    public function getAITDataByLoginId($loginId)
    {
        $userData = DB::table('logi_users')
            ->select(
                'id',
                'LoginID',
                'WebLoginID',
                'Password',
                'StaffName',
                'Email',
                'MobileNo',
                'land_line_phone',
                'StaffStatus',
                'ver',
                'stateID',
                'routeId',
                'villageId',
                'DistrictID',
                'pin_code',
                'DOB',
                'Gender',
                'Address_Curr',
                'Address_Per',
                'city',
                'postal_code',
                'latitude',
                'longitude',
                'parent_id',
                'franchise_id',
                'device_type',
                'device_token',
                'device_id',
                'operator_no',
                'operator_name',
                'App_Access_Status',
                'Last_location_service_hit_time',
                'gps_status',
                'Attendance_time',
                'ParentWebLoginID',
                'mapped_state',
                'mapped_district',
                'ait_et_whatsappphone',
                'mapped_theshil',
                'mapped_block',
                'mapped_village',
                'brand',
                'breed',
                'monthly_ai',
                'container_type',
                'land_mark',
                'Role',
                'Permission',
                'assign_product',
                'similar_franchise_product',
                'under',
                'rendom_no',
                'added_date',
                'local_generated_id',
                'fire_base_id',
                'notification_status',
                'Created'
            )
            ->where('LoginID', $loginId)
            ->first();
    
        if ($userData) {
            $data = (array)$userData;
            $data['mapped_state'] = $data['stateID'];
            return $data;
        }
    
        return null;
    }
    

    public function getAITProductDetails($stateId, $id)
    {
        try {
            // Get user and parent data
            $user = DB::table('logi_users')
                ->where('id', $id)
                ->first();

            if (!$user || !$stateId) {
                throw new \Exception('Invalid user or state ID');
            }

            $userParent = null;
            if ($user->franchise_id) {
                $userParent = DB::table('logi_users')
                    ->where('id', $user->franchise_id)
                    ->first();
            }

            // Get state info from state table
            $stateInfo = DB::table('state')
                ->where('id', $stateId)
                ->where('status', 1)
                ->first();

            if (!$stateInfo) {
                return [];
            }

            // Get categories for the state
            $categories = DB::table('logi_sub_franchise_state as sp')
                ->leftJoin('category as c', 'c.id', '=', 'sp.cat_id')
                ->where('sp.state_id', $stateId)
                ->where('sp.status', 1)
                ->select('sp.cat_id', 'c.name', 'c.species')
                ->distinct()
                ->orderBy('c.name')
                ->get();

            $aitList = [];
            
            if ($categories->isNotEmpty()) {
                $categoryArray = [];

                foreach ($categories as $category) {
                    $subcategories = DB::table('logi_sub_franchise_state as sp')
                        ->join('subcategory as sub', 'sub.id', '=', 'sp.sub_id')
                        ->where([
                            'sp.state_id' => $stateId,
                            'sp.cat_id' => $category->cat_id,
                            'sp.status' => 1,
                            'sub.deleted' => 1
                        ])
                        ->select('sub.id', 'sub.name', 'sub.description', 'sub.image')
                        ->distinct()
                        ->orderBy('sub.name')
                        ->get();

                    $subcategoryArray = [];

                    foreach ($subcategories as $subcategory) {
                        // Get rate based on user role and parent
                        $rate = ($userParent && $userParent->Role === 'franchise')
                            ? $this->getFranchiseRate($category->cat_id, $subcategory->id, $user->franchise_id, $stateId)
                            : $this->getSubFranchiseRate($category->cat_id, $subcategory->id, $user->franchise_id, $stateId);

                        $subcategoryArray[] = [
                            'sub_id' => $subcategory->id,
                            'subcategory_name' => $subcategory->name,
                            'rate' => $rate,
                            'subcategory_desc' => $subcategory->description,
                            'subcategory_img' => url('/uploads/platinum_img/' . $subcategory->image)
                        ];
                    }

                    if (!empty($subcategoryArray)) {
                        $categoryArray[] = [
                            'cat_id' => $category->cat_id,
                            'category_name' => $category->name,
                            'species' => $category->species,
                            'subcategory_list' => $subcategoryArray
                        ];
                    }
                }

                $aitList[] = [
                    'state_id' => $stateInfo->id,
                    'state_name' => $stateInfo->name,
                    'state_code' => strtoupper(substr($stateInfo->name, 0, 3)),
                    'category_list' => $categoryArray
                ];
            }

            return $aitList;

        } catch (\Exception $e) {
            \Log::error('AIT Product Details Error: ' . $e->getMessage());
            return [];
        }
    }

    // Helper methods remain the same
    private function getFranchiseRate($catId, $subId, $franchiseId, $stateId)
    {
        return DB::table('logi_franchise_state')
            ->where([
                'cat_id' => $catId,
                'sub_id' => $subId,
                'f_id' => $franchiseId,
                'state_id' => $stateId,
                'status' => 1
            ])
            ->value('rate') ?? 0;
    }

    private function getSubFranchiseRate($catId, $subId, $franchiseId, $stateId)
    {
        return DB::table('logi_sub_franchise_state')
            ->where([
                'cat_id' => $catId,
                'sub_id' => $subId,
                'sub_f_id' => $franchiseId,
                'state_id' => $stateId,
                'status' => 1
            ])
            ->value('rate') ?? 0;
    }

    public function getFarmerVillage($aitId)
    {
        try {
            return DB::table('logi_farmer_village')
                ->select('id', 'village')
                ->where('ait_id', $aitId)
                ->where('status', 1)
                ->orderBy('village', 'asc')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Farmer Village Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getBrandList()
    {
        try {
            return DB::table('logi_brand')
                ->where('status', 1)
                ->orderBy('brand_name')
                ->get();
        } catch (\Exception $e) {
            \Log::error('Brand List Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAITData(Request $request)
    {
        $loginId = $request->input('LoginID');

        if (!$loginId) {
            return response()->json([
                'error_code' => 1,
                'response_string' => 'LoginID is required'
            ], 400);
        }

        $user = new User();
        $data = $user->getAITDataByLoginId($loginId);

        if (!$data) {
            return response()->json([
                'error_code' => 1,
                'response_string' => 'User not found'
            ], 404);
        }

        return response()->json([
            'error_code' => 0,
            'response_string' => 'Success',
            'data' => $data
        ]);
    }
}