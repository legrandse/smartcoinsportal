<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\User;
use App\Models\LinkedDevices;
use App\Models\Transactions;
use App\Models\Devices;


use Illuminate\Support\Facades\Log;

Broadcast::channel('transaction.{device}', function (User $user, $device) {
    Log::info('--- Début vérification Canal Privé ---');
    Log::info('Utilisateur connecté ID: ' . $user->id);
    Log::info('Serial reçu du client: ' . $device);

    $deviceModel = Devices::where('serial', $device)->first();
    
    if (!$deviceModel) {
        Log::error('Erreur: Aucun appareil trouvé avec le serial: ' . $device);
        return false;
    }

    $ownerDevice = LinkedDevices::where('device_id', $deviceModel->id)->first();
    
    if (!$ownerDevice) {
        Log::error('Erreur: Aucun propriétaire trouvé pour device_id: ' . $deviceModel->id);
        return false;
    }

    Log::info('Propriétaire attendu (user_id): ' . $ownerDevice->user_id);
    
    $isAuthorized = (int) $user->id === (int) $ownerDevice->user_id;
    
    Log::info('Résultat autorisation: ' . ($isAuthorized ? 'OUI' : 'NON'));
    
    return $isAuthorized;
});
/*
Broadcast::channel('transaction.{deviceName}', function (Devices $device, $deviceName) {
    return $device->name === $deviceName;
});
*/