<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Myfarmer;
use App\Models\FarmerVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Myfarmer extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'logi_my_farmers';

    // Primary key of the table
    protected $primaryKey = 'id';

    public $timestamps = false;

    // Attributes that are mass assignable
    protected $fillable = [
        'FarmerName',
        'FarmerCode',
        'MobileNo',
        'stateId',
        'villageId',
        'longitude',
        'latitude',
        'ait_id',
        'RegisterDate',
        'whatsapp_mobile_no',
        'farmer_img',
        'id',
        'village',
    ];
    
    // Attributes that should be cast to native types
    protected $casts = [
        'RegisterDate' => 'datetime',
    ];

    public function getFarmerDetailById($farmerId)
    {
        return DB::table('logi_my_farmers as lf')
            ->select(
                'lf.*',
                'vill.village',
                DB::raw('DATE(lf.RegisterDate) as registerDate'),
                'lfs.name as state',
                'ait.StaffName as ait_name',
                'r.route'
            )
            ->leftJoin('logi_farmer_village as vill', 'lf.villageId', '=', 'vill.id')
            ->leftJoin('logi_users as ait', 'lf.ait_id', '=', 'ait.id')
            ->leftJoin('state as lfs', 'lf.stateId', '=', 'lfs.id')
            ->leftJoin('logi_franchise_route as r', 'r.id', '=', 'lf.routeId')
            ->where('lf.id', '=', DB::raw("UNHEX('" . $farmerId . "')"))
            ->where('lf.flag', '=', 0)
            ->orderByDesc('lf.id')
            ->first();
    }
    // Relationship with the FarmerVillage model
    public function village()
    {
        return $this->belongsTo(FarmerVillage::class, 'villageId', 'id');
    }

    // Relationship with the User (AIT) model
    public function user()
    {
        return $this->belongsTo(User::class, 'ait_id', 'id');
    }

    // Utility to generate farmer codes
    public static function generateFarmerCode($lastId)
    {
        return $lastId ? 'FAR0000' . $lastId : 'FAR00001';
    }

    // Scope for searching farmers by name
    public function scopeSearchByName($query, $name)
    {
        return $query->where('FarmerName', 'LIKE', "%$name%");
    }
}
