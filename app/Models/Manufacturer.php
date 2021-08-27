<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Manufacturer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'logo',
        'email',
        'manufacturer_id',        
    ];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }
}
