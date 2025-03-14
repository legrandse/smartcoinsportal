<div>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Worldwide Sales</h6>
                        <a href="">Show All</a>
                    </div>
                    <canvas id="worldwide-sales"></canvas>
                </div>
            </div>
            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Token sales</h6>
                        <a href="">Show All</a>
                    </div>
                    <canvas id="bar-chart"></canvas>
                </div>
            </div>
            {{--<div class="col-sm-12 col-xl-6">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Salse & Revenue</h6>
                        <a href="">Show All</a>
                    </div>
                    <canvas id="salse-revenue"></canvas>
                </div>
            </div>--}}
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:init', () => {
            
            var ctx = document.getElementById('worldwide-sales').getContext('2d');
            
            // Récupérer les données depuis Livewire
            var chartData = @js($chartData);
			
            // Créer le graphique avec Chart.js
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true
                }
            });
        
        
        	
        	var ctx4 = document.getElementById("bar-chart").getContext("2d");
		    var bar_chart = @js($bar_chart);
		    console.log(bar_chart);
		    new Chart(ctx4, {
		        type: "bar",
		        data: bar_chart,
		        options: {
		            responsive: true
		        }
		    });
        	
        	
        	
        
        });
        
        
        
        
        
    </script>
    
    <script>
    // Single Bar Chart
    
    </script>
   
    
    
</div>
