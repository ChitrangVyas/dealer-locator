<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'image',
        'parent_category',
        'description',       
    ];

   public function dealerCategory()
   {
       return $this->belongsToMany(User::class,DealerCategory::class,'category_id','dealer_id');
   }
}
