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

   public function products()
   {
       return $this->belongsToMany(Product::class,'product_category');
   }
}
