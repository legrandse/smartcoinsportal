<div>
    <div class="container-fluid pt-4 px-4">
        <div class="bg-secondary text-center rounded p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="mb-0">Recent Sales</h6>
                <a href="#" wire:click.prevent="toggleShowAll">
                    {{ $showAll ? 'Show Recent' : 'Show All' }}
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-white">
                            <th scope="col"><input class="form-check-input" type="checkbox"></th>
                            <th scope="col">Date</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Credited</th>
                            <th scope="col">Debited</th>
                            <th scope="col">Status</th>
                            <th scope="col">Jetons</th>
                            <th scope="col">Debtor</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td><input class="form-check-input" type="checkbox"></td>
                            <td>{{$transaction->updated_at}}</td>
                            <td>{{$transaction->amount}}€</td>
                            <td>{{$transaction->inserted_amount}}</td>
                            <td>{{$transaction->debited_amount}}</td>
                            <td>{{$transaction->status}}</td>
                            <td>{{$transaction->reference}}</td>
                            <td>{{$transaction->debtor}}</td>
                            <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>
                        </tr>
                    @endforeach 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
