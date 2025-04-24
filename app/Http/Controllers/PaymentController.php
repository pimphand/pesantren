<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('payment', [
            'title' => 'Payment',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $old = [
            'status' => $payment->status,
            'amount' => $payment->amount,
        ];
        if($payment->recipient) {
            if($request->status == 'paid') {
                $amount = $request->amount;
                $wallet = $payment->recipient->balance;

                $changeWallet = $payment->recipient->update([
                    'balance' => $wallet + $amount,
                ]);

                
                $changeStatus = $payment->update([
                    'status' => $request->status
                ]);

                $mutation = $payment->parent->balanceHistories()->create([
                    'type' => 'top up',
                    'balance' => $wallet,
                    'amount' => $wallet + $amount,
                    'debit' => $amount,
                    'reference_type' => Payment::class,
                    'reference_id' => $payment->id,
                    'description' => 'Pembayaran Top Up untuk ' . $payment->recipient->name,
                ]);

                $this->createLog('Payment', "Approve Payment", $payment, [
                    'old_data' => $old,
                    'new_data' => $payment->getChanges(),
                ], 'update');
            } else {
                $changeStatus = $payment->update([
                    'status' => $request->status
                ]);

                $this->createLog('Payment', "Approve Payment", $payment, [
                    'old_data' => $old['status'],
                    'new_data' => $payment->getChanges(),
                ], 'update');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    public function data(): AnonymousResourceCollection
    {
        $payment = QueryBuilder::for(Payment::class)
            ->where('payment_type', 'Top Up')
            ->allowedFilters([
                AllowedFilter::scope('name'),
                AllowedFilter::exact('parent.name'),
            ])
            ->allowedSorts(['name', 'description', 'price', 'created_at'])
            ->paginate(10)
            ->appends(request()->query());

        return PaymentResource::collection($payment);
    }
}
