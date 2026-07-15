<?php

namespace Webkul\Partner\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Webkul\Partner\Models\Industry;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Models\Title;
use Webkul\Support\Models\Company;

class SampleDataSeeder extends Seeder
{
    /**
     * Number of demo partners (customers/vendors) to generate.
     */
    protected int $count = 15;

    /**
     * Seed demo partners using the partner factory.
     *
     * Optional demo/sample data for development environments. Reuses the existing
     * company, titles and industries so the demo records stay consistent.
     */
    public function run(): void
    {
        if (Partner::query()->exists()) {
            $this->command?->warn('Partners already exist — skipping partner demo data.');

            return;
        }

        $companyId = Company::query()->value('id');

        $titleIds = Title::query()->pluck('id')->all();

        $industryIds = Industry::query()->pluck('id')->all();

        DB::transaction(function () use ($companyId, $titleIds, $industryIds) {
            Partner::factory()
                ->count($this->count)
                ->state(function () use ($companyId, $titleIds, $industryIds) {
                    $state = [];

                    if ($companyId) {
                        $state['company_id'] = $companyId;
                    }

                    if (! empty($titleIds)) {
                        $state['title_id'] = Arr::random($titleIds);
                    }

                    if (! empty($industryIds)) {
                        $state['industry_id'] = Arr::random($industryIds);
                    }

                    return $state;
                })
                ->create();
        });

        $this->command?->info("Created {$this->count} demo partners.");
    }
}
