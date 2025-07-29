<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use App\Models\Transactions;

class SalesTable extends Component
{
    public $user;
    public $transactions;
    public $showAll = false;

    public function mount()
    {
    	$this->user = auth()->user()->load('linkedDevices.device'); // Charger la relation imbriquée
        $this->loadTransactions();
    }

   public function loadTransactions()
	{
		$serials = $this->user->linkedDevices->pluck('device.serial')->toArray();
		
		
		
	    $this->transactions = $this->showAll
	        ? Transactions::whereIn('device',$serials)
	        	->orderBy('updated_at', 'desc')->get()
	        : Transactions::whereIn('device',$serials)
	        	->orderBy('updated_at', 'desc')->take(5)->get();
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
