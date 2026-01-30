<?php

namespace Webkul\Analytic\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class RecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'name'        => $this->name,
            'date'        => $this->date,
            'amount'      => (float) $this->amount,
            'unit_amount' => (float) $this->unit_amount,
            'partner_id'  => $this->partner_id,
            'company_id'  => $this->company_id,
            'user_id'     => $this->user_id,
            'creator_id'  => $this->creator_id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'partner'     => new PartnerResource($this->whenLoaded('partner')),
            'company'     => new CompanyResource($this->whenLoaded('company')),
            'user'        => new UserResource($this->whenLoaded('user')),
            'creator'     => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
