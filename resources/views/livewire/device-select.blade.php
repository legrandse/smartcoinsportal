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
</div>
