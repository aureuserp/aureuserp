<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Exports\PartnerLevelExporter;
use Webkul\Crm\Models\PartnerLevel;

class PartnerLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('partners'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/partner-level.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner_weight')
                    ->label(__('crm::filament/clusters/configurations/resources/partner-level.table.columns.weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('partners_count')
                    ->label(__('crm::filament/clusters/configurations/resources/partner-level.table.columns.partners'))
                    ->numeric(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (PartnerLevel $record): bool => $record->trashed())
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/partner-level.notifications.updated'))),
                RestoreAction::make()
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/partner-level.notifications.restored'))),
                DeleteAction::make()
                    ->label(__('crm::filament/clusters/configurations/resources/partner-level.actions.archive'))
                    ->icon('heroicon-o-archive-box')
                    ->modalHeading(__('crm::filament/clusters/configurations/resources/partner-level.actions.archive-heading'))
                    ->modalDescription(__('crm::filament/clusters/configurations/resources/partner-level.actions.archive-description'))
                    ->modalSubmitActionLabel(__('crm::filament/clusters/configurations/resources/partner-level.actions.archive'))
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/partner-level.notifications.archived'))),
                ForceDeleteAction::make()
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/partner-level.notifications.deleted'))),
            ])
            ->toolbarActions([
                RecordBulkActions::make(exporter: PartnerLevelExporter::class, archivable: true),
            ]);
    }
}
