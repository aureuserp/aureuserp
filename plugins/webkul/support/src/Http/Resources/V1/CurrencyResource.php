<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'symbol'         => $this->symbol,
            'iso_numeric'    => $this->iso_numeric,
            'decimal_places' => $this->decimal_places,
            'full_name'      => $this->full_name,
            'rounding'       => $this->rounding,
            'active'         => $this->active,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
