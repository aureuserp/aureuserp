<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amountUntaxed = $this->faker->randomFloat(4, 100, 10000);
        $amountTax = $amountUntaxed * 0.15;
        $amountTotal = $amountUntaxed + $amountTax;
        $partner = Partner::factory();

        return [
            'access_token'            => $this->faker->uuid(),
            'state'                   => OrderState::DRAFT,
            'client_order_ref'        => $this->faker->optional()->numerify('PO-####'),
            'origin'                  => $this->faker->optional()->numerify('SO-####'),
            'reference'               => $this->faker->optional()->word(),
            'signed_by'               => null,
            'invoice_status'          => InvoiceStatus::TO_INVOICE,
            'validity_date'           => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'note'                    => $this->faker->optional()->paragraph(),
            'locked'                  => false,
            'commitment_date'         => null,
            'date_order'              => $this->faker->dateTimeBetween('-30 days', 'now'),
            'signed_on'               => null,
            'prepayment_percent'      => null,
            'require_signature'       => false,
            'require_payment'         => false,
            'currency_rate'           => 1.0,
            'amount_untaxed'          => $amountUntaxed,
            'amount_tax'              => $amountTax,
            'amount_total'            => $amountTotal,
            'utm_source_id'           => null,
            'medium_id'               => null,
            'company_id'              => Company::factory(),
            'partner_id'              => $partner,
            'journal_id'              => null,
            'partner_invoice_id'      => $partner,
            'partner_shipping_id'     => $partner,
            'fiscal_position_id'      => null,
            'sale_order_template_id'  => null,
            'payment_term_id'         => null,
            'currency_id'             => Currency::factory(),
            'user_id'                 => User::factory(),
            'team_id'                 => null,
            'creator_id'              => User::factory(),
            'campaign_id'             => null,
            'warehouse_id'            => null,
        ];
    }

    /**
     * Indicate that the order is in draft state.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::DRAFT,
        ]);
    }

    /**
     * Indicate that the order is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::SENT,
        ]);
    }

    /**
     * Indicate that the order is confirmed as sale.
     */
    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::SALE,
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::CANCEL,
        ]);
    }

    /**
     * Indicate that the order is locked.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'locked' => true,
        ]);
    }

    /**
     * Indicate that the order has separate shipping address.
     */
    public function withSeparateShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_shipping_id' => Partner::factory(),
        ]);
    }

    /**
     * Indicate that the order has no shipping address.
     */
    public function withoutShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_shipping_id' => null,
        ]);
    }

    /**
     * Indicate that the order is system-generated (no user/creator).
     */
    public function systemGenerated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id'    => null,
            'creator_id' => null,
        ]);
    }

    /**
     * Indicate that the order requires signature and payment.
     */
    public function withRequirements(): static
    {
        return $this->state(fn (array $attributes) => [
            'require_signature' => true,
            'require_payment'   => true,
        ]);
    }

    /**
     * Indicate that the order has UTM tracking.
     */
    public function withUTMTracking(): static
    {
        return $this->state(fn (array $attributes) => [
            'utm_source_id' => UTMSource::factory(),
            'medium_id'     => UTMMedium::factory(),
            'campaign_id'   => UtmCampaign::factory(),
        ]);
    }

    /**
     * Indicate that the order has a sales team.
     */
    public function withTeam(): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => Team::factory(),
        ]);
    }

    /**
     * Indicate that the order has a warehouse.
     */
    public function withWarehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_id' => Warehouse::factory(),
        ]);
    }

    /**
     * Indicate that the order has payment terms.
     */
    public function withPaymentTerms(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_term_id' => PaymentTerm::factory(),
        ]);
    }

    /**
     * Indicate that the order has fiscal position.
     */
    public function withFiscalPosition(): static
    {
        return $this->state(fn (array $attributes) => [
            'fiscal_position_id' => FiscalPosition::factory(),
        ]);
    }
}
