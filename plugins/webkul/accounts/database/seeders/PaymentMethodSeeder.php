<?php

namespace Webkul\Account\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\PaymentType;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts_payment_methods')->delete();

        $user = User::first();

        $now = now();

        $paymentMethods = [
            [
                'id'           => 1,
                'code'         => 'manual',
                'payment_type' => PaymentType::RECEIVE,
                'name'         => 'Manual Payment',
                'creator_id'   => $user?->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 2,
                'code'         => 'manual',
                'payment_type' => PaymentType::SEND,
                'name'         => 'Manual Payment',
                'creator_id'   => $user?->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        DB::table('accounts_payment_methods')->insert($paymentMethods);
    }
}
