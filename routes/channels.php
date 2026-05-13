<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\User;
use App\Models\LinkedDevices;
use App\Models\Transactions;
use App\Models\Devices;

Broadcast::channel('user.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});
/*
Broadcast::channel('transaction.{deviceName}', function (Devices $device, $deviceName) {
    return $device->name === $deviceName;
});
*/