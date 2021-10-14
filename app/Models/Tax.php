<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function taxGroupItems(){
        return $this->belongsToMany(TaxGroup::class,'tax_group_items','tax_id','tax_group_id')->withPivot('percentage');
    }

    public function taxGroup(){
        return $this->belongsToMany(TaxGroup::class,'tax_group_items')->withPivot(['percentage']);
    }

    public function getTaxPercentage($tax_group){
        $tax_group_item = $this->taxGroupItems()->where('tax_group_id',$tax_group)->first();
        return (!empty($tax_group_item))?$tax_group_item->getPercentageAttribute():'';
    }

    public function getTaxItem($tax_group){
        return json_decode($this->taxGroupItems()->where('tax_group_id',$tax_group)->first(),1);
    }

}
