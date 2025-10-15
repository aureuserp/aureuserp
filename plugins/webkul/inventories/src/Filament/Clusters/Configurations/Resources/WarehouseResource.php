<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\CreateWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\EditWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ListWarehouses;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ManageRoutes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ViewWarehouse;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Partner\Filament\Resources\PartnerResource;

class WarehouseResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Warehouse::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/warehouse.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/warehouse.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->unique(ignoreRecord: true),

                                TextInput::make('code')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code-placeholder'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code-hint-tooltip'))
                                    ->unique(ignoreRecord: true),

                                Group::make()
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->required()
                                            ->disabled(fn () => Auth::user()->default_company_id)
                                            ->default(Auth::user()->default_company_id),
                                        Select::make('partner_address_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.address'))
                                            ->relationship('partnerAddress', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema)),
                                    ])
                                    ->columns(2),
                            ]),

                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.additional.title'))
                            ->visible(! empty($customFormFields = static::getCustomFormFields()))
                            ->schema($customFormFields),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.title'))
                            ->schema([
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.shipment-management'))
                                    ->schema([
                                        Radio::make('reception_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.incoming-shipments'))
                                            ->options(ReceptionStep::class)
                                            ->default(ReceptionStep::ONE_STEP)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.incoming-shipments-hint-tooltip')),

                                        Radio::make('delivery_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.outgoing-shipments'))
                                            ->options(DeliveryStep::class)
                                            ->default(DeliveryStep::ONE_STEP)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.outgoing-shipments-hint-tooltip')),
                                    ])
                                    ->columns(1)
                                    ->visible(static::getWarehouseSettings()->enable_multi_steps_routes),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.resupply-management'))
                                    ->schema([
                                        CheckboxList::make('supplierWarehouses')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.resupply-from'))
                                            ->relationship('supplierWarehouses', 'name'),
                                    ])
                                    ->visible(Warehouse::count() > 1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->visible(static::getWarehouseSettings()->enable_multi_steps_routes),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.code'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('partnerAddress.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.address'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Warehouse $record, $action) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/warehouse.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
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
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-building-storefront')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('code')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.code'))
                                    ->icon('heroicon-m-hashtag'),

                                Group::make()
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                        TextEntry::make('partnerAddress.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.address'))
                                            ->icon('heroicon-o-map'),
                                    ])
                                    ->columns(2),
                            ]),

                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.title'))
                            ->schema([
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.shipment-management'))
                                    ->schema([
                                        TextEntry::make('reception_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.incoming-shipments'))
                                            ->icon('heroicon-o-truck'),

                                        TextEntry::make('delivery_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.outgoing-shipments'))
                                            ->icon('heroicon-o-paper-airplane'),
                                    ]),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.resupply-management'))
                                    ->schema([
                                        TextEntry::make('supplierWarehouses.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.resupply-from'))
                                            ->icon('heroicon-o-arrow-path')
                                            ->placeholder('—'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    static public function getWarehouseSettings(): WarehouseSettings
    {
        return once(fn () => app(WarehouseSettings::class));
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $route = request()->route()?->getName() ?? session('current_route');

        if ($route && $route != 'livewire.update') {
            session(['current_route' => $route]);
        } else {
            $route = session('current_route');
        }

        if ($route === self::getRouteBaseName().'.index') {
            return SubNavigationPosition::Start;
        }

        return SubNavigationPosition::Top;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewWarehouse::class,
            EditWarehouse::class,
            ManageRoutes::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'view'   => ViewWarehouse::route('/{record}'),
            'edit'   => EditWarehouse::route('/{record}/edit'),
            'routes' => ManageRoutes::route('/{record}/routes'),
        ];
    }
}
