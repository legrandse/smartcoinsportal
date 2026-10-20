<div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
	<div class="container-fluid pt-4 px-4">
		<div class="col-xl-12 col-sm-12">		
			<div class="row">
				<div class=" bg-secondary rounded-top rounded-bottom p-4">
					
					<h6 class="mb-4">Control panel for {{ $linkedDevice->ref }}</h6>
						<div class="mb-3">
							<label for="formFile" class="form-label">Serial </label>
							<input class="form-control" type="texte" value="{{ $device->serial }}" disabled>
						</div>
					
						<hr>
					
						<form wire:submit="save">
							<div class="row align-items-center">
								<div class="col-md-2">
								<img src="{{ asset('/storage/logo/logo.jpg') }}" height="50" alt="Logo" />
								</div>

								<div class="col-md-6">
								@if ($photo) 
									<img src="{{ $photo->temporaryUrl() }}"  class="d-block mb-2 img-thumbnail" style="height:150px">
								@endif

								<input class="form-control" type="file" wire:model="photo">

								@error('photo') 
									<span class="text-danger">{{ $message }}</span> 
								@enderror
								</div>

								<div class="col-md-4 text-end">
								<button class="btn btn-primary" type="submit">Save photo</button>
								</div>
							</div>
						</form>

						<hr>
						
						

						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="payconiqSwitch"
								wire:click="toggleSetting('payconiqToggle')"
								{{ $payconiqToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="payconiqSwitch">Payconiq</label>
						</div>
						{{--
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="stripeSwitch"
								wire:click="toggleSetting('stripeToggle')"
								{{ $stripeToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="stripeSwitch">Stripe</label>
						</div>

						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="paypalSwitch"
								wire:click="toggleSetting('paypalToggle')"
								{{ $paypalToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="paypalSwitch">Paypal</label>
						</div>
						--}}
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="cashSwitch"
								wire:click="toggleSetting('cashToggle')"
								{{ $cashToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="cashSwitch">Cash</label>
						</div>

						<hr>
			
						<form wire:submit="saveSlider">
						<div class="row align-items-center">
							<div class="col-md-6 d-flex flex-wrap gap-3">
							@forelse($sliders as $index => $slider)
								<div class="position-relative" style="width:50px; height:50px;">
								<img src="{{ $slider }}" class="img-thumbnail w-100 h-100 object-fit-cover" alt="slider {{ $index+1 }}">
								<button type="button" 
										class="btn-close position-absolute top-0 end-0 bg-white rounded-circle p-1" 
										style="transform: translate(30%,-30%);" 
										wire:click="deleteSlider('{{ $slider }}')">
								</button>
								</div>
							@empty
								<p>Aucune image trouvée</p>
							@endforelse
							</div>

							<div class="col-md-6">
							<input class="form-control mb-2" type="file" wire:model="slider">
							@error('slider') 
								<span class="text-danger">{{ $message }}</span> 
							@enderror
							<button class="btn btn-primary" type="submit">Save photo</button>
							</div>
						</div>
						</form>
						
						
						<hr>
						<div class="row mb-3 mt-3">
							<div class="col-md-3">	
								<label for="formFile" class="form-label">1 jeton = €</label>
							</div>
							<div class="col-md-4">
								<input class="form-control use-keyboard-input @error('parity') is-invalid @enderror" type="text" id="Parity" wire:model.live.blur="parity" >
							</div>
							
						</div>
							
						
						<hr>
						
						<div class="mb-3">
							<label for="formFile" class="form-label">Boutton jetons </label>
							<input class="form-control use-keyboard-input @error('tokenArray') is-invalid @enderror" type="text" id="tokenArray" wire:model.live.blur="tokenArray">
						</div>
						
						
						<hr>
						
						<div class="mb-3">
							<label for="formFile" class="form-label">Ngrok </label>
							<input class="form-control use-keyboard-input @error('ngrok') is-invalid @enderror" type="text" id="ngrok" wire:model.live.blur="ngrok">
						</div>
							
						<hr>
						
						
						<div class="mb-3">
							<label for="formFile" class="form-label">Magasin </label>
							<input class="form-control use-keyboard-input @error('magasin') is-invalid @enderror" type="text" id="Magasin"  wire:model.live.blur="magasin"> Jetons
						</div>
						
						
						<label for="formFile" class="form-label">Inventaire initial pièces </label>
						<div class="d-flex gap-2 mb-3">
							
							<select wire:model="denomination" class="form-select">
								<option value="">-- Choisir --</option>
								@foreach($hopperLevels as $level)
									<option value="{{ $level->value_eur }}">
										{{ number_format($level->value_eur, 2, ',', ' ') }} € ({{ $level->denomination_level }} disponibles)
									</option>
								@endforeach
							</select>
						
						
							<label class="form-label">ajouter</label>
							<input type="number" wire:model="quantity" min="1" class="form-control" />
							@error('quantity') <span class="error">{{ $message }}</span> @enderror
						</div>
						
						<div class="mb-3">
							<button wire:click="stackHopper" class="btn btn-primary">Envoyer</button>
						</div>
						
						
						<hr>
				
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch" wire:model="collectHopperToggle"  wire:change="collectHopper">
							<label class="form-check-label" for="stripeSwitch">Collect Hopper</label>
						</div>

						<div class="w-100 d-flex flex-column align-items-end mb-3">
							{{--<button type="button" class="btn btn-primary mb-2" wire:click="resetNotereader">Reset</button>--}}
							
							<div class="w-100 d-flex justify-content-end mb-2">
								<div class="form-check form-switch form-switch-reverse">
									<input class="form-check-input" type="checkbox" role="switch"
										wire:model="collectToggle"
										wire:change="collectNotereader">
									<label class="form-check-label" for="stripeSwitch">Collect Notereader</label>
								</div>
							</div>
							
							<div class="w-100 d-flex justify-content-end mb-2">
								<div class="form-check form-switch form-switch-reverse">
									<input class="form-check-input" type="checkbox" role="switch"
										wire:model="stackToggle"
										wire:change="stackNote">
									<label class="form-check-label" for="stripeSwitch">Stack Notes</label>
								</div>
							</div>
						</div>

						<hr>

						<div class="mb-3" >
							<button type="button" class="btn btn-primary float-start" wire:click="rebootRaspberry" wire:confirm="Are you sure you want to reboot?"><i class="fas fa-sync" style="color: #f9a907;"></i></button>
							<label for="formFile" class="form-label float-start mx-3">Reboot Distributor </label>
						</div>
						
						<div class="mb-3" >
							<button type="button" class="btn btn-primary float-end" wire:click="shutdownRaspberry" wire:confirm="Are you sure you want to shutdown?"><i class="fas fa-power-off" style="color: #ff0000;"></i></button>
							<label for="formFile" class="form-label float-end mx-3">Power off Distributor </label>
						</div>
							

				</div>
			</div>
		</div>
	</div>
</div>
