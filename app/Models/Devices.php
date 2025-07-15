<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    protected $fillable = ['model', 'name', 'serial','linked'];
    
    public function linkedDevices()
	{
	    return $this->hasMany(LinkedDevices::class);
	}
    
    
}
