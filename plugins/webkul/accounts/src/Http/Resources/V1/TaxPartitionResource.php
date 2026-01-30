<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class TaxPartitionResource extends JsonResource
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
            'account_id'          => $this->account_id,
            'tax_id'              => $this->tax_id,
            'company_id'          => $this->company_id,
            'sort'                => $this->sort,
            'repartition_type'    => $this->repartition_type,
            'document_type'       => $this->document_type,
            'use_in_tax_closing'  => $this->use_in_tax_closing,
            'factor_percent'      => $this->factor_percent,
            'creator_id'          => $this->creator_id,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'createdBy'           => new UserResource($this->whenLoaded('createdBy')),
            'account'             => new AccountResource($this->whenLoaded('account')),
            'tax'                 => new TaxResource($this->whenLoaded('tax')),
            'company'             => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}
