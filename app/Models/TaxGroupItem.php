<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaxGroupItem extends Model
{
    use HasFactory;
    protected $table = 'tax_group_items';
    protected $guarded = [];

    public function taxGroup(){
        return $this->belongsTo(TaxGroup::class);
    }
    public function tax(){
        return $this->belongsTo(Tax::class);
    }
}
