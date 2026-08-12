<?php

namespace Webkul\Support\Filament\Resources\CompanyResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/company.infolist.sections.company-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.company-information.entries.name')),
                                        TextEntry::make('registration_number')
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.company-information.entries.registration-number')),
                                        TextEntry::make('company_id')
                                            ->icon('heroicon-o-identification')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.company-information.entries.company-id')),
                                        TextEntry::make('tax_id')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.company-information.entries.tax-id')),
                                        TextEntry::make('website')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.company-information.entries.website')),
                                    ])
                                    ->columns(2),

                                Section::make(__('support::filament/resources/company.infolist.sections.address-information.title'))
                                    ->schema([
                                        TextEntry::make('street1')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.address-information.entries.street1')),
                                        TextEntry::make('street2')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.address-information.entries.street2')),
                                        TextEntry::make('city')
                                            ->label(__('support::filament/resources/company.infolist.sections.address-information.entries.city'))
                                            ->icon('heroicon-o-building-library')
                                            ->placeholder('—'),
                                        TextEntry::make('zip')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.address-information.entries.zipcode')),
                                        TextEntry::make('country.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.address-information.entries.country')),
                                        TextEntry::make('state.name')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.address-information.entries.state')),
                                    ])
                                    ->columns(2),

                                Section::make(__('support::filament/resources/company.infolist.sections.additional-information.title'))
                                    ->schema([
                                        TextEntry::make('currency.full_name')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/company.infolist.sections.additional-information.entries.default-currency')),
                                        TextEntry::make('founded_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('support::filament/resources/company.infolist.sections.additional-information.entries.company-foundation-date')),
                                        IconEntry::make('is_active')
                                            ->label(__('support::filament/resources/company.infolist.sections.additional-information.entries.status'))
                                            ->boolean(),
                                        ...$customInfolistEntries,
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(2),

                        Group::make([
                            Section::make(__('support::filament/resources/company.infolist.sections.branding.title'))
                                ->schema([
                                    ImageEntry::make('partner.avatar')
                                        ->label(__('support::filament/resources/company.infolist.sections.branding.entries.company-logo'))
                                        ->circular()
                                        ->placeholder('—'),
                                    ColorEntry::make('color')
                                        ->placeholder('—')
                                        ->label(__('support::filament/resources/company.infolist.sections.branding.entries.color')),
                                ]),

                            Section::make(__('support::filament/resources/company.infolist.sections.contact-information.title'))
                                ->schema([
                                    TextEntry::make('phone')
                                        ->icon('heroicon-o-phone')
                                        ->placeholder('—')
                                        ->label(__('support::filament/resources/company.infolist.sections.contact-information.entries.phone')),
                                    TextEntry::make('mobile')
                                        ->icon('heroicon-o-device-phone-mobile')
                                        ->placeholder('—')
                                        ->label(__('support::filament/resources/company.infolist.sections.contact-information.entries.mobile')),
                                    TextEntry::make('email')
                                        ->icon('heroicon-o-envelope')
                                        ->placeholder('—')
                                        ->label(__('support::filament/resources/company.infolist.sections.contact-information.entries.email'))
                                        ->copyable()
                                        ->copyMessage('Email address copied')
                                        ->copyMessageDuration(1500),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
