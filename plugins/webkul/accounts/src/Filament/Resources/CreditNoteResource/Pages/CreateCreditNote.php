<?php

namespace Webkul\Account\Filament\Resources\CreditNoteResource\Pages;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice as CreateRecord;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/credit-note/pages/create-credit-note.notification.title'))
            ->body(__('accounts::filament/resources/credit-note/pages/create-credit-note.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['creator_id'] = $user->id;
        $data['state'] ??= Enums\MoveState::DRAFT;
        $data['move_type'] ??= Enums\MoveType::OUT_REFUND;
        $data['date'] = now();
        $data['sort'] = Move::max('sort') + 1;
        $data['payment_state'] = PaymentState::NOT_PAID;

        if ($data['partner_id']) {
            $partner = Partner::find($data['partner_id']);
            $data['commercial_partner_id'] = $partner->id;
            $data['partner_shipping_id'] = $partner->addresses->where('type', 'present')->first()?->id;
            $data['invoice_partner_display_name'] = $partner->name;
        } else {
            $data['invoice_partner_display_name'] = "#Created By: {$user->name}";
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        $record->invoice_date_due = CreditNoteResource::calculateDateMaturity($record)->format('Y-m-d');

        $record->save();

        $this->getResource()::collectTotals($record);
    }
}
