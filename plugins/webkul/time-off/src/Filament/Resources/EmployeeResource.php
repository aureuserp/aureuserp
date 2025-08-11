<?php

namespace Webkul\TimeOff\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Webkul\Employee\Filament\Resources\EmployeeResource as BaseEmployeeResource;

class EmployeeResource extends BaseEmployeeResource
{
    public static function getSlug(): string
    {
        return 'time-off/employees';
    }

    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        dd($table->getActions());

        return $table->actions([
            $table->getActions(),
        ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->outlined(),
                Tables\Actions\EditAction::make()
                    ->outlined(),
                Tables\Actions\RestoreAction::make()
                    ->outlined()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee.table.actions.restore.notification.title'))
                            ->body(__('employees::filament/resources/employee.table.actions.restore.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/employee.table.actions.delete.notification.body'))
                    ),
            ]);
    }
}
