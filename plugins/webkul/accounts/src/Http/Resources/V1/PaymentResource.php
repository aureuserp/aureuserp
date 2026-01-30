<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\BankAccountResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                     => $this->id,
            'move_id'                                => $this->move_id,
            'journal_id'                             => $this->journal_id,
            'company_id'                             => $this->company_id,
            'partner_bank_id'                        => $this->partner_bank_id,
            'paired_internal_transfer_payment_id'    => $this->paired_internal_transfer_payment_id,
            'payment_method_line_id'                 => $this->payment_method_line_id,
            'payment_method_id'                      => $this->payment_method_id,
            'currency_id'                            => $this->currency_id,
            'partner_id'                             => $this->partner_id,
            'outstanding_account_id'                 => $this->outstanding_account_id,
            'destination_account_id'                 => $this->destination_account_id,
            'created_by'                             => $this->created_by,
            'name'                                   => $this->name,
            'state'                                  => $this->state,
            'payment_type'                           => $this->payment_type,
            'partner_type'                           => $this->partner_type,
            'memo'                                   => $this->memo,
            'payment_reference'                      => $this->payment_reference,
            'date'                                   => $this->date,
            'amount'                                 => $this->amount,
            'amount_company_currency_signed'         => $this->amount_company_currency_signed,
            'is_reconciled'                          => $this->is_reconciled,
            'is_matched'                             => $this->is_matched,
            'is_sent'                                => $this->is_sent,
            'payment_transaction_id'                 => $this->payment_transaction_id,
            'source_payment_id'                      => $this->source_payment_id,
            'payment_token_id'                       => $this->payment_token_id,
            'created_at'                             => $this->created_at,
            'updated_at'                             => $this->updated_at,
            'move'                                   => new MoveResource($this->whenLoaded('move')),
            'journal'                                => new JournalResource($this->whenLoaded('journal')),
            'company'                                => new CompanyResource($this->whenLoaded('company')),
            'partnerBank'                            => new BankAccountResource($this->whenLoaded('partnerBank')),
            'pairedInternalTransferPayment'          => new self($this->whenLoaded('pairedInternalTransferPayment')),
            'paymentMethodLine'                      => new PaymentMethodLineResource($this->whenLoaded('paymentMethodLine')),
            'paymentMethod'                          => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'currency'                               => new CurrencyResource($this->whenLoaded('currency')),
            'partner'                                => new PartnerResource($this->whenLoaded('partner')),
            'outstandingAccount'                     => new AccountResource($this->whenLoaded('outstandingAccount')),
            'destinationAccount'                     => new AccountResource($this->whenLoaded('destinationAccount')),
            'createdBy'                              => new UserResource($this->whenLoaded('createdBy')),
            'invoices'                               => MoveResource::collection($this->whenLoaded('invoices')),
        ];
    }
}
