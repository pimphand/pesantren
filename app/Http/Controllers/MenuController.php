<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\MenuResurce;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Requests\StoreMenuRequest;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Menu;
use App\Models\Permission;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menu', [
            'title' => 'Menu',
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
    public function store(StoreMenuRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $menu = Menu::create(array_merge($request->validated(), [
                'status' => $request->has('active') ?? 0,
                'permission' => '',
                'order_menus' => 99,
                'parent_id' => $request->parent_id ?? null,1
            ]));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)//: \Illuminate\Http\JsonResponse
    {
        $oldMenu = $menu->getOriginal();
        $menu->update(array_merge($request->validated()));

        $this->createLog('Menu', 'Update Menu', $menu, [
            'old_data' => $oldMenu,
            'new_data' => $menu->toArray(),
        ], 'update');
        
        return response()->json([
            'message' => 'Kategori berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        //
    }

    public function data(): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(Menu::class)
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return MenuResurce::collection($categories);
    }
}
