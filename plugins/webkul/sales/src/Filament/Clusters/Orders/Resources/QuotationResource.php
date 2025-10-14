<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Product\Models\Packaging;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageDeliveries;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Sale\Livewire\Summary;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Settings;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\Package;

class QuotationResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Orders::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->disabled()
                    ->live()
                    ->reactive(),
                Section::make(__('sales::filament/clusters/orders/resources/quotation.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.customer'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query
                                                    ->withTrashed()
                                                    ->where('sub_type', 'customer')
                                                    ->orderBy('id')
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL]))
                                            ->columnSpan(1)
                                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''))
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)')),
                                    ]),
                                DatePicker::make('validity_date')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.expiration'))
                                    ->native(false)
                                    ->default(fn (QuotationAndOrderSettings $settings) => now()->addDays($settings->default_quotation_validity))
                                    ->required()
                                    ->hidden(fn ($record) => $record)
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                                DatePicker::make('date_order')
                                    ->label(function ($record) {
                                        return $record?->state == OrderState::SALE
                                            ? __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.order-date')
                                            : __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.quotation-date');
                                    })
                                    ->default(now())
                                    ->native(false)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                                Select::make('payment_term_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.payment-term'))
                                    ->relationship('paymentTerm', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(PaymentTerm::find(10)?->id)
                                    ->columnSpan(1),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(Summary::class, function (Get $get, PriceSettings $settings) {
                                    return [
                                        'currency'     => Currency::find($get('currency_id')),
                                        'products'     => $get('products'),
                                        'enableMargin' => $settings->enable_margin,
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),
                        Tab::make(__('Optional Products'))
                            ->hidden(fn ($record) => in_array($record?->state, [OrderState::CANCEL]))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->schema(function (Set $set, Get $get) {
                                return [
                                    static::getOptionalProductRepeater($get, $set),
                                ];
                            }),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.title'))
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.sales-person')),
                                        TextInput::make('client_order_ref')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.customer-reference')),
                                        Select::make('sales_order_tags')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.title'))
                                    ->schema([
                                        DatePicker::make('commitment_date')
                                            ->disabled(fn ($record) => in_array($record?->state, [OrderState::CANCEL]))
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.fields.commitment-date'))
                                            ->native(false),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.title'))
                                    ->schema([
                                        TextInput::make('origin')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('campaign_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.campaign'))
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('medium_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.medium'))
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('utm_source_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source'))
                                            ->relationship('utmSource', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.company'))
                                            ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $company = $get('company_id') ? \Webkul\Support\Models\Company::find($get('company_id')) : null;

                                                if ($company) {
                                                    $set('currency_id', $company->currency_id);
                                                }
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(fn (callable $set, $state) => $set('currency_id', Company::find($state)?->currency_id))
                                            ->default(Auth::user()->default_company_id),
                                        Select::make('currency_id')
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
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('note')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.number'))
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.status'))
                    ->placeholder('-')
                    ->badge()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('invoice_status')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.invoice-status'))
                    ->placeholder('-')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.creation-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount_untaxed')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.untaxed-amount'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Total'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                TextColumn::make('amount_tax')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-tax'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Taxes'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                TextColumn::make('amount_total')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-total'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Total Amount'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                TextColumn::make('commitment_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.commitment-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('expected_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.expected-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-person'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('team.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-team'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('client_order_ref')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.customer-reference'))
                    ->placeholder('-')
                    ->badge()
                    ->searchable()
                    ->sortable(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('user.name')
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
                        RelationshipConstraint::make('utm_source_id.name')
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
                        RelationshipConstraint::make('company.name')
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
                        RelationshipConstraint::make('partner.name')
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
                        RelationshipConstraint::make('journal.name')
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
                        RelationshipConstraint::make('partnerInvoice.name')
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
                        RelationshipConstraint::make('partnerShipping.name')
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
                        RelationshipConstraint::make('fiscalPosition.name')
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
                        RelationshipConstraint::make('paymentTerm.name')
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
                        RelationshipConstraint::make('currency.name')
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
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.created-at')),
                        DateConstraint::make('updated_at')
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Model $record) => $record->state == OrderState::SALE)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.delete.notification.body'))
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.force-delete.notification.body'))
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== OrderState::SALE,
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency');
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('state')
                            ->badge(),
                    ])
                    ->compact(),
                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.sale-order'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.customer'))
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('validity_date')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.expiration'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                TextEntry::make('date_order')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.quotation-date'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                TextEntry::make('paymentTerm.name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.payment-term'))
                                    ->icon('heroicon-o-calendar-days'),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('product_uom_qty')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.uom'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('customer_lead')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.lead-time'))
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('product_packaging_qty')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging-qty'))
                                            ->icon('heroicon-o-arrow-path-rounded-square'),
                                        TextEntry::make('product_packaging_id')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging'))
                                            ->icon('heroicon-o-archive-box'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('purchase_price')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.cost'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('margin')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('margin_percent')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin-percentage'))
                                            ->icon('heroicon-o-chart-bar')
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                            ->suffix('%'),
                                        TextEntry::make('taxes.name')
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
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_discount)
                                            ->suffix('%'),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency->code),
                                    ])->columns(5),
                                Livewire::make(Summary::class, function ($record, PriceSettings $settings) {
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
                        Tab::make(__('Optional Products'))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->hidden(fn (Order $record) => $record->optionalLines->isEmpty())
                            ->schema([
                                RepeatableEntry::make('optionalLines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.uom'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_discount)
                                            ->suffix('%'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar'),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.sub-total'))
                                            ->icon('heroicon-o-calculator'),
                                    ])->columns(4),
                            ]),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.title'))
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('user.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.sales-person'))
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('client_order_ref')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.customer-reference'))
                                                    ->icon('heroicon-o-hashtag'),
                                                TextEntry::make('tags.name')
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
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.title'))
                                    ->icon('heroicon-o-truck')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('commitment_date')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.entries.commitment-date'))
                                                    ->icon('heroicon-o-calendar')
                                                    ->date(),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.title'))
                                    ->icon('heroicon-o-chart-bar')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('origin')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.source-document'))
                                                    ->icon('heroicon-o-document'),
                                                TextEntry::make('campaign.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.campaign'))
                                                    ->icon('heroicon-o-presentation-chart-line'),
                                                TextEntry::make('medium.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.medium'))
                                                    ->icon('heroicon-o-device-phone-mobile'),
                                                TextEntry::make('utmSource.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.source'))
                                                    ->icon('heroicon-o-link'),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.title'))
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.entries.currency'))
                                                    ->icon('heroicon-o-currency-dollar'),
                                            ])->columns(2),
                                    ]),
                            ]),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('note')
                                    ->html()
                                    ->placeholder('-')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getOptionalProductRepeater(Get $parentGet, Set $parentSet): Repeater
    {
        return Repeater::make('optionalProducts')
            ->relationship('optionalLines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(function ($state) {
                if (! empty($state['name'])) {
                    return $state['name'];
                }

                $product = Product::find($state['product_id']);

                return $product->name ?? null;
            })
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->table([
                TableColumn::make('product_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.product'))
                    ->width(250)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.description'))
                    ->width(250)
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('quantity')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.quantity'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('product_uom_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.uom'))
                    ->width(150)
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.unit-price'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.discount-percentage'))
                    ->width(150)
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn ($query) => $query->withTrashed()->where('is_configurable', null),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(true)
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($value, $state, $component, $label) {
                        if (str_contains($label, ' (Deleted)')) {
                            return true;
                        }

                        $repeater = $component->getParentRepeater();
                        if (! $repeater) {
                            return false;
                        }

                        return collect($repeater->getState())
                            ->pluck(
                                (string) str($component->getStatePath())
                                    ->after("{$repeater->getStatePath()}.")
                                    ->after('.'),
                            )
                            ->flatten()
                            ->diff(Arr::wrap($state))
                            ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                            ->contains($value);
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $product = Product::withTrashed()->find($get('product_id'));

                        $set('name', $product->name);

                        $set('price_unit', $product->price);
                    })
                    ->required(),
                TextInput::make('name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.description'))
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated(),
                TextInput::make('quantity')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated(),
                Select::make('product_uom_id')
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
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                TextInput::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated(),
                TextInput::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_discount)
                    ->dehydrated(),
                Actions::make([
                    Action::make('add_order_line')
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
            ]);
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OrderState::CANCEL]) && $record?->state !== OrderState::SALE)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::CANCEL]))
            ->columnManagerColumns(2)
            ->table(fn ($record) => [
                TableColumn::make('product_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.product'))
                    ->width(250)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('product_qty')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.quantity'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('qty_delivered')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.qty-delivered'))
                    ->width(150)
                    ->toggleable()
                    ->markAsRequired()
                    ->visible(fn () => in_array($record?->state, [OrderState::SALE])),
                TableColumn::make('qty_invoiced')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.qty-invoiced'))
                    ->width(150)
                    ->markAsRequired()
                    ->toggleable()
                    ->visible(fn () => in_array($record?->state, [OrderState::SALE])),
                TableColumn::make('product_uom_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.uom'))
                    ->width(150)
                    ->markAsRequired()
                    ->visible(fn () => resolve(ProductSettings::class)->enable_uom),
                TableColumn::make('customer_lead')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.lead-time'))
                    ->width(150)
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('product_packaging_qty')
                    ->toggleable()
                    ->width(180)
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.packaging-qty'))
                    ->visible(fn () => resolve(ProductSettings::class)->enable_packagings),
                TableColumn::make('product_packaging_id')
                    ->toggleable()
                    ->width(200)
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.packaging'))
                    ->visible(fn () => resolve(ProductSettings::class)->enable_packagings),
                TableColumn::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.unit-price'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('margin')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.margin'))
                    ->width(100)
                    ->toggleable()
                    ->visible(fn () => resolve(PriceSettings::class)->enable_margin),
                TableColumn::make('margin_percent')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.margin-percentage'))
                    ->width(100)
                    ->toggleable()
                    ->visible(fn () => resolve(PriceSettings::class)->enable_margin),
                TableColumn::make('taxes')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.taxes'))
                    ->width(250)
                    ->toggleable(),
                TableColumn::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.discount-percentage'))
                    ->width(250)
                    ->toggleable()
                    ->visible(fn () => resolve(Settings\PriceSettings::class)->enable_discount),
                TableColumn::make('price_subtotal')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.amount'))
                    ->width(100)
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(fn (ProductSettings $settings) => $settings->enable_variants ? __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-variants') : __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-simple'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->withTrashed()
                            ->whereNull('is_configurable'),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label, $record, $value, $state, $component) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        $isOrderLocked = $record && (
                            $record->order?->locked
                            || in_array($record?->order?->state, [OrderState::CANCEL])
                        );

                        $isDuplicate = false;
                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDeleted || $isOrderLocked || $isDuplicate;
                    })

                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(true)
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                    ->required()
                    ->selectablePlaceholder(false),
                TextInput::make('product_qty')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->afterStateHydrated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get))
                    ->readOnly(fn ($record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('qty_delivered')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-delivered'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->readOnly(fn ($record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])))
                    ->visible(fn ($record): bool => in_array($record?->order?->state, [OrderState::SALE])),
                TextInput::make('qty_invoiced')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-invoiced'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->readOnly()
                    ->visible(fn ($record): bool => in_array($record?->order?->state, [OrderState::SALE])),
                Select::make('product_uom_id')
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
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom)
                    ->disableOptionWhen(fn ($value, $record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('customer_lead')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.lead-time'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->readOnly(fn ($record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('product_packaging_qty')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging-qty'))
                    ->live(onBlur: true)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductPackagingQtyUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),
                Select::make('product_packaging_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging'))
                    ->relationship(
                        'productPackaging',
                        'name',
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductPackagingUpdated($set, $get))
                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings)
                    ->disableOptionWhen(fn ($value, $record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                    ->readOnly(fn ($record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('margin')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin'))
                    ->numeric()
                    ->default(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_margin)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                    ->readOnly(),
                TextInput::make('margin_percent')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin-percentage'))
                    ->numeric()
                    ->default(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_margin)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                    ->readOnly(),
                Select::make('taxes')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        fn (Builder $query) => $query->where('type_tax_use', TypeTaxUse::SALE->value),
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->live()
                    ->disableOptionWhen(fn ($value, $record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->visible(fn (Settings\PriceSettings $settings) => $settings->enable_discount)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                    ->readOnly(fn ($record): bool => $record && ($record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))),
                TextInput::make('price_subtotal')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.amount'))
                    ->default(0)
                    ->readOnly(),
                Hidden::make('product_uom_qty')
                    ->default(0),
                Hidden::make('price_tax')
                    ->default(0),
                Hidden::make('price_total')
                    ->default(0),
                Hidden::make('purchase_price')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.cost'))
                    ->default(0),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire));
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $product = Product::withTrashed()->find($data['product_id']);

        $qtyDeliveredMethod = QtyDeliveredMethod::MANUAL;

        if (Package::isPluginInstalled('inventories')) {
            $qtyDeliveredMethod = QtyDeliveredMethod::STOCK_MOVE;
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

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::withTrashed()->find($get('product_id'));

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

    private static function afterProductQtyUpdated(Set $set, Get $get): void
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

    private static function afterUOMUpdated(Set $set, Get $get): void
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

    private static function afterProductPackagingQtyUpdated(Set $set, Get $get): void
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

    private static function afterProductPackagingUpdated(Set $set, Get $get): void
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
        $product = Product::withTrashed()->find($get('product_id'));

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

    private static function calculateLineTotals(Set $set, Get $get, ?string $prefix = ''): void
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
            ViewQuotation::class,
            EditQuotation::class,
            ManageInvoices::class,
            ManageDeliveries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListQuotations::route('/'),
            'create'     => CreateQuotation::route('/create'),
            'view'       => ViewQuotation::route('/{record}'),
            'edit'       => EditQuotation::route('/{record}/edit'),
            'invoices'   => ManageInvoices::route('/{record}/invoices'),
            'deliveries' => ManageDeliveries::route('/{record}/deliveries'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
