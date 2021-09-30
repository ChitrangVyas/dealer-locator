<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'manufacturer_id',
        'logo'
    ];
    public function manufacturer(){
        return $this->belongsTo(Manufacturer::class);
    }

    public function product(){
        return $this->hasMany(Product::class);
    }
}
