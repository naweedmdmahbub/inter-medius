<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('productVariantPrices.productVariantOne',
                                    'productVariantPrices.productVariantTwo',
                                    'productVariantPrices.productVariantThree')
                            ->paginate(2);
        $total = Product::count();
        $variants = Variant::with('productVariants')->get();

        $distinctVariants = DB::table('product_variants')
                        ->select(DB::raw('lower(variant) as variant'),
                             'product_variants.variant_id', 'variants.title')
                        ->leftJoin('variants', 'product_variants.variant_id', '=', 'variants.id')
                        ->get()
                        ->unique('variant');
        // $distinctVariants = DB::table('product_variants')->distinct('variant')->pluck('id', 'variant')->flip();
        $distincts = [];
        foreach ($distinctVariants as $key => $value) {
            $distincts[$value->title] = [];
        }
        foreach ($distinctVariants as $key => $value) {
            array_push($distincts[$value->title], $value->variant);
        }
        $variants = Variant::with('productVariants')->get();
        // dd($distinctVariants, $distincts);
        return view('products.index', compact('products', 'total', 'variants', 'distincts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function filter(Request $request)
    {
        dd($request->all());

    }

}
