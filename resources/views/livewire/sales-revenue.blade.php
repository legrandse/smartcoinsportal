<div>
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
</div>

