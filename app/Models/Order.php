<?php

namespace App\Models;

use App\Models\Inquiry;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function inquiry(){
        return $this->belongsTo(Inquiry::class);
    }

    public function customer(){
        return $this->belongsTo(User::class,'to_user','id');
        /* if($this->type == 'po'){            
            if($this->getUser($this->from_user)->users_group_id == 2 && (!empty($this->for_manufacturer))){
                // return $this->belongsTo(Manufacturer::class,'to_user','id');
            } else {
                return $this->belongsTo(User::class,'to_user','id');
            }
        }else{
            return $this->belongsTo(User::class,'to_user','id');
        } */
    }

    public function addedBy(){
        // if($this->type == 'po'){
        //     return $this->belongsTo(User::class,'from_user','id');
        // }else{
            return $this->belongsTo(User::class,'from_user','id');
        // }
    }

    public function toUser(){
        $user = User::where('id','=',$this->to_user)->first();
        return (!empty($user))?$user->name:'';
        /* if($this->type == 'po' && $this->getUser($this->from_user)->users_group_id == 2 && (!empty($this->for_manufacturer))){
            // $user = Manufacturer::where('id','=',$this->to_user)->first();
            // return $user->name;
        } else {
            $user = User::where('id','=',$this->to_user)->first();
            return $user->name;
        } */
    }
    public function getUser($user_id){
        return User::findOrfail($user_id);
    }

    public function getFromUser(){
        if(!empty($this->customer)){
            return User::where('id','=',$this->to_user)->first();
        }else{
            dd($this->settingJson);
        }
    }


    public function orderProduct(){
        return $this->hasMany(OrderProduct::class);
    }

    public function orderImage(){
        return $this->hasMany(OrderImage::class);
    }
    // public function products(){
    //     return $this->belongsToMany(Product::class)->withPivot(['qty','amount'])->withTimestamps();
    // }

    public function orderStatus(){
        return $this->hasMany(OrderStatus::class);
    }

    public function getOrderStatus(){
        return [1 => 'Pendding', 2 => 'Paid'];
    }

    public function getUserOrderStatus()
    {
       return OrderStatus::where('order_id',$this->id)->where('user_id', (!empty(auth()->user()->users_group_id))?auth()->user()->id:NULL)->get();
    }   
    public function getInvoiceStatus($invoice_status){
        return (!empty($invoice_status))?$this->getOrderStatus()[$invoice_status]:'N/A';
    }

    // $order->getInvoiceStatus($order->getUserOrderStatus()[0]->status_id)
}
