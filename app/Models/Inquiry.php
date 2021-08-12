<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'inquiries_source_id',
        'product_id',
        'customer_id',
        'dealer_id',
    ];
    public function customer(){
        return $this->belongsTo(User::class);
    }
    public function dealer(){
        return $this->belongsTo(User::class);
    }
    public function source(){
        return $this->belongsTo(InquiriesSource::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
