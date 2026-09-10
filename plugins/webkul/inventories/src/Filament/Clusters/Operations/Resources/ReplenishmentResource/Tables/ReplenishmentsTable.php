<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;

class ReplenishmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([

            ])
            ->groups(
                collect([
                ])->filter(function ($group) {
                    return match ($group->getId()) {
                        default        => true
                    };
                })->all()
            )
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect([
                    ])->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/operations/resources/replenishment.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {

                        return $data;
                    })
                    ->before(function (array $data) {})
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/replenishment.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/replenishment.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
            ]);
    }
}
