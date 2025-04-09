<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="Product API",
 *     version="1.0",
 *     description="API documentation for managing products",
 *     @OA\Contact(
 *         name="Your Name",
 *         email="youremail@example.com"
 *     )
 * )
 */
class ProductController extends Controller
{

    /**
     * Upload image to ImgBB
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    private function uploadToImgBB($file)
    {
        $IMGBB_URL = "https://api.imgbb.com/1/upload";
        $API_KEY = "01cb7af79c76730bf67874aa4e06f964";

        try {
            $fileContent = file_get_contents($file->getRealPath());

            $response = Http::attach('image', $fileContent, $file->getClientOriginalName())
                ->post($IMGBB_URL, [
                    'key' => $API_KEY,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['success']) {
                    return $responseData['data']['url'];
                }
            }

            Log::error('ImgBB upload failed', [
                'response' => $response->json(),
                'file' => $file->getClientOriginalName(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception during ImgBB upload', [
                'error_message' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            return null;
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get a list of products",
     *     operationId="getProducts",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter products by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter products by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort products by specific field",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order: asc or desc",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate(10);

        return response()->json([
            'data' => ProductTransformer::transformCollection($products),
            'meta' => [
                'pagination' => [
                    'total' => $products->total(),
                    'count' => $products->count(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     operationId="createProduct",
     *     tags={"Product"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('img_url')) {
            $request->validate([
                'img_url' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageUrl = $this->uploadToImgBB($request->file('img_url'));

            if ($imageUrl) {
                $data['img_url'] = $imageUrl;
            } else {
                return response()->json(['error' => 'Image upload failed'], Response::HTTP_BAD_REQUEST);
            }
        }

        $product = Product::create($data);

        return response()->json([
            'data' => (new ProductTransformer)->transform($product)
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get details of a specific product",
     *     operationId="getProduct",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => (new ProductTransformer)->transform($product)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     operationId="updateProduct",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "price"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="price", type="number", format="float"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="description", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();

        $product->update($data);

        return response()->json([
            'data' => (new ProductTransformer)->transform($product)
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     operationId="deleteProduct",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *     ),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=409, description="Product has associated orders")
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($product->orders()->exists()) {
            return response()->json([
                'error' => 'Product has associated orders and cannot be deleted'
            ], Response::HTTP_CONFLICT);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }
}
