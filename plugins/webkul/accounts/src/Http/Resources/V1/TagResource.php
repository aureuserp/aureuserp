<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CountryResource;

class TagResource extends JsonResource
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
            'color'          => $this->color,
            'country_id'     => $this->country_id,
            'creator_id'     => $this->creator_id,
            'applicability'  => $this->applicability,
            'name'           => $this->name,
            'tax_negate'     => $this->tax_negate,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'country'        => new CountryResource($this->whenLoaded('country')),
            'createdBy'      => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
