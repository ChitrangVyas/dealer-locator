<?php

namespace App\Models;

use App\Models\Admin\Order;
use App\Models\Admin\OrderProduct;
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
        'inventory',
        'price',
    ];
    protected $dates = ['deleted_at'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class,'product_category')->withTimestamps();
    }

    public function inquiries(){
        return $this->belongsToMany(Inquiry::class,'inquiries_products');
    }

    public function orders(){
        return $this->belongsToMany(Order::class);
    }

}
