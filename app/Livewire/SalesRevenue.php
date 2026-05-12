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
    public $period = '1';
    public $startDate; // Nouvelle propriété
    public $endDate;   // Nouvelle propriété
    
    public function mount()
    { 	   	
    	$this->user = auth()->user()->load('linkedDevices.device');
        // Initialisation par défaut (aujourd'hui)
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        
        $this->loadTransactions();
        
        
        
    }
    
    // On surveille les changements des dates ou de la période
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['period', 'startDate', 'endDate'])) {
            $this->loadTransactions();
        }
    }
    
    
    
    // Cette méthode sera appelée automatiquement quand $period change dans la vue
    public function updatedPeriod()
    {
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
        if (!$this->user || empty($serials)) {
            $this->dailySales = 0; $this->yearlySales = 0; return;
        }

        $query = Transactions::where('status', 'SUCCEEDED')->whereIn('device', $serials);
        if ($this->device_id) { $query->where('device', $this->device_id); }

        // --- Logique de Période ---
        if ($this->period !== 'custom') {
            $start = match($this->period) {
                '2' => Carbon::today()->subDays(1),
                '7' => Carbon::today()->subDays(6),
                '30' => Carbon::today()->subDays(29),
                default => Carbon::today(),
            };
            $end = Carbon::now();
        } else {
            // Utilisation des dates du datepicker
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
        }

        $this->dailySales = (clone $query)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $this->yearlySales = (clone $query)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
    }
	
	
    public function render()
    {
        return view('livewire.sales-revenue');
    }
}
