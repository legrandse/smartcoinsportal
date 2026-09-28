<div>
    <div class="container-fluid pt-4 px-4">
	    <div class="row g-4">
	    	<div class="col-sm-12 col-xl-12">
	        	<div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
					<select class="form-select" wire:model.live="deviceId">
					  <option disabled value="" >Sélectionner un appareil</option>
					  @foreach($devices as $device)
					  <option value="{{ $device->device_id }}">{{ $device->ref }}</option>
					  @endforeach
					</select>
				</div>
			</div>
		</div>
	</div>


    <div class="container-fluid pt-4 px-4">
        <div class="d-flex justify-content-end mb-3 ">
            
        

        
            <div class="col-sm-12 col-xl-12">
                <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-chart-line fa-3x text-primary"></i>
                    <select wire:model.live="period" class="form-select form-select-sm bg-secondary text-white border-0" style="width: 150px;">
                        <option value="1">Aujourd'hui</option>
                        <option value="2">2 jours</option>
                        <option value="7">1 semaine</option>
                        <option value="30">1 mois</option>
                        <option value="custom">Personnalisé...</option>
                    </select>

                <div x-data="{ 
                        init() { 
                            flatpickr($refs.picker, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                defaultDate: ['{{ $startDate }}', '{{ $endDate }}'],
                                onChange: (selectedDates) => {
                                    if (selectedDates.length === 2) {
                                        @this.set('startDate', selectedDates[0].toISOString().split('T')[0]);
                                        @this.set('endDate', selectedDates[1].toISOString().split('T')[0]);
                                    }
                                }
                            }) 
                        } 
                    }" 
                    class="{{ $period === 'custom' ? '' : 'd-none' }}">
                    <input x-ref="picker" type="text" class="form-control form-control-sm bg-secondary text-white border-0" placeholder="Choisir les dates">
                </div>
                        <div class="ms-3">
                            <p class="mb-2">
                                {{ $period === 'custom' ? 'Période sélectionnée' : 'Ventes du moment' }}
                            </p>
                            <h6 class="mb-0">{{ number_format($dailySales, 2, ',', ' ') }} €</h6>
                        </div>
                    </div>
                </div>
        
    	    </div>
        </div>

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
   


        <!--toast message-->
        <div wire:ignore  class="toast align-items-right bg-primary border-0" id="toast-loading" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Nouvelle transaction...
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

        <div class="container-fluid pt-4 px-4">
            <div class="bg-secondary text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Recent Sales</h6>
                    <button class="btn btn-danger d-none" id="deleteBtn" wire:click="deleteSelected" wire:confirm="Are you sure you want to delete this transaction?"  @disabled(empty($selected))>Delete Selected</button>
                    <a href="#" wire:click.prevent="toggleShowAll">
                        {{ $showAll ? 'Show Recent' : 'Show All' }}
                    </a>
                    
                </div>
                
                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-white">
                                <th scope="col"><input class="form-check-input" type="checkbox" wire:model.live="selectAll"></th>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Credited</th>
                                <th scope="col">Debited</th>
                                <th scope="col">Status</th>
                                <th scope="col">Jetons</th>
                                <th scope="col">Type</th>
                                <th scope="col">Debtor</th>
                                <!--<th scope="col">Action</th>-->
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><input class="form-check-input" type="checkbox" value="{{ $transaction->id }}" wire:model.live="selected"></td>
                                <td>{{$transaction->updated_at}}</td>
                                <td>{{$transaction->amount}}€</td>
                                <td>{{$transaction->inserted_amount}}€</td>
                                <td>{{$transaction->debited_amount}}€</td>
                                <td>{{$transaction->status}}</td>
                                <td>{{$transaction->reference}}</td>
                                <td>@if($transaction->debtor == '')Cash @else Bancontact @endif</td>
                                <td>{{$transaction->debtor}}</td>
                            <!-- <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>-->
                            </tr>
                        @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>    
@script
	<script>
		Livewire.on('transaction-received', () => {
		    // Afficher un petit toast bootstrap
		    $('#toast-loading .toast-body').text("Nouvelle transaction...");
		    $('#toast-loading').toast('show');
		});
	</script>
	
	
@endscript   
@script
	<script>
		$wire.on('showDeleteButton', (event) => {
			console.log('delete');
			$(function(){
				const show = document.getElementById('deleteBtn');
				show.classList.remove('d-none');

				/*setTimeout(() => {
					window.location.href = "/";
				}, 6000);*/
			});
		});
	</script>
@endscript

@script
	<script>
		Livewire.on('deleted', () => {
		    // Afficher un petit toast bootstrap
		    $('#toast-loading .toast-body').text("Transactions deleted.");
		    $('#toast-loading').toast('show');
		});
	</script>
@endscript





















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

