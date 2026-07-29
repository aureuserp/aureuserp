<?php

namespace Webkul\Support\Support;

use Webkul\Support\Exceptions\CrossCompanyException;

class CompanyConsistencyGuard
{
    /**
     * Names of the referenced records that do not belong to the given company.
     *
     * A record without a company is shared across every company and never conflicts.
     *
     * @param  array<int|string, mixed>  $rows
     * @param  array<string, class-string>  $fields
     * @return array<int, string>
     */
    public static function detect($companyId, array $rows, array $fields): array
    {
        $companyId = $companyId ?: current_company_id();

        $conflicts = [];

        foreach ($fields as $field => $model) {
            $ids = [];

            foreach ($rows as $row) {
                if (! is_array($row) || blank($row[$field] ?? null)) {
                    continue;
                }

                foreach ((array) $row[$field] as $id) {
                    $ids[] = $id;
                }
            }

            $ids = array_values(array_unique(array_filter($ids)));

            if (! $ids) {
                continue;
            }

            $allowed = $model::query()
                ->withoutGlobalScopes()
                ->whereKey($ids)
                ->where(owned_by_company($companyId))
                ->pluck((new $model)->getKeyName())
                ->all();

            $foreign = array_diff($ids, $allowed);

            if (! $foreign) {
                continue;
            }

            $names = $model::query()
                ->withoutGlobalScopes()
                ->whereKey($foreign)
                ->get()
                ->mapWithKeys(fn ($record) => [$record->getKey() => $record->name ?? $record->getKey()]);

            foreach ($foreign as $id) {
                // An id with no matching row is still a conflict; fall back to the raw id.
                $conflicts[] = $names[$id] ?? $id;
            }
        }

        return array_values(array_unique($conflicts));
    }

    /**
     * @param  array<int|string, mixed>  $rows
     * @param  array<string, class-string>  $fields
     */
    public static function assert($companyId, array $rows, array $fields): void
    {
        if ($conflicts = static::detect($companyId, $rows, $fields)) {
            throw CrossCompanyException::forRecords($conflicts);
        }
    }
}
