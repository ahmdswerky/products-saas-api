<?php

namespace App\Http\Controllers\Website;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\Website\ProductFilter;
use App\Http\Resources\MainPaginatedCollection;
use App\Http\Resources\Website\ProductResource;
use App\Http\Requests\Website\ProductStoreRequest;
use App\Http\Requests\Website\ProductUpdateRequest;

class ProductController extends Controller
{
    /** \App\Models\Merchant */
    protected $merchant;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductFilter $filter)
    {
        //$merchant = Merchant::byApiKey(request()->header('api-key'))->firstOrFail();
        //$products = Product::filter($filter)->latest()->paginate(per_page());
        //$products = $this->cache(
        return $this->cache(
            //'api:products-list:page-' . $filter->request->query('page') ?: 1,
            'api:products-list:',
            fn () => new MainPaginatedCollection(
                ProductResource::collection(
                    $this->merchant()
                        ->products()
                        ->filter($filter)
                        ->latest()
                        ->paginate(per_page()),
                ),
            ),
        );

        //return new MainPaginatedCollection(
        //    ProductResource::collection($products)
        //);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        //$merchant = Merchant::find($request->merchant_id);
        $product = $this->merchant()->products()->create($request->validated());

        if ($request->has('photo')) {
            $product->addMedia($request->file('photo'), 'photo', true, [
                'name' => 'photo',
            ]);
        }

        return response([
            'product' => new ProductResource($product),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $this->cache(
            'api:products-show:' . $product->id . ':',
            fn () => response([
                'product' => new ProductResource($product),
            ]),
        );

        return response([
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->update($request->all());

        if ($request->has('photo')) {
            $product->addOrUpdateMedia($product->photo, $request->file('photo'), 'photo', true, [
                'name' => 'photo',
            ], true);
        }

        return response([
            'product' => new ProductResource($product->refresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response([], 204);
    }
}
