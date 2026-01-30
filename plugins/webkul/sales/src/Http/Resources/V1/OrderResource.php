<?php

namespace Webkul\Sale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Account\Http\Resources\V1\FiscalPositionResource;
use Webkul\Account\Http\Resources\V1\JournalResource;
use Webkul\Account\Http\Resources\V1\PaymentTermResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Security\Http\Resources\V1\TeamResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'state'              => $this->state?->value,
            'invoice_status'     => $this->invoice_status?->value,
            'client_order_ref'   => $this->client_order_ref,
            'origin'             => $this->origin,
            'reference'          => $this->reference,
            'date_order'         => $this->date_order,
            'validity_date'      => $this->validity_date,
            'commitment_date'    => $this->commitment_date,
            'currency_rate'      => (float) $this->currency_rate,
            'amount_untaxed'     => (float) $this->amount_untaxed,
            'amount_tax'         => (float) $this->amount_tax,
            'amount_total'       => (float) $this->amount_total,
            'locked'             => (bool) $this->locked,
            'require_signature'  => (bool) $this->require_signature,
            'require_payment'    => (bool) $this->require_payment,
            'signed_by'          => $this->signed_by,
            'signed_on'          => $this->signed_on,
            'prepayment_percent' => (float) $this->prepayment_percent,
            'note'               => $this->note,
            'access_token'       => $this->access_token,
            'partner'          => PartnerResource::make($this->whenLoaded('partner')),
            'partner_invoice'  => PartnerResource::make($this->whenLoaded('partnerInvoice')),
            'partner_shipping' => PartnerResource::make($this->whenLoaded('partnerShipping')),
            'user'             => UserResource::make($this->whenLoaded('user')),
            'team'             => TeamResource::make($this->whenLoaded('team')),
            'company'         => CompanyResource::make($this->whenLoaded('company')),
            'currency'        => CurrencyResource::make($this->whenLoaded('currency')),
            'payment_term'    => PaymentTermResource::make($this->whenLoaded('paymentTerm')),
            'fiscal_position' => FiscalPositionResource::make($this->whenLoaded('fiscalPosition')),
            'journal'         => JournalResource::make($this->whenLoaded('journal')),
            'campaign'        => $this->whenLoaded('campaign', fn () => [
                'id'   => $this->campaign->id,
                'name' => $this->campaign->name,
            ]),
            'utm_source' => $this->whenLoaded('utmSource', fn () => [
                'id'   => $this->utmSource->id,
                'name' => $this->utmSource->name,
            ]),
            'medium' => $this->whenLoaded('medium', fn () => [
                'id'   => $this->medium->id,
                'name' => $this->medium->name,
            ]),
            'warehouse' => $this->whenLoaded('warehouse', fn () => [
                'id'   => $this->warehouse->id,
                'name' => $this->warehouse->name,
            ]),
            'lines'      => OrderLineResource::collection($this->whenLoaded('lines')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
