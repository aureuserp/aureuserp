<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class PaymentMethodLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts_payment_method_lines')->delete();

        $user = User::first();

        $now = now();

        $paymentMethodLines = [
            [
                'id'                 => 1,
                'sort'               => 1,
                'payment_method_id'  => 1,
                'payment_account_id' => null,
                'journal_id'         => 6,
                'creator_id'         => $user?->id,
                'name'               => 'Manual Payment',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id'                 => 2,
                'sort'               => 2,
                'payment_method_id'  => 2,
                'payment_account_id' => null,
                'journal_id'         => 6,
                'creator_id'         => $user?->id,
                'name'               => 'Manual Payment',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id'                 => 3,
                'sort'               => 3,
                'payment_method_id'  => 2,
                'payment_account_id' => null,
                'journal_id'         => 6,
                'creator_id'         => $user?->id,
                'name'               => 'Manual Payment',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id'                 => 4,
                'sort'               => 4,
                'payment_method_id'  => 1,
                'payment_account_id' => null,
                'journal_id'         => 6,
                'creator_id'         => $user?->id,
                'name'               => 'Manual Payment',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ];

        DB::table('accounts_payment_method_lines')->insert($paymentMethodLines);
    }
}
