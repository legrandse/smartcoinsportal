<div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
	<div class="col-sm-12">		
		<div class="card bg-secondary rounded p-4 mt-4">
			<div class="card-header ">
			<h6 class="mb-4">Control panel</h6>
			</div>
			
			<div class="card-body bg-secondary rounded p-4">
				<div class="mb-3">
				  <label for="formFile" class="form-label">Logo </label>
				  <form wire:submit="save">
				      @if ($photo) 
				        <img src="{{ $photo->temporaryUrl() }}">
				    @endif
					    <input class="form-control" type="file" wire:model="photo">
					 
					    @error('photo') <span class="error">{{ $message }}</span> @enderror
					 
					    <button class="btn btn-primary" type="submit">Save photo</button>
					</form>
				</div>

				
				

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
			<div class="row mb-3 mt-3">
				  <div class="col-md-3">	
				  	<label for="formFile" class="form-label">1 jeton = €</label>
				  </div>
				  <div class="col-md-4">
				  	<input class="form-control use-keyboard-input @error('parity') is-invalid @enderror" type="text" id="Parity" wire:model.live.debounce.500ms="parity" >
				  </div>
				  
			</div>
				
			
			<hr>
			
			<div class="mb-3">
				  <label for="formFile" class="form-label">Boutton jetons </label>
				  <input class="form-control use-keyboard-input @error('tokenArray') is-invalid @enderror" type="text" id="tokenArray" wire:model.live.debounce.500ms="tokenArray">
			</div>
			
			
			<hr>
			
			<div class="mb-3">
				  <label for="formFile" class="form-label">Ngrok </label>
				  <input class="form-control use-keyboard-input @error('ngrok') is-invalid @enderror" type="text" id="ngrok" wire:model.live.debounce.500ms="ngrok">
			</div>
				
			<hr>
			
			
			<div class="mb-3">
				  <label for="formFile" class="form-label">Magasin </label>
				  <input class="form-control use-keyboard-input @error('magasin') is-invalid @enderror" type="text" id="Magasin"  wire:model.live.debounce.500ms="magasin"> Jetons
			</div>
			
			<div class="mb-3">
				<button type="button" class="btn btn-primary" wire:click="resetNotereader">Reset</button>
			</div>

			<div class="mb-3">
				<button type="button" class="btn btn-primary" wire:click="collectNotereader">Collect</button>
			</div>
			
			<div class="mb-3">
				<button type="button" class="btn btn-primary" wire:click="stackNote">Stack Note</button>
			</div>
			
			<div class="mb-3" >
				<button type="button" class="btn btn-primary float-end" wire:click="shutdownRaspberry" wire:confirm="Are you sure you want to shutdown?"><i class="fas fa-power-off" style="color: #ff0000;"></i></button>
				<label for="formFile" class="form-label float-end mx-3">Power off Distributor </label>
			</div>
			<!--	<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
				  <label class="form-check-label" for="flexSwitchCheckDefault">Default switch checkbox input</label>
				</div>
				<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
				  <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label>
				</div>
				<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDisabled" disabled>
				  <label class="form-check-label" for="flexSwitchCheckDisabled">Disabled switch checkbox input</label>
				</div>
				<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled" checked disabled>
				  <label class="form-check-label" for="flexSwitchCheckCheckedDisabled">Disabled checked switch checkbox input</label>
				</div>-->


			</div>
		</div>
	</div>
</div>
