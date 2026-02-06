<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class PaymentTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'company_id'           => $this->company_id,
            'creator_id'           => $this->creator_id,
            'name'                 => $this->name,
            'note'                 => $this->note,
            'sort'                 => $this->sort,
            'display_on_invoice'   => $this->display_on_invoice,
            'early_pay_discount'   => $this->early_pay_discount,
            'discount_percentage'  => $this->discount_percentage,
            'discount_days'        => $this->discount_days,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'deleted_at'           => $this->deleted_at,
            'company'              => CompanyResource::make($this->whenLoaded('company')),
            'createdBy'            => UserResource::make($this->whenLoaded('createdBy')),
            'dueTerms'             => PaymentDueTermResource::collection($this->whenLoaded('dueTerms')),
        ];
    }
}
