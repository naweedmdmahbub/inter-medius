<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productRepository;
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


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
    public function store(StoreUpdateProductRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->productRepository->createOrUpdateProduct($request, 'create');
            DB::commit();
        } catch (Exception $ex){
            DB::rollBack();
            return redirect()->back()->withErrors(new \Illuminate\Support\MessageBag(['catch_exception'=>$ex->getMessage()]));
        }
        // dd($request->all());

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
        $productR = Product::with('productVariantPrices.productVariantOne',
                                'productVariantPrices.productVariantTwo',
                                'productVariantPrices.productVariantThree','productVariants')
                            ->where('id', $product)->first();
        // dd($structure);
        return new ProductResource($productR);
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
        $mode = 'edit';
        return view('products.edit', compact('variants', 'mode', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateProductRequest $request, Product $product)
    {
        // dd($request->all(), $product);
        try{
            DB::beginTransaction();
            $this->productRepository->createOrUpdateProduct($request, 'update', $product->id);
            DB::commit();
        } catch (Exception $ex){
            DB::rollBack();
            return redirect()->back()->withErrors(new \Illuminate\Support\MessageBag(['catch_exception'=>$ex->getMessage()]));
        }
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
        // dd($request->all());
        $title = $request['title'];
        $variant = $request['variant'];
        $price_from = $request['price_from'];
        $price_to = $request['price_to'];
        $date = $request['date'];

        $query = Product::query();
        if($title){
            $query = $query->where('title', 'like', '%' .$title . '%');
        }
        if($date){            
            $query = $query->whereDate('created_at', $date);
        }
        if($price_from || $price_to){
            if($price_from && $price_from){
                $query = $query->whereHas('productVariantPrices',function($subquery) use ($price_from, $price_to){
                    $subquery->whereBetween('price',[$price_from, $price_to]);
                });
            } else if($price_from){
                $query = $query->whereHas(['productVariantPrices',function($subquery) use ($price_from){
                    $subquery->where('price','>=', $price_from);
                }]);
                // $query = $query->with(['productVariantPrices'=>function($subquery) use ($price_from){
                //     $subquery->where('price','>=', $price_from);
                // }]);
            } else{
                $query = $query->whereHas(['productVariantPrices'=>function($subquery) use ($price_to){
                    $subquery->where('price','<=', $price_to);
                }]);
                // $query = $query->with(['productVariantPrices'=>function($subquery) use ($price_to){
                //     $subquery->where('price','<=', $price_to);
                // }]);
            }
        }
        if($variant){
            $query = $query->whereHas('productVariantPrices',function ($subquery) use($variant) {
                        $subquery->whereHas('productVariantOne', function($nestedquery) use($variant) {
                                    $nestedquery->where('variant', $variant);
                                })->orWhereHas('productVariantTwo', function($nestedquery) use($variant) {
                                    $nestedquery->where('variant', $variant);
                                })->orWhereHas('productVariantThree', function($nestedquery) use($variant) {
                                    $nestedquery->where('variant', $variant);
                                });
                    });
        }
        // $products = $query->paginate(2);
        $products = $query->with('productVariantPrices.productVariantOne',
                                'productVariantPrices.productVariantTwo',
                                'productVariantPrices.productVariantThree')
                        ->paginate(2);
                        // ->get();
        // dd($query, $request->all());
        // dd($products);
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
        $total = Product::count();
        return view('products.index', compact('products', 'total', 'distincts'));
    }

}
