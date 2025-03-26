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
    public function index()// : PaymentResource
    {
        $payment = $this->user->payments()->with('recipient', 'bank')->paginate(10)->appends(request()->query());

        return PaymentResource::collection($payment)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * Payment top up
     *
     * @response array{data: object, message: string}
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    public function store(TopUpRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $payment = Payment::create(array_merge($request->validated(), [
                    'payment_type' => 'Top Up',
                    'user_id' => $this->user->id,
                ])
            );

            if ($request->hasFile('proof_of_payment')) {
                $payment->paid_at = now();
                $payment->proof_of_payment = asset('storage/' . $request->file('proof_of_payment')->store('proof-of-payment', 'public'));
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
