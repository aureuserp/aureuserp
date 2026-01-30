<?php

namespace Webkul\Partner\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CountryResource;
use Webkul\Support\Http\Resources\V1\StateResource;

class PartnerResource extends JsonResource
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
            'account_type'     => $this->account_type,
            'sub_type'         => $this->sub_type,
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'mobile'           => $this->mobile,
            'avatar'           => $this->avatar,
            'avatar_url'       => $this->avatar_url,
            'job_title'        => $this->job_title,
            'website'          => $this->website,
            'tax_id'           => $this->tax_id,
            'company_registry' => $this->company_registry,
            'reference'        => $this->reference,
            'color'            => $this->color,
            'street1'          => $this->street1,
            'street2'          => $this->street2,
            'city'             => $this->city,
            'zip'              => $this->zip,
            'is_active'        => $this->is_active,
            'parent'           => PartnerResource::make($this->whenLoaded('parent')),
            'country'          => CountryResource::make($this->whenLoaded('country')),
            'state'            => StateResource::make($this->whenLoaded('state')),
            'title'            => TitleResource::make($this->whenLoaded('title')),
            'company'          => CompanyResource::make($this->whenLoaded('company')),
            'industry'         => IndustryResource::make($this->whenLoaded('industry')),
            'user'             => UserResource::make($this->whenLoaded('user')),
            'creator'          => UserResource::make($this->whenLoaded('creator')),
            'addresses'        => PartnerResource::collection($this->whenLoaded('addresses')),
            'contacts'         => PartnerResource::collection($this->whenLoaded('contacts')),
            'bank_accounts'    => BankAccountResource::collection($this->whenLoaded('bankAccounts')),
            'tags'             => TagResource::collection($this->whenLoaded('tags')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'       => $this->deleted_at,
        ];
    }
}
