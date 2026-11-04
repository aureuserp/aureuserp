<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Chatter\Models\Message;
use Webkul\Partner\Models\Partner;

class CustomerMerger
{
    public const FILLABLE_FIELDS = [
        'email', 'phone', 'mobile', 'website', 'job_title', 'tax_id', 'company_registry', 'reference',
        'street1', 'street2', 'city', 'zip', 'state_id', 'country_id', 'user_id', 'title_id', 'industry_id', 'parent_id',
    ];

    public function merge(Partner $destination, Collection $contacts): Partner
    {
        $sources = $contacts->reject(fn (Partner $contact): bool => $contact->is($destination))->values();

        if ($sources->isEmpty()) {
            return $destination;
        }

        $sourceIds = $sources->modelKeys();

        DB::transaction(function () use ($destination, $sources, $sourceIds): void {
            foreach (static::FILLABLE_FIELDS as $field) {
                if (blank($destination->{$field})) {
                    $destination->{$field} = $sources->first(fn (Partner $source): bool => filled($source->{$field}))?->{$field};
                }
            }

            if (in_array($destination->parent_id, [...$sourceIds, $destination->getKey()], true)) {
                $destination->parent_id = null;
            }

            $destination->save();

            foreach ($this->partnerForeignKeys() as [$table, $column]) {
                $this->reassign($table, $column, $sourceIds, $destination->getKey());
            }

            if ($destination->refresh()->parent_id === $destination->getKey()) {
                $destination->forceFill(['parent_id' => null])->save();
            }

            Message::query()
                ->where('messageable_type', $destination->getMorphClass())
                ->whereIn('messageable_id', $sourceIds)
                ->update(['messageable_id' => $destination->getKey()]);

            $sources->each(fn (Partner $source): ?bool => $source->delete());

            $destination->addMessage([
                'type' => 'comment',
                'body' => e(__('crm::filament/clusters/sales/resources/customer.actions.merge.log', ['names' => $sources->pluck('name')->implode(', ')])),
            ]);
        });

        return $destination->refresh();
    }

    protected function partnerForeignKeys(): array
    {
        $table = (new Partner)->getTable();

        return collect(Schema::getTableListing(schemaQualified: false))
            ->flatMap(fn (string $name): array => collect(Schema::getForeignKeys($name))
                ->filter(fn (array $key): bool => $key['foreign_table'] === $table && $key['foreign_columns'] === ['id'] && count($key['columns']) === 1)
                ->map(fn (array $key): array => [$name, $key['columns'][0]])
                ->all())
            ->all();
    }

    protected function reassign(string $table, string $column, array $sourceIds, int $destinationId): void
    {
        $unique = collect(Schema::getIndexes($table))
            ->first(fn (array $index): bool => $index['unique'] && in_array($column, $index['columns'], true) && count($index['columns']) > 1);

        if (! $unique) {
            DB::table($table)->whereIn($column, $sourceIds)->update([$column => $destinationId]);

            return;
        }

        $others = array_values(array_diff($unique['columns'], [$column]));

        foreach (DB::table($table)->whereIn($column, $sourceIds)->get() as $row) {
            $match = collect($others)->mapWithKeys(fn (string $other): array => [$other => $row->{$other}])->all();

            $rows = DB::table($table)->where($match)->where($column, $row->{$column});

            DB::table($table)->where($match)->where($column, $destinationId)->exists()
                ? $rows->delete()
                : $rows->update([$column => $destinationId]);
        }
    }
}
