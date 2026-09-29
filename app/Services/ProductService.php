<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getLowStockProducts(
        ?int $threshold = null,
        int $perPage = 15,
        string $sort = 'stock_asc'
    ): LengthAwarePaginator {
        $threshold ??= (int) config('inventory.low_stock_threshold');

        $query = Product::query()->where('stock', '<', $threshold);

        match ($sort) {
            'stock_asc'  => $query->orderBy('stock'),
            'stock_desc' => $query->orderByDesc('stock'),
            'name_asc'   => $query->orderBy('name'),
            'name_desc'  => $query->orderByDesc('name'),
            default      => $query->orderBy('stock'),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
