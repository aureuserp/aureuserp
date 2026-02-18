<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\BankAccountResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;
use Webkul\Support\Http\Resources\V1\UtmCampaignResource;
use Webkul\Support\Http\Resources\V1\UTMMediumResource;
use Webkul\Support\Http\Resources\V1\UTMSourceResource;

class MoveResource extends JsonResource
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
            'sort'                                   => $this->sort,
            'journal_id'                             => $this->journal_id,
            'company_id'                             => $this->company_id,
            'campaign_id'                            => $this->campaign_id,
            'tax_cash_basis_origin_move_id'          => $this->tax_cash_basis_origin_move_id,
            'auto_post_origin_id'                    => $this->auto_post_origin_id,
            'origin_payment_id'                      => $this->origin_payment_id,
            'secure_sequence_number'                 => $this->secure_sequence_number,
            'invoice_payment_term_id'                => $this->invoice_payment_term_id,
            'partner_id'                             => $this->partner_id,
            'commercial_partner_id'                  => $this->commercial_partner_id,
            'partner_shipping_id'                    => $this->partner_shipping_id,
            'partner_bank_id'                        => $this->partner_bank_id,
            'fiscal_position_id'                     => $this->fiscal_position_id,
            'currency_id'                            => $this->currency_id,
            'reversed_entry_id'                      => $this->reversed_entry_id,
            'invoice_user_id'                        => $this->invoice_user_id,
            'invoice_incoterm_id'                    => $this->invoice_incoterm_id,
            'invoice_cash_rounding_id'               => $this->invoice_cash_rounding_id,
            'preferred_payment_method_line_id'       => $this->preferred_payment_method_line_id,
            'creator_id'                             => $this->creator_id,
            'sequence_prefix'                        => $this->sequence_prefix,
            'access_token'                           => $this->access_token,
            'name'                                   => $this->name,
            'reference'                              => $this->reference,
            'state'                                  => $this->state,
            'move_type'                              => $this->move_type,
            'auto_post'                              => $this->auto_post,
            'inalterable_hash'                       => $this->inalterable_hash,
            'payment_reference'                      => $this->payment_reference,
            'qr_code_method'                         => $this->qr_code_method,
            'payment_state'                          => $this->payment_state,
            'invoice_source_email'                   => $this->invoice_source_email,
            'invoice_partner_display_name'           => $this->invoice_partner_display_name,
            'invoice_origin'                         => $this->invoice_origin,
            'incoterm_location'                      => $this->incoterm_location,
            'date'                                   => $this->date,
            'auto_post_until'                        => $this->auto_post_until,
            'invoice_date'                           => $this->invoice_date,
            'invoice_date_due'                       => $this->invoice_date_due,
            'delivery_date'                          => $this->delivery_date,
            'sending_data'                           => $this->sending_data,
            'narration'                              => $this->narration,
            'invoice_currency_rate'                  => $this->invoice_currency_rate,
            'amount_untaxed'                         => $this->amount_untaxed,
            'amount_tax'                             => $this->amount_tax,
            'amount_total'                           => $this->amount_total,
            'amount_residual'                        => $this->amount_residual,
            'amount_untaxed_signed'                  => $this->amount_untaxed_signed,
            'amount_untaxed_in_currency_signed'      => $this->amount_untaxed_in_currency_signed,
            'amount_tax_signed'                      => $this->amount_tax_signed,
            'amount_total_signed'                    => $this->amount_total_signed,
            'amount_total_in_currency_signed'        => $this->amount_total_in_currency_signed,
            'amount_residual_signed'                 => $this->amount_residual_signed,
            'quick_edit_total_amount'                => $this->quick_edit_total_amount,
            'is_storno'                              => $this->is_storno,
            'always_tax_exigible'                    => $this->always_tax_exigible,
            'checked'                                => $this->checked,
            'posted_before'                          => $this->posted_before,
            'made_sequence_gap'                      => $this->made_sequence_gap,
            'is_manually_modified'                   => $this->is_manually_modified,
            'is_move_sent'                           => $this->is_move_sent,
            'source_id'                              => $this->source_id,
            'medium_id'                              => $this->medium_id,
            'created_at'                             => $this->created_at,
            'updated_at'                             => $this->updated_at,
            'campaign'                               => new UtmCampaignResource($this->whenLoaded('campaign')),
            'journal'                                => new JournalResource($this->whenLoaded('journal')),
            'company'                                => new CompanyResource($this->whenLoaded('company')),
            'originPayment'                          => new PaymentResource($this->whenLoaded('originPayment')),
            'taxCashBasisOriginMove'                 => new self($this->whenLoaded('taxCashBasisOriginMove')),
            'autoPostOrigin'                         => new self($this->whenLoaded('autoPostOrigin')),
            'invoicePaymentTerm'                     => new PaymentTermResource($this->whenLoaded('invoicePaymentTerm')),
            'partner'                                => new PartnerResource($this->whenLoaded('partner')),
            'commercialPartner'                      => new PartnerResource($this->whenLoaded('commercialPartner')),
            'partnerShipping'                        => new PartnerResource($this->whenLoaded('partnerShipping')),
            'partnerBank'                            => new BankAccountResource($this->whenLoaded('partnerBank')),
            'fiscalPosition'                         => new FiscalPositionResource($this->whenLoaded('fiscalPosition')),
            'currency'                               => new CurrencyResource($this->whenLoaded('currency')),
            'reversedEntry'                          => new self($this->whenLoaded('reversedEntry')),
            'invoiceUser'                            => new UserResource($this->whenLoaded('invoiceUser')),
            'invoiceIncoterm'                        => new IncotermResource($this->whenLoaded('invoiceIncoterm')),
            'invoiceCashRounding'                    => new CashRoundingResource($this->whenLoaded('invoiceCashRounding')),
            'createdBy'                              => new UserResource($this->whenLoaded('createdBy')),
            'source'                                 => new UTMSourceResource($this->whenLoaded('source')),
            'medium'                                 => new UTMMediumResource($this->whenLoaded('medium')),
            'paymentMethodLine'                      => new PaymentMethodLineResource($this->whenLoaded('paymentMethodLine')),
        ];
    }
}
