<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HopperLevel;
use App\Models\Devices;

use App\Events\HopperLevelsUpdated;
//use App\Events\TransactionsListener;




class HopperController extends Controller
{

    



    public function receiveLevels(Request $request)
    {
       
       \Log::info('📩 Données reçues du Hopper:', $request->all());
        // Vérifie que la clé "levels" existe
        $levels = $request->input('levels');

        if (!$levels || !is_array($levels)) {
            Log::warning('❌ Format invalide ou manquant pour les niveaux Hopper', [
                'received_data' => $request->all()
            ]);
        
            return response()->json(['error' => 'Format invalide : levels manquant ou mal formé'], 400);
        }

        $saved = [];

        foreach ($levels as $level) {
            $channel = $level['channel'] ?? null;
            $device = $level['device'] ?? '';

            if (!$channel) {
                continue; // ignore les entrées incomplètes
            }

            $hopper = HopperLevel::updateOrCreate(
                [
                    'channel' => $channel,
                    'device' => $device,
                ],
                [
                    'denomination_level' => $level['denomination_level'] ?? 0,
                    'value_cent' => $level['value_cent'] ?? 0,
                    'value_eur' => $level['value_eur'] ?? 0,
                    'country_code' => $level['country_code'] ?? 'EUR',
                    
                ]
            );

            $saved[] = $hopper;
        }

        HopperLevelsUpdated::dispatch();
        //TransactionsListener::dispatch($level['device']);

        return response()->json([
            'status' => 'success',
            'message' => 'Niveaux synchronisés avec succès',
            'count' => count($saved),
        ]);
    }
    





}
