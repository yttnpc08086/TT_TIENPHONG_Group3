<?php

namespace App\Transformers;

use App\Models\Product;

class ProductTransformer
{
    /**
     * Transform the product data for API response.
     *
     * @param  \App\Models\Product  $product
     * @return array
     */
    public static function transform(Product $product)
    {
        return [
            'id' => (int) $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => number_format($product->price, 2),
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'created_at' => $product->created_at->toDateTimeString(),
            'updated_at' => $product->updated_at->toDateTimeString(),
            'img_url' => $product->img_url,
        ];
    }

     public static function transformCollection($products)
    {
        return $products->map(function ($product) {
            return self::transform($product);
        });
    }
}
