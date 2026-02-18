<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CountryResource;

class FiscalPositionResource extends JsonResource
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
            'company_id'       => $this->company_id,
            'country_id'       => $this->country_id,
            'country_group_id' => $this->country_group_id,
            'creator_id'       => $this->creator_id,
            'name'             => $this->name,
            'zip_from'         => $this->zip_from,
            'zip_to'           => $this->zip_to,
            'foreign_vat'      => $this->foreign_vat,
            'notes'            => $this->notes,
            'auto_reply'       => $this->auto_reply,
            'vat_required'     => $this->vat_required,
            'sort'             => $this->sort,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'company'          => CompanyResource::make($this->whenLoaded('company')),
            'country'          => CountryResource::make($this->whenLoaded('country')),
            'countryGroup'     => CountryResource::make($this->whenLoaded('countryGroup')),
            'createdBy'        => UserResource::make($this->whenLoaded('createdBy')),
            'taxes'            => FiscalPositionTaxResource::collection($this->whenLoaded('taxes')),
            'accounts'         => FiscalPositionAccountResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
