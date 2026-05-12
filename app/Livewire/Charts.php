<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Transactions;

class Charts extends Component
{
    public $device_id;
    public $chartData = [];
    public $bar_chart = [];
    public $donut_chart = [];
    public $user;

    public function mount($device = null)
    {
        $this->user = auth()->user()->load('linkedDevices.device');
        $this->device_id = $device ?? null;

        $this->loadData();
    }

    #[On('deviceSelected')]
    public function updateDevice($device)
    {
        $this->device_id = $device;
        
        $this->loadData($this->device_id);
    }

    private function loadData($device = null)
    {
        // ⚡ récupère tous les serials des devices liés à l’utilisateur
        $serials = $this->user->linkedDevices->pluck('device.serial')->toArray();

        // -------- Chart 1 (montants par année) --------
        $query = Transactions::selectRaw('YEAR(created_at) as year, SUM(amount) as amount')
            ->whereIn('device', $serials) // sécurité : seulement les devices de l’utilisateur
            ->where('status', 'SUCCEEDED');
		
	
		
        // si un device précis est choisi → filtre supplémentaire
        if ($device) {
        	
            $query->where('device', $device);
        }

        $data = $query->groupByRaw('YEAR(created_at)')
                      ->orderByRaw('YEAR(created_at)')
                      ->get();

        $this->chartData = [
            'labels' => $data->pluck('year')->toArray(),
            'datasets' => [[
                'label' => 'Montant total',
                'data' => $data->pluck('amount')->toArray(),
                'backgroundColor' => 'rgba(255, 196, 81, .7)',
            ]],
        ];




        // -------- Chart 2 (barres par référence) --------
        $query_bar = Transactions::selectRaw('reference, COUNT(reference) as total')
            ->whereIn('device', $serials)
            ->where('status', 'SUCCEEDED');

        if ($device) {
            $query_bar->where('device', $device);
        }

        $data_bar_chart = $query_bar->groupBy('reference')
        							->orderBy('reference')
        							->get();
        //dd($data_bar_chart);

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
        
        // -------- Chart 3 (barres par transaction) --------
        // On définit la base de la requête
		$query = Transactions::whereIn('device', $serials)
		    ->where('status', 'SUCCEEDED');

		if ($device) {
		    $query->where('device', $device);
		}

		// Requête spécifique pour le donut
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
		            'rgba(255, 196, 81, .8)', // Couleur pour le premier groupe
		            'rgba(54, 162, 235, .8)', // Couleur pour le deuxième groupe
		        ],
		    ]],
		];
        
        
        
        // ⚡ Émettre un event Livewire v3 pour le JS
        $this->dispatch('chartsUpdated', [
            'chartData' => $this->chartData,
            'barChart' => $this->bar_chart,
            'donutChart' => $this->donut_chart
        ]);
        
    }

    public function render()
    {
        return view('livewire.charts');
    }
}


