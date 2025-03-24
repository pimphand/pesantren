<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

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
            $user = User::where('uuid', $request->user_id)->first();
            $order = $user->orders()->create([
                'user_id' => $user->id,
                'merchant_id' => auth()->user()->merchant->id,
                'invoice_number' => 'INV/' . time(),
                'status' => 'success',
                'payment_status' => 'success',
            ]);
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product']);
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->price,
                ]);

                $total += $item['qty'] * $product->price;

                $product->update([
                    'stock' => $product->stock - $item['qty']
                ]);
            }
            $order->update([
                'total' => $total
            ]);

            $user->balanceHistories()->create([
                'type' => 'transaction',
                'amount' => $user->balance- $total,
                'balance' => $user->balance,
                'debit' => $total,
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'description' => 'Pembayaran transaksi ' . $order->invoice_number
            ]);

            if ($user->balance < $total){
                abort(403, 'Saldo tidak mencukupi');
            }

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

    /**
     * get data for datatable
     */
    public function data(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->where('merchant_id', auth()->user()->merchant->id)
            ->allowedFilters([
                'invoice_number',
                'status',
                'payment_status',
                'created_at',
                'updated_at',
            ]);
        if ($request->create){

            return [
                'total_order'=>$orders->whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->count(),
                'total_amount'=>$orders->whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->sum('total')
            ];
        }

        $data= $orders->paginate(10)
            ->appends($request->all());

        return OrderResource::collection($data);
    }
}
