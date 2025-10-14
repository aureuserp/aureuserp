<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource\Pages\CreateVendorPrice;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource\Pages\EditVendorPrice;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource\Pages\ListVendorPrices;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource\Pages\ViewVendorPrice;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVendors;
use Webkul\Purchase\Models\ProductSupplier;

class VendorPriceResource extends Resource
{
    protected static ?string $model = ProductSupplier::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/vendor-price.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.title'))
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                    )
                                    ->searchable()
                                    ->required()
                                    ->preload(),
                                TextInput::make('product_name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-name'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-name-tooltip')),
                                TextInput::make('product_code')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-code'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-code-tooltip')),
                                TextInput::make('delay')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.delay'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.delay-tooltip'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999)
                                    ->default(1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.title'))
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn ($query) => $query->where('is_configurable', null)
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->hiddenOn(ManageVendors::class),
                                TextInput::make('min_qty')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.quantity'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.quantity-tooltip'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->default(0),
                                Group::make()
                                    ->schema([
                                        TextInput::make('price')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.unit-price'))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.unit-price-tooltip'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0),
                                        Select::make('currency_id')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->default(Auth::user()->defaultCompany?->currency_id)
                                            ->preload(),
                                        DatePicker::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.valid-from'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar'),
                                        DatePicker::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.valid-to'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar'),
                                    ])
                                    ->columns(2),
                                TextInput::make('discount')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.discount'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->default(0),
                                Select::make('company_id')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->default(Auth::user()->default_company_id)
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.vendor'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.product'))
                    ->searchable(),
                TextColumn::make('product_name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.vendor-product-name'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('product_code')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.vendor-product-code'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('starts_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.valid-from'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ends_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.valid-to'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.company'))
                    ->sortable(),
                TextColumn::make('min_qty')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.quantity'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.unit-price'))
                    ->sortable(),
                TextColumn::make('discount')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.discount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.currency'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.vendor')),
                Tables\Grouping\Group::make('product.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.product')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('partner_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.vendor'))
                    ->relationship('partner', 'name', fn ($query) => $query->where('sub_type', 'supplier'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('currency_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.currency'))
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('company_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('price_range')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('price_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.price-from'))
                                    ->numeric()
                                    ->prefix('From'),
                                TextInput::make('price_to')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.price-to'))
                                    ->numeric()
                                    ->prefix('To'),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),

                Filter::make('min_qty_range')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('min_qty_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.min-qty-from'))
                                    ->numeric()
                                    ->prefix('From'),
                                TextInput::make('min_qty_to')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.min-qty-to'))
                                    ->numeric()
                                    ->prefix('To'),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_qty_from'],
                                fn (Builder $query, $qty): Builder => $query->where('min_qty', '>=', $qty),
                            )
                            ->when(
                                $data['min_qty_to'],
                                fn (Builder $query, $qty): Builder => $query->where('min_qty', '<=', $qty),
                            );
                    }),

                Filter::make('validity_period')
                    ->schema([
                        Grid::make()
                            ->schema([
                                DatePicker::make('starts_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.starts-from'))
                                    ->native(false),
                                DatePicker::make('ends_before')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.ends-before'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['starts_from'],
                                fn (Builder $query, $date): Builder => $query->where('starts_at', '>=', $date),
                            )
                            ->when(
                                $data['ends_before'],
                                fn (Builder $query, $date): Builder => $query->where('ends_at', '<=', $date),
                            );
                    }),

                Filter::make('created_at')
                    ->schema([
                        Grid::make()
                            ->schema([
                                DatePicker::make('created_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.created-from'))
                                    ->native(false),
                                DatePicker::make('created_until')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.created-until'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (ProductSupplier $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.error.title'))
                                ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.success.title'))
                            ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->action(function (Collection $records) {
                        try {
                            $records->each(fn (Model $record) => $record->delete());
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.success.title'))
                            ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.success.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.vendor'))
                                    ->icon('heroicon-o-user-group'),

                                TextEntry::make('product_name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.vendor-product-name'))
                                    ->icon('heroicon-o-tag')
                                    ->placeholder('—'),

                                TextEntry::make('product_code')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.vendor-product-code'))
                                    ->placeholder('—'),

                                TextEntry::make('delay')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.delay'))
                                    ->icon('heroicon-o-clock')
                                    ->suffix(' days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.product'))
                                    ->icon('heroicon-o-cube'),

                                TextEntry::make('min_qty')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.quantity'))
                                    ->icon('heroicon-o-calculator'),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('price')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.unit-price'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => $record->currency->code ?? 'USD'),

                                        TextEntry::make('currency.name')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.currency'))
                                            ->icon('heroicon-o-globe-alt'),

                                        TextEntry::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.valid-from'))
                                            ->icon('heroicon-o-calendar')
                                            ->date()
                                            ->placeholder('—'),

                                        TextEntry::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.valid-to'))
                                            ->icon('heroicon-o-calendar')
                                            ->date()
                                            ->placeholder('—'),
                                    ])
                                    ->columns(2),

                                TextEntry::make('discount')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.discount'))
                                    ->icon('heroicon-o-gift')
                                    ->suffix('%'),

                                TextEntry::make('company.name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.company'))
                                    ->icon('heroicon-o-building-office'),

                                TextEntry::make('created_at')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.created-at'))
                                    ->icon('heroicon-o-clock')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.updated-at'))
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime(),
                            ]),

                        Group::make()
                            ->schema([
                                Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.title'))
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.entries.created-at'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar'),

                                        TextEntry::make('creator.name')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.entries.created-by'))
                                            ->icon('heroicon-m-user'),

                                        TextEntry::make('updated_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.entries.last-updated'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar-days'),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),

                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListVendorPrices::route('/'),
            'create' => CreateVendorPrice::route('/create'),
            'view'   => ViewVendorPrice::route('/{record}'),
            'edit'   => EditVendorPrice::route('/{record}/edit'),
        ];
    }
}
