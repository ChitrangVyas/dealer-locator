<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductTax extends Model
{
    use HasFactory;
    protected $table = 'order_product_tax';
    protected $guarded = [];

    public function orderProduct(){
        return $this->belongsTo(OrderProduct::class,'order_product_id');
    }

    public function tax(){
        return $this->belongsTo(Tax::class);
    }
}
