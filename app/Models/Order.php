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
    }
    public function getUser($user_id){
        return User::findOrfail($user_id);
    }

    public function getFromUser(){
        if(!empty($this->customer)){
            return User::where('id','=',$this->to_user)->first();
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
    public function orderStage(){
        return $this->hasOne(OrderStage::class);
    }

    public function getOrderStatus(){
        return [1 => 'Pending', 2 => 'Paid'];
    }

    public function getOrderStage(){
        return [0=>"Pending", 1=>"Processing", '4'=>"Dispatched" , 2=>"Ready for Installation", 3=>"Installed",'5'=>'Closed'];
    }

    public function getUserOrderStatus()
    {
       return OrderStatus::where('order_id',$this->id)->where('user_id', (!empty(auth()->user()->users_group_id))?auth()->user()->id:NULL)->first() ;
    }   
    public function getInvoiceStatus($invoice_status){
        return (!empty($invoice_status))?$this->getOrderStatus()[$invoice_status]:'N/A.';
    }

    public function getSalesStage($order_stage){
        return (isset($order_stage))?$this->getOrderStage()[$order_stage]:'N/A';
    }

    public function getSalesOrderStage(){
        return OrderStage::where('order_id',$this->id)->get();
    }

    public function getTotalTax(){
        $total_tax = [];
        $total_tax = $this->select('taxes.name', DB::raw('SUM(order_product_tax.price) as total_tax'))
        ->join('order_product', 'orders.id', '=', 'order_product.order_id')
        ->join('order_product_tax', 'order_product.id', '=', 'order_product_tax.order_product_id')
        ->join('taxes', 'order_product_tax.tax_id', '=', 'taxes.id')
        ->where('orders.id', $this->id)
        ->groupBy('order_product_tax.tax_id')
        ->get();
        if(!empty($total_tax)){
            return $total_tax;
        }else{
            return '';
        }
    }

    public function getSubTotal(){
        $sub_total = [];
         $sub_total = $this->select(DB::raw('SUM((order_product.amount * order_product.qty) - order_product.discount)  AS sub_total'))->leftJoin('order_product', 'orders.id', '=', 'order_product.order_id')->where('orders.id', $this->id)
        ->get()->first()->toArray();
        if(!empty($sub_total)){
            return $sub_total;
        }else{
             return ['sub_total' => 0.00];
        }
    }    
    public function inquiries(){
        return $this->hasMany(OrderInquiries::class);
    }
    public function getInquiries(){
        return DB::table('inquiries')
        ->selectRaw('inquiries.id,users.name,inquiries.created_at')        
        ->join('users', 'users.id', '=', 'inquiries.customer_id')
        ->join('order_inquiries', 'inquiries.id', '=', 'order_inquiries.inquiry_id')
        ->where('order_inquiries.order_id', $this->id)
        ->get();   
    }
    // $order->getInvoiceStatus($order->getUserOrderStatus()[0]->status_id)
}
