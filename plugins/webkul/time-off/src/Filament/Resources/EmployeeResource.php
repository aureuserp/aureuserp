<?php

namespace Webkul\TimeOff\Filament\Resources;

use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Employee\Filament\Resources\EmployeeResource as BaseEmployeeResource;

class EmployeeResource extends BaseEmployeeResource
{
    public static function table(Table $table): Table
    {
        $table = parent::table($table);
        return $table
            ->bulkActions(
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/employee.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                $record?->timeOffLeaves()?->delete();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/employee.table.bulk-actions.force-delete.notification.body'))
                        ),
                ])
            );
    }
}
