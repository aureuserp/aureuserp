<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Settings;
use Webkul\Purchase\Settings\OrderSettings;

class PurchaseAgreementResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Requisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 3;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(OrderSettings::class)->enable_purchase_agreements;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(Enums\RequisitionState::options())
                    ->default(Enums\RequisitionState::DRAFT)
                    ->disabled(),
                Forms\Components\Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.title'))
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        fn (Builder $query) => $query->withTrashed()->where('sub_type', 'supplier')
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != Enums\RequisitionState::DRAFT),
                                Forms\Components\Select::make('user_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.buyer'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != Enums\RequisitionState::DRAFT),
                                Forms\Components\Select::make('type')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.agreement-type'))
                                    ->options(Enums\RequisitionType::class)
                                    ->required()
                                    ->default(Enums\RequisitionType::BLANKET_ORDER)
                                    ->disabled(fn ($record): bool => $record && $record?->state != Enums\RequisitionState::DRAFT)
                                    ->live(),
                                Forms\Components\Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.currency'))
                                    ->relationship('currency', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-from'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->minDate(now()->toDateString())
                                            ->live()
                                            ->afterStateUpdated(fn (Forms\Set $set) => $set('ends_at', null))
                                            ->rules([
                                                'date',
                                                'after_or_equal:today',
                                            ]),
                                        Forms\Components\DatePicker::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-to'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->native(false)
                                            ->minDate(fn (Forms\Get $get) => $get('starts_at') ?: now()->toDateString())
                                            ->live()
                                            ->rules([
                                                'date',
                                                'after_or_equal:date_from',
                                            ]),
                                    ])
                                    ->columns(2)
                                    ->hidden(function (Forms\Get $get): bool {
                                        return $get('type') != Enums\RequisitionType::BLANKET_ORDER;
                                    }),
                                Forms\Components\TextInput::make('reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->placeholder(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference-placeholder')),
                                Forms\Components\Select::make('company_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.company'))
                                    ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->default(filament()->auth()?->user()?->default_company_id)
                                    ->disabled(fn ($record): bool => $record && $record?->state != Enums\RequisitionState::DRAFT),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.title'))
                            ->schema([
                                static::getProductsRepeater(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.additional.title'))
                            ->visible(! empty($customFormFields = static::getCustomFormFields()))
                            ->schema($customFormFields),

                        Forms\Components\Tabs\Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.terms.title'))
                            ->schema([
                                Forms\Components\RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getProductsRepeater(): Forms\Components\Repeater
    {
        $columns = 3;

        if (app(Settings\ProductSettings::class)->enable_uom) {
            $columns++;
        }

        return Forms\Components\Repeater::make('lines')
            ->hiddenLabel()
            ->relationship()
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.product'))
                    ->relationship('product', 'name')
                    ->relationship(
                        'product',
                        'name',
                        fn ($query) => $query->where('type', ProductType::GOODS),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                        if ($product = Product::find($get('product_id'))) {
                            $set('uom_id', $product->uom_id);
                        }
                    })
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [Enums\RequisitionState::CLOSED, Enums\RequisitionState::CANCELED])),
                Forms\Components\TextInput::make('qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.quantity'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [Enums\RequisitionState::CLOSED, Enums\RequisitionState::CANCELED])),
                Forms\Components\Select::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [Enums\RequisitionState::CLOSED, Enums\RequisitionState::CANCELED])),
                Forms\Components\TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.unit-price'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [Enums\RequisitionState::CLOSED, Enums\RequisitionState::CANCELED])),
            ])
            ->columns($columns);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::mergeCustomTableColumns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.agreement'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.vendor'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.agreement-type'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.buyer'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.company'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.valid-from'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.valid-to'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.reference'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.status'))
                    ->sortable()
                    ->badge()
                    ->toggleable(),
            ]))
            ->groups([
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.vendor')),
                Tables\Grouping\Group::make('state')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.state')),
                Tables\Grouping\Group::make('type')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.agreement-type')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.agreement')),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('state')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.status'))
                            ->multiple()
                            ->options(Enums\RequisitionState::class)
                            ->icon('heroicon-o-bars-2'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.vendor'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('user')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.buyer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('starts_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.valid-from')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('ends_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.valid-to')),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('reference')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.reference'))
                            ->icon('heroicon-o-identification'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.updated-at')),
                    ]))->filter()->values()->all()),
            ], layout: \Filament\Tables\Enums\FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.restore.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(fn (Model $record) => $record->state == Enums\RequisitionState::CLOSED)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.delete.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->action(function (Requisition $record) {
                            try {
                                $record->forceDelete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.error.title'))
                                    ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.success.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.restore.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.delete.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== Enums\RequisitionState::CLOSED,
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

                Infolists\Components\Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('partner.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.vendor'))
                                        ->icon('heroicon-o-building-storefront'),

                                    Infolists\Components\TextEntry::make('user.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.buyer'))
                                        ->icon('heroicon-o-user'),

                                    Infolists\Components\TextEntry::make('type')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.agreement-type'))
                                        ->icon('heroicon-o-document')
                                        ->badge(),

                                    Infolists\Components\TextEntry::make('currency.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.currency'))
                                        ->icon('heroicon-o-currency-dollar'),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('starts_at')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.valid-from'))
                                                ->icon('heroicon-o-calendar')
                                                ->date(),

                                            Infolists\Components\TextEntry::make('ends_at')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.valid-to'))
                                                ->icon('heroicon-o-calendar')
                                                ->date(),
                                        ])
                                        ->visible(fn ($record) => $record->type === Enums\RequisitionType::BLANKET_ORDER),

                                    Infolists\Components\TextEntry::make('reference')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.reference'))
                                        ->icon('heroicon-o-identification'),

                                    Infolists\Components\TextEntry::make('company.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.company'))
                                        ->icon('heroicon-o-building-office'),
                                ]),
                            ]),
                    ]),

                Infolists\Components\Tabs::make('Tabs')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.title'))
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('lines')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('product.name')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.product')),

                                        Infolists\Components\TextEntry::make('qty')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.quantity')),

                                        Infolists\Components\TextEntry::make('uom.name')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.entries.unit'))
                                            ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom),

                                        Infolists\Components\TextEntry::make('price_unit')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.unit-price'))
                                            ->money(fn ($record) => $record->requisition->currency->code ?? 'USD'),
                                    ])
                                    ->columns([
                                        'sm' => 2,
                                        'xl' => 4,
                                    ]),
                            ]),

                        Infolists\Components\Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.additional.title'))
                            ->visible(! empty($customInfolistEntries = static::getCustomInfolistEntries()))
                            ->schema($customInfolistEntries),

                        Infolists\Components\Tabs\Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.terms.title'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->prose(),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.title'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.entries.created-by'))
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-arrow-path'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPurchaseAgreement::class,
            Pages\EditPurchaseAgreement::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchaseAgreements::route('/'),
            'create' => Pages\CreatePurchaseAgreement::route('/create'),
            'edit'   => Pages\EditPurchaseAgreement::route('/{record}/edit'),
            'view'   => Pages\ViewPurchaseAgreement::route('/{record}/view'),
        ];
    }
}
