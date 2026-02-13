<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'code'             => $this->code,
            'sort'             => $this->sort,
            'reception_steps'  => $this->reception_steps?->value,
            'delivery_steps'   => $this->delivery_steps?->value,
            'company'          => CompanyResource::make($this->whenLoaded('company')),
            'partner_address'  => PartnerResource::make($this->whenLoaded('partnerAddress')),
            'creator'          => UserResource::make($this->whenLoaded('creator')),
            'locations'        => LocationResource::collection($this->whenLoaded('locations')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'       => $this->deleted_at,
        ];
    }
}
