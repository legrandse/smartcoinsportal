<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;


class Charts extends Component
{
	
	public $chartData;
	public $bar_chart;
	
	public function mount()
    {
        // Exemple de récupération des données dynamiques depuis une table MySQL
        $data = DB::table('transactions')
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as amount')
            ->where('status','=','SUCCEEDED')
            ->groupBy('year')
            ->orderBy('year')
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
        $data_bar_chart = DB::table('transactions')
            ->selectRaw('reference, COUNT(reference) as total')
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
