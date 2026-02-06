<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Reconcile;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<\App\Models\Reconcile>
 */
class ReconcileFactory extends Factory
{
    protected $model = Reconcile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'                              => 0,
            'company_id'                        => Company::factory(),
            'past_months_limit'                 => 18,
            'created_by'                        => User::factory(),
            'rule_type'                         => 'invoice_matching',
            'matching_order'                    => 'new_first',
            'counter_part_type'                 => 'general',
            'match_nature'                      => 'both',
            'match_amount'                      => 'between',
            'match_label'                       => 'contains',
            'match_level_parameters'            => null,
            'match_note'                        => null,
            'match_note_parameters'             => null,
            'match_transaction_type'            => null,
            'match_transaction_type_parameters' => null,
            'payment_tolerance_type'            => 'percentage',
            'decimal_separator'                 => '.',
            'name'                              => $this->faker->words(3, true),
            'auto_reconcile'                    => true,
            'to_check'                          => false,
            'match_text_location_label'         => true,
            'match_text_location_note'          => false,
            'match_text_location_reference'     => false,
            'match_same_currency'               => true,
            'allow_payment_tolerance'           => false,
            'match_partner'                     => true,
            'match_amount_min'                  => 0,
            'match_amount_max'                  => 100,
            'payment_tolerance_parameters'      => 0,
        ];
    }

    public function writeoffSuggestion(): static
    {
        return $this->state(fn (array $attributes) => [
            'rule_type' => 'writeoff_suggestion',
        ]);
    }
}
