<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       /* User::factory()->create([
            'name' => 'Legrand Sebastien',
            'email' => 'legrandse@gmail.com',
            
        ]);*/
        
        // Préparer les paramètres par défaut
	    $settings = [
	        ['device' => $device->serial, 'name' => 'payconiq',   'value' => 1],
	        ['device' => $device->serial, 'name' => 'cash',       'value' => 1],
	        ['device' => $device->serial, 'name' => 'parity',     'value' => 1],
	        ['device' => $device->serial, 'name' => 'tokenArray', 'value' => 1],
	        ['device' => $device->serial, 'name' => 'ngrok',      'value' => null],
	        ['device' => $device->serial, 'name' => 'magasin',    'value' => 0],
	    ];

	    // Insérer en une seule fois
	    Settings::factory()->insert($settings);
        
        
    }
}
