<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Models\Merchant;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|Application|View
    {
        return view('merchant.profile', [
            'title' => 'Profile',
            'merchant' => auth()->user()?->merchant,
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
    public function store(StoreEmployeeRequest $request)
    {
        //
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
    public function update(UpdateMerchantRequest $request, Merchant $merchant): \Illuminate\Http\JsonResponse
    {
        if ($merchant->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke merchant ini',
            ], 403);
        }

        $merchant->update(array_merge($request->validated(),[
            'photo' => $request->hasFile('photo') ? asset('storage/'.$request->file('photo')->store('photo-', 'public')) : $merchant->photo,
            'is_pin' => $request->is_pin ? $request->is_pin : 0,
            'is_tax' => $request->is_tax ? $request->is_tax : 0,
            'tax' => $request->is_tax ? $request->tax : 0,
        ]));

        return response()->json([
            'message' => 'Merchant berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant) {}
}
