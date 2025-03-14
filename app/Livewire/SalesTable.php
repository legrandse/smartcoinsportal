<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transactions;

class SalesTable extends Component
{
    public $transactions;
    public $showAll = false;

    public function mount()
    {
        $this->loadTransactions();
    }

   public function loadTransactions()
	{
	    $this->transactions = $this->showAll
	        ? Transactions::orderBy('updated_at', 'desc')->get()
	        : Transactions::orderBy('updated_at', 'desc')->take(5)->get();
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
