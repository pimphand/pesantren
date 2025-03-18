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

class PaymentController extends Controller
{
    protected object $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * List Payment.
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function index()//: PaymentResource
    {
        $payment = $this->user->payments()->with('recipient', 'bank')->paginate(10)->appends(request()->query());

        return PaymentResource::collection($payment)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * Payment top up
     * @response array{data: object, message: string}
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function store(TopUpRequest $request): JsonResponse
    {
        $payment = Payment::create(array_merge($request->all(), [
            'payment_type' => "Top Up",
            'user_id' => $this->user->id,
            ])
        );

        if ($request->hasFile('proof_of_payment')) {
            $payment->paid_at = now();
            $payment->proof_of_payment = asset('storage/' . $request->file('proof_of_payment')->store('proof-of-payment', 'public'));
            $payment->save();
        }

        return response()->json([
            'message' => 'success',
            'data' => $payment
        ]);
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
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function show(Request $request, Payment $payment): PaymentResource
    {
        return PaymentResource::make($payment)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * list bank
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    #[QueryParameter('search', 'Bank name', null)]
    public function banks(Request $request)
    {
        $banks = Bank::where('name', "like", "%$request->search%")->get();
        return response()->json([
            'message' => 'success',
            'data' => $banks
        ]);
    }
}
