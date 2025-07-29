<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Transactions;
use App\Models\User;

class SalesRevenue extends Component
{
	
	public $dailySales;
    public $yearlySales;
    public $user;
    
    public function mount()
    { 	   	
    	$this->user = auth()->user()->load('linkedDevices.device'); // Charger la relation imbriquée
        $this->loadTransactions();
        
        
        
    }
    
    public function loadTransactions()
    {
    	$serials = $this->user->linkedDevices->pluck('device.serial')->toArray();
    	
    	
    	if (!$this->user || !$serials) {
            $this->dailySales = 0;
            $this->yearlySales = 0;
            return;
        }
    	 
    	 
    	 

    	
        // Ventes du jour (Eloquent)
        $this->dailySales = Transactions::whereDate('created_at', Carbon::today())
            ->where('status', 'SUCCEEDED')
            ->where('device',$serials)
            ->sum('amount');

        // Ventes de l'année en cours (Eloquent)
        $this->yearlySales = Transactions::whereYear('created_at', Carbon::now()->year)
            ->where('status', 'SUCCEEDED')
            ->where('device',$serials)
            ->sum('amount');
    }
	
	
    public function render()
    {
        return view('livewire.sales-revenue');
    }
}
