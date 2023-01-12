<?php

namespace App\Interfaces;

interface ProductInterface 
{
    public function getAllProducts();
    public function getProductById($productId);
    public function deleteProduct($productId);
    public function createOrUpdateProduct($request, $method, $id);
}
