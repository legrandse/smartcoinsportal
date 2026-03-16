<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HopperLevel extends Model
{
    //use HasFactory;

    // Nom de la table
    protected $table = 'hopper_levels';

    // Clé primaire (le canal du hopper)
    protected $primaryKey = 'channel';
    public $incrementing = false; // car channel n’est pas auto-incrémenté
    protected $keyType = 'int';

    // Champs autorisés pour remplissage de masse
    protected $fillable = [
        'channel',
        'denomination_level',
        'value_cent',
        'value_eur',
        'country_code',
    ];
}
