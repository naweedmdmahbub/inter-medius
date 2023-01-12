<?php

namespace App\Repositories;

use App\Interfaces\ProductInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepository implements ProductInterface 
{
    public function getAllProducts() 
    {
        return Product::all();
    }

    public function getProductById($productId) 
    {
        return Product::findOrFail($productId);
    }

    public function deleteProduct($productId) 
    {
        Product::destroy($productId);
    }

    public function createOrUpdateProduct($request, $method, $id = null)
    {
        $input = $request->only('title', 'sku', 'description', );
        // dd($request->all());
        
        if($method == 'create'){
            $product = Product::create($input);
        } else {
            $product = Product::find($id);            
            $product->fill($input)->update();
        }
        $this->saveProductVariants($product, $request);
        $this->saveProductVariantPrices($product, $request);
    }

    
    public function saveProductVariants($product, $request)
    {
        // dd($request->all());
        foreach($request['product_variant'] as $variant){
            // dd($variant);
            $input['variant_id'] = $variant['option'];
            foreach($variant['tags'] as $tag){
                $input['product_id'] = $product->id;
                $input['variant'] = Str::lower($tag);
                ProductVariant::create($input);
            }
            // isset($variant['id']) ? ProductVariant::where('id', $variant['id'])->update($input) : ProductVariant::create($input);
        }
    }

    
    
    public function saveProductVariantPrices($product, $request)
    {
        $variants = ProductVariant::where('product_id', $product->id)->get();

        // dd($request->all(), $variants);
        foreach($request['product_variant_prices'] as $variant_price){
            $variant_names = explode('/', $variant_price['title']);
            $input = [];
            foreach($variant_names as $name){
                if(isset($name)){
                    $product_variant = ProductVariant::with('modelVariant')->where('product_id', $product->id)->where('variant', Str::lower($name))->first();
                    if($product_variant){
                        // dd($product_variant->modelVariant, $product_variant);
                        if($product_variant->modelVariant->title == 'Color'){
                            $input['product_variant_one'] = $product_variant->id;
                        } else if($product_variant->modelVariant->title == 'Size'){
                            $input['product_variant_two'] = $product_variant->id;
                        } else if($product_variant->modelVariant->title == 'Style'){
                            $input['product_variant_three'] = $product_variant->id;
                        }
                    }
                }
            }
            $input['price'] = $variant_price['price'];
            $input['stock'] = $variant_price['stock'];
            $input['product_id'] = $product->id;
            ProductVariantPrice::create($input);
            // dd($variant_price, $variant_names, $vars);

            
            // isset($variant['id']) ? ProductVariant::where('id', $variant['id'])->update($input) : ProductVariant::create($input);
        }
    }
}