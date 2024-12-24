<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    // Attributes that should be cast to native types
    protected $casts = [
        'RegisterDate' => 'datetime',
    ];

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
