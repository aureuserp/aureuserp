<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class MoveLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                            => $this->id,
            'sort'                          => $this->sort,
            'move_id'                       => $this->move_id,
            'journal_id'                    => $this->journal_id,
            'company_id'                    => $this->company_id,
            'company_currency_id'           => $this->company_currency_id,
            'reconcile_id'                  => $this->reconcile_id,
            'payment_id'                    => $this->payment_id,
            'tax_repartition_line_id'       => $this->tax_repartition_line_id,
            'account_id'                    => $this->account_id,
            'currency_id'                   => $this->currency_id,
            'partner_id'                    => $this->partner_id,
            'group_tax_id'                  => $this->group_tax_id,
            'tax_line_id'                   => $this->tax_line_id,
            'tax_group_id'                  => $this->tax_group_id,
            'statement_id'                  => $this->statement_id,
            'statement_line_id'             => $this->statement_line_id,
            'product_id'                    => $this->product_id,
            'uom_id'                        => $this->uom_id,
            'created_by'                    => $this->created_by,
            'move_name'                     => $this->move_name,
            'parent_state'                  => $this->parent_state,
            'reference'                     => $this->reference,
            'name'                          => $this->name,
            'matching_number'               => $this->matching_number,
            'display_type'                  => $this->display_type,
            'date'                          => $this->date,
            'invoice_date'                  => $this->invoice_date,
            'date_maturity'                 => $this->date_maturity,
            'discount_date'                 => $this->discount_date,
            'analytic_distribution'         => $this->analytic_distribution,
            'debit'                         => $this->debit,
            'credit'                        => $this->credit,
            'balance'                       => $this->balance,
            'amount_currency'               => $this->amount_currency,
            'tax_base_amount'               => $this->tax_base_amount,
            'amount_residual'               => $this->amount_residual,
            'amount_residual_currency'      => $this->amount_residual_currency,
            'quantity'                      => $this->quantity,
            'price_unit'                    => $this->price_unit,
            'price_subtotal'                => $this->price_subtotal,
            'price_total'                   => $this->price_total,
            'discount'                      => $this->discount,
            'discount_amount_currency'      => $this->discount_amount_currency,
            'discount_balance'              => $this->discount_balance,
            'is_imported'                   => $this->is_imported,
            'tax_tag_invert'                => $this->tax_tag_invert,
            'reconciled'                    => $this->reconciled,
            'is_downpayment'                => $this->is_downpayment,
            'full_reconcile_id'             => $this->full_reconcile_id,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'move'                          => new MoveResource($this->whenLoaded('move')),
            'journal'                       => new JournalResource($this->whenLoaded('journal')),
            'company'                       => new CompanyResource($this->whenLoaded('company')),
            'account'                       => new AccountResource($this->whenLoaded('account')),
            'currency'                      => new CurrencyResource($this->whenLoaded('currency')),
            'companyCurrency'               => new CurrencyResource($this->whenLoaded('companyCurrency')),
            'partner'                       => new PartnerResource($this->whenLoaded('partner')),
            'groupTax'                      => new TaxResource($this->whenLoaded('groupTax')),
            'taxes'                         => TaxResource::collection($this->whenLoaded('taxes')),
            'taxGroup'                      => new TaxGroupResource($this->whenLoaded('taxGroup')),
            'statement'                     => new BankStatementResource($this->whenLoaded('statement')),
            'statementLine'                 => new BankStatementLineResource($this->whenLoaded('statementLine')),
            'product'                       => new ProductResource($this->whenLoaded('product')),
            'uom'                           => new UOMResource($this->whenLoaded('uom')),
            'createdBy'                     => new UserResource($this->whenLoaded('createdBy')),
            'moveLines'                     => self::collection($this->whenLoaded('moveLines')),
            'payment'                       => new PaymentResource($this->whenLoaded('payment')),
            'taxRepartitionLine'            => new TaxPartitionResource($this->whenLoaded('taxRepartitionLine')),
        ];
    }
}
