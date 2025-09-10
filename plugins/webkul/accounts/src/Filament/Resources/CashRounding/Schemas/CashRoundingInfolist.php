<?php

namespace Webkul\Account\Filament\Resources\CashRounding\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;

class CashRoundingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.name'))
                                    ->icon('heroicon-o-document-text'),
                                TextEntry::make('rounding')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.rounding-precision'))
                                    ->icon('heroicon-o-calculator')
                                    ->numeric(
                                        decimalPlaces: 2,
                                        decimalSeparator: '.',
                                        thousandsSeparator: ','
                                    ),
                                TextEntry::make('strategy')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.rounding-strategy'))
                                    ->icon('heroicon-o-cog')
                                    ->formatStateUsing(fn (string $state): string => RoundingStrategy::options()[$state]),
                                TextEntry::make('rounding_method')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.rounding-method'))
                                    ->icon('heroicon-o-adjustments-horizontal')
                                    ->formatStateUsing(fn (string $state): string => RoundingMethod::options()[$state]),
                            ])->columns(2),
                    ])->columns(2),
            ]);
    }
}
