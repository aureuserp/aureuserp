<?php

namespace Webkul\Security\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Security\Enums\PermissionType;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('security::filament/resources/user.infolist.sections.general-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->extraAttributes([
                                                'style' => 'word-break: break-all;',
                                            ])
                                            ->label(__('security::filament/resources/user.infolist.sections.general-information.entries.name')),
                                        TextEntry::make('email')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.general-information.entries.email')),
                                        TextEntry::make('language')
                                            ->icon('heroicon-o-language')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.lang-and-status.entries.language')),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/user.infolist.sections.permissions.title'))
                                    ->schema([
                                        TextEntry::make('roles.name')
                                            ->icon('heroicon-o-key')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.permissions.entries.roles'))
                                            ->listWithLineBreaks()
                                            ->formatStateUsing(fn ($state) => ucfirst($state))
                                            ->bulleted(),
                                        TextEntry::make('teams.name')
                                            ->icon('heroicon-o-user-group')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.permissions.entries.teams'))
                                            ->listWithLineBreaks()
                                            ->bulleted(),
                                        TextEntry::make('resource_permission')
                                            ->icon(function ($record) {
                                                return [
                                                    PermissionType::GLOBAL->value     => 'heroicon-o-globe-alt',
                                                    PermissionType::INDIVIDUAL->value => 'heroicon-o-user',
                                                    PermissionType::GROUP->value      => 'heroicon-o-user-group',
                                                ][$record->resource_permission];
                                            })
                                            ->formatStateUsing(fn ($state) => PermissionType::options()[$state] ?? $state)
                                            ->placeholder('-')
                                            ->label(__('security::filament/resources/user.infolist.sections.permissions.entries.resource-permission')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(2),

                        Group::make([
                            Section::make(__('security::filament/resources/user.infolist.sections.avatar.title'))
                                ->schema([
                                    ImageEntry::make('partner.avatar')
                                        ->hiddenLabel()
                                        ->circular()
                                        ->placeholder('—'),
                                ]),

                            Section::make(__('security::filament/resources/user.infolist.sections.multi-company.title'))
                                ->schema([
                                    TextEntry::make('allowedCompanies.name')
                                        ->icon('heroicon-o-building-office')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/user.infolist.sections.multi-company.allowed-companies'))
                                        ->listWithLineBreaks()
                                        ->bulleted(),
                                    TextEntry::make('defaultCompany.name')
                                        ->icon('heroicon-o-building-office-2')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/user.infolist.sections.multi-company.default-company')),
                                ]),

                            Section::make(__('security::filament/resources/user.infolist.sections.lang-and-status.title'))
                                ->schema([
                                    IconEntry::make('is_active')
                                        ->label(__('security::filament/resources/user.infolist.sections.lang-and-status.entries.status'))
                                        ->boolean(),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ])
            ->columns(1);
    }
}
