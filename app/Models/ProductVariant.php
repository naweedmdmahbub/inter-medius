<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'variant', 'variant_id'
    ];

    public function modelVariant(){
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
