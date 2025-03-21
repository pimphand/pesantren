<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('merchant.transaction', [
            'title' => "Transaksi",
            'categories' => auth()->user()->merchant->productCategory->select('name', 'id')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id' => $request->user_id,
                'merchant_id' => auth()->user()->merchant->id,
                'invoice_number' => 'INV/' . time(),
                'status' => 'success',
                'payment_status' => 'success',
            ]);
            $total = 0;
            foreach ($request->items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product'],
                    'quantity' => $item['qty'],
                    'price' => $item['product']['price'],
                ]);

                $total += $item['quantity'] * $item['product']['price'];
            }
            $order->update([
                'total' => $total
            ]);

            $user = User::where('uuid', $request->user_id)->first();

            $user->balanceHistories()->create([
                'type' => 'transaction',
                'amount' => $user->balance- $total,
                'balance' => $user->balance,
                'debit' => $total,
                'reference_type' => Order::class,
                'reference_id' => $order->id
            ]);

            $user->update([
                'balance' => $user->balance - $total
            ]);

            return response()->json([
                'message' => 'Transaksi berhasil'
            ]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * get QR Code for Merchant User
     */
    public function qrCode($id): \Illuminate\Http\JsonResponse
    {
        $user = User::where('uuid', $id)->first();
        return response()->json([
            'data' => [
                'id' => $user->uuid,
                'name' => $user->name,
                'balance' => $user->balance,
            ]
        ]);
    }
}
