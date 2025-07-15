<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedDevices extends Model
{
    protected $fillable = ['device_id', 'user_id', 'ref'];

	public function device()
    {
        return $this->belongsTo(Devices::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
