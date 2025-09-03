<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ManageRoutes;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.title'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.route'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.route-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Forms\Components\Select::make('company_id')
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

                Forms\Components\Section::make(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.title'))
                    ->description(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.description'))
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Toggle::make('product_category_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.product-categories'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.product-categories-hint-tooltip')),
                                Forms\Components\Toggle::make('product_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.products'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.products-hint-tooltip')),
                                Forms\Components\Toggle::make('packaging_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.packaging'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.packaging-hint-tooltip'))
                                    ->visible(fn (ProductSettings $settings): bool => $settings->enable_packagings),
                            ]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Toggle::make('warehouse_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.warehouses'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.warehouses-hint-tooltip'))
                                    ->live(),
                                Forms\Components\Select::make('warehouses')
                                    ->hiddenLabel()
                                    ->relationship('warehouses', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->visible(fn (Forms\Get $get) => $get('warehouse_selectable')),
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.route'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.company'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/route.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/route.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
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
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.entries.route'))
                                    ->icon('heroicon-o-arrow-path')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.title'))
                            ->description(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.description'))
                            ->schema([
                                Infolists\Components\Grid::make()
                                    ->schema([
                                        Infolists\Components\IconEntry::make('product_category_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.product-categories'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        Infolists\Components\IconEntry::make('product_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.products'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        Infolists\Components\IconEntry::make('packaging_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.packaging'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                    ])
                                    ->columns(3),

                                Infolists\Components\Grid::make()
                                    ->schema([
                                        Infolists\Components\IconEntry::make('warehouse_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.warehouses'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        Infolists\Components\TextEntry::make('warehouses.name')
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

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('updated_at')
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
            Pages\ViewRoute::class,
            Pages\EditRoute::class,
            Pages\ManageRules::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListRoutes::route('/'),
            'create'  => Pages\CreateRoute::route('/create'),
            'view'    => Pages\ViewRoute::route('/{record}'),
            'edit'    => Pages\EditRoute::route('/{record}/edit'),
            'rules'   => Pages\ManageRules::route('/{record}/rules'),
        ];
    }
}
