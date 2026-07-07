<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\CancelAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\ConfirmAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\DoneAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\PlanAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\Print\PrintLabelsAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\Print\PrintMOAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\StartAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Actions\UnplanAction;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditManufacturingOrder extends EditRecord
{
    use HasRecordNavigationTabs, HasRepeaterColumnManager;

    protected ?bool $hasDatabaseTransactions = true;

    protected static string $resource = ManufacturingOrderResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->reference ?: __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/edit-manufacturing-order.title');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['destination_location_id'] = $data['final_location_id'] ?? $data['destination_location_id'] ?? null;

        return $data;
    }

    protected function configureAction(Action $action): void
    {
        if ($action instanceof ChatterAction) {
            $action
                ->record($this->getRecord())
                ->recordTitle($this->getRecordTitle());

            return;
        }

        parent::configureAction($action);
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->record($this->getRecord())
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            DoneAction::make('done'),
            ConfirmAction::make('confirm'),
            PlanAction::make('plan'),
            UnplanAction::make('unplan'),
            StartAction::make('start'),
            CancelAction::make('cancel'),
            ActionGroup::make([
                PrintMOAction::make(),
                PrintLabelsAction::make(),
            ])
                ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/edit-manufacturing-order.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->button(),
        ];
    }

    public function updateForm(): void
    {
        $this->fillForm();
    }
}
