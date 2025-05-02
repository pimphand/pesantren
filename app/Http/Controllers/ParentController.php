<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\UpdateParentRequest;
use App\Http\Requests\StoreParentRequest;
use App\Http\Resources\ParentResource;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;



class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('parents', [
            'title' => 'Parents',
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
    public function store(StoreParentRequest $request)
    {
        $parent = User::create(array_merge($request->validated(), [
            'uuid'     => Str::uuid(),
            'parent_id'     => null,
            'phone'    => $request->phone == '-' ? null : $request->phone,
            'password' => bcrypt($request->password),
        ]))->addRole('orang_tua');

        $this->createLog('Parent', 'Create Parent', $parent, [
            'old_data' => null,
            'new_data' => $parent->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Parent berhasil ditambahkan',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParentRequest $request, User $orang_tua): \Illuminate\Http\JsonResponse
    {
        $oldParent = $orang_tua->getOriginal();

        // Update data user
        $orang_tua->update(array_merge(
            $request->validated(),
            [
                'password' => $request->password ? bcrypt($request->password) : $orang_tua->password,
                'phone' => $request->phone == '-' ? null : $request->phone,
                'parent_id' => null,
                ]
            ));

        // Logging perubahan
        $this->createLog('Parent', 'Update Parent', $orang_tua,
            [
                'old_data' => [
                    'old_parent' => $oldParent,
                ],
                'new_data' => [
                    'parent' => $orang_tua->getChanges(),
                ],
            ],
            'update'
        );

        return response()->json([
            'message' => 'Parent berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $orang_tua)
    {
        $oldParent = $orang_tua->getOriginal();
        $orang_tua->delete();

        $this->createLog('Parent', 'Delete Parent', $orang_tua, [
            'old_data' => $oldParent,
            'new_data' => $orang_tua->toArray(),
        ], 'delete');

        return response()->json([
            'message' => 'Parent berhasil dihapus',
        ]);
    }

    public function data(): AnonymousResourceCollection
    {
        $parent = QueryBuilder::for(User::class)
            ->withRole('orang_tua')
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return ParentResource::collection($parent);
    }
}
