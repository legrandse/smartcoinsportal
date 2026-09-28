<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HopperLevel;

class HopperController extends Controller
{
    public function receiveLevels(Request $request)
    {
        // Vérifie que la clé "levels" existe
        $levels = $request->input('levels');

        if (!$levels || !is_array($levels)) {
            return response()->json(['error' => 'Format invalide : levels manquant ou mal formé'], 400);
        }

        $saved = [];

        foreach ($levels as $level) {
            $channel = $level['channel'] ?? null;

            if (!$channel) {
                continue; // ignore les entrées incomplètes
            }

            $hopper = HopperLevel::updateOrCreate(
                ['channel' => $channel],
                [
                    'denomination_level' => $level['denomination_level'] ?? 0,
                    'value_cent' => $level['value_cent'] ?? 0,
                    'value_eur' => $level['value_eur'] ?? 0,
                    'country_code' => $level['country_code'] ?? 'EUR',
                ]
            );

            $saved[] = $hopper;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Niveaux synchronisés avec succès',
            'count' => count($saved),
        ]);
    }
    /*
    public function refreshTransactions()
	{
	    $this->loadRevenues();
        $this->loadData();
        $this->loadTransactions();
	    
	}
	
	
	//permet de rafraichir la table avec un private channel
	public function getListeners()
	{
	    $user = auth()->user();
	    $listeners = [];

	    // On parcourt les appareils liés pour créer un écouteur par canal privé
	    foreach ($user->linkedDevices as $linked) {
	        $serial = $linked->device->serial;
	        
	        // Syntaxe : echo-private:{canal},{événement}
	        // Sans broadcastAs, l'événement est le namespace complet précédé d'un point
	        $listeners["echo-private:transaction.{$serial},.App\Events\TransactionsListener"] = 'refreshTransactions';
	    }

	    return $listeners;
	}*/





}
