<?php
namespace App\Models;

use App\User ;
use Illuminate\Database\Eloquent\Model;

class Characters extends Model {
	protected $table='characters';
	
	
    protected $fillable = [
    	'id' , 'character' , 'spell' , 'character' , 'strokes' , 'five_element' , 'description' , 'weight'
    ];

    
}