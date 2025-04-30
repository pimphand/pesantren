<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\MerchantResource;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Str;
use App\Models\Merchant;
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
    public function store(StoreMerchantRequest $merchantRequest): \Illuminate\Http\JsonResponse
    {
        $user = User::create(array_merge($merchantRequest->validated(), [
            'uuid'     => Str::uuid(),
            'parent_id'     => null,
            'password' => bcrypt($merchantRequest->password),
            'pin'      => bcrypt($merchantRequest->pin),
        ]))->addRole('merchant');

        $merchant = Merchant::create([
            'user_id' => $user->id,
            'name' => $merchantRequest->name ?? null,
            'category' => $merchantRequest->category ?? null,
            'address' => $merchantRequest->address ?? null,
            'is_pin' => $merchantRequest->is_pin ? true : false,
            'is_tax' => $merchantRequest->is_tax ? true : false,
            'tax' => $merchantRequest->tax ?? 0,
        ]);

        $this->createLog('Merchant', 'Create merchant', $merchant, [
            'old_data' => null,
            'new_data' => $merchant->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Merchant berhasil ditambahkan',
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
    public function update(UpdateMerchantRequest $request, string $merchant)
    {
        // Get user
        $user = User::find($merchant);
        // Get merchant data
        $merchantdata = Merchant::where('user_id', $user->id)->first();
        // Get old data
        $oldUser = $user->getOriginal();
        $oldMerchant = $merchantdata->getOriginal();
    
        // Update data user
        $user->update(array_merge(
            $request->validated(),
            [
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'phone' => $request->phone,
                'parent_id' => $request->parent_id,
            ]
        ));

        // Update or create merchant data
        $merchantdata->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->name ?? null,
                'address' => $request->address ?? null,
                'level' => $request->level ?? null,
                'description' => $request->description ?? null,
                'category' => $request->category ?? null,
                'is_pin' => $request->is_pin ? true : false,
                'is_tax' => $request->is_tax ? true : false,
                'tax' => $request->tax_input ?? 0,
            ]
        );

        // Logging perubahan
        $this->createLog('Merchant', 'Update Merchant', $user,
            [
                'old_data' => [
                    'user' => $oldUser,
                    'merchant' => $oldMerchant,
                ],
                'new_data' => [
                    'user' => $user->getChanges(),
                    'merchant' => $merchantdata->getChanges(),
                ],
            ],
            'update'
        );

        return response()->json([
            'message' => 'Merchant berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user $merchant)
    {
        $oldMerchant = $merchant->getOriginal();
        $merchant->delete();

        $this->createLog('Merchant', 'Delete merchant', $merchant, [
            'old_data' => $oldMerchant,
            'new_data' => null,
        ], 'delete');

        return response()->json([
            'message' => 'Merchant berhasil dihapus',
        ]);
    }
    public function data(): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(User::class)
            ->withRole('merchant')
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return MerchantResource::collection($categories);
    }
}
