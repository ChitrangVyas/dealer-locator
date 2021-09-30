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
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'users_group_id',
        'created_by',
        'updated_by',
        'parent_id',
        'address',
        'city_id',
        'state_id',
        'zipcode',
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
    /* public function getCustomerType(){
        if($this->contact_type==1){
            return "Customer";
        }else{
            return "Vendor";
        }
    } */

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
        $this->notify(new SendResetPassword($token));
    }
}
