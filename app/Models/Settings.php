<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
   //protected $connection = 'mysql_primary';
    protected $fillable = ['name', 'value'];
}


class SettingsSecondary extends Model
{
    protected $connection = 'mysql_secondary';
    protected $table = 'settings';
    protected $fillable = ['name', 'value'];
}