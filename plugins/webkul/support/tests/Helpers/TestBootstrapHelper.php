<?php

use Webkul\Support\Models\Currency;

class TestBootstrapHelper
{
    public static function ensureBaseCurrencies(array $requiredIds = [1, 2, 3]): void
    {
        $existingIds = Currency::query()
            ->whereIn('id', $requiredIds)
            ->pluck('id')
            ->all();

        $missingIds = array_values(array_diff($requiredIds, $existingIds));

        if ($missingIds === []) {
            return;
        }

        $sequenceStates = array_map(
            fn (int $id): array => ['id' => $id],
            $missingIds
        );

        Currency::factory()
            ->count(count($missingIds))
            ->sequence(...$sequenceStates)
            ->create();
    }
}
