<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InquiryLog;
use Illuminate\Support\Facades\DB;

class Inquiry extends Model
{
    use HasFactory;
    use SoftDeletes;
    /* protected $fillable = [
        'inquiries_source_id',
        'product_id',
        'customer_id',
        'dealer_id',
    ]; */
    protected $guarded = [];
    
    public function customer(){
        return $this->belongsTo(User::class,'customer_id');
    }        

    public function dealer(){
        return $this->belongsTo(User::class,'dealer_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function retailer(){
        return $this->belongsTo(User::class,'retailer_id');
    }

    public function inquirySource(){
        return $this->belongsTo(InquiriesSource::class,'inquiries_source_id');
    }

    public function product(){
        return $this->belongsToMany(Product::class,'inquiries_products')->withTimestamps();
    }

    public function log(){
        return $this->hasMany(InquiryLog::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function getLogByDate($date){
        $logs = InquiryLog::where(DB::raw("(DATE_FORMAT(for_created_at,'%Y-%m-%d'))"),'=',$date)->where('inquiry_id','=',$this->id)->get();        
        return $logs;
    }
    public function getInquiryStatus(){
        return [1 => "New", 2 => "In Progress", 3 => "Attempted to Contact", 4 => "Connected", 5 => "Open Quote"];
    }
    public function getInquirystatusName(){
        return $this->getInquiryStatus()[$this->status];
    }
    
}
