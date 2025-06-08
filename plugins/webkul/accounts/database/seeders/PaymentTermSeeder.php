<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\EarlyPayDiscount;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PaymentTermSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('accounts_payment_terms')->delete();

        $user = User::first();

        $company = Company::first();

        $paymentTerms = [
            [
                'id'                  => 1,
                'company_id'          => $company?->id,
                'sort'                => 1,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => 'Immediate Payment',
                'note'                => '<p>Payment terms: Immediate Payment</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 2,
                'company_id'          => $company?->id,
                'sort'                => 2,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '15 Days',
                'note'                => '<p>Payment terms: 15 Days</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 3,
                'company_id'          => $company?->id,
                'sort'                => 3,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '21 Days',
                'note'                => '<p>Payment terms: 21 Days</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 4,
                'company_id'          => $company?->id,
                'sort'                => 4,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '30 Days',
                'note'                => '<p>Payment terms: 30 Days</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 5,
                'company_id'          => $company?->id,
                'sort'                => 5,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '45 Days',
                'note'                => '<p>Payment terms: 45 Days</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 6,
                'company_id'          => $company?->id,
                'sort'                => 6,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => 'End of Following Month',
                'note'                => '<p>Payment terms: End of Following Month</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 7,
                'company_id'          => $company?->id,
                'sort'                => 7,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '10 Days after End of Next Month',
                'note'                => '<p>Payment terms: 10 Days after End of Next Month</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 8,
                'company_id'          => $company?->id,
                'sort'                => 8,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '30% Now, Balance 60 Days',
                'note'                => '<p>Payment terms: 30% Now, Balance 60 Days</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 9,
                'company_id'          => $company?->id,
                'sort'                => 9,
                'discount_days'       => 7,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '2/7 Net 30',
                'note'                => '<p>Payment terms: 30 Days, 2% Early Payment Discount under 7 days</p>',
                'display_on_invoice'  => true,
                'early_discount'      => true,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ], [
                'id'                  => 10,
                'company_id'          => $company?->id,
                'sort'                => 10,
                'discount_days'       => 10,
                'creator_id'          => $user?->id,
                'early_pay_discount'  => EarlyPayDiscount::INCLUDED,
                'name'                => '90 Days, on the 10th',
                'note'                => '<p>Payment terms: 90 days, on the 10th</p>',
                'display_on_invoice'  => true,
                'early_discount'      => false,
                'discount_percentage' => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ];

        DB::table('accounts_payment_terms')->insert($paymentTerms);
    }
}
