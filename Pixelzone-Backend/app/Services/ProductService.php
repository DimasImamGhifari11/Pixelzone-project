<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getAllProducts(): Collection
    {
        return Product::with('category')->get();
    }

    public function createProduct(array $data): Product
    {
        return Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'image' => $data['image'],
        ]);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = Product::find($id);

        if (!$product) {
            return null;
        }

        $product->update($data);

        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::find($id);

        if (!$product) {
            return false;
        }

        $product->delete();

        return true;
    }
}
