<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;

class DevicesController extends Controller
{
    // Affiche la liste des devices
    public function index()
    {
        $devices = Devices::all();
        return view('admin.devices.index', compact('devices'));
    }

    // Formulaire de création
    public function create()
    {
        return view('admin.devices.create');
    }

    // Enregistre un nouveau device
    public function store(Request $request)
    {
        $request->validate([
            'serial' => 'required|unique:devices,serial|max:255',
            'ref' => 'required|max:255',
        ]);

        Device::create($request->only(['serial', 'ref']));

        return redirect()->route('devices.index')->with('success', 'Device créé avec succès.');
    }

    // Affiche le formulaire d’édition
    public function edit(Devices $device)
    {
        return view('admin.devices.edit', compact('device'));
    }

    // Met à jour un device
    public function update(Request $request, Devices $device)
    {
        $request->validate([
            'serial' => 'required|max:255|unique:devices,serial,' . $device->id,
            'ref' => 'required|max:255',
        ]);

        $device->update($request->only(['serial', 'ref']));

        return redirect()->route('devices.index')->with('success', 'Device mis à jour.');
    }

    // Supprime un device
    public function destroy(Devices $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device supprimé.');
    }
}
