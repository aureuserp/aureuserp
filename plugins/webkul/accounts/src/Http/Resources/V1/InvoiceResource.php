<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\BankAccountResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;
use Webkul\Support\Http\Resources\V1\UtmCampaignResource;
use Webkul\Support\Http\Resources\V1\UTMMediumResource;
use Webkul\Support\Http\Resources\V1\UTMSourceResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                  => $this->id,
            'name'                                => $this->name,
            'reference'                           => $this->reference,
            'state'                               => $this->state,
            'move_type'                           => $this->move_type,
            'payment_state'                       => $this->payment_state,
            'partner_id'                          => $this->partner_id,
            'currency_id'                         => $this->currency_id,
            'journal_id'                          => $this->journal_id,
            'company_id'                          => $this->company_id,
            'invoice_date'                        => $this->invoice_date?->format('Y-m-d'),
            'invoice_date_due'                    => $this->invoice_date_due?->format('Y-m-d'),
            'date'                                => $this->date?->format('Y-m-d'),
            'invoice_payment_term_id'             => $this->invoice_payment_term_id,
            'fiscal_position_id'                  => $this->fiscal_position_id,
            'invoice_user_id'                     => $this->invoice_user_id,
            'partner_shipping_id'                 => $this->partner_shipping_id,
            'partner_bank_id'                     => $this->partner_bank_id,
            'invoice_incoterm_id'                 => $this->invoice_incoterm_id,
            'invoice_cash_rounding_id'            => $this->invoice_cash_rounding_id,
            'preferred_payment_method_line_id'    => $this->preferred_payment_method_line_id,
            'invoice_origin'                      => $this->invoice_origin,
            'payment_reference'                   => $this->payment_reference,
            'narration'                           => $this->narration,
            'incoterm_location'                   => $this->incoterm_location,
            'invoice_source_email'                => $this->invoice_source_email,
            'invoice_partner_display_name'        => $this->invoice_partner_display_name,
            'campaign_id'                         => $this->campaign_id,
            'source_id'                           => $this->source_id,
            'medium_id'                           => $this->medium_id,
            'amount_untaxed'                      => $this->amount_untaxed,
            'amount_tax'                          => $this->amount_tax,
            'amount_total'                        => $this->amount_total,
            'amount_residual'                     => $this->amount_residual,
            'invoice_currency_rate'               => $this->invoice_currency_rate,
            'is_move_sent'                        => $this->is_move_sent,
            'created_at'                          => $this->created_at,
            'updated_at'                          => $this->updated_at,
            'partner'                             => new PartnerResource($this->whenLoaded('partner')),
            'currency'                            => new CurrencyResource($this->whenLoaded('currency')),
            'journal'                             => new JournalResource($this->whenLoaded('journal')),
            'company'                             => new CompanyResource($this->whenLoaded('company')),
            'invoicePaymentTerm'                  => new PaymentTermResource($this->whenLoaded('invoicePaymentTerm')),
            'fiscalPosition'                      => new FiscalPositionResource($this->whenLoaded('fiscalPosition')),
            'invoiceUser'                         => new UserResource($this->whenLoaded('invoiceUser')),
            'partnerShipping'                     => new PartnerResource($this->whenLoaded('partnerShipping')),
            'partnerBank'                         => new BankAccountResource($this->whenLoaded('partnerBank')),
            'invoiceIncoterm'                     => new IncotermResource($this->whenLoaded('invoiceIncoterm')),
            'invoiceCashRounding'                 => new CashRoundingResource($this->whenLoaded('invoiceCashRounding')),
            'paymentMethodLine'                   => new PaymentMethodLineResource($this->whenLoaded('paymentMethodLine')),
            'campaign'                            => new UtmCampaignResource($this->whenLoaded('campaign')),
            'source'                              => new UTMSourceResource($this->whenLoaded('source')),
            'medium'                              => new UTMMediumResource($this->whenLoaded('medium')),
            'creator'                             => new UserResource($this->whenLoaded('creator')),
            'invoiceLines'                        => MoveLineResource::collection($this->whenLoaded('invoiceLines')),
        ];
    }
}
