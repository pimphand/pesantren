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

    public function subMenu(Menu $menu)
    {
        return view('sub_menu', [
            'title' => 'Sub Menu',
            'id' => $menu->id,
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
        $permission = Permission::where('name', 'ILIKE', '%read%')
                ->where('name', 'ILIKE', "%$request->name%")
                ->first();
                // dd($permission);
        if($permission) {
            $menu = Menu::create(array_merge($request->validated(), [
                'permission_id' => $permission->id,
                'order_menu' => 99,
            ]));
            
            $this->createLog('Menu', 'Create Menu', $menu, [
                'old_data' => null,
                'new_data' => $menu->toArray(),
            ], 'update');
                    
            return response()->json([
                'message' => 'Menu berhasil ditambah',
            ]);
        } else {
            $permissionId = Permission::insertGetId([
                'name'  => strtolower(str_replace(' ', '_', $request->name)) . '-read',
                'display_name'  => 'Read ' . $request->name,
                'description'   => 'Read ' . $request->name,
            ]);
            Menu::create(array_merge($request->validated(),[
                'permission_id' => $permissionId,
                'menu_id' => NULL,
                'order_menu'=> 99
            ]));
            
            return response()->json([
                'message' => 'Menu berhasil ditambah',
            ]);
        }
    }

    public function store_sub_menu() {

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
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $oldMenu = $menu->getOriginal();
        $menu->update(array_merge($request->validated()));

        $this->createLog('Menu', 'Update Menu', $menu, [
            'old_data' => $oldMenu,
            'new_data' => $menu->toArray(),
        ], 'update');
        
        return response()->json([
            'message' => 'Menu berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $oldMenu = $menu->getOriginal();
        $menu->delete();

        $this->createLog('Menu', 'Delete Menu', $menu, [
            'old_data' => $oldMenu,
            'new_data' => $menu->toArray(),
        ], 'delete');

        return response()->json([
            'message' => 'Menu berhasil dihapus',
        ]);
    }

    public function data(): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(Menu::class)
            ->whereNull('menu_id')
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return MenuResurce::collection($categories);
    }
    public function dataSubmenu($menu): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(Menu::class)
            ->where('menu_id', $menu)
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return MenuResurce::collection($categories);
    }
}
