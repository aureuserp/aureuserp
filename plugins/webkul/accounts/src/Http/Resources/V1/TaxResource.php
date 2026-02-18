<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CountryResource;

class TaxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                               => $this->id,
            'type_tax_use'                     => $this->type_tax_use,
            'tax_scope'                        => $this->tax_scope,
            'amount_type'                      => $this->amount_type,
            'price_include_override'           => $this->price_include_override,
            'tax_exigibility'                  => $this->tax_exigibility,
            'name'                             => $this->name,
            'description'                      => $this->description,
            'invoice_label'                    => $this->invoice_label,
            'invoice_legal_notes'              => $this->invoice_legal_notes,
            'amount'                           => $this->amount,
            'is_active'                        => $this->is_active,
            'include_base_amount'              => $this->include_base_amount,
            'is_base_affected'                 => $this->is_base_affected,
            'analytic'                         => $this->analytic,
            'sort'                             => $this->sort,
            'company_id'                       => $this->company_id,
            'tax_group_id'                     => $this->tax_group_id,
            'cash_basis_transition_account_id' => $this->cash_basis_transition_account_id,
            'country_id'                       => $this->country_id,
            'creator_id'                       => $this->creator_id,
            'created_at'                       => $this->created_at,
            'updated_at'                       => $this->updated_at,
            'company'                          => CompanyResource::make($this->whenLoaded('company')),
            'tax_group'                        => TaxGroupResource::make($this->whenLoaded('taxGroup')),
            'cash_basis_transition_account'    => AccountResource::make($this->whenLoaded('cashBasisTransitionAccount')),
            'country'                          => CountryResource::make($this->whenLoaded('country')),
            'creator'                          => UserResource::make($this->whenLoaded('creator')),
            'children_taxes'                   => self::collection($this->whenLoaded('childrenTaxes')),
            'invoice_repartition_lines'        => TaxPartitionResource::collection($this->whenLoaded('invoiceRepartitionLines')),
            'refund_repartition_lines'         => TaxPartitionResource::collection($this->whenLoaded('refundRepartitionLines')),
        ];
    }
}
