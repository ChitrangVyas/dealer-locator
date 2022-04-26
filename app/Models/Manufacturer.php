<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Manufacturer extends Model
{
    use HasFactory;
    use SoftDeletes;
    /* protected $fillable = [
        'name',
        'description',
        'short_description',
        'logo',
        'email',
        'manufacturer_id',        
    ]; */

    protected $guarded = [];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
    public function state(){
        return $this->belongsTo(State::class);
    }
    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function getMobileWithCountryCode(){
        if(!empty($this->mobile))
        return $this->country->phonecode." ".$this->mobile;
    else
        return "";
    }
}
