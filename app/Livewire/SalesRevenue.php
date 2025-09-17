<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Carbon\Carbon;

use App\Models\Transactions;
use App\Models\User;

class SalesRevenue extends Component
{
	public $device_id;
	public $dailySales;
    public $yearlySales;
    public $user;
    
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
    	//$this->device_id = 'Yu7PSb49Q879gml';
        $serials = $this->user->linkedDevices->pluck('device.serial')->toArray();
        
        if (!$this->user || empty($serials)) {
            $this->dailySales = 0;
            $this->yearlySales = 0;
            return;
        }

        // Query de base (filtrée par devices autorisés)
        $query = Transactions::where('status', 'SUCCEEDED')
            ->whereIn('device', $serials);

        // Filtre sur device précis si choisi
        if ($this->device_id) {
            $query->where('device', $this->device_id);
        }

        // Ventes du jour
        $this->dailySales = (clone $query)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // Ventes de l'année
        $this->yearlySales = (clone $query)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
            
        
    }
	
	
    public function render()
    {
        return view('livewire.sales-revenue');
    }
}
