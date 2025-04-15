<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\SantriResource;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Santri;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('santri', [
            'title' => 'Santri'
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
    public function store(Request $request)
    {
        
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
    public function update(Request $request, string $id)
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

    public function data(): AnonymousResourceCollection
    {
        $santri = QueryBuilder::for(User::class)
            ->whereNotNull('parent_id')
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return SantriResource::collection($santri);
    }
}
