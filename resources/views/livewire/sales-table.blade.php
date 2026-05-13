<div>
	<!--toast message-->
 	<div  class="toast align-items-right bg-primary border-0" id="toast-loading" role="alert" aria-live="assertive" aria-atomic="true">
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
@script
	<script>
	const deviceSerial = "{{ $transaction->device }}";
		Echo.private(`transaction.${deviceSerial}`)
		.listen('TransactionsListener', e => {
		    console.log(e.transaction)
		    $(function(){
		 		 
				  $('#toast-loading').toast('show');

		          
				});
			$wire.$refresh();
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
