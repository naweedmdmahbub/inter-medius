<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'stock' => $this->stock,
            'productVariantOne' => new ProductVariantResource($this->whenLoaded('productVariantOne')),
            'productVariantTwo' => new ProductVariantResource($this->whenLoaded('productVariantTwo')),
            'productVariantThree' => new ProductVariantResource($this->whenLoaded('productVariantThree')),
            'title' => ($this->productVariantOne? $this->productVariantOne->variant : '').'/'
                        .($this->productVariantTwo? $this->productVariantTwo->variant : '').'/'
                        .($this->productVariantThree? $this->productVariantThree->variant : '').'/'
        ];
    }
}
