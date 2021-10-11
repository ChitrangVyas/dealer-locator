<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tax(){
        return $this->belongsToMany(Tax::class,'tax_group_item')->withPivot(['percentage']);
    }

}
