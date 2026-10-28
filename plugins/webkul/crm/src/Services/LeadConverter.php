<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Crm\Enums\LeadConversionAction;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Models\LeadMassConversion;
use Webkul\Partner\Models\Partner;

class LeadConverter
{
    public function __construct(protected LeadMerger $merger) {}

    public function convert(Lead $lead, array $options, ?Collection $duplicates = null): LeadConversion
    {
        return DB::transaction(function () use ($lead, $options, $duplicates): LeadConversion {
            $duplicates = ($options['action'] ?? LeadConversionAction::CONVERT) === LeadConversionAction::MERGE
                ? ($duplicates ?? collect())->reject(fn (Lead $record): bool => $record->is($lead))->values()
                : collect();

            [$opportunity, $merged] = $this->convertOne($lead, $options, $duplicates);

            $conversion = LeadConversion::query()->create([
                'lead_id'          => Lead::withTrashed()->whereKey($lead->getKey())->exists() ? $lead->getKey() : null,
                'original_lead_id' => $lead->getKey(),
                'lead_name'        => $lead->name,
                'opportunity_id'   => $opportunity->getKey(),
                'action'           => $merged->isNotEmpty() ? LeadConversionAction::MERGE : LeadConversionAction::CONVERT,
                'customer'         => $options['customer'] ?? LeadConversionCustomer::NONE,
                'partner_id'       => $opportunity->partner_id,
                'user_id'          => $opportunity->user_id,
                'team_id'          => $opportunity->team_id,
                'company_id'       => $opportunity->company_id,
            ]);

            $conversion->duplicates()->createMany($merged->map(fn (array $record): array => [
                'lead_id'          => Lead::withTrashed()->whereKey($record['id'])->exists() ? $record['id'] : null,
                'original_lead_id' => $record['id'],
                'lead_name'        => $record['name'],
            ])->all());

            return $conversion;
        });
    }

    public function massConvert(Collection $leads, array $options, ?callable $duplicatesFor = null): LeadMassConversion
    {
        return DB::transaction(function () use ($leads, $options, $duplicatesFor): LeadMassConversion {
            $salespeople = array_values(array_unique(array_map('intval', array_filter((array) ($options['salespeople'] ?? [])))));
            $deduplicate = (bool) ($options['deduplicate'] ?? false);
            $forceAssignment = (bool) ($options['force_assignment'] ?? false);
            $customer = $options['customer'] ?? LeadConversionCustomer::NONE;

            $massConversion = LeadMassConversion::query()->create([
                'action'           => $deduplicate ? LeadConversionAction::MERGE : LeadConversionAction::CONVERT,
                'customer'         => $customer,
                'deduplicate'      => $deduplicate,
                'force_assignment' => $forceAssignment,
                'team_id'          => $options['team_id'] ?? null,
                'company_id'       => $leads->first()?->company_id,
            ]);

            $massConversion->salespeople()->sync($salespeople);

            $turn = 0;
            $converted = 0;
            $mergedCount = 0;

            foreach ($leads as $lead) {
                $row = [
                    'original_lead_id' => $lead->getKey(),
                    'lead_name'        => $lead->name,
                ];

                $lead = Lead::query()->whereKey($lead->getKey())->first();

                if (! $lead
                    || ! $lead->isLead()) {
                    $massConversion->leads()->create($row + ['lead_id' => null, 'opportunity_id' => null]);

                    continue;
                }

                $userId = $lead->user_id;

                if ($salespeople
                    && ($forceAssignment || blank($lead->user_id))) {
                    $userId = $salespeople[$turn % count($salespeople)];
                    $turn++;
                }

                $duplicates = $deduplicate && $duplicatesFor ? $duplicatesFor($lead) : collect();

                [$opportunity, $merged] = $this->convertOne($lead, [
                    'customer'   => $customer === LeadConversionCustomer::NONE ? LeadConversionCustomer::NONE : LeadConversionCustomer::LINK,
                    'partner_id' => $this->massCustomerFor($lead, $customer),
                    'user_id'    => $userId,
                    'team_id'    => filled($options['team_id'] ?? null) ? $options['team_id'] : $lead->team_id,
                ], $duplicates);

                $massConversion->leads()->create($row + [
                    'lead_id'        => Lead::withTrashed()->whereKey($row['original_lead_id'])->exists() ? $row['original_lead_id'] : null,
                    'opportunity_id' => $opportunity->getKey(),
                ]);

                $converted++;
                $mergedCount += $merged->count();
            }

            $massConversion->update(['converted_count' => $converted, 'merged_count' => $mergedCount]);

            return $massConversion;
        });
    }

    protected function convertOne(Lead $lead, array $options, Collection $duplicates): array
    {
        $duplicates = $duplicates->filter(fn (Lead $record): bool => (int) $record->company_id === (int) $lead->company_id)->values();

        $merged = $duplicates->map(fn (Lead $record): array => ['id' => $record->getKey(), 'name' => $record->name]);

        $opportunity = $duplicates->isNotEmpty()
            ? $this->merger->merge(new EloquentCollection([$lead, ...$duplicates]))
            : $lead;

        $opportunity->convertToOpportunity($options);

        return [$opportunity->refresh(), $merged];
    }

    protected function massCustomerFor(Lead $lead, LeadConversionCustomer $customer): ?int
    {
        if ($customer === LeadConversionCustomer::NONE
            || filled($lead->partner_id)) {
            return $lead->partner_id;
        }

        $existing = filled($lead->email)
            ? ConvertToOpportunityAction::customersFor($lead)->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($lead->email))])->orderBy('id')->value('id')
            : null;

        if ($existing
            || $customer === LeadConversionCustomer::LINK) {
            return $existing;
        }

        /** @var Partner $partner */
        $partner = $lead->createCustomer();

        return $partner->getKey();
    }
}
