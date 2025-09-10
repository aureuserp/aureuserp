<?php

namespace Webkul\Account\Filament\Resources\AccountTags\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AccountTagInfoList
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 2])
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        TextEntry::make('color')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.color'))
                            ->formatStateUsing(fn ($state) => "<span style='display:inline-block;width:15px;height:15px;background-color:{$state};border-radius:50%;'></span> ".$state)
                            ->html()
                            ->placeholder('—'),
                        TextEntry::make('applicability')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.applicability'))
                            ->placeholder('—'),
                        TextEntry::make('country.name')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.country'))
                            ->placeholder('—'),
                        IconEntry::make('tax_negate')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.tax-negate'))
                            ->boolean(),
                    ]),
            ]);
    }
}
