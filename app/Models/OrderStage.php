<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStage extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    protected $table = 'order_stages';
    public $timestamps = true;

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
