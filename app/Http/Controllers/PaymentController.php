<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device' => 'required|string',
            'payment_id' => 'required|string',
            'status' => 'required|string',
            'amount' => 'required|numeric',
            'inserted_amount' => 'nullable|numeric',
            'debited_amount' => 'nullable|numeric',
            'reference' => 'nullable|numeric',
            'debtor' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $payment = Transactions::create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $payment,
        ], 201);
    }
    
    
    public function update(Request $request, $payment_id)
    {
    	
    	
        $validator = Validator::make($request->all(), [
            
            //'payment_id' => 'required|string',
            'status' => 'required|string',
            
            'inserted_amount' => 'nullable|numeric',
            'debited_amount' => 'nullable|numeric',
            
            'debtor' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $transaction = Transactions::where('payment_id', $payment_id)
				->latest('created_at')->first();

        $transaction->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ], 201);
    }
    
    
    
}
