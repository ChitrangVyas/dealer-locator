<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPSTORM_META\type;

class InquiryLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'inquiries_log';
    /* protected $fillable = [
        'type',
        'reminder',
        'description',
        'inquiry_id',
        'logged_by',
    ]; */

    protected $guarded = [];

    public function inquiry(){
        return $this->belongsTo(Inquiry::class);
    }
    public function getIconClass(){
        $icon = '';
        if($this->is_reminder){
            $icon = "fa-clock";
        } else if($this->type=='email'){
            $icon = "fa-envelope";
        }elseif($this->type=='calls'){
            $icon = "fa-phone-alt";
        }elseif($this->type=='meetings'){
            $icon = "fa-handshake";
        }elseif($this->type=='notes'){
            $icon ="fa-clipboard";
        }else{
            $icon = "fa-clock";
        }
        return $icon;
    }
    public function getTitle(){
        $icon = '';
        if($this->is_reminder){
            return $icon = "You have a remider to ".$this->type." on ".$this->source;
        } else if($this->type=='email'){
            $icon = "sent email on ".$this->source;
        }elseif($this->type=='calls'){
            $icon = "called on ".$this->source;
        }elseif($this->type=='meetings'){
            $icon = "had a meeting";
        }elseif($this->type=='notes'){
            $icon ="Note";
        }
        return $icon;
    }
}
