<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'brand_id',
        'description',
        'image',
        'logo',
        'inventory'
    ];
    protected $dates = ['deleted_at'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class,'product_category')->withTimestamps();
    }

}
