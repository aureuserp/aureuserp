<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Models\Location;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditLocation extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = LocationResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return DB::transaction(fn () => parent::handleRecordUpdate($record, $data));
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('inventories::filament/clusters/configurations/resources/location/pages/edit-location.notification.error.title'))
                ->body($e->getMessage())
                ->send();

            throw new Halt;
        }
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/location/pages/edit-location.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/location/pages/edit-location.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label(__('inventories::filament/clusters/configurations/resources/location/pages/edit-location.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (Location $record) {
                    $pdf = Pdf::loadView('inventories::filament.clusters.configurations.locations.actions.print', [
                        'records' => collect([$record]),
                    ]);

                    $pdf->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Location-'.$record->name.'.pdf');
                }),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/location/pages/edit-location.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/location/pages/edit-location.header-actions.delete.notification.body')),
                ),
        ];
    }
}
