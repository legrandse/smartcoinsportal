<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LinkedDevices;
use App\Models\Devices;

class DeviceSelect extends Component
{
	public $deviceId = '';

    public function updatedDeviceId($deviceId)
    {
    	
    	$device = Devices::find($this->deviceId);
    	
        // Émettre l'event vers les autres composants
        $this->dispatch('deviceSelected', device: $device->serial);
    }
	
	
	
    public function render()
    {
    	
    	
    	$devices = LinkedDevices::with(['device', 'user'])
        ->where('user_id', auth()->id()) // filtre sur l'utilisateur connecté
        ->get();
      
       
        
        return view('livewire.device-select', compact('devices'));
    }
}
