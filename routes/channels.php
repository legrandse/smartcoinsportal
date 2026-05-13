<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\User;
use App\Models\LinkedDevices;
use App\Models\Transactions;
use App\Models\Devices;

Broadcast::channel('transaction.{device}', function (User $user, $device) {
    $deviceId = Devices::where('serial',$device)->first();
    $ownerDevice = LinkedDevices::where('device_id',$deviceId->id)->first();
    return $user->id ===  $ownerDevice->user_id;
});
/*
Broadcast::channel('transaction.{deviceName}', function (Devices $device, $deviceName) {
    return $device->name === $deviceName;
});
*/