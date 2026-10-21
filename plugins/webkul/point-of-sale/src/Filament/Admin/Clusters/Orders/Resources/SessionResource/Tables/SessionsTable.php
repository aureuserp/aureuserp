<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\CashMovementAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\CloseSessionAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\ConfirmOpeningControlAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\OpenRescueSessionAction;
use Webkul\PointOfSale\Models\Session;
use Webkul\Support\Models\Company;

class SessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.name'))
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('config.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.config'))
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.user'))
                    ->placeholder('—'),
                TextColumn::make('started_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.started-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('stopped_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.stopped-at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('cash_balance_start')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.cash-balance-start'))
                    ->money(fn (Session $record): ?string => $record->currency?->name)
                    ->alignEnd()
                    ->summarize(Sum::make()->money(fn (): ?string => Company::first()?->currency?->name)),
                TextColumn::make('cash_balance_end_real')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.cash-balance-end-real'))
                    ->money(fn (Session $record): ?string => $record->currency?->name)
                    ->placeholder('—')
                    ->alignEnd()
                    ->summarize(Sum::make()->money(fn (): ?string => Company::first()?->currency?->name)),
                TextColumn::make('cash_balance_end')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.cash-balance-end'))
                    ->money(fn (Session $record): ?string => $record->currency?->name)
                    ->placeholder('—')
                    ->alignEnd()
                    ->summarize(Sum::make()->money(fn (): ?string => Company::first()?->currency?->name)),
                TextColumn::make('state')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.state'))
                    ->badge(),
                TextColumn::make('order_count')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.order-count'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_payments_amount')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.total-payments-amount'))
                    ->money(fn (Session $record): ?string => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cash_difference')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.cash-difference'))
                    ->money(fn (Session $record): ?string => $record->currency?->name)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_rescue')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.is-rescue'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_failed_operations')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.has-failed-operations'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.columns.company'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                TableGroup::make('config.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.groups.config')),
                TableGroup::make('state')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.groups.state')),
                TableGroup::make('started_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.groups.started-at'))
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.filters.state'))
                    ->options(SessionState::class)
                    ->native(false),
                SelectFilter::make('config_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/session.table.filters.config'))
                    ->relationship('config', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->recordTitleAttribute('name')
            ->recordActions([
                ViewAction::make(),
                ConfirmOpeningControlAction::make(),
                CashMovementAction::make(),
                CloseSessionAction::make(),
                OpenRescueSessionAction::make(),
            ]);
    }
}
