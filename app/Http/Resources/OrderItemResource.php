<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product_id'     => $this->product_id,
            'product_name'   => $this->product?->name,
            'quantity'       => $this->quantity,
            'unit_price'     => (float) $this->unit_price,
            'tax_percentage' => (float) $this->tax_percentage,
            'line_subtotal'  => (float) $this->line_subtotal,
            'line_tax'       => (float) $this->line_tax,
            'line_total'     => (float) $this->line_total,
        ];
    }
}
