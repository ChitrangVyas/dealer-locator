<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Inquiry;
class OrderInquiries extends Model
{
    use HasFactory;
    protected $table = 'order_inquiries';
    protected $guarded = [];

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function inquiry(){
        return $this->belongsTo(Inquiry::class);        
    }
}
