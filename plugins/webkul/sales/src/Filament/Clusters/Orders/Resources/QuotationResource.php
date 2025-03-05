<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Services\TaxService;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Packaging;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Settings;
use Webkul\Sale\Models\Product;
use Webkul\Support\Models\UOM;

class QuotationResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Orders::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if (
                            $record
                            && $record->state != OrderState::CANCEL->value
                        ) {
                            unset($options[OrderState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[OrderState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),
                Forms\Components\Section::make(__('accounts::filament/resources/invoice.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Select::make('partner_id')
                                            ->label(__('Customer'))
                                            ->relationship('partner', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('partner_address')
                                            ->hiddenLabel()
                                            ->visible(
                                                fn(Get $get) => Partner::with('addresses')->find($get('partner_id'))?->addresses->isNotEmpty()
                                            )
                                            ->content(function (Get $get) {
                                                $partner = Partner::with('addresses.state', 'addresses.country')->find($get('partner_id'));

                                                if (
                                                    ! $partner
                                                    || $partner->addresses->isEmpty()
                                                ) {
                                                    return null;
                                                }

                                                $address = $partner->addresses->first();

                                                return sprintf(
                                                    "%s\n%s%s\n%s, %s %s\n%s",
                                                    $address->name ?? '',
                                                    $address->street1 ?? '',
                                                    $address->street2 ? ', ' . $address->street2 : '',
                                                    $address->city ?? '',
                                                    $address->state ? $address->state->name : '',
                                                    $address->zip ?? '',
                                                    $address->country ? $address->country->name : ''
                                                );
                                            }),
                                    ]),
                                Forms\Components\DatePicker::make('validity_date')
                                    ->label(__('Expiration'))
                                    ->native(false)
                                    ->default(now())
                                    ->required(),
                                Forms\Components\DatePicker::make('date_order')
                                    ->label(__('Quotation Date'))
                                    ->default(now())
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Select::make('payment_term_id')
                                    ->label(__('Payment Term'))
                                    ->relationship('paymentTerm', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),
                            ])->columns(2),
                    ]),
                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make(__('Order Lines'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getProductRepeater(),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('Optional Products'))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->schema([
                                static::getOptionalProductRepeater(),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('accounts::filament/resources/invoice.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Fieldset::make(__('Sales'))
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('Salesperson')),
                                        Forms\Components\TextInput::make('client_order_ref')
                                            ->label(__('Customer Reference')),
                                        Forms\Components\Select::make('sales_order_tags')
                                            ->label(__('Tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Fieldset::make(__('Shipping'))
                                    ->schema([
                                        Forms\Components\DatePicker::make('commentcommitment_date')
                                            ->label(__('Delivery Date'))
                                            ->native(false),
                                    ]),
                                Forms\Components\Fieldset::make(__('Tracking'))
                                    ->schema([
                                        Forms\Components\TextInput::make('origin')
                                            ->label(__('Source Documen'))
                                            ->maxLength(255),
                                        Forms\Components\Select::make('campaign_id')
                                            ->label(__('Campaign'))
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('medium_id')
                                            ->label(__('Medium'))
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('utm_source_id')
                                            ->label(__('Source'))
                                            ->relationship('utmSource', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Fieldset::make(__('Additional Information'))
                                    ->schema([
                                        Forms\Components\Select::make('company_id')
                                            ->label(__('Company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::user()->default_company_id),
                                        Forms\Components\Select::make('currency_id')
                                            ->label(__('Currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(Auth::user()->defaultCompany?->currency_id),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('accounts::filament/resources/invoice.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.status'))
                    ->placeholder('-')
                    ->formatStateUsing(fn($state) => OrderState::options()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        OrderState::DRAFT->value  => 'gray',
                        OrderState::SENT->value   => 'primary',
                        OrderState::SALE->value   => 'success',
                        OrderState::CANCEL->value => 'danger',
                        default                   => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_status')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.invoice-status'))
                    ->placeholder('-')
                    ->formatStateUsing(fn($state) => InvoiceStatus::options()[$state] ?? $state)
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.creation-date'))
                    ->placeholder('-')
                    ->searchable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commitment_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.commitment-date'))
                    ->placeholder('-')
                    ->searchable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.expected-date'))
                    ->placeholder('-')
                    ->searchable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-person'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-team'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_untaxed')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.untaxed-amount'))
                    ->placeholder('-')
                    ->searchable()
                    ->summarize(Sum::make()->label('Total'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_tax')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-tax'))
                    ->placeholder('-')
                    ->searchable()
                    ->summarize(Sum::make()->label('Taxes'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_total')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-total'))
                    ->placeholder('-')
                    ->searchable()
                    ->summarize(Sum::make()->label('Total Amount'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_order_ref')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.customer-reference'))
                    ->placeholder('-')
                    ->badge()
                    ->searchable()
                    ->sortable(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('user.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.sales-person'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.sales-person'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('utm_source_id.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.utm-source'))
                            ->icon('heroicon-o-speaker-wave')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.utm-source'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.company'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.customer'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.customer'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('journal.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.journal'))
                            ->icon('heroicon-o-speaker-wave')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.journal'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partnerInvoice.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.invoice-address'))
                            ->icon('heroicon-o-map')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.invoice-address'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partnerShipping.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.shipping-address'))
                            ->icon('heroicon-o-map')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.shipping-address'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('fiscalPosition.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.fiscal-position'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.fiscal-position'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                                OrderState::DRAFT->value
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('paymentTerm.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.payment-term'))
                            ->icon('heroicon-o-currency-dollar')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.payment-term'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('currency.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.currency'))
                            ->icon('heroicon-o-banknotes')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.currency'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('medium.name')
                    ->label(__('Medium'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.medium'))
                    ->collapsible(),
                Tables\Grouping\Group::make('utmSource.name')
                    ->label(__('Source'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.source'))
                    ->collapsible(),
                Tables\Grouping\Group::make('team.name')
                    ->label(__('Team'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.team'))
                    ->collapsible(),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('Sales Person'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.full_name')
                    ->label(__('Currency'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('Company'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('Customer'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.customer'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_order')
                    ->label(__('Quotation Date'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.quotation-date'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('commitment_date')
                    ->label(__('Commitment Date'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.commitment-date'))
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             Infolists\Components\Grid::make(['default' => 3])
    //                 ->schema([
    //                     Infolists\Components\Group::make()
    //                         ->schema([
    //                             Infolists\Components\Tabs::make('Tabs')
    //                                 ->tabs([
    //                                     Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.title'))
    //                                         ->schema([
    //                                             Infolists\Components\RepeatableEntry::make('salesOrderLines')
    //                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.product'))
    //                                                 ->schema([
    //                                                     Infolists\Components\TextEntry::make('product.name')
    //                                                         ->icon('heroicon-o-shopping-bag')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.product')),
    //                                                     Infolists\Components\TextEntry::make('name')
    //                                                         ->icon('heroicon-o-document')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.name')),
    //                                                     Infolists\Components\RepeatableEntry::make('product.productTaxes')
    //                                                         ->contained(false)
    //                                                         ->hiddenLabel()
    //                                                         ->schema([
    //                                                             Infolists\Components\TextEntry::make('name')
    //                                                                 ->badge()
    //                                                                 ->tooltip(fn($state) => $state)
    //                                                                 ->icon('heroicon-o-receipt-percent')
    //                                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.taxes')),
    //                                                         ])
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.tax')),
    //                                                     Infolists\Components\TextEntry::make('product_uom_qty')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.quantity'))
    //                                                         ->numeric(),
    //                                                     Infolists\Components\TextEntry::make('price_unit')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.unit-price'))
    //                                                         ->icon('heroicon-o-banknotes')
    //                                                         ->money('USD'),
    //                                                     Infolists\Components\TextEntry::make('price_subtotal')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.subtotal'))
    //                                                         ->icon('heroicon-o-banknotes')
    //                                                         ->money('USD'),
    //                                                     Infolists\Components\TextEntry::make('price_total')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.total'))
    //                                                         ->icon('heroicon-o-banknotes')
    //                                                         ->money('USD'),
    //                                                 ])
    //                                                 ->columns(6),
    //                                             Infolists\Components\RepeatableEntry::make('salesOrderSectionLines')
    //                                                 ->hidden(fn($record) => $record->salesOrderSectionLines->isEmpty())
    //                                                 ->schema([
    //                                                     Infolists\Components\TextEntry::make('product.name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.product')),
    //                                                     Infolists\Components\TextEntry::make('name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.name')),
    //                                                     Infolists\Components\TextEntry::make('quantity')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.quantity'))
    //                                                         ->numeric(),
    //                                                 ])
    //                                                 ->columns(3),
    //                                             Infolists\Components\RepeatableEntry::make('salesOrderNoteLines')
    //                                                 ->hidden(fn($record) => $record->salesOrderNoteLines->isEmpty())
    //                                                 ->schema([
    //                                                     Infolists\Components\TextEntry::make('product.name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.product')),
    //                                                     Infolists\Components\TextEntry::make('name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.name')),
    //                                                     Infolists\Components\TextEntry::make('quantity')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.products.fields.quantity'))
    //                                                         ->numeric(),
    //                                                 ])
    //                                                 ->columns(3),
    //                                             // Infolists\Components\Livewire::make(Summary::class, function ($record) {
    //                                             //     return [
    //                                             //         'products' => $record->salesOrderLines->map(function ($item) {
    //                                             //             return [
    //                                             //                 ...$item->toArray(),
    //                                             //                 'tax' => $item?->product?->productTaxes->pluck('id')->toArray() ?? [],
    //                                             //             ];
    //                                             //         })->toArray(),
    //                                             //     ];
    //                                             // }),
    //                                         ]),
    //                                     Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.title'))
    //                                         ->schema([
    //                                             Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.title'))
    //                                                 ->schema([
    //                                                     Infolists\Components\Grid::make()
    //                                                         ->schema([
    //                                                             Infolists\Components\TextEntry::make('user.name')
    //                                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fields.sales-person'))
    //                                                                 ->placeholder('—')
    //                                                                 ->icon('heroicon-o-user'),
    //                                                             Infolists\Components\TextEntry::make('team.name')
    //                                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fields.sales-team'))
    //                                                                 ->placeholder('—')
    //                                                                 ->icon('heroicon-o-users'),
    //                                                             Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fieldset.signature-and-payment.title'))
    //                                                                 ->schema([
    //                                                                     Infolists\Components\IconEntry::make('require_signature')
    //                                                                         ->boolean()
    //                                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fieldset.signature-and-payment.fields.online-signature'))
    //                                                                         ->placeholder('—'),
    //                                                                     Infolists\Components\IconEntry::make('require_payment')
    //                                                                         ->boolean()
    //                                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fieldset.signature-and-payment.fields.online-payment'))
    //                                                                         ->placeholder('—'),
    //                                                                     Infolists\Components\IconEntry::make('prepayment_percentage')
    //                                                                         ->boolean()
    //                                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fieldset.signature-and-payment.fields.prepayment-percentage'))
    //                                                                         ->placeholder('—'),
    //                                                                 ]),
    //                                                             Infolists\Components\TextEntry::make('client_order_ref')
    //                                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.fields.customer-reference'))
    //                                                                 ->placeholder('—')
    //                                                                 ->icon('heroicon-o-document'),
    //                                                         ])->columns(2),
    //                                                 ]),
    //                                             Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.invoicing.title'))
    //                                                 ->schema([
    //                                                     Infolists\Components\TextEntry::make('fiscalPosition.name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.invoicing.fields.fiscal-position'))
    //                                                         ->placeholder('—')
    //                                                         ->icon('heroicon-o-receipt-percent'),
    //                                                     Infolists\Components\TextEntry::make('journal.name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.invoicing.fields.invoicing-journal'))
    //                                                         ->placeholder('—')
    //                                                         ->icon('heroicon-o-book-open'),
    //                                                 ]),
    //                                             Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.title'))
    //                                                 ->schema([
    //                                                     Infolists\Components\TextEntry::make('commitment_date')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.fields.commitment-date'))
    //                                                         ->placeholder('—')
    //                                                         ->icon('heroicon-o-calendar'),
    //                                                 ]),
    //                                             Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.title'))
    //                                                 ->schema([
    //                                                     Infolists\Components\TextEntry::make('origin')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.fields.source-document'))
    //                                                         ->placeholder('—')
    //                                                         ->icon('heroicon-o-globe-alt'),
    //                                                     Infolists\Components\TextEntry::make('medium.name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.fields.medium'))
    //                                                         ->placeholder('—'),
    //                                                     Infolists\Components\TextEntry::make('source.name')
    //                                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.fields.source'))
    //                                                         ->placeholder('—'),
    //                                                 ]),
    //                                         ]),
    //                                     Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.term-and-conditions.title'))
    //                                         ->schema([
    //                                             Infolists\Components\TextEntry::make('note')
    //                                                 ->markdown()
    //                                                 ->columnSpanFull()
    //                                                 ->icon('heroicon-o-information-circle'),
    //                                         ]),
    //                                 ])->persistTabInQueryString(),
    //                         ])->columnSpan(2),
    //                     Infolists\Components\Group::make()
    //                         ->schema([
    //                             Infolists\Components\Section::make()
    //                                 ->schema([
    //                                     Infolists\Components\TextEntry::make('name')
    //                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fields.customer'))
    //                                         ->placeholder('—')
    //                                         ->size(TextEntrySize::Large)
    //                                         ->icon('heroicon-o-identification'),
    //                                     Infolists\Components\TextEntry::make('partner.name')
    //                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fields.customer'))
    //                                         ->placeholder('—')
    //                                         ->icon('heroicon-o-user-circle'),
    //                                     Infolists\Components\TextEntry::make('partner_address')
    //                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fields.partner-address'))
    //                                         ->placeholder('—')
    //                                         ->icon('heroicon-o-map'),
    //                                     Infolists\Components\TextEntry::make('paymentTerm.name')
    //                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fields.payment-terms'))
    //                                         ->placeholder('—')
    //                                         ->icon('heroicon-o-credit-card'),
    //                                     Infolists\Components\TextEntry::make('quotationTemplate.name')
    //                                         ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fields.quotation-template'))
    //                                         ->placeholder('—')
    //                                         ->icon('heroicon-o-document-duplicate'),
    //                                 ]),
    //                             Infolists\Components\Section::make()
    //                                 ->schema([
    //                                     Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.fieldset.invoice-and-delivery-addresses.title'))
    //                                         ->schema([
    //                                             Infolists\Components\TextEntry::make('partnerInvoice.name')
    //                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fieldset.invoice-and-delivery-addresses.fields.invoice-address'))
    //                                                 ->placeholder('—')
    //                                                 ->icon('heroicon-o-home'),
    //                                             Infolists\Components\TextEntry::make('partnerShipping.name')
    //                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fieldset.invoice-and-delivery-addresses.fields.delivery-address'))
    //                                                 ->placeholder('—')
    //                                                 ->icon('heroicon-o-truck'),
    //                                         ]),
    //                                 ]),
    //                             Infolists\Components\Section::make()
    //                                 ->schema([
    //                                     Infolists\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.infolist.fieldset.expiration-and-quotation-date.title'))
    //                                         ->schema([
    //                                             Infolists\Components\TextEntry::make('validity_date')
    //                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fieldset.expiration-and-quotation-date.fields.expiration-date'))
    //                                                 ->date()
    //                                                 ->placeholder('—')
    //                                                 ->icon('heroicon-o-clock'),
    //                                             Infolists\Components\TextEntry::make('date_order')
    //                                                 ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.fieldset.expiration-and-quotation-date.fields.quotation-date'))
    //                                                 ->date()
    //                                                 ->placeholder('—')
    //                                                 ->icon('heroicon-o-calendar'),
    //                                         ]),
    //                                 ]),
    //                         ])
    //                         ->columnSpan(['lg' => 1]),
    //                 ]),
    //         ]);
    // }

    public static function getOptionalProductRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('optionalProducts')
            ->relationship('optionalLines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('Products'))
            ->addActionLabel(__('Add Product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn($query) => $query->where('is_configurable', null),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        $product = Product::find($get('product_id'));

                                        $set('name', $product->name);

                                        $set('price_unit', $product->price);
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Description'))
                                    ->required()
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\Select::make('uom_id')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->selectablePlaceholder(false)
                                    ->dehydrated()
                                    ->visible(fn(Settings\ProductSettings $settings) => $settings->enable_uom),
                                Forms\Components\TextInput::make('price_unit')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('discount')
                                    ->label(__('Discount Percentage'))
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getProductRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.title'))
            ->addActionLabel(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.add-product-line'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
            // ->deletable(fn($record): bool => ! in_array($record?->state, [Enums\OrderState::DONE, Enums\OrderState::CANCELED]))
            // ->addable(fn($record): bool => ! in_array($record?->state, [Enums\OrderState::DONE, Enums\OrderState::CANCELED]))
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        function ($query, Settings\ProductSettings $settings) {
                                            if (! $settings?->enable_variants) {
                                                return $query->whereNull('parent_id')
                                                    ->where(function ($q) {
                                                        $q->where('is_configurable', true)
                                                            ->orWhere(function ($subq) {
                                                                $subq->whereNull('is_configurable')
                                                                    ->orWhere('is_configurable', false);
                                                            });
                                                    });
                                            }

                                            return $query->where(function ($q) {
                                                $q->whereNull('parent_id')
                                                    ->orWhereNotNull('parent_id');
                                            });
                                        }
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        static::afterProductUpdated($set, $get);
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('product_qty')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        static::afterProductQtyUpdated($set, $get);
                                    }),
                                Forms\Components\Select::make('uom_id')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->selectablePlaceholder(false)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        static::afterUOMUpdated($set, $get);
                                    })
                                    ->visible(fn(Settings\ProductSettings $settings) => $settings->enable_uom),
                                Forms\Components\TextInput::make('customer_lead')
                                    ->label(__('Lead Time'))
                                    ->default(0)
                                    ->required(),
                                Forms\Components\TextInput::make('product_packaging_qty')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging-qty'))
                                    ->live()
                                    ->numeric()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        static::afterProductPackagingQtyUpdated($set, $get);
                                    })
                                    ->visible(fn(Settings\ProductSettings $settings) => $settings->enable_packagings),
                                Forms\Components\Select::make('product_packaging_id')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging'))
                                    ->relationship(
                                        'productPackaging',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        static::afterProductPackagingUpdated($set, $get);
                                    })
                                    ->visible(fn(Settings\ProductSettings $settings) => $settings->enable_packagings),
                                Forms\Components\TextInput::make('price_unit')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    }),
                                Forms\Components\TextInput::make('purchase_price')
                                    ->label(__('Cost'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    }),
                                Forms\Components\TextInput::make('margin')
                                    ->label(__('Margin'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->visible(fn(Settings\PriceSettings $settings) => $settings->enable_margin)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    }),
                                Forms\Components\TextInput::make('margin_percent')
                                    ->label(__('Margin(%)'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->visible(fn(Settings\PriceSettings $settings) => $settings->enable_margin)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    }),
                                Forms\Components\Select::make('taxes')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.taxes'))
                                    ->relationship(
                                        'taxes',
                                        'name',
                                        function (Builder $query) {
                                            return $query->where('type_tax_use', TypeTaxUse::SALE->value);
                                        },
                                    )
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        self::calculateLineTotals($set, $get);
                                    })
                                    ->live(),
                                Forms\Components\TextInput::make('discount')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    }),
                                Forms\Components\TextInput::make('price_subtotal')
                                    ->label(__('purchases::filament/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.amount'))
                                    ->default(0)
                                    ->readOnly(),
                                Forms\Components\Hidden::make('product_uom_qty')
                                    ->default(0),
                                Forms\Components\Hidden::make('price_tax')
                                    ->default(0),
                                Forms\Components\Hidden::make('price_total')
                                    ->default(0),
                            ]),
                    ])
                    ->columns(2),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn(array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire))
            ->mutateRelationshipDataBeforeSaveUsing(fn(array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire));
    }

    public static function mutateProductRelationship(array $data, $record, $livewire): array
    {
        $product = Product::find($data['product_id']);

        $data = [
            ...$data,
            'name'         => $product->name,
            'uom_id'       => $data['uom_id'] ?? $product->uom_id,
            'currency_id'  => $record->currency_id,
            'partner_id'   => $record->partner_id,
            'creator_id'   => Auth::id(),
            'company_id'   => Auth::user()->default_company_id,
        ];

        return $data;
    }

    private static function afterProductUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', $product->productTaxes->pluck('id')->toArray());

        $packaging = static::getBestPackaging($get('product_id'), round($uomQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $set('purchase_price', $product->cost ?? 0);

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        self::calculateLineTotals($set, $get);
    }

    private static function afterUOMUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingQtyUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $packagingQty = floatval($get('product_packaging_qty') ?? 0);

            $productUOMQty = $packagingQty * $packaging->qty;

            $set('product_uom_qty', round($productUOMQty, 2));

            $uom = Uom::find($get('uom_id'));

            $productQty = $uom ? $productUOMQty * $uom->factor : $productUOMQty;

            $set('product_qty', round($productQty, 2));
        }

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $productUOMQty = $get('product_uom_qty') ?: 1;

            if ($packaging) {
                $packagingQty = $productUOMQty / $packaging->qty;

                $set('product_packaging_qty', $packagingQty);
            }
        } else {
            $set('product_packaging_qty', null);
        }

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId) {
            return $quantity;
        }

        $uom = Uom::find($uomId);

        return (float) ($quantity ?? 0) / $uom->factor;
    }

    private static function calculateUnitPrice($get)
    {
        $product = Product::find($get('product_id'));

        $vendorPrices = $product->supplierInformation->sortByDesc('sort');

        if ($get('../../partner_id')) {
            $vendorPrices = $vendorPrices->where('partner_id', $get('../../partner_id'));
        }

        $vendorPrices = $vendorPrices->where('min_qty', '<=', $get('product_qty') ?? 1)->where('currency_id', $get('../../currency_id'));

        if (! $vendorPrices->isEmpty()) {
            $vendorPrice = $vendorPrices->first()->price;
        } else {
            $vendorPrice = $product->price ?? $product->cost;
        }

        if (! $get('uom_id')) {
            return $vendorPrice;
        }

        $uom = Uom::find($get('uom_id'));

        return (float) ($vendorPrice / $uom->factor);
    }

    private static function getBestPackaging($productId, $quantity)
    {
        $packagings = Packaging::where('product_id', $productId)
            ->orderByDesc('qty')
            ->get();

        foreach ($packagings as $packaging) {
            if ($quantity && $quantity % $packaging->qty == 0) {
                return [
                    'packaging_id'  => $packaging->id,
                    'packaging_qty' => round($quantity / $packaging->qty, 2),
                ];
            }
        }

        return null;
    }

    private static function calculateLineTotals(Forms\Set $set, Forms\Get $get, ?string $prefix = ''): void
    {
        if (!$get($prefix . 'product_id')) {
            $set($prefix . 'price_unit', 0);

            $set($prefix . 'discount', 0);

            $set($prefix . 'price_tax', 0);

            $set($prefix . 'price_subtotal', 0);

            $set($prefix . 'price_total', 0);

            $set($prefix . 'purchase_price', 0);

            $set($prefix . 'margin', 0);

            $set($prefix . 'margin_percent', 0);

            return;
        }

        $priceUnit = floatval($get($prefix . 'price_unit') ?? 0);

        $quantity = floatval($get($prefix . 'product_qty') ?? 1);

        $purchasePrice = floatval($get($prefix . 'purchase_price') ?? 0);

        $discountValue = floatval($get($prefix . 'discount') ?? 0);

        $subTotal = $priceUnit * $quantity;

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal -= $discountAmount;
        }

        $taxIds = $get($prefix . 'taxes') ?? [];

        [$subTotal, $taxAmount] = app(TaxService::class)->collectionTaxes($taxIds, $subTotal, $quantity);

        $total = $subTotal + $taxAmount;

        $set($prefix . 'price_subtotal', round($subTotal, 4));

        $set($prefix . 'price_tax', round($taxAmount, 4));

        $set($prefix . 'price_total', round($total, 4));

        [$margin, $marginPercentage] = static::calculateMargin($priceUnit, $purchasePrice, $quantity, $discountValue);

        $set($prefix . 'margin', round($margin, 4));

        $set($prefix . 'margin_percent', round($marginPercentage, 4));
    }

    public static function calculateMargin($sellingPrice, $costPrice, $quantity, $discount = 0)
    {
        $discountedPrice = $sellingPrice - ($sellingPrice * ($discount / 100));

        $marginPerUnit = $discountedPrice - $costPrice;

        $totalMargin = $marginPerUnit * $quantity;

        $marginPercentage = ($marginPerUnit / $discountedPrice) * 100;

        return [
            $totalMargin,
            $marginPercentage
        ];
    }

    public static function collectTotals(Order $record): void
    {
        $record->untaxed_amount = 0;
        $record->tax_amount = 0;
        $record->total_amount = 0;
        $record->total_cc_amount = 0;
        $record->invoice_count = 0;

        foreach ($record->lines as $line) {
            $line = static::collectLineTotals($line);

            $record->untaxed_amount += $line->price_subtotal;
            $record->tax_amount += $line->price_tax;
            $record->total_amount += $line->price_total;
            $record->total_cc_amount += $line->price_total;
        }

        $record->invoice_count = $record->accountMoves->count();

        if ($record->qty_to_invoice != 0) {
            $record->invoice_status = InvoiceStatus::TO_INVOICE;
        } else {
            if ($record->invoice_count) {
                $record->invoice_status = InvoiceStatus::INVOICED;
            } else {
                $record->invoice_status = InvoiceStatus::NO;
            }
        }

        $record->save();
    }

    public static function collectLineTotals(OrderLine $line): OrderLine
    {
        $line->qty_received_manual = $line->qty_received ?? 0;

        $line->qty_to_invoice = $line->qty_received - $line->qty_invoiced;

        $subTotal = $line->price_unit * $line->product_qty;

        $discountAmount = 0;

        if ($line->discount > 0) {
            $discountAmount = $subTotal * ($line->discount / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $line->taxes->pluck('id')->toArray();

        [$subTotal, $taxAmount] = app(TaxService::class)->collectionTaxes($taxIds, $subTotal, $line->product_qty);

        $line->price_subtotal = round($subTotal, 4);

        $line->price_tax = $taxAmount;

        $line->price_total = $subTotal + $taxAmount;

        $line->save();

        return $line;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'view'   => Pages\ViewQuotation::route('/{record}'),
            'edit'   => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
