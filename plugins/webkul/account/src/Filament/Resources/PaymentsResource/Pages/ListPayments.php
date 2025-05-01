<?php

namespace Webkul\Account\Filament\Resources\PaymentsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Filament\Resources\PaymentsResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPayments extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PaymentsResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'customer_payments' => PresetView::make(__('Customer Payments'))
                ->favorite()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('partner_type', 'customer')),
            'vendor_payments' => PresetView::make(__('Vendor Payments'))
                ->favorite()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('partner_type', 'supplier')),
            'draft' => PresetView::make(__('Draft'))
                ->favorite()
                ->default()
                ->icon('heroicon-s-stop')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', PaymentStatus::DRAFT->value)),
            'in_process' => PresetView::make(__('In Process'))
                ->favorite()
                ->default()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', PaymentStatus::IN_PROCESS->value)),
            'is_sent' => PresetView::make(__('Sent'))
                ->default()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_sent', true)),
            'not_sent' => PresetView::make(__('No Sent'))
                ->default()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_sent', false)),
            'no_bank_matching' => PresetView::make(__('No Bank Matching'))
                ->default()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_matched', false)),
            'is_reconciled' => PresetView::make(__('Reconciled'))
                ->default()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_reconciled', true)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
