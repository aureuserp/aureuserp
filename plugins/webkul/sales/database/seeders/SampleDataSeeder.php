<?php

namespace Webkul\Sale\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class SampleDataSeeder extends Seeder
{
    /**
     * Number of demo sale orders to generate.
     */
    protected int $count = 12;

    /**
     * Seed demo sale orders (quotations and confirmed orders) with their lines.
     *
     * Optional demo/sample data for development environments. Depends on partners
     * and products; it will generate a small set of each if none exist yet so
     * the seeder can run standalone.
     */
    public function run(): void
    {
        if (Order::query()->exists()) {
            $this->command?->warn('Sale orders already exist — skipping sales demo data.');

            return;
        }

        $this->ensureDependencies();

        $company = Company::query()->first();
        $currency = Currency::query()->first();
        $user = User::query()->first();
        $fallbackUomId = UOM::query()->value('id');

        $partnerIds = Partner::query()->pluck('id')->all();
        $products = Product::query()->get();

        if (empty($partnerIds) || $products->isEmpty()) {
            $this->command?->warn('Could not seed sales demo data: missing partners or products.');

            return;
        }

        DB::transaction(function () use ($company, $currency, $user, $fallbackUomId, $partnerIds, $products) {
            for ($i = 0; $i < $this->count; $i++) {
                $partnerId = Arr::random($partnerIds);

                // Confirm roughly two-thirds of the orders as actual sales, keep the rest as quotations.
                $isSale = $i % 3 !== 0;

                $order = Order::factory()
                    ->state([
                        'company_id'          => $company?->id,
                        'currency_id'         => $currency?->id,
                        'user_id'             => $user?->id,
                        'creator_id'          => $user?->id,
                        'partner_id'          => $partnerId,
                        'partner_invoice_id'  => $partnerId,
                        'partner_shipping_id' => $partnerId,
                    ])
                    ->when($isSale, fn ($factory) => $factory->sale(), fn ($factory) => $factory->draft())
                    ->create();

                $lineCount = random_int(1, 4);

                $lines = collect();

                for ($j = 0; $j < $lineCount; $j++) {
                    $product = $products->random();

                    $line = OrderLine::factory()
                        ->when($isSale, fn ($factory) => $factory->sale(), fn ($factory) => $factory->draft())
                        ->state([
                            'order_id'         => $order->id,
                            'company_id'       => $company?->id,
                            'currency_id'      => $currency?->id,
                            'order_partner_id' => $partnerId,
                            'salesman_id'      => $user?->id,
                            'creator_id'       => $user?->id,
                            'product_id'       => $product->id,
                            'product_uom_id'   => $product->uom_id ?? $fallbackUomId,
                            'name'             => $product->name,
                            'price_unit'       => $product->price ?? fake()->randomFloat(2, 10, 500),
                        ])
                        ->create();

                    $lines->push($line);
                }

                // Keep the order totals consistent with the generated lines.
                $amountUntaxed = (float) $lines->sum('price_subtotal');
                $amountTax = (float) $lines->sum('price_tax');

                $order->update([
                    'amount_untaxed' => $amountUntaxed,
                    'amount_tax'     => $amountTax,
                    'amount_total'   => $amountUntaxed + $amountTax,
                ]);
            }
        });

        $this->command?->info("Created {$this->count} demo sale orders with lines.");
    }

    /**
     * Make sure the partners and products the orders reference actually exist.
     */
    protected function ensureDependencies(): void
    {
        if (! Partner::query()->exists()) {
            $this->command?->info('No partners found — seeding partner demo data first.');

            $this->call(\Webkul\Partner\Database\Seeders\SampleDataSeeder::class);
        }

        if (! Product::query()->exists()) {
            $this->command?->info('No products found — seeding product demo data first.');

            $this->call(\Webkul\Product\Database\Seeders\SampleDataSeeder::class);
        }
    }
}
