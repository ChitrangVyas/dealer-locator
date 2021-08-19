<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InquiryLog;
use Illuminate\Support\Facades\DB;

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

    public function log(){
        return $this->hasMany(InquiryLog::class);
    }
    public function getLogByDate($date){
        $logs = InquiryLog::where(DB::raw("(DATE_FORMAT(for_created_at,'%Y-%m-%d'))"),'=',$date)->where('inquiry_id','=',$this->id)->get();        
        return $logs;
    }
}
