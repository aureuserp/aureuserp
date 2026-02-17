<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'phone_code'     => $this->phone_code,
            'state_required' => $this->state_required,
            'zip_required'   => $this->zip_required,
            'currency_id'    => $this->currency_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'currency'       => CurrencyResource::make($this->whenLoaded('currency')),
            'states'         => StateResource::collection($this->whenLoaded('states')),
        ];
    }
}
