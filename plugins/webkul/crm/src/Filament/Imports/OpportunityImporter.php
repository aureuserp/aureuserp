<?php

namespace Webkul\Crm\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Policies\LeadPolicy;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Scopes\CompanyScope;

class OpportunityImporter extends LeadRecordImporter
{
    protected static function getLeadType(): LeadType
    {
        return LeadType::OPPORTUNITY;
    }

    protected static function getPolicy(): string
    {
        return LeadPolicy::class;
    }

    public static function getColumns(): array
    {
        $columns = static::leadColumns();

        array_splice($columns, 2, 0, [
            ImportColumn::make('partner')
                ->label(__('crm::filament/transfers.columns.customer'))
                ->guess(['customer', 'partner', __('crm::filament/transfers.columns.customer')])
                ->relationship(resolveUsing: fn (?string $state, OpportunityImporter $importer): ?Partner => $importer->resolveCustomer($state))
                ->example('Azure Interior')
                ->ignoreBlankState(),
            ImportColumn::make('expected_revenue')
                ->label(__('crm::filament/transfers.columns.expected-revenue'))
                ->guess(['revenue', 'expected revenue', __('crm::filament/transfers.columns.expected-revenue')])
                ->numeric(decimalPlaces: 2)
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('12000')
                ->ignoreBlankState(),
            ImportColumn::make('expected_closing_date')
                ->label(__('crm::filament/transfers.columns.expected-closing'))
                ->guess(['expected closing', 'closing date', __('crm::filament/transfers.columns.expected-closing')])
                ->rules(['nullable', 'date'])
                ->example(now()->addMonth()->endOfMonth()->toDateString())
                ->ignoreBlankState(),
        ]);

        return $columns;
    }

    public function resolveCustomer(?string $state): ?Partner
    {
        if (blank($state)) {
            return null;
        }

        $state = trim($state);

        $partner = Partner::query()
            ->withoutGlobalScopes([CompanyScope::class])
            ->where(fn (Builder $query): Builder => $query->whereIn('company_id', $this->getCompanyIds())->orWhereNull('company_id'))
            ->where(fn (Builder $query): Builder => $query->where('name', $state)->orWhere('email', $state))
            ->first();

        return $partner ?? throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
            'field' => __('crm::filament/transfers.columns.customer'),
            'value' => $state,
        ]));
    }
}
