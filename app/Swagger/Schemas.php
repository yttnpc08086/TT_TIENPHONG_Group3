<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"id", "name", "description", "price", "stock", "category_id", "img_url", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Product Name"),
 *     @OA\Property(property="description", type="string", example="Product Description"),
 *     @OA\Property(property="price", type="number", format="float", example=100.99),
 *     @OA\Property(property="stock", type="integer", example=50),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="img_url", type="string", example="https://example.com/image.jpg", description="URL of the product image"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-05T12:34:56"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-05T12:34:56")
 * )
 */

// Empty class to ensure it's autoloaded
class Schemas {}
