<?php

namespace Webkul\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\PointOfSale\Support\PosAccess;

class OrderSyncRequest extends FormRequest
{
    protected function reachable(string $table): Exists
    {
        $companyIds = PosAccess::companyIds();

        return Rule::exists($table, 'id')->where(function ($query) use ($companyIds) {
            if ($companyIds === []) {
                return $query;
            }

            return $query->where(fn ($scope) => $scope->whereIn('company_id', $companyIds)->orWhereNull('company_id'));
        });
    }

    public function rules(): array
    {
        return [
            'orders'                                  => ['required', 'array', 'min:1', 'max:100'],
            'orders.*.uuid'                           => ['required', 'uuid'],
            'orders.*.reference'                      => ['nullable', 'string', 'max:64'],
            'orders.*.tracking_number'                => ['nullable', 'string', 'max:32'],
            'orders.*.sequence_number'                => ['nullable', 'integer', 'min:1'],
            'orders.*.config_id'                      => ['required', 'integer', $this->reachable('pos_configs')],
            'orders.*.session_id'                     => ['nullable', 'integer', $this->reachable('pos_sessions')],
            'orders.*.partner_id'                     => ['nullable', 'integer', 'exists:partners_partners,id'],
            'orders.*.partner'                        => ['nullable', 'array'],
            'orders.*.partner.name'                   => ['required_with:orders.*.partner', 'string', 'max:255'],
            'orders.*.partner.email'                  => ['nullable', 'email', 'max:255'],
            'orders.*.partner.phone'                  => ['nullable', 'string', 'max:64'],
            'orders.*.partner.mobile'                 => ['nullable', 'string', 'max:64'],
            'orders.*.partner.street1'                => ['nullable', 'string', 'max:255'],
            'orders.*.partner.city'                   => ['nullable', 'string', 'max:128'],
            'orders.*.partner.zip'                    => ['nullable', 'string', 'max:32'],
            'orders.*.table_id'                       => ['nullable', 'integer', 'exists:pos_tables,id'],
            'orders.*.shipped_at'                     => ['nullable', 'date'],
            'orders.*.ordered_at'                     => ['nullable', 'date'],
            'orders.*.price_list_id'                  => ['nullable', 'integer', 'exists:products_product_price_lists,id'],
            'orders.*.fiscal_position_id'             => ['nullable', 'integer', 'exists:accounts_fiscal_positions,id'],
            'orders.*.is_to_invoice'                  => ['nullable', 'boolean'],
            'orders.*.is_takeaway'                    => ['nullable', 'boolean'],
            'orders.*.customer_count'                 => ['nullable', 'integer', 'min:0'],
            'orders.*.floating_name'                  => ['nullable', 'string', 'max:64'],
            'orders.*.is_booked'                      => ['nullable', 'boolean'],
            'orders.*.preparation_state'              => ['nullable', 'array'],
            'orders.*.note'                           => ['nullable', 'string'],
            'orders.*.email'                          => ['nullable', 'email', 'max:255'],
            'orders.*.mobile'                         => ['nullable', 'string', 'max:64'],
            'orders.*.refunded_order_id'              => ['nullable', 'integer', $this->reachable('pos_orders')],
            'orders.*.lines'                          => ['required', 'array', 'min:1'],
            'orders.*.lines.*.uuid'                   => ['required', 'uuid'],
            'orders.*.lines.*.product_id'             => ['required', 'integer', 'exists:products_products,id'],
            'orders.*.lines.*.qty'                    => ['required', 'numeric', 'not_in:0'],
            'orders.*.lines.*.price_unit'             => ['nullable', 'numeric'],
            'orders.*.lines.*.price_type'             => ['nullable', Rule::enum(PriceType::class)],
            'orders.*.lines.*.discount'               => ['nullable', 'numeric', 'min:0', 'max:100'],
            'orders.*.lines.*.note'                   => ['nullable', 'string'],
            'orders.*.lines.*.customer_note'          => ['nullable', 'string'],
            'orders.*.lines.*.tax_ids'                => ['nullable', 'array'],
            'orders.*.lines.*.refunded_order_line_id' => ['nullable', 'integer', $this->reachable('pos_order_lines')],
            'orders.*.lines.*.lots'                   => ['nullable', 'array'],
            'orders.*.lines.*.lots.*.lot_name'        => ['required_with:orders.*.lines.*.lots', 'string'],
            'orders.*.lines.*.lots.*.lot_id'          => ['nullable', 'integer'],
            'orders.*.lines.*.lots.*.qty'             => ['nullable', 'numeric'],
            'orders.*.payments'                       => ['required', 'array', 'min:1'],
            'orders.*.payments.*.uuid'                => ['required', 'uuid'],
            'orders.*.payments.*.payment_method_id'   => ['required', 'integer', 'exists:pos_payment_methods,id'],
            'orders.*.payments.*.amount'              => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'orders.*.lines.*.product_id.exists'             => __('point-of-sale::system.order-sync.product-missing'),
            'orders.*.partner_id.exists'                     => __('point-of-sale::system.order-sync.customer-missing'),
            'orders.*.table_id.exists'                       => __('point-of-sale::system.order-sync.table-missing'),
            'orders.*.price_list_id.exists'                  => __('point-of-sale::system.order-sync.price-list-missing'),
            'orders.*.fiscal_position_id.exists'             => __('point-of-sale::system.order-sync.fiscal-position-missing'),
            'orders.*.payments.*.payment_method_id.exists'   => __('point-of-sale::system.order-sync.payment-method-missing'),
            'orders.*.refunded_order_id.exists'              => __('point-of-sale::system.order-sync.refunded-order-missing'),
            'orders.*.lines.*.refunded_order_line_id.exists' => __('point-of-sale::system.order-sync.refunded-order-missing'),
            'orders.*.partner.email.email'                   => __('point-of-sale::system.order-sync.customer-email-invalid'),
            'orders.*.email.email'                           => __('point-of-sale::system.order-sync.customer-email-invalid'),
            'orders.*.lines.*.discount.max'                  => __('point-of-sale::system.order-sync.discount-invalid'),
            'orders.*.lines.*.discount.min'                  => __('point-of-sale::system.order-sync.discount-invalid'),
        ];
    }
}
