<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'mobile_no', 'landline_no', 'state_id', 'route_id', 'village_id',
        'pincode', 'hamlet', 'longitude', 'latitude', 'gender', 'dob', 'image_path'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
