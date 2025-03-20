<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('merchant.product',[
            'title' => 'Product',
            'categories' => auth()->user()->merchant->productCategory->select('name', 'id')
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
    public function store(StoreProductRequest $request)
    {
        Product::create(array_merge($request->all(), [
            'merchant_id' => auth()->user()->merchant->id,
            'photo' => $request->hasFile('photo') ?  asset('storage/' . $request->file('proof_of_payment')->store('proof-of-payment', 'public')) : null
        ]));

        return response()->json([
            "message" => "Product berhasil ditambahkan"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return response()->json([
            "message" => "Product deleted successfully"
        ]);
    }

    /**
     * Display a listing of the resource.
     */

    public function data(): AnonymousResourceCollection
    {
        $products = QueryBuilder::for(Product::class)
            ->with('category')
            ->where('merchant_id', auth()->user()->merchant->id)
            ->allowedFilters([
                AllowedFilter::scope('name'),
                AllowedFilter::scope('description'),
                AllowedFilter::exact('category.id'),
            ])
            ->allowedSorts(['name', 'description', 'price', 'created_at'])
            ->paginate(10)
            ->appends(request()->query());

        return ProductResource::collection($products);
    }
}
