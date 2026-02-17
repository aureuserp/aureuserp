<?php

namespace Webkul\Partner\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CountryResource;
use Webkul\Support\Http\Resources\V1\StateResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'sub_type'   => $this->sub_type,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'mobile'     => $this->mobile,
            'street1'    => $this->street1,
            'street2'    => $this->street2,
            'city'       => $this->city,
            'zip'        => $this->zip,
            'country_id' => $this->country_id,
            'state_id'   => $this->state_id,
            'parent_id'  => $this->parent_id,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'country'    => new CountryResource($this->country),
            'state'      => new StateResource($this->state),
            'parent'     => new PartnerResource($this->whenLoaded('parent')),
            'creator'    => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
