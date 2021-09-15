<?php

namespace App\Models;

use App\Models\Order;
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

    public function productInventory(){
        return $this->hasMany(Inventory::class)->where('user_id',auth()->user()->id);
    }

    public function getInventory()
    {
        if((empty(auth()->user()->users_group_id))|| auth()->user()->users_group_id ==1){
            return $this->inventory;
        }else{
            return (empty($this->productInventory->toArray()) && count($this->productInventory->toArray()) < 1 && (empty($this->productInventory->where('user_id',auth()->user()->id)->toArray()))) ? 0 : $this->productInventory->where('user_id',auth()->user()->id)->first()->qty;
        }
    }

}
