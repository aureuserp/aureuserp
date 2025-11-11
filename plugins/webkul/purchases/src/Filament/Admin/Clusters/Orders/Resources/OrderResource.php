<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\IncoTermResource;
use Webkul\Account\Models\Partner;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Packaging;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Livewire\Summary;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\Product;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Purchase\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\Package;

class OrderResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if ($record && $record->state !== OrderState::CANCELED) {
                            unset($options[OrderState::CANCELED->value]);
                        }

                        if ($record && $record->state !== OrderState::DONE) {
                            unset($options[OrderState::DONE->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->withTrashed()
                                            ->where('sub_type', 'supplier')
                                            ->orderBy('id')
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => VendorResource::form($schema))
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if ($state) {
                                            $vendor = Partner::find($state);

                                            $set('payment_term_id', $vendor->property_supplier_payment_term_id);

                                            $products = $get('products');
                                            if (is_array($products)) {
                                                foreach ($products as $key => $product) {
                                                    if (isset($product['product_id'])) {
                                                        $productModel = Product::find($product['product_id']);
                                                        if ($productModel) {
                                                            $vendorPrices = $productModel->supplierInformation
                                                                ->where('partner_id', $state)
                                                                ->where('currency_id', $get('currency_id'))
                                                                ->where('min_qty', '<=', $product['product_qty'] ?? 1)
                                                                ->sortByDesc('sort');

                                                            if ($vendorPrices->isNotEmpty()) {
                                                                $vendorPrice = $vendorPrices->first()->price;
                                                            } else {
                                                                $vendorPrice = $productModel->cost ?? $productModel->price;
                                                            }

                                                            $set("products.$key.price_unit", round($vendorPrice, 2));

                                                            self::calculateLineTotals($set, $get, "products.$key.");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    })
                                    ->live()
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                TextInput::make('partner_reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference-tooltip')),
                                Select::make('requisition_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.agreement'))
                                    ->relationship('requisition', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->visible(static::getOrderSettings()->enable_purchase_agreements)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (! $state) {
                                            $set('products', []);

                                            return;
                                        }

                                        $requisition = Requisition::find($state);
                                        if (! $requisition) {
                                            $set('products', []);

                                            return;
                                        }

                                        $products = [];
                                        foreach ($requisition->lines as $line) {
                                            $product = $line->product;
                                            $uom = $line->uom;

                                            $products[] = [
                                                'product_id'  => $product?->id,
                                                'uom_id'      => $uom?->id,
                                                'product_qty' => $line->qty,
                                                'price_unit'  => $line->price_unit,
                                                'planned_at'  => now(),
                                                'taxes'       => $product->productTaxes->pluck('id')->toArray(),
                                                'discount'    => 0
                                            ];
                                        }
                                        $set('products', $products);

                                        foreach (array_keys($products) as $key) {
                                            self::calculateLineTotals($set, $get, "products.$key.");
                                        }
                                    })
                                    ->live(),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.currency'))
                                    ->relationship('currency', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->defaultCompany?->currency_id)
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                            ]),

                        Group::make()
                            ->schema([
                                DateTimePicker::make('approved_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmation-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->disabled()
                                    ->visible(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('ordered_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.order-deadline'))
                                    ->native(false)
                                    ->required()
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->hidden(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('planned_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.expected-arrival'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->hint('Test')
                                    ->hint(fn ($record): string => $record && $record->mail_reminder_confirmed ? __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmed-by-vendor') : '')
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.title'))
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(Summary::class, function (Get $get) {
                                    return [
                                        'currency' => Currency::find($get('currency_id')),
                                        'products' => $get('products'),
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.title'))
                            ->schema(static::mergeCustomFormFields([
                                Group::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.buyer'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::id())
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        Select::make('company_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.company'))
                                            ->relationship(
                                                'company',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(Auth::user()->default_company_id)
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                        TextInput::make('reference')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('incoterm_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm'))
                                            ->relationship('incoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema) => IncoTermResource::form($schema))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-tooltip'))
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        TextInput::make('reference')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-location'))
                                            ->maxLength(255)
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Select::make('payment_term_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.payment-term'))
                                            ->relationship('paymentTerm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),
                            ]))
                            ->columns(2),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
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
            ->columns(static::mergeCustomTableColumns([
                IconColumn::make('priority')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.favorite'))
                    ->icon(fn (Order $record): string => $record->priority ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Order $record): string => $record->priority ? 'warning' : 'gray')
                    ->action(function (Order $record): void {
                        $record->update([
                            'priority' => ! $record->priority,
                        ]);
                    }),
                TextColumn::make('partner_reference')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.vendor-reference'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.reference'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.vendor'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('company.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.company'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.buyer'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('ordered_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.order-deadline'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('origin')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.source-document'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('untaxed_amount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.untaxed-amount'))
                    ->sortable()
                    ->money(fn (Order $record) => $record->currency?->name)
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.total-amount'))
                    ->sortable()
                    ->money(fn (Order $record) => $record->currency?->name)
                    ->toggleable(),
                TextColumn::make('invoice_status')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.billing-status'))
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                TextColumn::make('state')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.status'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]))
            ->groups([
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.groups.vendor')),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.groups.buyer')),
                Tables\Grouping\Group::make('state')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.groups.state')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        SelectConstraint::make('state')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.status'))
                            ->multiple()
                            ->options(OrderState::class)
                            ->icon('heroicon-o-bars-2'),
                        TextConstraint::make('partner_reference')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.vendor-reference'))
                            ->icon('heroicon-o-identification'),
                        TextConstraint::make('name')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.reference'))
                            ->icon('heroicon-o-identification'),
                        NumberConstraint::make('untaxed_amount')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.untaxed-amount')),
                        NumberConstraint::make('total_amount')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.total-amount')),
                        RelationshipConstraint::make('partner')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.vendor'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.buyer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('company')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('paymentTerm')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.payment-term'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-currency-dollar'),
                        RelationshipConstraint::make('incoterm')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.incoterm'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-globe-alt'),
                        DateConstraint::make('ordered_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.order-deadline')),
                        DateConstraint::make('created_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.filters.updated-at')),
                    ]))->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Model $record) => $record->state == OrderState::DONE)
                        ->action(function (Model $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('purchases::filament/admin/clusters/orders/resources/order.table.actions.delete.notification.error.title'))
                                    ->body(__('purchases::filament/admin/clusters/orders/resources/order.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/order.table.actions.delete.notification.success.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/order.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->action(function (Collection $records) {
                        try {
                            $records->each(fn (Model $record) => $record->delete());
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/order.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/order.table.bulk-actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('purchases::filament/admin/clusters/orders/resources/order.table.bulk-actions.delete.notification.success.title'))
                            ->body(__('purchases::filament/admin/clusters/orders/resources/order.table.bulk-actions.delete.notification.success.body')),
                    ),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== OrderState::DONE,
            )
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('currency');
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

                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.title'))
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.purchase-order'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('partner.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.vendor'))
                                        ->icon('heroicon-o-user-group'),
                                    TextEntry::make('partner_reference')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.vendor-reference'))
                                        ->icon('heroicon-o-document-text')
                                        ->placeholder('—'),
                                    TextEntry::make('requisition.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.agreement'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-document-check')
                                        ->visible(static::getOrderSettings()->enable_purchase_agreements),
                                    TextEntry::make('currency.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.currency'))
                                        ->icon('heroicon-o-currency-dollar'),
                                ]),

                                Group::make([
                                    TextEntry::make('approved_at')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.confirmation-date'))
                                        ->dateTime()
                                        ->icon('heroicon-o-calendar')
                                        ->visible(fn ($record): bool => ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                    TextEntry::make('ordered_at')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.order-deadline'))
                                        ->dateTime()
                                        ->icon('heroicon-o-calendar')
                                        ->hidden(fn ($record): bool => ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                    TextEntry::make('planned_at')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.expected-arrival'))
                                        ->dateTime()
                                        ->icon('heroicon-o-calendar')
                                        ->hintColor('success')
                                        ->hint(fn ($record): string => $record->mail_reminder_confirmed ? __('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.confirmed-by-vendor') : ''),
                                ]),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.title'))
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->columnManagerColumns(2)
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.title'))
                                    ->table([
                                        InfolistTableColumn::make('name')
                                            ->alignStart()
                                            ->toggleable()
                                            ->width(250)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.product')),
                                        InfolistTableColumn::make('planned_at')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.expected-arrival')),
                                        InfolistTableColumn::make('product_qty')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.quantity')),
                                        InfolistTableColumn::make('qty_received')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.received')),
                                        InfolistTableColumn::make('qty_invoiced')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.billed')),
                                        InfolistTableColumn::make('uom')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->visible(static::getProductSettings()->enable_uom)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.unit')),
                                        InfolistTableColumn::make('product_packaging_qty')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->visible(static::getProductSettings()->enable_packagings)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.packaging-qty')),
                                        InfolistTableColumn::make('productPackaging')
                                            ->alignStart()
                                            ->width(200)
                                            ->toggleable()
                                            ->visible(static::getProductSettings()->enable_packagings)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.packaging')),
                                        InfolistTableColumn::make('price_unit')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.unit-price')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.taxes')),
                                        InfolistTableColumn::make('discount')
                                            ->alignStart()
                                            ->width(120)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.discount-percentage')),
                                        InfolistTableColumn::make('price_subtotal')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.amount')),
                                    ])
                                    ->schema([

                                        TextEntry::make('name'),
                                        TextEntry::make('planned_at')
                                            ->date(),
                                        TextEntry::make('product_qty'),
                                        TextEntry::make('qty_received')
                                            ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE])),
                                        TextEntry::make('qty_invoiced')
                                            ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE])),
                                        TextEntry::make('uom')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->visible(static::getProductSettings()->enable_uom),
                                        TextEntry::make('product_packaging_qty')
                                            ->visible(static::getProductSettings()->enable_packagings),
                                        TextEntry::make('productPackaging')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->visible(static::getProductSettings()->enable_packagings),
                                        TextEntry::make('price_unit')
                                            ->money(fn ($record) => $record->order->currency->code),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-'),
                                        TextEntry::make('discount')
                                            ->suffix('%'),
                                        TextEntry::make('price_subtotal')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.amount'))
                                            ->money(fn ($record) => $record->order->currency->code),
                                    ]),
                                Group::make([
                                    TextEntry::make('untaxed_amount')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.untaxed-amount'))
                                        ->money(fn (Order $record) => $record->currency?->name),
                                    TextEntry::make('tax_amount')
                                        ->label('Tax Amount')
                                        ->money(fn (Order $record) => $record->currency?->name),
                                    TextEntry::make('total_amount')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.total-amount'))
                                        ->money(fn (Order $record) => $record->currency?->name),
                                    TextEntry::make('invoice_status')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.billing-status'))
                                        ->badge(),
                                ])
                                    ->columnSpanFull()
                                    ->columns(4),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Group::make([
                                            TextEntry::make('user.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.buyer'))
                                                ->placeholder('—'),
                                            TextEntry::make('company.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.company'))
                                                ->placeholder('—'),
                                            TextEntry::make('reference')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.source-document'))
                                                ->placeholder('—'),
                                            TextEntry::make('incoterm.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.incoterm'))
                                                ->icon('heroicon-o-question-mark-circle')
                                                ->placeholder('—')
                                                ->tooltip(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.incoterm-tooltip')),
                                        ]),

                                        Group::make([
                                            TextEntry::make('paymentTerm.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.payment-term'))
                                                ->placeholder('—'),
                                        ]),
                                    ]),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.terms.title'))
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->compact()
            ->reactive()
            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.title'))
            ->addActionLabel(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.add-product-line'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->columnManagerColumns(2)
            ->table(fn ($record) => [
                TableColumn::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.product'))
                    ->width(300)
                    ->markAsRequired(),
                TableColumn::make('planned_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.expected-arrival'))
                    ->width(250)
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('product_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.quantity'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('qty_received')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.received'))
                    ->width(150)
                    ->markAsRequired()
                    ->visible(fn ($record): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),
                TableColumn::make('qty_invoiced')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.billed'))
                    ->width(150)
                    ->visible(fn ($record): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),
                TableColumn::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.unit'))
                    ->width(150)
                    ->markAsRequired()
                    ->visible(static::getProductSettings()->enable_uom)
                    ->toggleable(),
                TableColumn::make('product_packaging_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.packaging-qty'))
                    ->width(150)
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->toggleable(),
                TableColumn::make('product_packaging_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.packaging'))
                    ->width(250)
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->toggleable(),
                TableColumn::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.unit-price'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('taxes')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.taxes'))
                    ->width(250)
                    ->toggleable(),
                TableColumn::make('discount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.discount-percentage'))
                    ->width(150)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('price_subtotal')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.amount'))
                    ->width(150),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn ($query) => $query->where('type', ProductType::GOODS)->withTrashed()->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
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
                        static::afterProductUpdated($set, $get);
                    })
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::SENT, OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED])),
                DateTimePicker::make('planned_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.expected-arrival'))
                    ->native(false)
                    ->suffixIcon('heroicon-o-calendar')
                    ->required()
                    ->default(now())
                    ->default(function (Get $get, Set $set) {
                        if (empty($get('../../planned_at'))) {
                            $set('../../planned_at', now());
                        }

                        return now();
                    })
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $set('../../planned_at', $state);
                    })
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                TextInput::make('product_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductQtyUpdated($set, $get);
                    })
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                TextInput::make('qty_received')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.received'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED]) || $record?->qty_received_method == QtyReceivedMethod::STOCK_MOVE),
                TextInput::make('qty_invoiced')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.billed'))
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(),
                Select::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                    )
                    ->required()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterUOMUpdated($set, $get);
                    })
                    ->visible(static::getProductSettings()->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED])),
                TextInput::make('product_packaging_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging-qty'))
                    ->live(onBlur: true)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductPackagingQtyUpdated($set, $get);
                    })
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                Select::make('product_packaging_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging'))
                    ->relationship(
                        'productPackaging',
                        'name',
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductPackagingUpdated($set, $get);
                    })
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                Select::make('taxes')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        function (Builder $query) {
                            return $query->where('type_tax_use', TypeTaxUse::PURCHASE->value);
                        },
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->live()
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                TextInput::make('discount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                TextInput::make('price_subtotal')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.amount'))
                    ->default(0)
                    ->readOnly()
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                Hidden::make('product_uom_qty')
                    ->default(0),
                Hidden::make('price_tax')
                    ->default(0),
                Hidden::make('price_total')
                    ->default(0),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                $product = Product::find($data['product_id']);

                $qtyReceivedMethod = QtyReceivedMethod::MANUAL;

                if (Package::isPluginInstalled('inventories')) {
                    $qtyReceivedMethod = QtyReceivedMethod::STOCK_MOVE;
                }

                $data = array_merge($data, [
                    'name'                => $product->name,
                    'state'               => $record->state->value,
                    'qty_received_method' => $qtyReceivedMethod,
                    'uom_id'              => $data['uom_id'] ?? $product->uom_id,
                    'currency_id'         => $record->currency_id,
                    'partner_id'          => $record->partner_id,
                    'creator_id'          => Auth::id(),
                    'company_id'          => Auth::user()->default_company_id,
                ]);

                return $data;
            })->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Get $get): ?string {
                        $productId = $get("products.{$arguments['item']}.product_id");

                        if (! $productId) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $productId]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Get $get): bool => empty($get("products.{$arguments['item']}.product_id"))
                    ),
            ]);
    }

    private static function afterProductUpdated(Set $set, Get $get): void
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

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
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

    private static function afterUOMUpdated(Set $set, Get $get): void
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

            $uom = Uom::find($get('uom_id'));

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
        $product = Product::find($get('product_id'));

        $vendorPrices = $product->supplierInformation->sortByDesc('sort');

        if ($get('../../partner_id')) {
            $vendorPrices = $vendorPrices->where('partner_id', $get('../../partner_id'));
        }

        $vendorPrices = $vendorPrices->where('min_qty', '<=', $get('product_qty') ?? 1)->where('currency_id', $get('../../currency_id'));

        if (! $vendorPrices->isEmpty()) {
            $vendorPrice = $vendorPrices->first()->price;
        } else {
            $vendorPrice = $product->cost ?: $product->price;
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

    private static function calculateLineTotals(Set $set, Get $get, ?string $prefix = ''): void
    {
        if (! $get($prefix.'product_id')) {
            $set($prefix.'price_unit', 0);

            $set($prefix.'discount', 0);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_subtotal', 0);

            $set($prefix.'price_total', 0);

            return;
        }

        $priceUnit = floatval($get($prefix.'price_unit'));

        $quantity = floatval($get($prefix.'product_qty') ?? 1);

        $subTotal = $priceUnit * $quantity;

        $discountValue = floatval($get($prefix.'discount') ?? 0);

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $get($prefix.'taxes') ?? [];

        [$subTotal, $taxAmount] = TaxFacade::collect($taxIds, $subTotal, $quantity);

        $set($prefix.'price_subtotal', round($subTotal, 4));

        $set($prefix.'price_tax', $taxAmount);

        $set($prefix.'price_total', $subTotal + $taxAmount);
    }

    public static function getOrderSettings(): OrderSettings
    {
        return once(fn () => app(OrderSettings::class));
    }

    public static function getProductSettings(): ProductSettings
    {
        return once(fn () => app(ProductSettings::class));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
