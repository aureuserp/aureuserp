<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry as Registry;
use Webkul\Partner\Models\Partner;

class PartnerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(array_merge(
                [static::generalSection()],
                Registry::renderInfolist('general.after'),
                [static::tabs()],
            ))
            ->columns(1);
    }

    public static function generalSection(): Section
    {
        return Section::make(__('partners::filament/resources/partner.infolist.sections.general.title'))
            ->schema([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('account_type')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('name')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large),

                                TextEntry::make('parent.name')
                                    ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.company'))
                                    ->visible(fn ($record): bool => $record->account_type === AccountType::INDIVIDUAL->value),
                            ]),

                        Group::make()
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->circular()
                                    ->height(100)
                                    ->width(100),
                            ]),
                    ])->columns(2),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('tax_id')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.tax-id'))
                            ->placeholder('—'),

                        TextEntry::make('job_title')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.job-title'))
                            ->placeholder('—')
                            ->visible(fn ($record): bool => in_array($record->account_type, [AccountType::INDIVIDUAL, AccountType::INDIVIDUAL->value], true)),

                        TextEntry::make('phone')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.phone'))
                            ->icon('heroicon-o-phone')
                            ->placeholder('—'),

                        TextEntry::make('mobile')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.mobile'))
                            ->icon('heroicon-o-device-phone-mobile')
                            ->placeholder('—'),

                        TextEntry::make('email')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.email'))
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('website')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.website'))
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('—'),

                        TextEntry::make('title.name')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.title'))
                            ->placeholder('—')
                            ->visible(fn ($record): bool => in_array($record->account_type, [AccountType::INDIVIDUAL, AccountType::INDIVIDUAL->value], true)),

                        TextEntry::make('tags.name')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.fields.tags'))
                            ->badge()
                            ->state(function (Partner $record): array {
                                return $record->tags()->get()->map(fn ($tag) => [
                                    'label' => $tag->name,
                                    'color' => $tag->color ?? '#808080',
                                ])->toArray();
                            })
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state['label'])
                            ->color(fn ($state) => Color::generateV3Palette($state['color']))
                            ->separator(',')
                            ->visible(fn ($record): bool => (bool) $record->tags()->count()),
                    ]),

                Fieldset::make('Address')
                    ->schema([
                        TextEntry::make('street1')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.street1'))
                            ->placeholder('—'),

                        TextEntry::make('street2')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.street2'))
                            ->placeholder('—'),

                        TextEntry::make('city')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.city'))
                            ->placeholder('—'),

                        TextEntry::make('zip')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.zip'))
                            ->placeholder('—'),

                        TextEntry::make('country.name')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.country'))
                            ->placeholder('—'),

                        TextEntry::make('state.name')
                            ->label(__('partners::filament/resources/partner.infolist.sections.general.address.fields.state'))
                            ->placeholder('—'),
                    ]),
            ]);
    }

    public static function tabs(): Tabs
    {
        return Tabs::make('Tabs')
            ->tabs(array_merge(
                [static::salesPurchaseTab()],
                Registry::renderInfolist('tabs.append'),
            ))
            ->columnSpan(2);
    }

    public static function salesPurchaseTab(): Tab
    {
        return Tab::make(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.title'))
            ->icon('heroicon-o-currency-dollar')
            ->schema(array_merge([
                Section::make('Sales')
                    ->schema(array_merge([
                        TextEntry::make('user.name')
                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.responsible'))
                            ->placeholder('—'),
                    ], Registry::renderInfolist('sales.fields')))
                    ->columns(1),

                Section::make('Others')
                    ->schema([
                        TextEntry::make('company_registry')
                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.company-id'))
                            ->placeholder('—'),

                        TextEntry::make('reference')
                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.reference'))
                            ->placeholder('—'),

                        TextEntry::make('industry.name')
                            ->label(__('partners::filament/resources/partner.infolist.tabs.sales-purchase.fields.industry'))
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ], Registry::renderInfolist('salesPurchase.append')));
    }
}
