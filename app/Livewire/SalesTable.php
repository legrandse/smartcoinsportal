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
    public $selected = []; // IDs sélectionnés
	public $selectAll = false;


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
	    
	    $this->selectAll = count($this->selected) === $this->transactions->count();
	    
	}

	//#[On('echo:transaction,TransactionsListener')] ne fonctionne pas avec un private channel
	public function refreshTransactions()
	{
	    $this->loadTransactions();
	    $this->dispatch('transaction-received');
	}
	
	
	//permet de rafraichir la table avec un private channel
	public function getListeners()
	{
	    $user = auth()->user();
	    $listeners = [];

	    // On parcourt les appareils liés pour créer un écouteur par canal privé
	    foreach ($user->linkedDevices as $linked) {
	        $serial = $linked->device->serial;
	        
	        // Syntaxe : echo-private:{canal},{événement}
	        // Sans broadcastAs, l'événement est le namespace complet précédé d'un point
	        $listeners["echo-private:transaction.{$serial},.App\Events\TransactionsListener"] = 'refreshTransactions';
	    }

	    return $listeners;
	}
	
	
	
	
	public function updatedSelectAll($value)
	{
	    if ($value) {
	        // Sélectionner toutes les transactions affichées
	        $this->selected = $this->transactions->pluck('id')->toArray();
	        $this->dispatch('showDeleteButton');
	    } else {
	        // Désélectionner toutes
	        $this->selected = [];
	    }
	    
	}
	
	public function updatedSelected()
	{
	    $this->selectAll = count($this->selected) === $this->transactions->count();
	    $this->dispatch('showDeleteButton');
	}
	
	
	public function deleteSelected()
	{
	    if (empty($this->selected)) {
	        return; // rien à supprimer
	    }

	    Transactions::whereIn('id', $this->selected)->delete();

	    // Réinitialiser la sélection
	    $this->selected = [];
	    $this->selectAll = false;

	    // Recharger la liste
	    $this->loadTransactions();

	    // Message toast Livewire (optionnel)
	    $this->dispatch('deleted');
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
