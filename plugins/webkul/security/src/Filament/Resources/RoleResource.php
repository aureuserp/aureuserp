<?php

namespace Webkul\Security\Filament\Resources;

use BackedEnum;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as RolesRoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Webkul\Security\Filament\Resources\RoleResource\Pages\CreateRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\EditRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\ListRoles;
use Webkul\Security\Filament\Resources\RoleResource\Pages\ViewRole;
use Webkul\Security\Models\Role;

class RoleResource extends RolesRoleResource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static bool $isGloballySearchable = false;

    protected static ?Collection $allFormPermissions = null;

    protected static ?array $matrixData = null;

    public static function canGloballySearch(): bool
    {
        return false;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public static function getActiveNavigationIcon(): BackedEnum|Htmlable|null|string
    {
        return null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule): Unique => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                    )
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->isSystemRole())
                                    ->dehydrated(),

                                Select::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->options([
                                        'web'     => __('security::filament/resources/role.form.fields.web'),
                                        'sanctum' => __('security::filament/resources/role.form.fields.sanctum'),
                                    ])
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->isSystemRole())
                                    ->dehydrated(),
                                Toggle::make('select_all_permissions')
                                    ->label(__('security::filament/resources/role.form.fields.select-all-permissions'))
                                    ->helperText(__('security::filament/resources/role.form.fields.select-all-permissions-hint'))
                                    ->dehydrated(false)
                                    ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->isSystemRole()),
                            ])
                            ->columns([
                                'sm' => 3,
                                'lg' => 3,
                            ]),
                        Select::make(config('permission.column_names.team_foreign_key'))
                            ->label(__('filament-shield::filament-shield.field.team'))
                            ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                            ->default(Filament::getTenant()?->id)
                            ->options(fn (): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                            ->hidden(fn (): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                            ->dehydrated(fn (): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled())),

                        Hidden::make('permissions_sync_mode')
                            ->default('manual'),
                    ])
                    ->columns([
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->columnSpanFull(),

                static::getPermissionMatrixField(),
            ]);
    }

    /**
     * Module-navigator matrix field replacing the raw checkbox tabs.
     */
    public static function getPermissionMatrixField(): Component
    {
        $matrix = static::getPermissionMatrixData();

        return ViewField::make('permission_matrix')
            ->view('security::filament.resources.role.permission-matrix')
            ->viewData(['matrix' => $matrix])
            ->dehydrated()
            ->afterStateHydrated(function (ViewField $component, ?Model $record) use ($matrix): void {
                $names = collect($matrix['allNames']);
                $granted = $record instanceof Model ? $record->permissions->pluck('name') : collect();

                $component->state($granted->intersect($names)->values()->all());
            })
            ->columnSpanFull();
    }

    /**
     * Builds the module → resources/pages/widgets structure the matrix renders.
     */
    public static function getPermissionMatrixData(): array
    {
        if (static::$matrixData !== null) {
            return static::$matrixData;
        }

        $abilityOrder = static::getMatrixAbilityPrefixes();
        $resourcesByPlugin = static::getPluginResources() ?? [];
        $pagesByPlugin = static::getPluginPages();
        $widgetsByPlugin = static::getPluginWidgets();

        $moduleKeys = collect(array_keys($resourcesByPlugin))
            ->merge(array_keys($pagesByPlugin))
            ->merge(array_keys($widgetsByPlugin))
            ->unique()
            ->sort()
            ->values();

        $modules = [];
        $allNames = [];

        foreach ($moduleKeys as $moduleKey) {
            $resources = [];
            $moduleAbilities = [];
            $moduleNames = [];

            foreach (($resourcesByPlugin[$moduleKey] ?? []) as $entity) {
                $allOptions = parent::getResourcePermissionOptions($entity);
                $options = static::getResourcePermissionOptions($entity);

                if (empty($allOptions)) {
                    continue;
                }

                $abilities = [];
                $hasColumn = false;

                foreach (array_keys($allOptions) as $permission) {
                    $ability = static::matchAbilityPrefix($permission, $abilityOrder);

                    if ($ability === null) {
                        continue;
                    }

                    $moduleAbilities[$ability] = true;
                    $hasColumn = true;

                    if (isset($options[$permission])) {
                        $abilities[$ability] = $permission;
                        $moduleNames[] = $permission;
                    }
                }

                if (! $hasColumn) {
                    continue;
                }

                $resources[] = [
                    'label'     => strval(static::shield()->hasLocalizedPermissionLabels()
                        ? FilamentShield::getLocalizedResourceLabel($entity['resourceFqcn'])
                        : $entity['model']),
                    'abilities' => $abilities,
                ];
            }

            usort($resources, fn (array $a, array $b): int => strcasecmp($a['label'], $b['label']));

            $columns = array_values(array_filter($abilityOrder, fn (string $ability): bool => isset($moduleAbilities[$ability])));

            $pages = [];
            foreach (($pagesByPlugin[$moduleKey] ?? []) as $page) {
                foreach ($page['permissions'] as $permission => $label) {
                    $pages[$permission] = $label;
                    $moduleNames[] = $permission;
                }
            }

            $widgets = [];
            foreach (($widgetsByPlugin[$moduleKey] ?? []) as $widget) {
                foreach ($widget['permissions'] as $permission => $label) {
                    $widgets[$permission] = $label;
                    $moduleNames[] = $permission;
                }
            }

            $moduleNames = array_values(array_unique($moduleNames));

            if (empty($moduleNames)) {
                continue;
            }

            $allNames = array_merge($allNames, $moduleNames);

            $modules[] = [
                'key'       => $moduleKey,
                'label'     => Str::headline($moduleKey),
                'columns'   => $columns,
                'resources' => $resources,
                'pages'     => $pages,
                'widgets'   => $widgets,
                'names'     => $moduleNames,
            ];
        }

        return static::$matrixData = [
            'modules'       => $modules,
            'allNames'      => array_values(array_unique($allNames)),
            'abilityLabels' => static::getAbilityLabels($abilityOrder),
        ];
    }

    /**
     * Ability prefixes ordered longest-first so "view_any" wins over "view".
     */
    protected static function getMatrixAbilityPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'create',
            'update',
            'delete_any',
            'delete',
            'reorder',
            'restore_any',
            'restore',
            'force_delete_any',
            'force_delete',
        ];
    }

    protected static function matchAbilityPrefix(string $permission, array $orderedPrefixes): ?string
    {
        foreach ($orderedPrefixes as $prefix) {
            if (str_starts_with($permission, $prefix.'_')) {
                return $prefix;
            }
        }

        return null;
    }

    protected static function getAbilityLabels(array $prefixes): array
    {
        return collect($prefixes)
            ->mapWithKeys(fn (string $prefix): array => [$prefix => Str::headline($prefix)])
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['primary'])
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Model $record): void {
                        if (static::isProtectedRoleRecord($record)) {
                            Notification::make()
                                ->danger()
                                ->title(__('security::filament/resources/role.notification.system-role-delete.title'))
                                ->body(__('security::filament/resources/role.notification.system-role-delete.body'))
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->fetchSelectedRecords(true)
                    ->authorizeIndividualRecords('delete')
                    ->action(function (DeleteBulkAction $action, Collection $records): void {
                        $deletableRecords = $records->reject(
                            fn (Model $record): bool => static::isProtectedRoleRecord($record)
                        );

                        if ($deletableRecords->isEmpty()) {
                            $action->cancel();

                            return;
                        }

                        $deletableRecords->each(fn (Model $record): ?bool => $record->delete());
                    }),
            ])
            ->defaultSort('created_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view'   => ViewRole::route('/{record}'),
            'edit'   => EditRole::route('/{record}/edit'),
        ];
    }

    /**
     * Returns the full set of permission names that the form checkboxes represent.
     * Uses the same data sources as the form so "Select All" saves exactly what is shown.
     */
    public static function getAllFormPermissions(): Collection
    {
        if (static::$allFormPermissions instanceof Collection) {
            return static::$allFormPermissions;
        }

        $resourcePermissions = collect(static::getResources())
            ->flatMap(fn (array $entity): array => array_keys(static::getResourcePermissionOptions($entity)));

        return static::$allFormPermissions = $resourcePermissions
            ->merge(array_keys(static::getPageOptions()))
            ->merge(array_keys(static::getWidgetOptions()))
            ->unique()
            ->values();
    }

    /**
     * Modules whose resources expose only a limited set of ability prefixes.
     */
    protected static function getRestrictedModuleAbilities(): array
    {
        return [
            'PluginManager' => ['view_any', 'view', 'update'],
        ];
    }

    /**
     * Filters a resource's permissions when its module is ability-restricted.
     */
    public static function getResourcePermissionOptions(array $entity): array
    {
        $options = parent::getResourcePermissionOptions($entity);

        $module = explode('\\', $entity['resourceFqcn'])[1] ?? null;
        $allowed = static::getRestrictedModuleAbilities()[$module] ?? null;

        if ($allowed === null) {
            return $options;
        }

        $prefixes = static::getMatrixAbilityPrefixes();

        return array_filter(
            $options,
            fn (string $permission): bool => in_array(static::matchAbilityPrefix($permission, $prefixes), $allowed, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function getPluginResources(): ?array
    {
        return once(fn (): array => collect(static::getResources())
            ->groupBy(function ($value, $key) {
                return explode('\\', $key)[1] ?? 'Unknown';
            })
            ->toArray());
    }

    public static function getResources(): ?array
    {
        return FilamentShield::discoverResources()
            ->reject(function ($resource) {
                if ($resource == 'BezhanSalleh\FilamentShield\Resources\Roles\RoleResource') {
                    return true;
                }

                if (Utils::getConfig()->resources->exclude) {
                    return in_array(
                        Str::of($resource)->afterLast('\\'),
                        Utils::getConfig()->resources->exclude
                    );
                }
            })
            ->mapWithKeys(function (string $resource) {
                return [
                    $resource => [
                        'model'        => str($resource::getModel())->afterLast('\\')->toString(),
                        'modelFqcn'    => str($resource::getModel())->toString(),
                        'resourceFqcn' => $resource,
                    ],
                ];
            })
            ->sortKeys()
            ->toArray();
    }

    public static function getPluginPages(): array
    {
        return collect(FilamentShield::getPages())
            ->groupBy(function ($value, $key) {
                return explode('\\', $key)[1] ?? 'Unknown';
            })
            ->toArray();
    }

    public static function getPluginWidgets(): array
    {
        return collect(FilamentShield::getWidgets())
            ->groupBy(function ($value, $key) {
                return explode('\\', $key)[1] ?? 'Unknown';
            })
            ->toArray();
    }

    public static function isProtectedRoleRecord(?Model $record): bool
    {
        return $record instanceof Role && $record->isSystemRole();
    }
}
