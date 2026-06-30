<?php

namespace Webkul\Purchase\Livewire\Customer;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Webkul\Purchase\Models\OrderLine;

class ListProducts extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderLine::query()->where('order_id', $this->record->id)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.products.columns.product')),
                TextColumn::make('product_qty')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.products.columns.quantity'))
                    ->formatStateUsing(fn (string $state): string => $state.' Units'),
                TextColumn::make('price_unit')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.products.columns.unit-price'))
                    ->money(fn (OrderLine $record) => $record->currency->code),
                TextColumn::make('taxes.name')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.products.columns.taxes'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('discount')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.products.columns.discount'))
                    ->suffix('%'),
                TextColumn::make('price_subtotal')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.products.columns.amount'))
                    ->money(fn (OrderLine $record) => $record->currency->code),
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('purchases::livewire.customer.account.clusters.order.pages.view-record.products');
    }
}
