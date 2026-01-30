<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<\App\Models\Move>
 */
class MoveFactory extends Factory
{
    protected $model = Move::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'                              => 0,
            'journal_id'                        => Journal::factory(),
            'company_id'                        => Company::factory(),
            'campaign_id'                       => null,
            'tax_cash_basis_origin_move_id'     => null,
            'auto_post_origin_id'               => null,
            'origin_payment_id'                 => null,
            'secure_sequence_number'            => 0,
            'invoice_payment_term_id'           => null,
            'partner_id'                        => null,
            'commercial_partner_id'             => null,
            'partner_shipping_id'               => null,
            'partner_bank_id'                   => null,
            'fiscal_position_id'                => null,
            'currency_id'                       => Currency::factory(),
            'reversed_entry_id'                 => null,
            'invoice_user_id'                   => null,
            'invoice_incoterm_id'               => null,
            'invoice_cash_rounding_id'          => null,
            'preferred_payment_method_line_id'  => null,
            'creator_id'                        => User::factory(),
            'sequence_prefix'                   => null,
            'access_token'                      => null,
            'name'                              => $this->faker->optional()->bothify('MISC/####/####'),
            'reference'                         => null,
            'state'                             => MoveState::DRAFT,
            'move_type'                         => MoveType::ENTRY,
            'auto_post'                         => 'no',
            'inalterable_hash'                  => null,
            'payment_reference'                 => null,
            'qr_code_method'                    => null,
            'payment_state'                     => 'not_paid',
            'invoice_source_email'              => null,
            'invoice_partner_display_name'      => null,
            'invoice_origin'                    => null,
            'incoterm_location'                 => null,
            'date'                              => $this->faker->date(),
            'auto_post_until'                   => null,
            'invoice_date'                      => null,
            'invoice_date_due'                  => null,
            'delivery_date'                     => null,
            'sending_data'                      => null,
            'narration'                         => null,
            'invoice_currency_rate'             => 1.0,
            'amount_untaxed'                    => 0.0,
            'amount_tax'                        => 0.0,
            'amount_total'                      => 0.0,
            'amount_residual'                   => 0.0,
            'amount_untaxed_signed'             => 0.0,
            'amount_untaxed_in_currency_signed' => 0.0,
            'amount_tax_signed'                 => 0.0,
            'amount_total_signed'               => 0.0,
            'amount_total_in_currency_signed'   => 0.0,
            'amount_residual_signed'            => 0.0,
            'quick_edit_total_amount'           => 0.0,
            'is_storno'                         => false,
            'always_tax_exigible'               => true,
            'checked'                           => false,
            'posted_before'                     => false,
            'made_sequence_gap'                 => false,
        ];
    }

    public function posted(): static
    {
        return $this->state(fn (array $attributes) => [
            'state'         => MoveState::POSTED,
            'posted_before' => true,
        ]);
    }

    public function invoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'move_type'    => MoveType::OUT_INVOICE,
            'partner_id'   => Partner::factory(),
            'invoice_date' => $this->faker->date(),
        ]);
    }

    public function withFiscalPosition(): static
    {
        return $this->state(fn (array $attributes) => [
            'fiscal_position_id' => FiscalPosition::factory(),
        ]);
    }
}
