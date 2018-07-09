<?php
namespace App\Models;

use App\User ;
use Illuminate\Database\Eloquent\Model;

class Words extends Model {
	protected $table='words';
	
	
    protected $fillable = [
    	'id' , 'word' , 'property' , 'from' , 'description' , 'weight'
    ];

    
}