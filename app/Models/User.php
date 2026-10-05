<?php

namespace App\Models;

use App\Notifications\SendResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class,'users_group_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'parent_id');
    }
    public function city(){
        return $this->belongsTo(City::class);
    }
    public function state(){
        return $this->belongsTo(State::class);
    }
    public function country(){
        return $this->belongsTo(Country::class);
    }
    /* public function getCustomerType(){
        if($this->contact_type==1){
            return "Customer";
        }else{
            return "Vendor";
        }
    } */
    public function dealerCategory()
   {
       return $this->belongsToMany(Category::class,DealerCategory::class,'dealer_id','category_id');
   }
    public function getMobileWithCountryCode(){
        if(!empty($this->mobile)){
            if(!empty($this->country) && $this->country !== 0){
                return $this->country->phonecode." ".$this->mobile;
            }
            return $this->mobile;
        }
        else{
            return "";
        }
    }
    public function parentUser(){
        $this->parent_id;
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
                     ->with([$relation => $constraint]);
    }
    public function getSelect2Data($table,$id,$where=""){
        return DB::table($table)->where('id', $id)->where(function($query) use($where){
            if(!empty($where)){
                return $query->whereRaw($where);
            }
        })->first();
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new SendResetPassword($token,$this));
    }
}
