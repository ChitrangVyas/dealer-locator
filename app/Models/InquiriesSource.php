<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InquiriesSource extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'inquiries_source';
    protected $fillable = [
        'source'        
    ];

    function inquiry(){
        return $this->belongsToMany(Inquiry::class);
    }
}
