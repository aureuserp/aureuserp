<?php

namespace Webkul\Sale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Account\Http\Resources\V1\FiscalPositionResource;
use Webkul\Account\Http\Resources\V1\JournalResource;
use Webkul\Account\Http\Resources\V1\PaymentTermResource;
use Webkul\Inventory\Http\Resources\V1\WarehouseResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\TeamResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;
use Webkul\Support\Http\Resources\V1\UtmCampaignResource;
use Webkul\Support\Http\Resources\V1\UTMMediumResource;
use Webkul\Support\Http\Resources\V1\UTMSourceResource;

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
            'id'                  => $this->id,
            'name'                => $this->name,
            'state'               => $this->state?->value,
            'invoice_status'      => $this->invoice_status?->value,
            'client_order_ref'    => $this->client_order_ref,
            'origin'              => $this->origin,
            'reference'           => $this->reference,
            'date_order'          => $this->date_order,
            'validity_date'       => $this->validity_date,
            'commitment_date'     => $this->commitment_date,
            'currency_rate'       => (float) $this->currency_rate,
            'amount_untaxed'      => (float) $this->amount_untaxed,
            'amount_tax'          => (float) $this->amount_tax,
            'amount_total'        => (float) $this->amount_total,
            'locked'              => (bool) $this->locked,
            'require_signature'   => (bool) $this->require_signature,
            'require_payment'     => (bool) $this->require_payment,
            'signed_by'           => $this->signed_by,
            'signed_on'           => $this->signed_on,
            'prepayment_percent'  => (float) $this->prepayment_percent,
            'note'                => $this->note,
            'access_token'        => $this->access_token,
            'partner_id'          => $this->partner_id,
            'partner_invoice_id'  => $this->partner_invoice_id,
            'partner_shipping_id' => $this->partner_shipping_id,
            'user_id'             => $this->user_id,
            'team_id'             => $this->team_id,
            'company_id'          => $this->company_id,
            'currency_id'         => $this->currency_id,
            'payment_term_id'     => $this->payment_term_id,
            'fiscal_position_id'  => $this->fiscal_position_id,
            'journal_id'          => $this->journal_id,
            'campaign_id'         => $this->campaign_id,
            'utm_source_id'       => $this->utm_source_id,
            'medium_id'           => $this->medium_id,
            'warehouse_id'        => $this->warehouse_id,
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
            'deleted_at'          => $this->deleted_at?->toIso8601String(),
            'partner'             => new PartnerResource($this->whenLoaded('partner')),
            'partner_invoice'     => new PartnerResource($this->whenLoaded('partnerInvoice')),
            'partner_shipping'    => new PartnerResource($this->whenLoaded('partnerShipping')),
            'user'                => new UserResource($this->whenLoaded('user')),
            'team'                => new TeamResource($this->whenLoaded('team')),
            'company'             => new CompanyResource($this->whenLoaded('company')),
            'currency'            => new CurrencyResource($this->whenLoaded('currency')),
            'payment_term'        => new PaymentTermResource($this->whenLoaded('paymentTerm')),
            'fiscal_position'     => new FiscalPositionResource($this->whenLoaded('fiscalPosition')),
            'journal'             => new JournalResource($this->whenLoaded('journal')),
            'campaign'            => new UtmCampaignResource($this->whenLoaded('campaign')),
            'utm_source'          => new UTMSourceResource($this->whenLoaded('utmSource')),
            'medium'              => new UTMMediumResource($this->whenLoaded('medium')),
            'warehouse'           => new WarehouseResource($this->whenLoaded('warehouse')),
            'lines'               => OrderLineResource::collection($this->whenLoaded('lines')),
        ];
    }
}
