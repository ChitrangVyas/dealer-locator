<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $icons = [
        0 => ['name'=>'Notification','tag'=>'<span class="fas fa-envelope"></span>'],
        1 => ['name'=>'Inquiry','tag'=>'<span class="fas fa-phone-alt"></span>'],
        2 => ['name'=>'Reminder','tag'=>'<span class="fas fa-clock"></span>'],
        3 => ['name'=>'Sale','tag'=>'<span class="fas fa-shopping-cart"></span>'],
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIcon(){
        return $this->icons[$this->icon_id];
    }
}
