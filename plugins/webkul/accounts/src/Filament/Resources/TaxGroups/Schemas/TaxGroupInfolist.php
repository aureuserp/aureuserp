<?php

namespace Webkul\Account\Filament\Resources\TaxGroups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaxGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('company.name')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.company')),
                        TextEntry::make('country.name')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.country')),
                        TextEntry::make('name')
                            ->icon('heroicon-o-tag')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.name')),
                        TextEntry::make('preceding_subtotal')
                            ->icon('heroicon-o-rectangle-group')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.preceding-subtotal')),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }
}
