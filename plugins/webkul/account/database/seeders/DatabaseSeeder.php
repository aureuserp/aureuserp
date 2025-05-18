<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,
            AccountTagSeeder::class,
            BankStatementLineSeeder::class,
            BankStatementSeeder::class,
            CashRoundingSeeder::class,
            FiscalPositionSeeder::class,
            FiscalPositionTaxSeeder::class,
            FullReconcileSeeder::class,
            IncotermSeeder::class,
            JournalSeeder::class,
            MoveLineSeeder::class,
            MoveSeeder::class,
            PartialReconcileSeeder::class,
            PaymentSeeder::class,
            PaymentTermSeeder::class,
            PaymentDueTermSeeder::class,
            PaymentMethodSeeder::class,
            PaymentMethodLineSeeder::class,
            ReconcileSeeder::class,
            TaxGroupSeeder::class,
            TaxPartitionSeeder::class,
            TaxSeeder::class,
        ]);
    }
}
