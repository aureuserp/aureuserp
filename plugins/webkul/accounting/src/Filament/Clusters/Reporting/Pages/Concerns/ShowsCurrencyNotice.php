<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns;

use Webkul\Accounting\Support\CompanyRateMap;
use Webkul\Support\Models\Company;

trait ShowsCurrencyNotice
{
    public function currencyNotice(): ?array
    {
        $rateMap = CompanyRateMap::make();

        if ($rateMap->isMonoCurrency() && ! $rateMap->hasMissingRates()) {
            return null;
        }

        $currency = $rateMap->presentationCurrency();

        return [
            'consolidated'      => ! $rateMap->isMonoCurrency(),
            'company_count'     => count($rateMap->companyIds()),
            'currency'          => $currency?->name ?? $currency?->code ?? '—',
            'missing_rates'     => $rateMap->hasMissingRates(),
            'missing_companies' => Company::query()
                ->whereIn('id', $rateMap->missingRateCompanyIds())
                ->pluck('name')
                ->all(),
        ];
    }
}
