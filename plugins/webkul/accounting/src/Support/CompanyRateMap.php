<?php

namespace Webkul\Accounting\Support;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;

class CompanyRateMap
{
    protected function __construct(
        protected array $companyIds,
        protected array $rates,
        protected array $currencyIds,
        protected array $missingRateCompanyIds,
        protected ?Currency $presentationCurrency,
    ) {}

    public static function make(?array $companyIds = null, ?string $date = null): self
    {
        $companyIds = $companyIds ?: app(CompanyContext::class)->activeIds();

        if (empty($companyIds)) {
            $companyIds = array_filter([current_company_id()]);
        }

        $presentationCompany = current_company();

        $presentationCurrency = $presentationCompany?->currency;

        $companies = Company::query()
            ->whereIn('id', $companyIds)
            ->get(['id', 'currency_id']);

        $rates = [];

        $missingRateCompanyIds = [];

        foreach ($companies as $company) {
            $rates[$company->id] = static::resolveRate($company, $presentationCurrency, $presentationCompany, $date);

            if (static::needsTranslation($company, $presentationCurrency) && $rates[$company->id] === 1.0) {
                $missingRateCompanyIds[] = $company->id;
            }
        }

        return new self(
            $companies->pluck('id')->all(),
            $rates,
            $companies->pluck('currency_id')->filter()->unique()->values()->all(),
            $missingRateCompanyIds,
            $presentationCurrency,
        );
    }

    protected static function needsTranslation(Company $company, ?Currency $presentationCurrency): bool
    {
        return $presentationCurrency
            && $company->currency_id
            && $company->currency_id !== $presentationCurrency->id;
    }

    protected static function resolveRate(Company $company, ?Currency $presentationCurrency, ?Company $presentationCompany, ?string $date): float
    {
        if (! $presentationCurrency || ! $company->currency_id) {
            return 1.0;
        }

        if ($company->currency_id === $presentationCurrency->id) {
            return 1.0;
        }

        $companyCurrency = Currency::find($company->currency_id);

        if (! $companyCurrency) {
            return 1.0;
        }

        return (float) $companyCurrency->getConversionRate(
            $companyCurrency,
            $presentationCurrency,
            $presentationCompany,
            $date,
        );
    }

    public function companyIds(): array
    {
        return $this->companyIds;
    }

    public function presentationCurrency(): ?Currency
    {
        return $this->presentationCurrency;
    }

    public function isMonoCurrency(): bool
    {
        return count($this->currencyIds) <= 1;
    }

    public function missingRateCompanyIds(): array
    {
        return $this->missingRateCompanyIds;
    }

    public function hasMissingRates(): bool
    {
        return $this->missingRateCompanyIds !== [];
    }

    public function rateFor(int $companyId): float
    {
        return $this->rates[$companyId] ?? 1.0;
    }

    public function weight(string $expression, string $companyColumn = 'accounts_account_moves.company_id'): string
    {
        if ($this->isMonoCurrency()) {
            return $expression;
        }

        $cases = '';

        foreach ($this->rates as $companyId => $rate) {
            $cases .= ' WHEN '.(int) $companyId.' THEN '.sprintf('%.10F', $rate);
        }

        return "({$expression}) * (CASE {$companyColumn}{$cases} ELSE 1 END)";
    }

    public function sum(string $expression, string $alias, string $companyColumn = 'accounts_account_moves.company_id'): Expression
    {
        return DB::raw('SUM('.$this->weight($expression, $companyColumn).") as {$alias}");
    }
}
