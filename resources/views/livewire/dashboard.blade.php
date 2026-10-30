<div>
    <div class="container-fluid pt-4 px-4">
	    <div class="row">
	    	<div class="col-sm-12 col-xl-12 mb-3">
	        	<div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
					<select class="form-select" wire:model.live="deviceId">
					  <option disabled value="">{{ __('Select a device') }}</option>
					  @foreach($devices as $device)
					  <option value="{{ $device->device_id }}">{{ $device->ref }}</option>
					  @endforeach
					</select>
				</div>
			</div>
		
            <div class="d-flex justify-content-end mb-3">
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-secondary rounded p-4">
                        
                        <!-- Ligne principale : Icône, Sélecteur et Montant -->
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            
                            <select wire:model.live="period" class="form-select form-select-sm bg-secondary text-white border-0" style="width: 150px;">
                                <option value="1">{{ __('Today') }}</option>
                                <option value="2">{{ __('2 days') }}</option>
                                <option value="7">{{ __('1 week') }}</option>
                                <option value="30">{{ __('1 month') }}</option>
                                <option value="custom">{{ __('Custom...') }}</option>
                            </select>

                            <div class="ms-3">
                                <p class="mb-2">
                                    {{ $period === 'custom' ? __('Selected period') : __('Current sales') }}
                                </p>
                                <h6 class="mb-0">{{ number_format($dailySales, 2, ',', ' ') }} €</h6>
                            </div>
                        </div>

                        <!-- Div du datepicker placée en dessous -->
                        <div x-data="{ 
                            init() { 
                                flatpickr($refs.picker, {
                                    mode: 'range',
                                    dateFormat: 'Y-m-d',
                                    onChange: (selectedDates) => {
                                        if (selectedDates.length === 2) {
                                            @this.set('startDate', selectedDates[0].toISOString().split('T')[0]);
                                            @this.set('endDate', selectedDates[1].toISOString().split('T')[0]);
                                        }
                                    }
                                }) 
                            } 
                        }" 
                        class="mt-3 {{ $period === 'custom' ? '' : 'd-none' }}">
                            <input x-ref="picker" type="text" class="form-control form-control-sm bg-secondary text-white" placeholder="{{ __('Choose dates') }}">
                        </div>

                    </div>
                </div>
            </div>
    
            <div class="col-sm-12 col-xl-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">{{ __('Token sales')  }} {{ $tokenSales }}</h6>
                    </div>
                    <div wire:ignore>
                        <canvas id="bar-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4">
                <div wire:ignore class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">{{ __('Cash vs Bancontact') }}</h6>
                    </div>
                    <canvas id="donut-chart"></canvas>
                </div>
            </div>

            <!--toast message-->
            <div wire:ignore class="toast align-items-right bg-primary border-0" id="toast-loading" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ __('New transaction...') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>

            <div class="bg-secondary text-center rounded p-4 mt-3">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">{{ __('Recent Sales') }}</h6>
                    <button class="btn btn-danger d-none" id="deleteBtn" wire:click="deleteSelected" wire:confirm="{{ __('Are you sure you want to delete this transaction?') }}" @disabled(empty($selected))>{{ __('Delete Selected') }}</button>
                    <a href="#" wire:click.prevent="toggleShowAll">
                        {{ $showAll ? __('Show Recent') : __('Show All') }}
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-white">
                                <th scope="col"><input class="form-check-input" type="checkbox" wire:model.live="selectAll"></th>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col">{{ __('Amount') }}</th>
                                <th scope="col">{{ __('Credited') }}</th>
                                <th scope="col">{{ __('Debited') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Tokens') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Debtor') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><input class="form-check-input" type="checkbox" value="{{ $transaction->id }}" wire:model.live="selected"></td>
                                <td>{{ $transaction->updated_at }}</td>
                                <td>{{ $transaction->amount }}€</td>
                                <td>{{ $transaction->inserted_amount }}€</td>
                                <td>{{ $transaction->debited_amount }}€</td>
                                <td>{{ $transaction->status }}</td>
                                <td>{{ $transaction->reference }}</td>
                                <td>@if($transaction->debtor == '') {{ __('Cash') }} @else {{ __('Bancontact') }} @endif</td>
                                <td>{{ $transaction->debtor }}</td>
                            </tr>
                        @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>
    
@script
	<script>
		$wire.on('transaction-received', () => {
		    $('#toast-loading .toast-body').text("{{ __('New transaction...') }}");
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
			});
		});
	</script>
@endscript

@script
	<script>
		$wire.on('deleted', () => {
		    $('#toast-loading .toast-body').text("{{ __('Transactions deleted.') }}");
		    $('#toast-loading').toast('show');
		});
	</script>
@endscript

@script
<script>
    let worldwideChart = null;
    let barChart = null;
    let donutChart = null;

    function renderBarChart(data) {
        const ctx = document.getElementById('bar-chart').getContext('2d');
        
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
                                text: '{{ __('Tokens') }}',
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
                                text: '{{ __('Quantity') }}',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }
    
    function renderDonutChart(data) {
        const ctx = document.getElementById('donut-chart').getContext('2d');
        
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
                            display: false
                        }
                    }
                }
            });
        }
    }
    
    renderBarChart(@js($bar_chart));
    renderDonutChart(@js($donut_chart));

    $wire.on('chartsUpdated', ([payload]) => {
        renderBarChart(payload.barChart);
        renderDonutChart(payload.donutChart);
    });
</script>
@endscript