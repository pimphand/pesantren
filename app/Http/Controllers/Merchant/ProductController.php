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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|Application|View
    {
        $this->authorize('viewAny', Product::class);

        return view('merchant.product', [
            'title' => 'Produk',
            'categories' => auth()->user()->merchant->productCategory->select('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store(
                'products/' . auth()->user()->merchant->id,
                'public'
            );
        }

        $product = Product::create(array_merge($request->validated(), [
            'merchant_id' => auth()->user()->merchant->id,
            'photo' => asset('storage/' . $photoPath) ?? null,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
            'updated_at' => null,
        ]));

        $this->createLog('Product', 'Create Product', $product, [
            'old_data' => null,
            'new_data' => $product->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $old = $product->getOriginal();

        $product->update(array_merge($request->validated(), [
            'merchant_id' => auth()->user()->merchant->id,
            'photo' => $request->hasFile('photo') ? asset('storage/' . $request->file('photo')->store('products/' . auth()->user()->merchant->id, 'public')) : $product->photo,
            'updated_by' => auth()->user()->id,
            'updated_at' => now(),
        ]));

        $this->createLog('Product', 'Update Product', $product, [
            'old_data' => $old,
            'new_data' => $product->getChanges(),
        ], 'update');

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $old = $product->getOriginal();
        $product->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => now(),
        ]);
        $product->delete();

        $this->createLog('Product', 'Delete Product', $product, [
            'old_data' => $old,
            'new_data' => null
        ], 'delete');

        return response()->json([
            'message' => 'Produk berhasil dihapus',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function data(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $products = QueryBuilder::for(Product::class)
            ->with('category')
            ->where('merchant_id', auth()->user()->merchant->id)
            ->allowedFilters([
                AllowedFilter::scope('name'),
                AllowedFilter::scope('description'),
                AllowedFilter::exact('category.id'),
            ])
            ->allowedSorts(['name', 'category.name', 'price'])
            ->defaultSort('name')
            ->paginate(10)
            ->appends(request()->query());

        return ProductResource::collection($products);
    }
}
