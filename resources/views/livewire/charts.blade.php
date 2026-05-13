<div>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
           {{--<div class="col-sm-12 col-xl-4">
                <div wire:ignore  class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Worldwide Sales</h6>
                        
                    </div>
                    <canvas id="worldwide-sales"></canvas>
                </div>
            </div>--}}
            <div class="col-sm-12 col-xl-4">
                <div wire:ignore  class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Token sales</h6>
                        
                    </div>
                    <canvas id="bar-chart"></canvas>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4">
                <div wire:ignore class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Cash vs Bancontact</h6>
                        
                    </div>
                    <canvas id="donut-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>   
@script
<script>
    let worldwideChart = null;
    let barChart = null;
    let donutChart = null;

    /*function renderWorldwideSales(data) {
        const ctx = document.getElementById('worldwide-sales').getContext('2d');
        if (worldwideChart) {
            worldwideChart.data = data;
            worldwideChart.update();
        } else {
            worldwideChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: { responsive: true }
            });
        }
    }*/

    function renderBarChart(data) {
        const ctx = document.getElementById('bar-chart').getContext('2d');
        //console.log(ctx);
        
        if (barChart) {
            barChart.data = data;
            barChart.update();
        } else {
            barChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: { 
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jetons', // Label de l'axe Y
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity', // Label de l'axe Y
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Affiche le label du dataset en haut
                    }
                }
            }
            });
        }
    }
    
    
    function renderDonutChart(data) {
        const ctx = document.getElementById('donut-chart').getContext('2d');
        //console.log(ctx);
        
        if (donutChart) {
            donutChart.data = data;
            donutChart.update();
        } else {
            donutChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: { 
                responsive: true,
                
                plugins: {
                    legend: {
                        display: false // Affiche le label du dataset en haut
                    }
                }
            }
            });
        }
    }
    
    
    
    
	//renderWorldwideSales(@js($chartData));
    renderBarChart(@js($bar_chart));
    renderDonutChart(@js($donut_chart));
   

   
       $wire.on('chartsUpdated', ([payload]) => {
		    // payload est directement ton objet avec chartData et barChart
		    //renderWorldwideSales(payload.chartData);
		    renderBarChart(payload.barChart);
		    renderDonutChart(payload.donutChart);
		});
		
		
   
</script>
@endscript   
   
    
    

