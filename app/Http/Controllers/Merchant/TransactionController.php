<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\OrderResource;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Services\TokenService;
use App\Models\IdempotencyKey;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TransactionController extends Controller
{

    use AuthorizesRequests;
    protected mixed $merchant;

    public function __construct()
    {
        $this->merchant = auth()->user()->merchant;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('merchant.transaction', [
            'title' => 'Transaksi',
            'categories' => $this->merchant->productCategory->select('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Get idempotency key from header
            $idempotencyKey = $request->header('Idempotency-Key');
            if (!$idempotencyKey) {
                return response()->json([
                    'message' => 'Idempotency-Key header is required'
                ], 422);
            }

            // Check if this request has been processed before
            $existingKey = IdempotencyKey::where('key', $idempotencyKey)
                ->where('merchant_id', $this->merchant->id)
                ->first();

            if ($existingKey) {
                // Return the previous response
                return response()->json($existingKey->response_data);
            }

            // Process the transaction
            $user = User::where('uuid', $request->user_id)->first();

            if ($this->merchant->is_pin) {
                if (empty($request->pin)) {
                    return response()->json([
                        'message' => 'PIN diperlukan',
                        'pin' => true,
                    ], 422);
                }
                if (! Hash::check($request->pin, auth()->user()->pin)) {
                    return response()->json([
                        'message' => 'PIN yang anda masukkan salah',
                        'pin' => true,
                    ], 422);
                }
            }

            $taxRate = $this->merchant->is_tax ? ($this->merchant->tax / 100) : 0;

            $order = $user->orders()->create([
                'user_id' => $user->id,
                'merchant_id' => $this->merchant->id,
                'invoice_number' => 'INV/' . time(),
                'status' => 'success',
                'payment_status' => 'success',
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product']);
                $oldStock = $product->stock;
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->price,
                ]);

                $total += ($item['qty'] * $product->price);

                $product->update([
                    'stock' => $oldStock - $item['qty'],
                ]);

                $this->createLog('Product', "Update Stock Dari transaksi $order->invoice_number", $product, [
                    'stock' => $oldStock,
                    'new_stock' => $oldStock - $item['qty'],
                ], 'update');
            }

            $total_tax = $total * $taxRate;
            $grandTotal = $total + $total_tax;

            if ($user->balance < $grandTotal) {
                abort(403, 'Saldo tidak mencukupi');
            }

            $order->update([
                'total' => $grandTotal,
                'tax' => $total_tax,
            ]);

            $this->createLog('Transaksi', 'Transaksi berhasil', $order, $order->toArray(), 'create');

            $mutation = $user->balanceHistories()->create([
                'type' => 'transaction',
                'balance' => $user->balance,
                'debit' => $grandTotal,
                'amount' => $user->balance - $grandTotal,
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'description' => 'Pembayaran transaksi ' . $order->invoice_number,
            ]);

            $this->createLog('Mutasi', 'Pembayaran transaksi ' . $order->invoice_number, $mutation, $mutation->toArray(), 'create');

            $user->update([
                'balance' => $user->balance - $grandTotal,
            ]);

            $order->payment()->create([
                'amount' => $grandTotal,
                'status' => 'paid',
                'payment_type' => 'Transaction',
                'user_id' => $user->id,
                'payment_method' => 'QRCODE',
            ]);

            $this->createLog('User', 'Pengurangan Saldo dari Transaksi ' . $order->invoice_number, $user, [
                'saldo_awal' => $user->balance + $grandTotal,
                'saldo_akhir' => $user->balance,
            ], 'update');

            // Prepare success response
            $response = [
                'message' => 'Transaksi berhasil',
                'data' => $order->id,
            ];

            // Store the idempotency key and response
            IdempotencyKey::create([
                'key' => $idempotencyKey,
                'merchant_id' => $this->merchant->id,
                'request_data' => $request->all(),
                'response_data' => $response,
                'status' => 'success'
            ]);

            return response()->json($response);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

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
        $data = explode('|', $id);
        $user = User::where('uuid', $data[0])->first();
        if (! $user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }
        return response()->json([
            'data' => [
                'id' => $user->uuid,
                'name' => $user->name,
                'balance' => $user->balance,
            ],
        ]);
    }

    /**
     * get data for datatable
     */
    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection|array
    {
        $orders = QueryBuilder::for(Order::class)
            ->where('merchant_id', $this->merchant->id)
            ->allowedFilters([
                'invoice_number',
                'status',
                'payment_status',
                'created_at',
                'updated_at',
            ])->orderBy('created_at', 'desc');
        if ($request->create) {
            return [
                'total_order' => $orders->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
                'total_amount' => $orders->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->sum('total'),
            ];
        }

        $data = $orders->paginate($request->per_page ?? 10)
            ->appends($request->all());
        return OrderResource::collection($data);
    }

    /**
     * print invoice
     */
    public function printInvoice(Order $order): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('merchant.invoice', [
            'order' => $order->load('orderItems.product'),
        ]);
    }

    /**
     * Generate a new transaction token
     */
    public function generateToken(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'token' => TokenService::generateTransactionToken(),
            'expires_at' => now()->addMinutes(5)->toDateTimeString(),
        ]);
    }
}
