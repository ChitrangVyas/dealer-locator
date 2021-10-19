<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tax(){
        return $this->belongsToMany(Tax::class,'tax_group_items')->withPivot(['percentage'])->withTimestamps()->where('status',1);
    }
    public function taxGroupItems(){
        return $this->belongsToMany(TaxGroupItem::class);
    }

    public function getPercentageAttribute()
    {
        return $this->pivot->percentage;
    }

    public function getTaxAttribute()
    {
        return $this->pivot->tax;
    }
}
