<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkedDevices;
use App\Models\Devices;


class LinkedDevicesController extends Controller
{
    public function index()
    {
        $devices =  LinkedDevices::with(['device', 'user'])->get();
        return view('admin.devices.link.index',compact('devices'));
    }

	public function create()
    {
        
        return view('admin.devices.link.create');
    }
	

    public function store(Request $request)
    {
    	   	
    	// Valider d'abord les champs envoyés
	    $validated = $request->validate([
	        'serial' => 'required|string',
	        'user_id' => 'required|exists:users,id',
	        'ref' => 'required|string|max:255',
	    ]);

	    // Rechercher un device non lié avec ce numéro de série
	    $device = Devices::where('serial', $validated['serial'])
	                     ->where('linked', false)
	                     ->first();

	    // Si aucun device correspondant, retourner une erreur
	    if (!$device) {
	        return back()->withErrors(['serial' => 'Aucun appareil non lié avec ce numéro de série.']);
	    }

	    // Créer le lien dans linked_devices
	    $linkedDevice = LinkedDevices::create([
	        'device_id' => $device->id,
	        'user_id' => $validated['user_id'],
	        'ref' => $validated['ref'],
	    ]);

	    // Mettre à jour l'état du device (facultatif mais logique)
	    $device->linked = true;
	    $device->save();

	    // Rediriger avec bannière de succès
    	return redirect()->route('linked-devices.index')->with('success', 'Appareil lié avec succès.');
    }

    public function show(LinkedDevices $LinkedDevices)
    {
        return $LinkedDevices->load(['device', 'user']);
    }

    public function update(Request $request, LinkedDevices $LinkedDevices)
    {
        $validated = $request->validate([
            'device_id' => 'sometimes|exists:devices,id',
            'user_id' => 'sometimes|exists:users,id',
            'ref' => 'sometimes|string|max:255',
        ]);

        $LinkedDevices->update($validated);

        return $LinkedDevices;
    }

    public function destroy(LinkedDevices $LinkedDevices)
    {
        $LinkedDevices->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
