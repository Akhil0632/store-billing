<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LowStockProductsRequest;
use App\Http\Resources\LowStockProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function lowStock(LowStockProductsRequest $request): JsonResponse
    {
        try {
            $threshold = $request->validated(
                'threshold',
                (int) config('inventory.low_stock_threshold')
            );

            $products = $this->productService->getLowStockProducts(
                threshold: (int) $threshold,
                perPage:   (int) $request->validated('per_page', 15),
                sort:      $request->validated('sort', 'stock_asc'),
            );

            return response()->json([
                'success' => true,
                'meta'    => [
                    'threshold'    => (int) $threshold,
                    'total'        => $products->total(),
                    'current_page' => $products->currentPage(),
                    'per_page'     => $products->perPage(),
                    'last_page'    => $products->lastPage(),
                ],
                'data' => LowStockProductResource::collection($products)->resolve(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
