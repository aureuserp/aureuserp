<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Security\Models\User;

class PaymentDueTermSeeder extends Seeder
{
    public function run()
    {
        DB::table('accounts_payment_due_terms')->delete();

        $user = User::first();

        $paymentDueTerms = [
            [
                'nb_days'         => 0,
                'payment_id'      => 1,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 15,
                'payment_id'      => 2,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 21,
                'payment_id'      => 3,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nb_days'         => 30,
                'payment_id'      => 4,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nb_days'         => 45,
                'payment_id'      => 5,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 0,
                'payment_id'      => 6,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER_END_OF_NEXT_MONTH,
                'days_next_month' => 10,
                'value_amount'    => 30.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 10,
                'payment_id'      => 7,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER_END_OF_NEXT_MONTH,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 0,
                'payment_id'      => 8,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 30.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 60,
                'payment_id'      => 8,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 70.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 30,
                'payment_id'      => 9,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_AFTER,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ], [
                'nb_days'         => 90,
                'payment_id'      => 10,
                'creator_id'      => $user?->id,
                'value'           => DueTermValue::PERCENT,
                'delay_type'      => DelayType::DAYS_END_OF_MONTH_NO_THE,
                'days_next_month' => 10,
                'value_amount'    => 100.000000,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ];

        DB::table('accounts_payment_due_terms')->insert($paymentDueTerms);
    }
}
