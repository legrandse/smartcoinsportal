<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\LinkedDevices;
use App\Models\Devices;
use App\Models\Transactions;
use App\Models\User;

class Dashboard extends Component
{
    public $deviceId = '';

    public $chartData = [];
    public $bar_chart = [];
    public $donut_chart = [];
    public $user;
    public $dailySales;
    public $yearlySales;

    public $period = '1';
    public $startDate;
    public $endDate;

    public $transactions;
    public $showAll = false;
    public $selected = [];
    public $selectAll = false;


    public function mount($device = null)
    {
        $this->user = auth()->user()->load('linkedDevices.device');

        $this->deviceId = $device ?? '';
        

        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');

        $this->loadRevenues();
        $this->loadData();
        $this->loadTransactions();
    }


    /**
     * Changement du device sélectionné
     */
    public function updatedDeviceId($deviceId)
    {
        
        $this->loadRevenues();
        $this->loadData();
        $this->loadTransactions();
    }

    // On surveille les changements des dates ou de la période
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['period', 'startDate', 'endDate'])) {
            $this->loadRevenues();
        }
    }



    /**
     * Rechargement lorsqu'une période est modifiée
     */
    public function updatedPeriod()
    {
       
        $this->loadRevenues();
        $this->loadData();
        $this->loadTransactions();
        
    }


    /**
     * Rechargement lorsqu'une date de début est modifiée
     */
    public function updatedStartDate()
    {
        if ($this->period === 'custom') {
            $this->loadRevenues();
            $this->loadData();
            $this->loadTransactions();
        }
    }


    /**
     * Rechargement lorsqu'une date de fin est modifiée
     */
    public function updatedEndDate()
    {
        if ($this->period === 'custom') {
            $this->loadRevenues();
            $this->loadData();
            $this->loadTransactions();
        }
    }

   
    /**
     * Revenues
     */
    public function loadRevenues()
    {
        $serials = $this->user->linkedDevices->pluck('device.serial')->toArray();
        if (!$this->user || empty($serials)) {
            $this->dailySales = 0; $this->yearlySales = 0; return;
        }
       
        $query = Transactions::where('status', 'SUCCEEDED')->whereIn('device', $serials);
        
        if ($this->deviceId) { 
            $device = Devices::find($this->deviceId);
           
            $query->where('device', $device->serial); 
        }
        
        // --- Logique de Période ---
        if ($this->period !== 'custom') {
            $start = match($this->period) {
                '2' => Carbon::today()->subDays(1),
                '7' => Carbon::today()->subDays(6),
                '30' => Carbon::today()->subDays(29),
                default => Carbon::today(),
            };
            //$start =     Carbon::today()->subDays(29);

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


    /**
     * Charts
     */
    private function loadData()
    {
        if (!$this->user) {
            return;
        }

        // Récupère tous les serials des devices liés à l'utilisateur
        $serials = $this->user->linkedDevices
            ->pluck('device.serial')
            ->filter()
            ->toArray();

        
        // --- Logique de Période ---
        if ($this->period !== 'custom') {
            $start = match($this->period) {
                '2' => Carbon::today()->subDays(1)->format('Y-m-d H:i:s'),
                '7' => Carbon::today()->subDays(6)->format('Y-m-d H:i:s'),
                '30' => Carbon::today()->subDays(29)->format('Y-m-d H:i:s'),
                default => Carbon::today()->format('Y-m-d H:i:s'),
            };
            
            $end = Carbon::now();
        } else {
            // Utilisation des dates du datepicker
            $start = Carbon::parse($this->startDate)->startOfDay()->format('Y-m-d H:i:s');
            
            $end = Carbon::parse($this->endDate)->endOfDay()->format('Y-m-d H:i:s');
        }

        

        if (empty($serials)) {
            $this->chartData = [
                'labels' => [],
                'datasets' => [[
                    'label' => 'Montant total',
                    'data' => [],
                    'backgroundColor' => 'rgba(255, 196, 81, .7)',
                ]],
            ];

            $this->bar_chart = [
                'labels' => [],
                'datasets' => [[
                    'data' => [],
                    'backgroundColor' => [],
                ]],
            ];

            $this->donut_chart = [
                'labels' => [],
                'datasets' => [[
                    'label' => 'Répartition',
                    'data' => [],
                    'backgroundColor' => [],
                ]],
            ];

            return;
        }


        /*
         * Si un device précis est choisi,
         * on récupère son serial.
         */
        /*$deviceSerial = null;

        if ($device) {
            $selectedDevice = Devices::find($device);

            if ($selectedDevice && $selectedDevice->serial) {
                $deviceSerial = $selectedDevice->serial;
            } else {
                // Permet également de fonctionner si $device est déjà un serial
                $deviceSerial = $device;
            }
        }*/


        // ---------------------------------------------------------
        // Chart 1 : montants par année
        // ---------------------------------------------------------

        /*$query = Transactions::selectRaw(
            'YEAR(created_at) as year, SUM(amount) as amount'
        )
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('device', $serials)
            ->where('status', 'SUCCEEDED');*/

        $query = Transactions::selectRaw(
            'SUM(amount) as amount'
        )
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('device', $serials)
            ->where('status', 'SUCCEEDED');
        

       if ($this->deviceId) { 
            $device = Devices::find($this->deviceId);
           
            $query->where('device', $device->serial); 
        }

        $data = $query
            //->groupByRaw('YEAR(created_at)')
            //->orderByRaw('YEAR(created_at)')
            ->get();

        $this->chartData = [
            'labels' => $data->pluck('created_at')->toArray(),
            'datasets' => [[
                'label' => 'Montant total',
                'data' => $data->pluck('amount')->toArray(),
                'backgroundColor' => 'rgba(255, 196, 81, .7)',
            ]],
        ];


        // ---------------------------------------------------------
        // Chart 2 : barres par référence
        // ---------------------------------------------------------

        $query_bar = Transactions::selectRaw(
            'reference, COUNT(reference) as total'
        )
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('device', $serials)
            ->where('status', 'SUCCEEDED');

        if ($this->deviceId) { 
            $device = Devices::find($this->deviceId);
           
            $query_bar->where('device', $device->serial); 
        }

        $data_bar_chart = $query_bar
            ->groupBy('reference')
            ->orderBy('reference')
            ->get();

        $this->bar_chart = [
            'labels' => $data_bar_chart->pluck('reference')->toArray(),
            'datasets' => [[
                'data' => $data_bar_chart->pluck('total')->toArray(),
                'backgroundColor' => [
                    'rgba(255, 196, 81, .7)',
                    'rgba(255, 196, 81, .6)',
                    'rgba(255, 196, 81, .5)',
                    'rgba(255, 196, 81, .4)',
                    'rgba(255, 196, 81, .3)',
                ],
            ]],
        ];


        // ---------------------------------------------------------
        // Chart 3 : donut Cash vs Bancontact
        // ---------------------------------------------------------

        $query = Transactions::whereIn('device', $serials)
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 'SUCCEEDED');

       if ($this->deviceId) { 
            $device = Devices::find($this->deviceId);
           
            $query->where('device', $device->serial); 
        }

        $data_donut = $query
            ->selectRaw("
                CASE
                    WHEN debtor IS NULL OR debtor = '' THEN 'Cash'
                    ELSE 'Bancontact'
                END as status_debtor,
                COUNT(*) as total
            ")
            ->groupBy('status_debtor')
            ->get();

        $this->donut_chart = [
            'labels' => $data_donut->pluck('status_debtor')->toArray(),
            'datasets' => [[
                'label' => 'Répartition',
                'data' => $data_donut->pluck('total')->toArray(),
                'backgroundColor' => [
                    'rgba(255, 196, 81, .8)',
                    'rgba(54, 162, 235, .8)',
                ],
            ]],
        ];


        // Émettre un event Livewire v3 pour le JS
        $this->dispatch('chartsUpdated', [
            'chartData' => $this->chartData,
            'barChart' => $this->bar_chart,
            'donutChart' => $this->donut_chart,
        ]);
    }


    /**
     * Transactions
     */
    public function loadTransactions()
    {
        if (!$this->user) {
            $this->transactions = collect();

            return;
        }

        $serials = $this->user->linkedDevices
            ->pluck('device.serial')
            ->filter()
            ->toArray();


        // --- Logique de Période ---
        if ($this->period !== 'custom') {
            $start = match($this->period) {
                '2' => Carbon::today()->subDays(1)->format('Y-m-d H:i:s'),
                '7' => Carbon::today()->subDays(6)->format('Y-m-d H:i:s'),
                '30' => Carbon::today()->subDays(29)->format('Y-m-d H:i:s'),
                default => Carbon::today()->format('Y-m-d H:i:s'),
            };
            
            $end = Carbon::now();
        } else {
            // Utilisation des dates du datepicker
            $start = Carbon::parse($this->startDate)->startOfDay()->format('Y-m-d H:i:s');
            
            $end = Carbon::parse($this->endDate)->endOfDay()->format('Y-m-d H:i:s');
        }

        // On construit la requête de base
        $query = Transactions::whereIn('device', $serials)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('updated_at', 'desc');


        
                        



        /*
         * Si un device précis est sélectionné,
         * on récupère son serial.
         */
        if ($this->deviceId) {

            $device = Devices::find($this->deviceId);

            if ($device && $device->serial) {
                $query->where('device', $device->serial);
            } else {
                // Si deviceId est déjà un serial
                $query->where('device', $this->deviceId);
            }
        }


        // Limitation si on ne veut pas tout
        $this->transactions = $this->showAll
            ? $query->get()
            : $query->take(5)->get();
        

        $this->selectAll =
            count($this->selected) === $this->transactions->count();
    }


    /**
     * Sélectionner/désélectionner toutes les transactions
     */
    public function updatedSelectAll($value)
    {
        if ($value) {

            // Sélectionner toutes les transactions affichées
            $this->selected = $this->transactions
                ->pluck('id')
                ->toArray();

            $this->dispatch('showDeleteButton');

        } else {

            // Désélectionner toutes
            $this->selected = [];
        }
    }


    /**
     * Modification de la sélection
     */
    public function updatedSelected()
    {
        $this->selectAll =
            count($this->selected) === $this->transactions->count();

        $this->dispatch('showDeleteButton');
    }


    /**
     * Supprimer les transactions sélectionnées
     */
    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        Transactions::whereIn('id', $this->selected)->delete();

        // Réinitialiser la sélection
        $this->selected = [];
        $this->selectAll = false;

        // Recharger la liste
        $this->loadTransactions();
        $this->loadData();
        $this->loadTransactions();

        // Message toast Livewire
        $this->dispatch('deleted');
    }


    /**
     * Afficher toutes / uniquement les transactions récentes
     */
    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;

        $this->loadTransactions();
    }


    /**
     * Concerne le refresh via reverb du dashboard
     */

    public function refreshTransactions()
	{
	    $this->loadRevenues();
        $this->loadData();
        $this->loadTransactions();
	    
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




    /**
     * Render
     */
    public function render()
    {
        $devices = LinkedDevices::with(['device', 'user'])
            ->where('user_id', auth()->id())
            ->get();

        return view('livewire.dashboard', compact('devices'));
    }
}