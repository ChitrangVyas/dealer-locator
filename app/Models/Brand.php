<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'description',
        'short_description',
        'manufacturer_id',
        'logo'
    ];
    public function manufacturer(){
        return $this->belongsTo(Manufacturer::class);
    }
}
