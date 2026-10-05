<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Inquiry;
class DealerCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'dealer_category';
    protected $guarded = [];

    public function dealer(){
        return $this->hasMany(User::class);
    }
    public function category(){
        return $this->hasMany(Category::class);        
    }
}
