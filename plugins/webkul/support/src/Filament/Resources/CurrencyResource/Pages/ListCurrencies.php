<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Models\Currency;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/currency/pages/list-currency.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('support::filament/resources/currency/pages/list-currency.tabs.all'))
                ->badge(Currency::count()),
            'active' => Tab::make(__('support::filament/resources/currency/pages/list-currency.tabs.active'))
                ->badge(Currency::where('active', true)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('active', true);
                }),
            'inactive' => Tab::make(__('support::filament/resources/currency/pages/list-currency.tabs.inactive'))
                ->badge(Currency::where('active', false)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('active', false);
                }),
        ];
    }
}
