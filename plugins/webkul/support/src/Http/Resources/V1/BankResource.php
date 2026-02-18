<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class BankResource extends JsonResource
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
            'name'       => $this->name,
            'code'       => $this->code,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'street1'    => $this->street1,
            'street2'    => $this->street2,
            'city'       => $this->city,
            'zip'        => $this->zip,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'state'      => StateResource::make($this->state),
            'country'    => CountryResource::make($this->country),
            'creator'    => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
