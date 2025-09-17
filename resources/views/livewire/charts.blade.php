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
                    <canvas id="sales-revenue"></canvas>
                </div>
            </div>--}}
        </div>
    </div>
</div>   
@script
<script>
    let worldwideChart = null;
    let barChart = null;

    function renderWorldwideSales(data) {
        const ctx = document.getElementById('worldwide-sales').getContext('2d');
        if (worldwideChart) {
            worldwideChart.data = data;
            worldwideChart.update();
        } else {
            worldwideChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: { responsive: true }
            });
        }
    }

    function renderBarChart(data) {
        const ctx = document.getElementById('bar-chart').getContext('2d');
        if (barChart) {
            barChart.data = data;
            barChart.update();
        } else {
            barChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: { responsive: true }
            });
        }
    }
	renderWorldwideSales(@js($chartData));
    renderBarChart(@js($bar_chart));
   

   
       $wire.on('chartsUpdated', ([payload]) => {
    // payload est directement ton objet avec chartData et barChart
    renderWorldwideSales(payload.chartData);
    renderBarChart(payload.barChart);
});
   
</script>
@endscript   
   
    
    

