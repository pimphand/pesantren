<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class ProductCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', ProductCategory::class);

        return view('merchant.category', [
            'title' => 'Kategori',
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
    public function store(StoreProductCategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', ProductCategory::class);

        $category = ProductCategory::create(array_merge($request->validated(), [
            'merchant_id' => auth()->user()->merchant->id,
            'created_by' => auth()->user()->id,
        ]));

        $this->createLog('Product', 'Create Product', $category, [
            'old_data' => null,
            'new_data' => $category->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $productCategory);

        $oldCategory = $productCategory->getOriginal();
        $productCategory->update(array_merge($request->validated(), [
            'merchant_id' => auth()->user()->merchant->id,
            'updated_by' => auth()->user()->id,
        ]));

        $this->createLog('Product', 'Update Product', $productCategory, [
            'old_data' => $oldCategory,
            'new_data' => $productCategory->toArray(),
        ], 'update');

        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory): \Illuminate\Http\JsonResponse
    {
        $this->authorize('delete', $productCategory);

        $oldCategory = $productCategory->getOriginal();
        $productCategory->update([
            'deleted_by' => auth()->user()->id,
        ]);
        $productCategory->delete();

        $this->createLog('Product', 'Delete Product', $productCategory, [
            'old_data' => $oldCategory,
            'new_data' => null,
        ], 'delete');

        return response()->json([
            'message' => 'Kategori berhasil dihapus',
        ]);
    }

    /**
     * Get all data
     */
    public function data(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ProductCategory::class);

        $categories = QueryBuilder::for(ProductCategory::orderBy('created_at', 'desc'))
            ->with('createdBy:id,name', 'updatedBy:id,name', 'deletedBy:id,name')
            ->where('merchant_id', auth()->user()->merchant->id)
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return ProductCategoryResource::collection($categories);
    }
}
