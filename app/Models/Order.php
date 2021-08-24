<?php

namespace App\Models\Admin;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function inquiry(){
        return $this->belongsTo(Inquiry::class);
    }
}
