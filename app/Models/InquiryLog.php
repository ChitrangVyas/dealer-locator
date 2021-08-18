<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
