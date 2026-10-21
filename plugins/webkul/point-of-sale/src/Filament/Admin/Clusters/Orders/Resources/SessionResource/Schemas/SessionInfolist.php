<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Models\Session;

class SessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->columnSpanFull()
                    ->options(fn (): array => collect([SessionState::OPENED, SessionState::CLOSING_CONTROL, SessionState::CLOSED])
                        ->mapWithKeys(fn (SessionState $state): array => [$state->value => $state->getLabel()])
                        ->all())
                    ->default(SessionState::OPENING_CONTROL->value),

                Section::make(__('point-of-sale::filament/admin/clusters/orders/resources/session.infolist.section.general.title'))
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.infolist.section.general.entries.session'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),

                        Grid::make()
                            ->schema([
                                TextEntry::make('user.name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.infolist.section.general.entries.opened-by'))
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('config.name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.infolist.section.general.entries.point-of-sale'))
                                    ->icon('heroicon-o-building-storefront'),
                                TextEntry::make('started_at')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.infolist.section.general.entries.opening-date'))
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime(),
                                TextEntry::make('cash_balance_start')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.infolist.section.general.entries.starting-balance'))
                                    ->icon('heroicon-o-banknotes')
                                    ->money(fn (Session $record): ?string => $record->currency?->name),
                            ])->columns(2),
                    ]),
            ]);
    }
}
