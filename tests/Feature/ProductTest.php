<?php

// tests/Feature/ProductTest.php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a product.
     *
     * @return void
     */
    public function test_create_product()
    {
        $category = Category::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.00,
            'stock' => 10,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $response->assertJson([
            'data' => [
                'name' => 'Test Product',
                'description' => 'This is a test product',
                'price' => '100.00',
                'stock' => 10,
                'category_id' => $category->id,
            ]
        ]);

        $this->assertDatabaseHas('products', $productData);
    }

    /** @test */
    public function it_updates_a_product()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product Name',
            'description' => 'Updated product description',
            'price' => 99.99,
            'stock' => 100,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'description',
                         'price',
                         'stock',
                         'category',
                         'created_at',
                         'updated_at',
                     ],
                 ]);

        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function it_returns_not_found_if_product_does_not_exist()
    {
        $response = $this->putJson('/api/products/999', [
            'name' => 'Non-existent Product',
            'description' => 'This product does not exist',
            'price' => 50.00,
            'stock' => 10,
        ]);

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Product not found']);
    }
}
