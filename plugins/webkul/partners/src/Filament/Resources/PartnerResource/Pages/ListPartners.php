<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPartners extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('partners::filament/resources/partner/pages/list-partners.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'individuals' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.individuals'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::INDIVIDUAL)),

            'companies' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.companies'))
                ->icon('heroicon-s-building-office')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::COMPANY)),

            'archived' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
