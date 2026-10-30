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
					
					<h6 class="mb-4">{{ __('Control panel for') }} {{ $linkedDevice->ref }}</h6>
						<div class="mb-3">
							<label for="formFile" class="form-label">{{ __('Serial') }}</label>
							<input class="form-control" type="text" value="{{ $device->serial }}" disabled>
						</div>
					
						<hr>
					
						<form wire:submit="save">
							<div class="row align-items-center">
								<label class="form-check-label" for="photo">{{ __('Logo') }}</label>
								<div class="col-md-2">
								<img src="{{ asset('/storage/'.$linkedDevice->device_id.'/logo/logo.jpg') }}" height="50" alt="Logo" />
								</div>

								<div class="col-md-6">
								@if ($photo) 
									<img src="{{ $photo->temporaryUrl() }}" class="d-block mb-2 img-thumbnail" style="height:150px">
								@endif

								<input class="form-control" type="file" wire:model="photo">

								@error('photo') 
									<span class="text-danger">{{ $message }}</span> 
								@enderror
								</div>

								<div class="col-md-4 text-end">
								<button class="btn btn-primary" type="submit">{{ __('Save photo') }}</button>
								</div>
							</div>
						</form>

						<hr>
						
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="payconiqSwitch"
								wire:click="toggleSetting('payconiqToggle')"
								{{ $payconiqToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="payconiqSwitch">{{ __('Payconiq') }}</label>
						</div>
						{{--
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="stripeSwitch"
								wire:click="toggleSetting('stripeToggle')"
								{{ $stripeToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="stripeSwitch">{{ __('Stripe') }}</label>
						</div>

						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="paypalSwitch"
								wire:click="toggleSetting('paypalToggle')"
								{{ $paypalToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="paypalSwitch">{{ __('Paypal') }}</label>
						</div>
						--}}
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch"
								id="cashSwitch"
								wire:click="toggleSetting('cashToggle')"
								{{ $cashToggle ? 'checked' : '' }}>
							<label class="form-check-label" for="cashSwitch">{{ __('Cash') }}</label>
						</div>

						<hr>
			
						<form wire:submit="saveSlider">
						<div class="row align-items-center">
							<label class="form-check-label" for="slider">{{ __('Slider') }}</label>
							<div class="col-md-6 d-flex flex-wrap gap-3">
							
							@forelse($sliders as $index => $sliderUrl)
								<div class="position-relative" style="width:50px; height:50px;">
									<img src="{{ $sliderUrl }}" class="img-thumbnail w-100 h-100 object-fit-cover" alt="slider {{ $index+1 }}">
									<button type="button" 
											class="btn-close position-absolute top-0 end-0 bg-white rounded-circle p-1" 
											style="transform: translate(30%,-30%);" 
											wire:click="deleteSlider('{{ $sliderUrl }}')">
									</button>
								</div>
							@empty
								<p>{{ __('No image found') }}</p>
							@endforelse
							</div>

							<div class="col-md-6">
							@if ($slider) 
									<img src="{{ $slider->temporaryUrl() }}" class="d-block mb-2 img-thumbnail" style="height:150px">
							@endif
							<input class="form-control mb-2" type="file" wire:model="slider">
							@error('slider') 
								<span class="text-danger">{{ $message }}</span> 
							@enderror
							<button class="btn btn-primary" type="submit">{{ __('Save photo') }}</button>
							</div>
						</div>
						</form>
						
						<hr>
						<div class="row mb-3 mt-3">
							<div class="col-md-3">	
								<label for="formFile" class="form-label">{{ __('1 token = €') }}</label>
							</div>
							<div class="col-md-4">
								<input class="form-control use-keyboard-input @error('parity') is-invalid @enderror" type="text" id="Parity" wire:model.live.blur="parity">
							</div>
						</div>
							
						<hr>
						
						<div class="mb-3">
							<label for="formFile" class="form-label">{{ __('Token buttons') }}</label>
							<input class="form-control use-keyboard-input @error('tokenArray') is-invalid @enderror" type="text" id="tokenArray" wire:model.live.blur="tokenArray">
						</div>
						
						<hr>
						
						<div class="mb-3">
							<label for="formFile" class="form-label">{{ __('Ngrok') }}</label>
							<input class="form-control use-keyboard-input @error('ngrok') is-invalid @enderror" type="text" id="ngrok" wire:model.live.blur="ngrok">
						</div>
							
						<hr>
						
						<div class="mb-3">
							<label for="formFile" class="form-label">{{ __('Magazine') }}</label>
							<input class="form-control use-keyboard-input @error('magasin') is-invalid @enderror" type="text" id="Magasin" wire:model.live.blur="magasin"> {{ __('Tokens') }}
						</div>
						
						<label for="formFile" class="form-label">{{ __('Initial coin inventory') }}</label>
						<div class="d-flex gap-2 mb-3">
							
							<select wire:model="denomination" class="form-select">
								<option value="">-- {{ __('Choose') }} --</option>
								@foreach($hopperLevels as $level)
									<option value="{{ $level->value_eur }}">
										{{ number_format($level->value_eur, 2, ',', ' ') }} € ({{ $level->denomination_level }} {{ __('available') }})
									</option>
								@endforeach
							</select>
						
							<label class="form-label">{{ __('add') }}</label>
							<input type="number" wire:model="quantity" min="1" class="form-control" />
							@error('quantity') <span class="error">{{ $message }}</span> @enderror
						</div>
						
						<div class="mb-3">
							<button wire:click="stackHopper" class="btn btn-primary">{{ __('Send') }}</button>
						</div>
						
						<hr>
				
						<div class="form-check form-switch mb-3">
							<input class="form-check-input" type="checkbox" role="switch" wire:model="collectHopperToggle" wire:change="collectHopper">
							<label class="form-check-label" for="stripeSwitch">{{ __('Collect Hopper') }}</label>
						</div>

						<div class="w-100 d-flex flex-column align-items-end mb-3">
							{{--<button type="button" class="btn btn-primary mb-2" wire:click="resetNotereader">{{ __('Reset') }}</button>--}}
							
							<div class="w-100 d-flex justify-content-end mb-2">
								<div class="form-check form-switch form-switch-reverse">
									<input class="form-check-input" type="checkbox" role="switch"
										wire:model="collectToggle"
										wire:change="collectNotereader">
									<label class="form-check-label" for="stripeSwitch">{{ __('Collect Notereader') }}</label>
								</div>
							</div>
							
							<div class="w-100 d-flex justify-content-end mb-2">
								<div class="form-check form-switch form-switch-reverse">
									<input class="form-check-input" type="checkbox" role="switch"
										wire:model="stackToggle"
										wire:change="stackNote">
									<label class="form-check-label" for="stripeSwitch">{{ __('Stack Notes') }}</label>
								</div>
							</div>
						</div>

						<hr>

						<div class="mb-3">
							<button type="button" class="btn btn-primary float-start" wire:click="rebootRaspberry" wire:confirm="{{ __('Are you sure you want to reboot?') }}"><i class="fas fa-sync" style="color: #f9a907;"></i></button>
							<label for="formFile" class="form-label float-start mx-3">{{ __('Reboot Distributor') }}</label>
						</div>
						
						<div class="mb-3">
							<button type="button" class="btn btn-primary float-end" wire:click="shutdownRaspberry" wire:confirm="{{ __('Are you sure you want to shutdown?') }}"><i class="fas fa-power-off" style="color: #ff0000;"></i></button>
							<label for="formFile" class="form-label float-end mx-3">{{ __('Power off Distributor') }}</label>
						</div>

				</div>
			</div>
		</div>
	</div>
</div>