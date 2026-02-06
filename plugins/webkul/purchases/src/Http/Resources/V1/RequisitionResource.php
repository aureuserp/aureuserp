<?php

namespace Webkul\Purchase\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class RequisitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'type'        => $this->type?->value,
            'state'       => $this->state?->value,
            'reference'   => $this->reference,
            'starts_at'   => $this->starts_at,
            'ends_at'     => $this->ends_at,
            'description' => $this->description,
            'currency_id' => $this->currency_id,
            'partner_id'  => $this->partner_id,
            'user_id'     => $this->user_id,
            'company_id'  => $this->company_id,
            'creator_id'  => $this->creator_id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'deleted_at'  => $this->deleted_at,
            'currency'    => new CurrencyResource($this->whenLoaded('currency')),
            'partner'     => new PartnerResource($this->whenLoaded('partner')),
            'user'        => new UserResource($this->whenLoaded('user')),
            'company'     => new CompanyResource($this->whenLoaded('company')),
            'creator'     => new UserResource($this->whenLoaded('creator')),
            'lines'       => RequisitionLineResource::collection($this->whenLoaded('lines')),
        ];
    }
}
