<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductDiscount;
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
        'status',
        'tax_group_id',
        'hsn_sac',
        'installation_place',
        'max_pipe_size_and_water_flow'
    ];
    protected $dates = ['deleted_at'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    public function getDiscountPrice($price,$discount){
        $final_amount = $price - (($price*$discount)/100);
        return $final_amount;
    }
    public function DiscountProduct(){
        return $this->hasOne(ProductDiscount::class);
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
        $user_id = auth()->user()->id;
        if((empty(auth()->user()->users_group_id))|| auth()->user()->users_group_id ==1){
            $user_id = NULL;
        }
        return $this->hasMany(Inventory::class)->where('user_id',$user_id);
    }

    public function getInventory()
    {
        if((empty(auth()->user()->users_group_id))|| auth()->user()->users_group_id ==1){
            return (empty($this->productInventory->toArray()) && count($this->productInventory->toArray()) < 1 && (empty($this->productInventory->where('user_id',NULL)->toArray()))) ? 0 : $this->productInventory->where('user_id',NULL)->first();
        }else{
            return (empty($this->productInventory->toArray()) && count($this->productInventory->toArray()) < 1 && (empty($this->productInventory->where('user_id',auth()->user()->id)->toArray()))) ? 0 : $this->productInventory->where('user_id',auth()->user()->id)->first();
        }
    }

    public function taxGroup(){
        return $this->belongsTo(TaxGroup::class,'tax_group_id');
    }
    public function getDiscountedMRP($seller_id,$buyer_id){
        $ifDiscounted =  ProductDiscount::where('seller_id',$seller_id)->where('buyer_id', $buyer_id)->where('product_id', $this->id)->first() ;
        if($ifDiscounted){
            return  $this->price - (($this->price*$ifDiscounted->discount_percentage)/100);
        }        
        return $this->price;
    }

}
