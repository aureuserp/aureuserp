<?php

namespace Webkul\Sale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

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
            'id'                        => $this->id,
            'sort'                      => $this->sort,
            'product'                   => ProductResource::make($this->whenLoaded('product')),
            'name'                      => $this->name,
            'display_type'              => $this->display_type,
            'product_uom_qty'           => (float) $this->product_uom_qty,
            'product_qty'               => (float) $this->product_qty,
            'qty_delivered'             => (float) $this->qty_delivered,
            'qty_invoiced'              => (float) $this->qty_invoiced,
            'qty_to_invoice'            => (float) $this->qty_to_invoice,
            'product_packaging_qty'     => (float) $this->product_packaging_qty,
            'price_unit'                => (float) $this->price_unit,
            'technical_price_unit'      => (float) $this->technical_price_unit,
            'discount'                  => (float) $this->discount,
            'price_subtotal'            => (float) $this->price_subtotal,
            'price_tax'                 => (float) $this->price_tax,
            'price_total'               => (float) $this->price_total,
            'price_reduce_taxexcl'      => (float) $this->price_reduce_taxexcl,
            'price_reduce_taxinc'       => (float) $this->price_reduce_taxinc,
            'purchase_price'            => (float) $this->purchase_price,
            'margin'                    => (float) $this->margin,
            'margin_percent'            => (float) $this->margin_percent,
            'untaxed_amount_invoiced'   => (float) $this->untaxed_amount_invoiced,
            'untaxed_amount_to_invoice' => (float) $this->untaxed_amount_to_invoice,
            'state'                     => $this->state,
            'invoice_status'            => $this->invoice_status,
            'qty_delivered_method'      => $this->qty_delivered_method,
            'order_id'                  => $this->order_id,
            'product_uom'               => $this->whenLoaded('uom', fn () => [
                'id'   => $this->uom->id,
                'name' => $this->uom->name,
            ]),
            'product_packaging' => $this->whenLoaded('productPackaging', fn () => [
                'id'   => $this->productPackaging->id,
                'name' => $this->productPackaging->name,
            ]),
            'currency'      => CurrencyResource::make($this->whenLoaded('currency')),
            'order_partner' => PartnerResource::make($this->whenLoaded('orderPartner')),
            'salesman'      => UserResource::make($this->whenLoaded('salesman')),
            'warehouse'     => $this->whenLoaded('warehouse', fn () => [
                'id'   => $this->warehouse->id,
                'name' => $this->warehouse->name,
            ]),
            'route' => $this->whenLoaded('route', fn () => [
                'id'   => $this->route->id,
                'name' => $this->route->name,
            ]),
            'company'                   => CompanyResource::make($this->whenLoaded('company')),
            'linked_sale_order_sale_id' => $this->linked_sale_order_sale_id,
            'virtual_id'                => $this->virtual_id,
            'linked_virtual_id'         => $this->linked_virtual_id,
            'is_downpayment'            => (bool) $this->is_downpayment,
            'is_expense'                => (bool) $this->is_expense,
            'customer_lead'             => (float) $this->customer_lead,
            'analytic_distribution'     => $this->analytic_distribution,
            'created_at'                => $this->created_at?->toIso8601String(),
            'updated_at'                => $this->updated_at?->toIso8601String(),
        ];
    }
}
