<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageOperations extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = WorkCenterResource::class;

    protected static string $relationship = 'operations';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/work-center/pages/manage-operations.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return (string) Livewire::current()->getRecord()->operations()->count();
    }

    public function form(Schema $schema): Schema
    {
        return OperationResource::form($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/manage-operations.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->fillForm(fn (): array => [
                    'work_center_id'    => $this->getRecord()->getKey(),
                    'worksheet_type'    => OperationWorksheetType::TEXT->value,
                    'time_mode'         => OperationTimeMode::MANUAL->value,
                    'time_mode_batch'   => 10,
                    'manual_cycle_time' => '60:00',
                ])
                ->mutateDataUsing(function (array $data): array {
                    $data['work_center_id'] = $this->getRecord()->getKey();

                    return $data;
                })
                ->modalWidth(Width::SevenExtraLarge)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/manage-operations.header-actions.create.notification.title'))
                        ->body(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/manage-operations.header-actions.create.notification.body')),
                ),
        ];
    }

    public function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->modifyQueryUsing(fn ($query) => $query->where('work_center_id', $this->getRecord()->getKey()))
            ->toolbarActions([]);
    }
}
