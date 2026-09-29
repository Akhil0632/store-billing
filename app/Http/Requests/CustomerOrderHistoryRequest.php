<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrderHistoryRequest extends JsonResource
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'      => ['required', 'email', 'max:255'],
            'per_page'   => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page'       => ['sometimes', 'integer', 'min:1'],
            'from'       => ['sometimes', 'date'],
            'to'         => ['sometimes', 'date', 'after_or_equal:from'],
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'items_count'     => $this->items->sum('quantity'),
            'subtotal'        => (float) $this->subtotal,
            'tax_total'       => (float) $this->tax_total,
            'grand_total'     => (float) $this->grand_total,
            'amount_given'    => (float) $this->amount_given,
            'change_returned' => (float) $this->change_returned,
            'created_at'      => $this->created_at->toIso8601String(),
        ];
    }
}
