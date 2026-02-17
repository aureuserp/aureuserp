<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class FiscalPositionTaxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'fiscal_position_id' => $this->fiscal_position_id,
            'company_id'         => $this->company_id,
            'tax_source_id'      => $this->tax_source_id,
            'tax_destination_id' => $this->tax_destination_id,
            'creator_id'         => $this->creator_id,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'fiscalPosition'     => new FiscalPositionResource($this->whenLoaded('fiscalPosition')),
            'company'            => new CompanyResource($this->whenLoaded('company')),
            'taxSource'          => new TaxResource($this->whenLoaded('taxSource')),
            'taxDestination'     => new TaxResource($this->whenLoaded('taxDestination')),
            'creator'            => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
