<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Product\Models\Packaging;
use Webkul\Sale\Enums;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;
use Webkul\Sale\Livewire\Summary;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Settings;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\Package;

class QuotationResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Orders::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
                        $options = Enums\OrderState::options();

                        if (
                            $record
                            && $record->state != Enums\OrderState::CANCEL->value
                        ) {
                            unset($options[Enums\OrderState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[Enums\OrderState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(Enums\OrderState::DRAFT->value)
                    ->disabled()
                    ->live()
                    ->reactive(),
                Forms\Components\Section::make(__('sales::filament/clusters/orders/resources/quotation.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Select::make('partner_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.customer'))
                                            ->relationship('partner', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [Enums\OrderState::CANCEL]))
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\DatePicker::make('validity_date')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.expiration'))
                                    ->native(false)
                                    ->default(fn (Settings\QuotationAndOrderSettings $settings) => now()->addDays($settings->default_quotation_validity))
                                    ->required()
                                    ->hidden(fn ($record) => $record)
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\DatePicker::make('date_order')
                                    ->label(function ($record) {
                                        return $record?->state == Enums\OrderState::SALE
                                            ? __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.order-date')
                                            : __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.quotation-date');
                                    })
                                    ->default(now())
                                    ->native(false)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\Select::make('payment_term_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.payment-term'))
                                    ->relationship('paymentTerm', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(PaymentTerm::find(10)?->id)
                                    ->columnSpan(1),
                            ])->columns(2),
                    ]),
                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getProductRepeater(),
                                Forms\Components\Livewire::make(Summary::class, function (Forms\Get $get, Settings\PriceSettings $settings) {
                                    return [
                                        'currency'     => Currency::find($get('currency_id')),
                                        'products'     => $get('products'),
                                        'enableMargin' => $settings->enable_margin,
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('Optional Products'))
                            ->hidden(fn ($record) => in_array($record?->state, [Enums\OrderState::CANCEL]))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->schema(function (Set $set, Get $get) {
                                return [
                                    static::getOptionalProductRepeater($get, $set),
                                ];
                            }),
                        Forms\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.title'))
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.sales-person')),
                                        Forms\Components\TextInput::make('client_order_ref')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.customer-reference')),
                                        Forms\Components\Select::make('sales_order_tags')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.title'))
                                    ->schema([
                                        Forms\Components\DatePicker::make('commitment_date')
                                            ->disabled(fn ($record) => in_array($record?->state, [Enums\OrderState::CANCEL]))
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.fields.commitment-date'))
                                            ->native(false),
                                    ]),
                                Forms\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('origin')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source-document'))
                                            ->maxLength(255),
                                        Forms\Components\Select::make('campaign_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.campaign'))
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('medium_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.medium'))
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('utm_source_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source'))
                                            ->relationship('utmSource', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Forms\Components\Select::make('company_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::user()->default_company_id),
                                        Forms\Components\Select::make('currency_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(Auth::user()->defaultCompany?->currency_id),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\RichEditor::make('note')
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
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_status')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.invoice-status'))
                    ->placeholder('-')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.creation-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_untaxed')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.untaxed-amount'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Total'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_tax')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-tax'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Taxes'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_total')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-total'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Total Amount'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                Tables\Columns\TextColumn::make('commitment_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.commitment-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.expected-date'))
                    ->placeholder('-')
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-team'))
                    ->placeholder('-')
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
                        ->hidden(fn (Model $record) => $record->state == Enums\OrderState::SALE)
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
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.body'))
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
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== Enums\OrderState::SALE,
            );
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('state')
                            ->badge(),
                    ])
                    ->compact(),
                Infolists\Components\Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\Grid::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.customer'))
                                    ->icon('heroicon-o-user'),
                                Infolists\Components\TextEntry::make('validity_date')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.expiration'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                Infolists\Components\TextEntry::make('date_order')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.quotation-date'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                Infolists\Components\TextEntry::make('paymentTerm.name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.payment-term'))
                                    ->icon('heroicon-o-calendar-days'),
                            ])->columns(2),
                    ]),
                Infolists\Components\Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('product.name')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        Infolists\Components\TextEntry::make('product_uom_qty')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        Infolists\Components\TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.uom'))
                                            ->icon('heroicon-o-scale'),
                                        Infolists\Components\TextEntry::make('customer_lead')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.lead-time'))
                                            ->icon('heroicon-o-clock'),
                                        Infolists\Components\TextEntry::make('product_packaging_qty')
                                            ->placeholder('-')
                                            ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging-qty'))
                                            ->icon('heroicon-o-arrow-path-rounded-square'),
                                        Infolists\Components\TextEntry::make('product_packaging_id')
                                            ->placeholder('-')
                                            ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging'))
                                            ->icon('heroicon-o-archive-box'),
                                        Infolists\Components\TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->money(fn ($record) => $record->currency->code),
                                        Infolists\Components\TextEntry::make('purchase_price')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.cost'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => $record->currency->code),
                                        Infolists\Components\TextEntry::make('margin')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_margin)
                                            ->money(fn ($record) => $record->currency->code),
                                        Infolists\Components\TextEntry::make('margin_percent')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin-percentage'))
                                            ->icon('heroicon-o-chart-bar')
                                            ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_margin)
                                            ->suffix('%'),
                                        Infolists\Components\TextEntry::make('taxes.name')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->icon('heroicon-o-receipt-percent')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.taxes'))
                                            ->weight(FontWeight::Bold),
                                        Infolists\Components\TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->suffix('%'),
                                        Infolists\Components\TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency->code),
                                    ])->columns(5),
                                Infolists\Components\Livewire::make(Summary::class, function ($record, Settings\PriceSettings $settings) {
                                    return [
                                        'currency'     => $record->currency,
                                        'enableMargin' => $settings->enable_margin,
                                        'products'     => $record->lines->map(function ($item) {
                                            return [
                                                ...$item->toArray(),
                                                'taxes' => $item->taxes->pluck('id')->toArray() ?? [],
                                            ];
                                        })->toArray(),
                                    ];
                                }),
                            ]),
                        Infolists\Components\Tabs\Tab::make(__('Optional Products'))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->hidden(fn (Order $record) => $record->optionalLines->isEmpty())
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('optionalLines')
                                    ->hiddenLabel()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('product.name')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        Infolists\Components\TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.uom'))
                                            ->icon('heroicon-o-scale'),
                                        Infolists\Components\TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        Infolists\Components\TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->suffix('%'),
                                        Infolists\Components\TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar'),
                                        Infolists\Components\TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.sub-total'))
                                            ->icon('heroicon-o-calculator'),
                                    ])->columns(4),
                            ]),
                        Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Infolists\Components\Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.title'))
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        Infolists\Components\Grid::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('user.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.sales-person'))
                                                    ->icon('heroicon-o-user'),
                                                Infolists\Components\TextEntry::make('client_order_ref')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.customer-reference'))
                                                    ->icon('heroicon-o-hashtag'),
                                                Infolists\Components\TextEntry::make('tags.name')
                                                    ->badge()
                                                    ->state(function ($record): array {
                                                        return $record->tags->map(fn ($tag) => [
                                                            'name' => $tag->name,
                                                        ])->toArray();
                                                    })
                                                    ->formatStateUsing(fn ($state) => $state['name'])
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.tags'))
                                                    ->icon('heroicon-o-tag'),
                                            ])->columns(2),
                                    ]),
                                Infolists\Components\Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.title'))
                                    ->icon('heroicon-o-truck')
                                    ->schema([
                                        Infolists\Components\Grid::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('commitment_date')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.entries.commitment-date'))
                                                    ->icon('heroicon-o-calendar')
                                                    ->date(),
                                            ])->columns(2),
                                    ]),
                                Infolists\Components\Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.title'))
                                    ->icon('heroicon-o-chart-bar')
                                    ->schema([
                                        Infolists\Components\Grid::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('origin')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.source-document'))
                                                    ->icon('heroicon-o-document'),
                                                Infolists\Components\TextEntry::make('campaign.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.campaign'))
                                                    ->icon('heroicon-o-presentation-chart-line'),
                                                Infolists\Components\TextEntry::make('medium.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.medium'))
                                                    ->icon('heroicon-o-device-phone-mobile'),
                                                Infolists\Components\TextEntry::make('utmSource.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.source'))
                                                    ->icon('heroicon-o-link'),
                                            ])->columns(2),
                                    ]),
                                Infolists\Components\Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.title'))
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Infolists\Components\Grid::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('company.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                Infolists\Components\TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.entries.currency'))
                                                    ->icon('heroicon-o-currency-dollar'),
                                            ])->columns(2),
                                    ]),
                            ]),
                        Infolists\Components\Tabs\Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Infolists\Components\TextEntry::make('note')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }

    public static function getOptionalProductRepeater(Get $parentGet, Set $parentSet): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('optionalProducts')
            ->relationship('optionalLines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn ($query) => $query->where('is_configurable', null),
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
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.description'))
                                    ->required()
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\Select::make('product_uom_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.uom'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->default(UOM::first()?->id)
                                    ->selectablePlaceholder(false)
                                    ->dehydrated()
                                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom),
                                Forms\Components\TextInput::make('price_unit')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('discount')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->dehydrated(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('add_order_line')
                                        ->tooltip(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.tooltip.add-order-line'))
                                        ->hiddenLabel()
                                        ->icon('heroicon-o-shopping-cart')
                                        ->action(function ($state, $livewire, $record) use ($parentSet, $parentGet) {
                                            $data = [
                                                'product_id'      => $state['product_id'],
                                                'product_qty'     => $state['quantity'],
                                                'price_unit'      => $state['price_unit'],
                                                'discount'        => $state['discount'],
                                                'name'            => $state['name'],
                                                'customer_lead'   => 0,
                                                'purchase_price'  => 0,
                                                'product_uom_qty' => 0,
                                            ];

                                            $parentSet('products', [
                                                ...$parentGet('products'),
                                                $data,
                                            ]);

                                            $user = Auth::user();

                                            $data['order_id'] = $livewire->record->id;
                                            $data['creator_id'] = $user->id;
                                            $data['company_id'] = $user?->default_company_id;
                                            $data['currency_id'] = $livewire->record->currency_id;
                                            $data['product_uom_id'] = $state['product_uom_id'];
                                            $orderLine = OrderLine::create($data);

                                            $record->line_id = $orderLine->id;

                                            $record->save();

                                            $livewire->refreshFormData(['products']);

                                            $products = collect($parentGet('products'))->values();

                                            $orderLineEntry = $products->first(fn ($product) => $product['id'] == $orderLine->id);

                                            $orderLine->update($orderLineEntry);

                                            Notification::make()
                                                ->success()
                                                ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-added.title'))
                                                ->body(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-added.body'))
                                                ->send();
                                        })
                                        ->extraAttributes([
                                            'style' => 'margin-top: 2rem;',
                                        ]),
                                ])->hidden(fn ($record) => ! $record ?? false),
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
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
            ->deletable(fn ($record): bool => ! in_array($record?->state, [Enums\OrderState::CANCEL]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [Enums\OrderState::CANCEL]))
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(fn (Settings\ProductSettings $settings) => $settings->enable_variants ? __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-variants') : __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-simple'))
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

                                            return $query->withTrashed()->where(function ($q) {
                                                $q->whereNull('parent_id')
                                                    ->orWhereNotNull('parent_id');
                                            });
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::afterProductUpdated($set, $get))
                                    ->required()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('product_qty')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->afterStateHydrated(fn (Forms\Set $set, Forms\Get $get) => static::afterProductQtyUpdated($set, $get))
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::afterProductQtyUpdated($set, $get))
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('qty_delivered')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-delivered'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL]))
                                    ->visible(fn ($record): bool => in_array($record?->order->state, [Enums\OrderState::SALE])),
                                Forms\Components\TextInput::make('qty_invoiced')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-invoiced'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->disabled(true)
                                    ->disabled()
                                    ->visible(fn ($record): bool => in_array($record?->order->state, [Enums\OrderState::SALE])),
                                Forms\Components\Select::make('product_uom_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.uom'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->default(UOM::first()?->id)
                                    ->selectablePlaceholder(false)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::afterUOMUpdated($set, $get))
                                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom)
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('customer_lead')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.lead-time'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('product_packaging_qty')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging-qty'))
                                    ->live()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->default(0)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::afterProductPackagingQtyUpdated($set, $get))
                                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings),
                                Forms\Components\Select::make('product_packaging_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging'))
                                    ->relationship(
                                        'productPackaging',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::afterProductPackagingUpdated($set, $get))
                                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings)
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('price_unit')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\Hidden::make('purchase_price')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.cost'))
                                    ->default(0),
                                Forms\Components\TextInput::make('margin')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin'))
                                    ->numeric()
                                    ->default(0)
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_margin)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(),
                                Forms\Components\TextInput::make('margin_percent')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_margin)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(),
                                Forms\Components\Select::make('taxes')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.taxes'))
                                    ->relationship(
                                        'taxes',
                                        'name',
                                        fn (Builder $query) => $query->where('type_tax_use', TypeTaxUse::SALE->value),
                                    )
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->afterStateHydrated(fn (Forms\Get $get, Forms\Set $set) => self::calculateLineTotals($set, $get))
                                    ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::calculateLineTotals($set, $get))
                                    ->live()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('discount')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
                                Forms\Components\TextInput::make('price_subtotal')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.amount'))
                                    ->default(0)
                                    ->readOnly()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [Enums\OrderState::CANCEL])),
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
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire));
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $product = Product::find($data['product_id']);

        $qtyDeliveredMethod = Enums\QtyDeliveredMethod::MANUAL;

        if (Package::isPluginInstalled('inventories')) {
            $qtyDeliveredMethod = Enums\QtyDeliveredMethod::STOCK_MOVE;
        }

        return [
            'name'                 => $product->name,
            'qty_delivered_method' => $qtyDeliveredMethod,
            'product_uom_id'       => $data['product_uom_id'] ?? $product->uom_id,
            'currency_id'          => $record->currency_id,
            'partner_id'           => $record->partner_id,
            'creator_id'           => Auth::id(),
            'company_id'           => Auth::user()->default_company_id,
            ...$data,
        ];
    }

    private static function afterProductUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('product_uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'));

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

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'));

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

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'));

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

            $uom = Uom::find($get('product_uom_id'));

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

        if (! $get('product_uom_id')) {
            return $vendorPrice;
        }

        $uom = Uom::find($get('product_uom_id'));

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
        if (! $get($prefix.'product_id')) {
            $set($prefix.'price_unit', 0);

            $set($prefix.'discount', 0);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_subtotal', 0);

            $set($prefix.'price_total', 0);

            $set($prefix.'purchase_price', 0);

            $set($prefix.'margin', 0);

            $set($prefix.'margin_percent', 0);

            return;
        }

        $priceUnit = floatval($get($prefix.'price_unit') ?? 0);

        $quantity = floatval($get($prefix.'product_qty') ?? 1);

        $purchasePrice = floatval($get($prefix.'purchase_price') ?? 0);

        $discountValue = floatval($get($prefix.'discount') ?? 0);

        $subTotal = $priceUnit * $quantity;

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal -= $discountAmount;
        }

        $taxIds = $get($prefix.'taxes') ?? [];

        [$subTotal, $taxAmount] = Tax::collect($taxIds, $subTotal, $quantity);

        $total = $subTotal + $taxAmount;

        $set($prefix.'price_subtotal', round($subTotal, 4));

        $set($prefix.'price_tax', round($taxAmount, 4));

        $set($prefix.'price_total', round($total, 4));

        [$margin, $marginPercentage] = static::calculateMargin($priceUnit, $purchasePrice, $quantity, $discountValue);

        $set($prefix.'margin', round($margin, 4));

        $set($prefix.'margin_percent', round($marginPercentage, 4));
    }

    public static function calculateMargin($sellingPrice, $costPrice, $quantity, $discount = 0)
    {
        $discountedPrice = $sellingPrice - ($sellingPrice * ($discount / 100));

        $marginPerUnit = $discountedPrice - $costPrice;

        $totalMargin = $marginPerUnit * $quantity;

        if ($marginPerUnit != 0) {
            $marginPercentage = ($marginPerUnit / $discountedPrice) * 100;
        } else {
            $marginPercentage = 0;
        }

        return [
            $totalMargin,
            $marginPercentage,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewQuotation::class,
            Pages\EditQuotation::class,
            Pages\ManageInvoices::class,
            Pages\ManageDeliveries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListQuotations::route('/'),
            'create'     => Pages\CreateQuotation::route('/create'),
            'view'       => Pages\ViewQuotation::route('/{record}'),
            'edit'       => Pages\EditQuotation::route('/{record}/edit'),
            'invoices'   => Pages\ManageInvoices::route('/{record}/invoices'),
            'deliveries' => Pages\ManageDeliveries::route('/{record}/deliveries'),
        ];
    }
}
