<?php

namespace Webkul\Purchase\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Account\Http\Resources\V1\TaxResource;
use Webkul\Inventory\Http\Resources\V1\LocationResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\PackagingResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class OrderLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                           => $this->id,
            'name'                         => $this->name,
            'state'                        => $this->state,
            'sort'                         => $this->sort,
            'qty_received_method'          => $this->qty_received_method?->value,
            'display_type'                 => $this->display_type,
            'product_qty'                  => (float) $this->product_qty,
            'product_uom_qty'              => (float) $this->product_uom_qty,
            'product_packaging_qty'        => (float) $this->product_packaging_qty,
            'price_tax'                    => (float) $this->price_tax,
            'discount'                     => (float) $this->discount,
            'price_unit'                   => (float) $this->price_unit,
            'price_subtotal'               => (float) $this->price_subtotal,
            'price_total'                  => (float) $this->price_total,
            'qty_invoiced'                 => (float) $this->qty_invoiced,
            'qty_received'                 => (float) $this->qty_received,
            'qty_received_manual'          => (float) $this->qty_received_manual,
            'qty_to_invoice'               => (float) $this->qty_to_invoice,
            'is_downpayment'               => (bool) $this->is_downpayment,
            'planned_at'                   => $this->planned_at,
            'product_description_variants' => $this->product_description_variants,
            'propagate_cancel'             => (bool) $this->propagate_cancel,
            'price_total_cc'               => (float) $this->price_total_cc,
            'uom_id'                       => $this->uom_id,
            'product_id'                   => $this->product_id,
            'product_packaging_id'         => $this->product_packaging_id,
            'order_id'                     => $this->order_id,
            'partner_id'                   => $this->partner_id,
            'currency_id'                  => $this->currency_id,
            'company_id'                   => $this->company_id,
            'creator_id'                   => $this->creator_id,
            'final_location_id'            => $this->final_location_id,
            'order_point_id'               => $this->order_point_id,
            'created_at'                   => $this->created_at,
            'updated_at'                   => $this->updated_at,
            'uom'                          => new UOMResource($this->whenLoaded('uom')),
            'product'                      => new ProductResource($this->whenLoaded('product')),
            'product_packaging'            => new PackagingResource($this->whenLoaded('productPackaging')),
            'order'                        => new OrderResource($this->whenLoaded('order')),
            'partner'                      => new PartnerResource($this->whenLoaded('partner')),
            'currency'                     => new CurrencyResource($this->whenLoaded('currency')),
            'company'                      => new CompanyResource($this->whenLoaded('company')),
            'creator'                      => new UserResource($this->whenLoaded('creator')),
            'final_location'               => new LocationResource($this->whenLoaded('finalLocation')),
            'taxes'                        => TaxResource::collection($this->whenLoaded('taxes')),
        ];
    }
}
