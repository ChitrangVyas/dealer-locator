<?php

namespace App\Models;

use App\Models\Inquiry;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function inquiry(){
        return $this->belongsTo(Inquiry::class);
    }

    public function customer(){
        if($this->type == 'po'){
            return $this->belongsTo(User::class,'from_user','id');
        }else{
            return $this->belongsTo(User::class,'to_user','id');
        }
    }

    public function addedBy(){
        return $this->belongsTo(User::class,'from_user','id');
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
}
