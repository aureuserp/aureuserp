<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Services\ContactMerger;
use Webkul\Partner\Models\Partner;

class MergeContactsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'mergeContacts';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/contact.actions.merge.label'))
            ->icon('heroicon-o-arrows-pointing-in')
            ->modalHeading(__('crm::filament/clusters/sales/resources/contact.actions.merge.heading'))
            ->modalDescription(__('crm::filament/clusters/sales/resources/contact.actions.merge.description'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/contact.actions.merge.submit'))
            ->deselectRecordsAfterCompletion()
            ->fillForm(fn (Collection $records): array => ['destination' => $records->sortBy('id')->first()?->getKey()])
            ->schema(fn (Collection $records): array => [
                Select::make('destination')
                    ->label(__('crm::filament/clusters/sales/resources/contact.actions.merge.destination'))
                    ->helperText(__('crm::filament/clusters/sales/resources/contact.actions.merge.destination-helper'))
                    ->options($records->mapWithKeys(fn (Partner $contact): array => [$contact->getKey() => $contact->name])->all())
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $contacts = $records->filter(fn (Partner $contact): bool => ! $contact->trashed() && ContactResource::canEdit($contact));
                $destination = $contacts->firstWhere('id', (int) $data['destination']);

                if (! $destination || $contacts->count() < 2 || $contacts->contains(fn (Partner $contact): bool => ! $contact->is($destination) && ! ContactResource::canDelete($contact))) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/clusters/sales/resources/contact.actions.merge.invalid'))
                        ->send();

                    return;
                }

                app(ContactMerger::class)->merge($destination, $contacts);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/contact.actions.merge.notification', ['count' => $contacts->count() - 1, 'name' => $destination->name]))
                    ->send();
            });
    }
}
