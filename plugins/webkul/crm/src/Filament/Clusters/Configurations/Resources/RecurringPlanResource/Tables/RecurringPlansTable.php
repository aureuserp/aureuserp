<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Exports\RecurringPlanExporter;
use Webkul\Crm\Models\RecurringPlan;

class RecurringPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('leads'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number_of_months')
                    ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.table.columns.months'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leads_count')
                    ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.table.columns.opportunities'))
                    ->numeric(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (RecurringPlan $record): bool => $record->trashed())
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/recurring-plan.notifications.updated'))),
                RestoreAction::make()
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/recurring-plan.notifications.restored'))),
                DeleteAction::make(),
                ForceDeleteAction::make()
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/recurring-plan.notifications.deleted'))),
            ])
            ->toolbarActions([
                RecordBulkActions::make(exporter: RecurringPlanExporter::class, softDeletes: true),
            ]);
    }
}
