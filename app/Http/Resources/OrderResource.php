<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'customer'        => [
                'id'    => $this->customer->id,
                'name'  => $this->customer->name,
                'email' => $this->customer->email,
            ],
            'items'           => OrderItemResource::collection($this->whenLoaded('items')),
            'subtotal'        => (float) $this->subtotal,
            'tax_total'       => (float) $this->tax_total,
            'grand_total'     => (float) $this->grand_total,
            'amount_given'    => (float) $this->amount_given,
            'change_returned' => (float) $this->change_returned,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
