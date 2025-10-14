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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\CreateRoute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\EditRoute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ListRoutes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ManageRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ViewRoute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers\RulesRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ManageRoutes;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(WarehouseSettings::class)->enable_multi_steps_routes;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/route.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/route.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.route'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.route-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Select::make('company_id')
                            ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.company'))
                            ->relationship(
                                name: 'company',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''),
                            )
                            ->disableOptionWhen(
                                fn (string $label): bool => str_contains($label, ' (Deleted)'),
                            )
                            ->live()
                            ->searchable()
                            ->preload()
                            ->default(Auth::user()->default_company_id),
                    ]),

                Section::make(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.title'))
                    ->description(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.description'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Toggle::make('product_category_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.product-categories'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.product-categories-hint-tooltip')),
                                Toggle::make('product_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.products'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.products-hint-tooltip')),
                                Toggle::make('packaging_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.packaging'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.packaging-hint-tooltip'))
                                    ->visible(fn (ProductSettings $settings): bool => $settings->enable_packagings),
                            ]),
                        Group::make()
                            ->schema([
                                Toggle::make('warehouse_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.warehouses'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.warehouses-hint-tooltip'))
                                    ->live(),
                                Select::make('warehouses')
                                    ->hiddenLabel()
                                    ->relationship('warehouses', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->visible(fn (Get $get) => $get('warehouse_selectable')),
                            ])
                            ->hiddenOn(ManageRoutes::class),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.route'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.company'))
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Route $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.force-delete.notification.success.body')),
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
                        Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.entries.route'))
                                    ->icon('heroicon-o-arrow-path')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.title'))
                            ->description(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.description'))
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        IconEntry::make('product_category_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.product-categories'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        IconEntry::make('product_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.products'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        IconEntry::make('packaging_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.packaging'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                    ])
                                    ->columns(3),

                                Grid::make()
                                    ->schema([
                                        IconEntry::make('warehouse_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.warehouses'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        TextEntry::make('warehouses.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.warehouses'))
                                            ->listWithLineBreaks()
                                            ->visible(fn ($record) => $record->warehouse_selectable)
                                            ->icon('heroicon-o-building-office'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
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
            ViewRoute::class,
            EditRoute::class,
            ManageRules::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => ListRoutes::route('/'),
            'create'  => CreateRoute::route('/create'),
            'view'    => ViewRoute::route('/{record}'),
            'edit'    => EditRoute::route('/{record}/edit'),
            'rules'   => ManageRules::route('/{record}/rules'),
        ];
    }
}
