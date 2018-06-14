<?php

namespace App;

use Laravel\Passport\HasApiTokens ;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable , HasApiTokens , SoftDeletes ;
    public $timestamps = false ;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'level', 'password', 'avatar' , 'nickname' , 'gender' ,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    public function findForPassport( $name ) {
    	return $this->where('username' , $name )->first();
    }
    
    public function validateForPassportPasswordGrant( $password ) {
    	return true ;
    }
    
    public function rsync() {
    	return $this->hasMany( \App\Models\UserRsync::class , 'user_id' , 'id' );
    }
}
