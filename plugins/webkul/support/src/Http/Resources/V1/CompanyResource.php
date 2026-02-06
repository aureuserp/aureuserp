<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'sort'                => $this->sort,
            'name'                => $this->name,
            'tax_id'              => $this->tax_id,
            'registration_number' => $this->registration_number,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'mobile'              => $this->mobile,
            'street1'             => $this->street1,
            'street2'             => $this->street2,
            'city'                => $this->city,
            'zip'                 => $this->zip,
            'logo'                => $this->logo,
            'color'               => $this->color,
            'is_active'           => $this->is_active,
            'founded_date'        => $this->founded_date,
            'website'             => $this->website,
            'country_id'          => $this->country_id,
            'state_id'            => $this->state_id,
            'currency_id'         => $this->currency_id,
            'parent_id'           => $this->parent_id,
            'partner_id'          => $this->partner_id,
            'creator_id'          => $this->creator_id,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'deleted_at'          => $this->deleted_at,
            'country'             => new CountryResource($this->whenLoaded('country')),
            'state'               => new StateResource($this->whenLoaded('state')),
            'currency'            => new CurrencyResource($this->whenLoaded('currency')),
            'parent'              => new CompanyResource($this->whenLoaded('parent')),
            'partner'             => new PartnerResource($this->whenLoaded('partner')),
            'creator'             => new UserResource($this->whenLoaded('createdBy')),
            'branches'            => CompanyResource::collection($this->whenLoaded('branches')),
        ];
    }
}
