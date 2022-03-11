<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OrderImage extends Model
{
    use HasFactory;
    protected $table = 'order_image';
    protected $guarded = [];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function getSrc(){
        // $icon =
        $allowed_images = ['webp','jpg','png','jpeg'];
        $allowed_docs = ['pdf','doc','docx','xls','xlsx','txt'];
        $file_extention = \File::extension($this->image);
        $fileName = basename(url($this->image));
        if(in_array($file_extention,$allowed_images)){
            return ['title'=>$fileName, 'file_path'=>$this->image,'src'=>$this->image];
        }elseif(in_array($file_extention,$allowed_docs)){
            return ['title'=>$fileName,'file_path'=>$this->image,'src'=>'/assets/img/file.png'];
        }
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
