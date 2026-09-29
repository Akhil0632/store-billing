<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowStockProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'code'           => $this->code,
            'price'          => (float) $this->price,
            'tax_percentage' => (float) $this->tax_percentage,
            'stock'          => (int) $this->stock,
            'threshold'      => (int) $request->input('threshold', config('inventory.low_stock_threshold')),
            'deficit'        => max(0, (int) $request->input('threshold', config('inventory.low_stock_threshold')) - $this->stock),
        ];
    }
}
