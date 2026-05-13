<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\User;
use App\Models\LinkedDevices;
use App\Models\Transactions;
use App\Models\Devices;

Broadcast::channel('transaction.{device}', function (User $user, $device) {
    dd($device);
    
    return $user->id === (int) $id;
});
/*
Broadcast::channel('transaction.{deviceName}', function (Devices $device, $deviceName) {
    return $device->name === $deviceName;
});
*/