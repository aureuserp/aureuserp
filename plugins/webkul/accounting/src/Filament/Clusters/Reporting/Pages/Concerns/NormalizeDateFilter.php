<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns;

use Carbon\Carbon;
use Exception;

trait NormalizeDateFilter
{
    protected function parseDateRange(): ?array
    {
        $dateFilter = $this->form->getState()['date_range'] ?? null;

        if (! $dateFilter) {
            return null;
        }

        if (is_array($dateFilter) && isset($dateFilter['startDate'], $dateFilter['endDate'])) {
            return [$dateFilter['startDate'], $dateFilter['endDate']];
        }

        if (is_array($dateFilter) && count($dateFilter) === 2) {
            return $this->convertDateFormat($dateFilter);
        }

        if (is_string($dateFilter)) {
            $dates = explode(' - ', $dateFilter);

            if (count($dates) === 2) {
                return $this->convertDateFormat([trim($dates[0]), trim($dates[1])]);
            }
        }

        return null;
    }

    private function convertDateFormat(array $dates): array
    {
        $convertedDates = [];

        foreach ($dates as $date) {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                $dateParts = explode('/', $date);
                $convertedDates[] = $dateParts[2].'-'.$dateParts[1].'-'.$dateParts[0];
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $convertedDates[] = $date;
            } else {
                try {
                    $convertedDates[] = Carbon::parse($date)->format('Y-m-d');
                } catch (Exception $e) {
                    $convertedDates[] = $date;
                }
            }
        }

        return $convertedDates;
    }
}
