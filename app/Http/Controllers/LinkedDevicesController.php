<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkedDevices;
use App\Models\Devices;
use App\Models\Settings;


class LinkedDevicesController extends Controller
{
    public function index()
    {
        $devices = LinkedDevices::with(['device', 'user'])
        ->where('user_id', auth()->id()) // filtre sur l'utilisateur connecté
        ->get();
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
	    
	    // Préparer les paramètres par défaut
	    $settings = [
	        ['device' => $device->serial, 'name' => 'payconiq',   'value' => 1],
	        ['device' => $device->serial, 'name' => 'cash',       'value' => 1],
	        ['device' => $device->serial, 'name' => 'parity',     'value' => 1],
	        ['device' => $device->serial, 'name' => 'tokenArray', 'value' => '5,10,20,30,40,50'],
	        ['device' => $device->serial, 'name' => 'ngrok',      'value' => 'https://'],
	        ['device' => $device->serial, 'name' => 'magasin',    'value' => 0],
	        ['device' => $device->serial, 'name' => 'collectToggle',    'value' => 0],
	        ['device' => $device->serial, 'name' => 'stackToggle',    'value' => 0],
	        ['device' => $device->serial, 'name' => 'denomination',    'value' => 0],
	        ['device' => $device->serial, 'name' => 'quantity',    'value' => 0],
	    ];

	    // Insérer en une seule fois
	    Settings::insert($settings);
	    
	    

	    // Rediriger avec bannière de succès
    	return redirect()->route('linked-devices.index')->with('success', 'Appareil lié avec succès.');
    }

    public function show(LinkedDevices $linkedDevices)
    {
        return $linkedDevices->load(['device', 'user']);
    }
    
    public function edit(LinkedDevices $linkedDevices)
    {
        return view('admin.devices.link.edit', compact('linkedDevices'));
    }
    

    public function update(Request $request, LinkedDevices $linkedDevices)
    {
        $validated = $request->validate([
            'device_id' => 'sometimes|exists:devices,id',
            'user_id' => 'sometimes|exists:users,id',
            'ref' => 'sometimes|string|max:255',
        ]);

        $linkedDevices->update($validated);

        return $linkedDevices;
    }

    public function destroy(LinkedDevices $linkedDevices)
    {
        $linkedDevices->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
