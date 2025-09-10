<?php

namespace Webkul\Security\Filament\Resources\Companies\RelationManagers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BranchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Branch Information')
                    ->tabs([
                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.company-name')),
                                        TextEntry::make('registration_number')
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.registration-number')),
                                        TextEntry::make('tax_id')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label('Tax ID'),
                                        TextEntry::make('color')
                                            ->icon('heroicon-o-swatch')
                                            ->placeholder('—')
                                            ->badge()
                                            ->color(fn ($record) => $record->color ?? 'gray')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.color')),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branding.title'))
                                    ->schema([
                                        ImageEntry::make('partner.avatar')
                                            ->hiddenLabel()
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branding.entries.branch-logo'))
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.title'))
                                    ->schema([
                                        TextEntry::make('address.street1')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.street1')),
                                        TextEntry::make('address.street2')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.street2')),
                                        TextEntry::make('address.city')
                                            ->icon('heroicon-o-building-library')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.city')),
                                        TextEntry::make('address.zip')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.zip-code')),
                                        TextEntry::make('address.country.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.country')),
                                        TextEntry::make('address.state.name')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.state')),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.title'))
                                    ->schema([
                                        TextEntry::make('currency.full_name')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.default-currency')),
                                        TextEntry::make('founded_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.company-foundation-date')),
                                        IconEntry::make('is_active')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.status'))
                                            ->boolean(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.title'))
                                    ->schema([
                                        TextEntry::make('phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.phone-number')),
                                        TextEntry::make('mobile')
                                            ->icon('heroicon-o-device-phone-mobile')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.mobile-number')),
                                        TextEntry::make('email')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->copyable()
                                            ->copyMessage('Email copied')
                                            ->copyMessageDuration(1500)
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.email-address')),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
