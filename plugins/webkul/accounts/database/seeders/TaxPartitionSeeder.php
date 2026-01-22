<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;

class TaxPartitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts_tax_partition_lines')->delete();

        $saleTax = Tax::where('type_tax_use', TypeTaxUse::SALE)->first();

        $purchaseTax = Tax::where('type_tax_use', TypeTaxUse::PURCHASE)->first();

        DB::table('accounts_tax_partition_lines')->insert([
            // Sale Tax Partitions
            [
                'repartition_type'   => 'base',
                'document_type'      => 'invoice',
                'use_in_tax_closing' => false,
                'factor'             => 1.0,
                'factor_percent'     => null,
                'tax_id'             => $saleTax?->id,
                'account_id'         => null,
                'sort'               => 1,
                'company_id'         => $saleTax?->company_id,
                'creator_id'         => $saleTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ], [
                'repartition_type'   => 'tax',
                'document_type'      => 'invoice',
                'use_in_tax_closing' => true,
                'factor'             => 1.0,
                'factor_percent'     => 100.0,
                'tax_id'             => $saleTax?->id,
                'sort'               => 2,
                'account_id'         => 22,
                'company_id'         => $saleTax?->company_id,
                'creator_id'         => $saleTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ], [
                'repartition_type'   => 'base',
                'document_type'      => 'refund',
                'use_in_tax_closing' => false,
                'factor'             => 1.0,
                'factor_percent'     => null,
                'tax_id'             => $saleTax?->id,
                'account_id'         => null,
                'sort'               => 1,
                'company_id'         => $saleTax?->company_id,
                'creator_id'         => $saleTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ], [
                'repartition_type'   => 'tax',
                'document_type'      => 'refund',
                'use_in_tax_closing' => true,
                'factor'             => 1.0,
                'factor_percent'     => 100.0,
                'tax_id'             => $saleTax?->id,
                'account_id'         => 22,
                'sort'               => 2,
                'company_id'         => $saleTax?->company_id,
                'creator_id'         => $saleTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            // Purchase Tax Partitions
            [
                'repartition_type'   => 'base',
                'document_type'      => 'invoice',
                'use_in_tax_closing' => false,
                'factor'             => 1.0,
                'factor_percent'     => null,
                'tax_id'             => $purchaseTax?->id,
                'account_id'         => null,
                'sort'               => 1,
                'company_id'         => $purchaseTax?->company_id,
                'creator_id'         => $purchaseTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ], [
                'repartition_type'   => 'tax',
                'document_type'      => 'invoice',
                'use_in_tax_closing' => true,
                'factor'             => 1.0,
                'factor_percent'     => 100.0,
                'tax_id'             => $purchaseTax?->id,
                'account_id'         => 10,
                'sort'               => 2,
                'company_id'         => $purchaseTax?->company_id,
                'creator_id'         => $purchaseTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ], [
                'repartition_type'   => 'base',
                'document_type'      => 'refund',
                'use_in_tax_closing' => false,
                'factor'             => 1.0,
                'factor_percent'     => null,
                'tax_id'             => $purchaseTax?->id,
                'account_id'         => null,
                'sort'               => 1,
                'company_id'         => $purchaseTax?->company_id,
                'creator_id'         => $purchaseTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ], [
                'repartition_type'   => 'tax',
                'document_type'      => 'refund',
                'use_in_tax_closing' => true,
                'factor'             => 1.0,
                'factor_percent'     => 100.0,
                'tax_id'             => $purchaseTax?->id,
                'account_id'         => 10,
                'sort'               => 2,
                'company_id'         => $purchaseTax?->company_id,
                'creator_id'         => $purchaseTax?->creator_id,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}
