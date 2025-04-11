<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Models\Merchant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\MerchantResource;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('merchant', [
            'title' => 'Merchant',
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
    public function store(StoreMerchantRequest $merchantRequest, StoreUserRequest $userRequest): \Illuminate\Http\JsonResponse
    {
        $user = User::insertGetId(array_merge($userRequest->validate()));

        $merchant = Merchant::create(array_merge($merchantRequest->validated(), [
            'user_id' => $user->id,
        ]));

        $this->createLog('Product', 'Create Product', $merchant, [
            'old_data' => null,
            'new_data' => $merchant->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Merchant $merchant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Merchant $merchant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant)
    {
        //
    }
    public function data(): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(Merchant::class)
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return MerchantResource::collection($categories);
    }
}
