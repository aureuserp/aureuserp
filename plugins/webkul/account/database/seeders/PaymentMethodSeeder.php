<?php

namespace Webkul\Account\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'payment_type' => 'inbound',
                'name'         => 'Manual Payment',
                'created_by'   => $user?->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 2,
                'code'         => 'manual',
                'payment_type' => 'outbound',
                'name'         => 'Manual Payment',
                'created_by'   => $user?->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        DB::table('accounts_payment_methods')->insert($paymentMethods);
    }
}
