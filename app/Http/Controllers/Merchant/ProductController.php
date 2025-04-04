<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|Application|View
    {
        return view('merchant.product', [
            'title' => 'Product',
            'categories' => auth()->user()->merchant->productCategory->select('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create(array_merge($request->validated(), [
            'merchant_id' => auth()->user()->merchant->id,
            'photo' => $request->hasFile('photo') ? asset('storage/'.$request->file('products')->store('products/'.auth()->user()->merchant->id, 'public')) : null,
        ]));

        $this->createLog('Product', 'Update Product', $product, [
            'old_data' => null,
            'new_data' => $product->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Product berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $old = $product->getOriginal();
        if ($product->merchant_id !== auth()->user()->merchant->id) {
            return abort('403', 'Anda tidak memiliki akses ke produk ini');
        }

        $product->update(array_merge($request->validated(), [
            'merchant_id' => auth()->user()->merchant->id,
            'photo' => $request->hasFile('photo') ? asset('storage/'.$request->file('photo')->store('products/'.auth()->user()->merchant->id, 'public')) : null,
        ]));

        $this->createLog('Product', 'Update Product', $product, [
            'old_data' => $old,
            'new_data' => $product->getChanges(),
        ], 'update');

        return response()->json([
            'message' => 'Product berhasil ditambahkan',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $old = $product->getOriginal();
        if ($product->merchant_id !== auth()->user()->merchant->id) {
            return abort('403', 'Anda tidak memiliki akses ke produk ini');
        }

        $product->delete();

        $this->createLog('Product', 'Delete Product', $product, [
            'old_data' => $old,
            'new_data' => null
        ], 'delete');
        return response()->json([
            'message' => 'Product deleted successfully',
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
