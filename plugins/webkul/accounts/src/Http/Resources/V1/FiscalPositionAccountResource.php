<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class FiscalPositionAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'fiscal_position_id'     => $this->fiscal_position_id,
            'company_id'             => $this->company_id,
            'account_source_id'      => $this->account_source_id,
            'account_destination_id' => $this->account_destination_id,
            'creator_id'             => $this->creator_id,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
            'fiscalPosition'         => new FiscalPositionResource($this->whenLoaded('fiscalPosition')),
            'company'                => new CompanyResource($this->whenLoaded('company')),
            'accountSource'          => new AccountResource($this->whenLoaded('accountSource')),
            'accountDestination'     => new AccountResource($this->whenLoaded('accountDestination')),
            'creator'                => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
