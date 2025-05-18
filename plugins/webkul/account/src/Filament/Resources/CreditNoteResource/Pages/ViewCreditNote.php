<?php

namespace Webkul\Account\Filament\Resources\CreditNoteResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice as ViewRecord;

class ViewCreditNote extends ViewRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        $predefinedActions = parent::getHeaderActions();

        $predefinedActions = collect($predefinedActions)->filter(function ($action) {
            return ! in_array($action->getName(), [
                'customers.invoice.set-as-checked',
                'customers.invoice.credit-note',
            ]);
        })->map(function ($action) {
            if ($action->getName() == 'customers.invoice.preview') {
                return BaseActions\PreviewAction::make()
                    ->modalHeading(__('accounts::filament/resources/credit-note/pages/view-credit-note.header-actions.preview.modal-heading'))
                    ->setTemplate('accounts::credit-note/actions/preview.index');
            }

            return $action;
        })->toArray();

        return $predefinedActions;
    }
}
