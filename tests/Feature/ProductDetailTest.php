<?php

// tests/Feature/ProductDetailTest.php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting product details by id.
     *
     * @return void
     */
    public function test_get_product_detail()
    {
        $category = Category::create(['name' => 'Electronics']);
        $product = Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'price' => 100,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => number_format($product->price, 2),
                'stock' => $product->stock,
                'category' => $category->name,
            ],
        ]);
    }

    /**
     * Test getting product detail with non-existent product.
     *
     * @return void
     */
    public function test_get_product_detail_not_found()
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'Sản phẩm không tồn tại'
        ]);
    }
}
