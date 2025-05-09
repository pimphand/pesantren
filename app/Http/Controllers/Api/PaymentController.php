<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopUpRequest;
use App\Http\Resources\Api\PaymentResource;
use App\Models\Bank;
use App\Models\Payment;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\IdempotencyHelper;

class PaymentController extends Controller
{
    const BEARER_TOKEN_HEADER = 'Bearer {token}';

    protected object $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * List Payment.
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    public function index() // : PaymentResource
    {
        $payment = $this->user->payments()->with('recipient', 'bank')->paginate(10)->appends(request()->query());

        return PaymentResource::collection($payment)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * Generate idempotency key for payment
     *
     * @response array{data: object{idempotency_key: string}, message: string}
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    public function generateKey(): JsonResponse
    {
        $idempotencyKey = IdempotencyHelper::generateKey($this->user->id);

        return response()->json([
            'message' => 'success',
            'data' => [
                'idempotency_key' => $idempotencyKey
            ]
        ]);
    }

    /**
     * Payment top up
     *
     * @response array{data: object, message: string}
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    #[HeaderParameter('Idempotency-Key', 'Idempotency-Key', '')]
    public function store(TopUpRequest $request): JsonResponse
    {
        // Get idempotency key from header
        $idempotencyKey = request()->header('Idempotency-Key');
        if (!$idempotencyKey) {
            return response()->json([
                'message' => 'Idempotency-Key header diperlukan',
            ], 422);
        }

        // Validate idempotency key format
        if (!IdempotencyHelper::isValidFormat($idempotencyKey)) {
            return response()->json([
                'message' => 'Format Idempotency-Key tidak valid',
            ], 422);
        }

        // Check if this request has been processed before
        $existingPayment = Payment::where('idempotency_key', $idempotencyKey)
            ->where('user_id', $this->user->id)
            ->first();

        if ($existingPayment) {
            return response()->json([
                'message' => 'Transaksi sudah ada. Silahkan gunakan Idempotency-Key yang berbeda',
                'data' => $existingPayment,
            ], 422);
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $idempotencyKey) {
            $payment = Payment::create(
                array_merge($validated, [
                    'payment_type' => 'Top Up',
                    'user_id' => $this->user->id,
                    'status' => 'pending',
                    'idempotency_key' => $idempotencyKey,
                ])
            );

            if (request()->hasFile('proof_of_payment')) {
                $payment->paid_at = now();
                $payment->proof_of_payment = public_path('storage/' . request()->file('proof_of_payment')->store('proof-of-payment', 'public'));
                $payment->save();
            }

            $this->createLog('Top Up ', 'Top Up ', $payment, $payment->toArray(), 'create');

            return response()->json([
                'message' => 'success',
                'data' => $payment,
            ]);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Show Payment.
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    public function show(Request $request, Payment $payment): PaymentResource
    {
        return PaymentResource::make($payment)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * list bank
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    #[QueryParameter('search', 'Bank name', null)]
    public function banks(Request $request): JsonResponse
    {
        $banks = Bank::where('name', 'ilike', "%$request->search%")->get();

        return response()->json([
            'message' => 'success',
            'data' => $banks,
        ]);
    }
}
