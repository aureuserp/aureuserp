<?php

namespace Webkul\Security\Filament\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Webkul\Security\Filament\Resources\RoleResource\Pages;

class RoleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static $permissionsCollection;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->dehydrated()
                                    ->readOnly()
                                    ->maxLength(255),

                                ShieldSelectAllToggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(fn (): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
                                    ->dehydrated(fn ($state): bool => $state),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                Forms\Components\Tabs::make('Permissions')
                    ->contained()
                    ->tabs([
                        static::getTabFormComponentForResources(),
                        static::getTabFormComponentForPage(),
                        static::getTabFormComponentForWidget(),
                        static::getTabFormComponentForCustomPermissions(),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['primary'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (Model $record) => $record->name == config('filament-shield.panel_user.name')),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Model $record) => $record->name == config('filament-shield.panel_user.name')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'asc')
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => $record->name != config('filament-shield.panel_user.name'),
            );
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view'   => Pages\ViewRole::route('/{record}'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster() ?? static::$cluster;
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isResourceNavigationRegistered();
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    public static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(): string
    {
        return Utils::getResourceSlug();
    }

    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->count())
            : null;
    }

    public static function isScopedToTenant(): bool
    {
        return Utils::isScopedToTenant();
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    public static function getPluginResourceEntitiesSchema(): ?array
    {
        return collect(static::getPluginResources())
            ->sortKeys()
            ->map(function ($plugin, $key) {
                return Forms\Components\Section::make($key)
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema(function () use ($plugin) {
                                return collect($plugin)
                                    ->map(function ($entity) {
                                        $fieldsetLabel = strval(
                                            static::shield()->hasLocalizedPermissionLabels()
                                                ? FilamentShield::getLocalizedResourceLabel($entity['fqcn'])
                                                : $entity['model']
                                        );

                                        return Forms\Components\Fieldset::make($fieldsetLabel)
                                            ->schema([
                                                Forms\Components\Placeholder::make('model')
                                                    ->inlineLabel()
                                                    ->content(fn (): HtmlString => new HtmlString('<span style="word-break: break-word;">'.$entity['fqcn'].'</span>'))
                                                    ->columnSpan(2)
                                                    ->visible(config('filament-shield.shield_resource.show_model_path', false)),
                                                static::getCheckBoxListComponentForResource($entity),
                                            ])
                                            ->columnSpan(static::shield()->getSectionColumnSpan());
                                    })
                                    ->toArray();
                            })
                            ->columns(static::shield()->getGridColumns()),
                    ]);
            })
            ->toArray();
    }

    /**
     * Transform filament resources to key value pair for shield
     */
    public static function getPluginResources(): ?array
    {
        return collect(static::getResources())
            ->groupBy(function ($value, $key) {
                return explode('\\', $key)[1] ?? 'Unknown';
            })
            ->toArray();
    }

    /**
     * Transform filament resources to key value pair for shield
     */
    public static function getResources(): ?array
    {
        $resources = Filament::getResources();
        if (Utils::discoverAllResources()) {
            $resources = [];
            foreach (Filament::getPanels() as $panel) {
                $resources = array_merge($resources, $panel->getResources());
            }
            $resources = array_unique($resources);
        }

        return collect($resources)
            ->reject(function ($resource) {
                if (! $resource::shouldRegisterNavigation()) {
                    return true;
                }

                if ($resource == 'BezhanSalleh\FilamentShield\Resources\RoleResource') {
                    return true;
                }

                if (Utils::isGeneralExcludeEnabled()) {
                    return in_array(
                        Str::of($resource)->afterLast('\\'),
                        Utils::getExcludedResouces()
                    );
                }
            })
            ->mapWithKeys(function ($resource) {
                $name = FilamentShield::getPermissionIdentifier($resource);

                return [
                    $resource => [
                        'resource' => "{$name}",
                        'model'    => str($resource::getModel())->afterLast('\\')->toString(),
                        'fqcn'     => $resource,
                    ],
                ];
            })
            ->sortKeys()
            ->toArray();
    }

    public static function getResourceTabBadgeCount(): ?int
    {
        return collect(FilamentShield::getResources())
            ->map(fn ($resource) => count(static::getResourcePermissionOptions($resource)))
            ->sum();
    }

    public static function getResourcePermissionOptions(array $entity): array
    {
        return collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->flatMap(function ($permission) use ($entity) {
                $name = $permission.'_'.$entity['resource'];
                $label = static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedResourcePermissionLabel($permission)
                    : $name;

                return [
                    $name => $label,
                ];
            })
            ->toArray();
    }

    public static function setPermissionStateForRecordPermissions(Component $component, string $operation, array $permissions, ?Model $record): void
    {

        if (in_array($operation, ['edit', 'view'])) {

            if (blank($record)) {
                return;
            }
            if ($component->isVisible() && count($permissions) > 0) {
                $component->state(
                    collect($permissions)
                        /** @phpstan-ignore-next-line */
                        ->filter(fn ($value, $key) => $record->checkPermissionTo($key))
                        ->keys()
                        ->toArray()
                );
            }
        }
    }

    public static function getPageOptions(): array
    {
        return collect(FilamentShield::getPages())
            ->flatMap(fn ($page) => [
                $page['permission'] => static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedPageLabel($page['class'])
                    : $page['permission'],
            ])
            ->toArray();
    }

    public static function getWidgetOptions(): array
    {
        return collect(FilamentShield::getWidgets())
            ->flatMap(fn ($widget) => [
                $widget['permission'] => static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedWidgetLabel($widget['class'])
                    : $widget['permission'],
            ])
            ->toArray();
    }

    public static function getCustomPermissionOptions(): ?array
    {
        return FilamentShield::getCustomPermissions()
            ->mapWithKeys(fn ($customPermission) => [
                $customPermission => static::shield()->hasLocalizedPermissionLabels() ? str($customPermission)->headline()->toString() : $customPermission,
            ])
            ->toArray();
    }

    public static function getTabFormComponentForResources(): Component
    {
        return static::shield()->hasSimpleResourcePermissionView()
            ? static::getTabFormComponentForSimpleResourcePermissionsView()
            : Forms\Components\Tabs\Tab::make('resources')
                ->label(__('filament-shield::filament-shield.resources'))
                ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
                ->badge(static::getResourceTabBadgeCount())
                ->schema(static::getPluginResourceEntitiesSchema());
    }

    public static function getCheckBoxListComponentForResource(array $entity): Component
    {
        $permissionsArray = static::getResourcePermissionOptions($entity);

        return static::getCheckboxListFormComponent($entity['resource'], $permissionsArray, false);
    }

    public static function getTabFormComponentForPage(): Component
    {
        $options = static::getPageOptions();
        $count = count($options);

        return Forms\Components\Tabs\Tab::make('pages')
            ->label(__('filament-shield::filament-shield.pages'))
            ->visible(fn (): bool => (bool) Utils::isPageEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('pages_tab', $options),
            ]);
    }

    public static function getTabFormComponentForWidget(): Component
    {
        $options = static::getWidgetOptions();
        $count = count($options);

        return Forms\Components\Tabs\Tab::make('widgets')
            ->label(__('filament-shield::filament-shield.widgets'))
            ->visible(fn (): bool => (bool) Utils::isWidgetEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('widgets_tab', $options),
            ]);
    }

    public static function getTabFormComponentForCustomPermissions(): Component
    {
        $options = static::getCustomPermissionOptions();
        $count = count($options);

        return Forms\Components\Tabs\Tab::make('custom')
            ->label(__('vendor.filament-shield.custom'))
            ->visible(fn (): bool => (bool) Utils::isCustomPermissionEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('custom_permissions', $options),
            ]);
    }

    public static function getTabFormComponentForSimpleResourcePermissionsView(): Component
    {
        $options = FilamentShield::getAllResourcePermissions();
        $count = count($options);

        return Forms\Components\Tabs\Tab::make('resources')
            ->label(__('filament-shield::filament-shield.resources'))
            ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('resources_tab', $options),
            ]);
    }

    public static function getCheckboxListFormComponent(string $name, array $options, bool $searchable = true): Component
    {
        return Forms\Components\CheckboxList::make($name)
            ->label('')
            ->options(fn (): array => $options)
            ->searchable($searchable)
            ->afterStateHydrated(
                fn (Component $component, string $operation, ?Model $record) => static::setPermissionStateForRecordPermissions(
                    component: $component,
                    operation: $operation,
                    permissions: $options,
                    record: $record
                )
            )
            ->dehydrated(fn ($state) => ! blank($state))
            ->bulkToggleable()
            ->gridDirection('row')
            ->columns(static::shield()->getCheckboxListColumns())
            ->columnSpan(static::shield()->getCheckboxListColumnSpan());
    }

    public static function shield(): FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }
}
