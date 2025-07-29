<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transactions;





class Charts extends Component
{
	public $user;
	public $chartData;
	public $bar_chart;
	
	public function mount()
    {
    	$this->user = auth()->user()->load('linkedDevices.device'); // Charger la relation imbriquée
    	$serials = $this->user->linkedDevices->pluck('device.serial')->toArray();
        
        // Exemple de récupération des données dynamiques depuis une table MySQL
        $data = Transactions::selectRaw('YEAR(created_at) as year, SUM(amount) as amount')
						    ->whereIn('device', $serials)
						    ->where('status', 'SUCCEEDED')
						    ->groupByRaw('YEAR(created_at)')
						    ->orderByRaw('YEAR(created_at)')
						    ->get();
            
            

        // Transformer les données pour les rendre exploitables
        $this->chartData = [
            'labels' => $data->pluck('year')->toArray(),
            'datasets' => [
                [
                    'label' => 'e',
                    'data' => $data->pluck('amount')->toArray(),
                    'backgroundColor' => 'rgba(255, 196, 81, .7)',
                ],
                
            ],
        ];
        
        //bar chart
        $data_bar_chart = Transactions::selectRaw('reference, COUNT(reference) as total')
							            ->whereIn('device',$serials)
							            ->where('status','=','SUCCEEDED')
							            ->groupBy('reference')
							            //->orderBy('year')
							            ->get();
        
        $this->bar_chart = [
            'labels' => $data_bar_chart->pluck('reference')->toArray(),
            'datasets' => [
                [
                    
                    'data' => $data_bar_chart->pluck('total')->toArray(),
                    'backgroundColor' => [
                    						'rgba(255, 196, 81, .7)',
                    						'rgba(255, 196, 81, .6)',
                    						'rgba(255, 196, 81, .5)',
                    						'rgba(255, 196, 81, .4)',
                    						'rgba(255, 196, 81, .3)'
                    					],
                ],
                
            ],
        ];
        
        
        
        
        
        
    }
	
    public function render()
    {
        return view('livewire.charts');
    }
}
