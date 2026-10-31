<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;
use Webkul\Crm\Services\CustomerMerger;
use Webkul\Partner\Models\Partner;

class MergeCustomersBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'mergeCustomers';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/customer.actions.merge.label'))
            ->icon('heroicon-o-arrows-pointing-in')
            ->modalHeading(__('crm::filament/clusters/sales/resources/customer.actions.merge.heading'))
            ->modalDescription(__('crm::filament/clusters/sales/resources/customer.actions.merge.description'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/customer.actions.merge.submit'))
            ->deselectRecordsAfterCompletion()
            ->fillForm(fn (Collection $records): array => ['destination' => $records->sortBy('id')->first()?->getKey()])
            ->schema(fn (Collection $records): array => [
                Select::make('destination')
                    ->label(__('crm::filament/clusters/sales/resources/customer.actions.merge.destination'))
                    ->helperText(__('crm::filament/clusters/sales/resources/customer.actions.merge.destination-helper'))
                    ->options($records->mapWithKeys(fn (Partner $contact): array => [$contact->getKey() => $contact->name])->all())
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $contacts = $records->filter(fn (Partner $contact): bool => ! $contact->trashed() && CustomerResource::canEdit($contact));
                $destination = $contacts->firstWhere('id', (int) $data['destination']);

                if (! $destination
                    || $contacts->count() < 2
                    || $contacts->contains(fn (Partner $contact): bool => ! $contact->is($destination)
                    && ! CustomerResource::canDelete($contact))) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/clusters/sales/resources/customer.actions.merge.invalid'))
                        ->send();

                    return;
                }

                app(CustomerMerger::class)->merge($destination, $contacts);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/customer.actions.merge.notification', ['count' => $contacts->count() - 1, 'name' => $destination->name]))
                    ->send();
            });
    }
}
