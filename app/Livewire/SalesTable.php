<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

use App\Models\Transactions;

class SalesTable extends Component
{
	public $device_id;
    public $user;
    public $transactions;
    public $showAll = false;

    public function mount()
    {
    	$this->user = auth()->user()->load('linkedDevices.device'); // Charger la relation imbriquée
        $this->loadTransactions();
    }
    
    #[On('deviceSelected')]
    public function updateDevice($device)
    {
        $this->device_id = $device;
        
        $this->loadTransactions();
    }

    public function loadTransactions()
	{
	    $serials = $this->user->linkedDevices->pluck('device.serial')->toArray();

	    // On construit la requête de base
	    $query = Transactions::whereIn('device', $serials)
	        ->orderBy('updated_at', 'desc');

	    // Si un device précis est choisi → filtre supplémentaire
	    if ($this->device_id) {
	        $query->where('device', $this->device_id);
	    }

	    // Limitation si on ne veut pas tout
	    $this->transactions = $this->showAll
	        ? $query->get()
	        : $query->take(5)->get();
	}



    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
        $this->loadTransactions();
    }

    public function render()
    {
        return view('livewire.sales-table');
        
    }
}
