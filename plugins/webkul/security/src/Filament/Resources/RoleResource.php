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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Webkul\Security\Filament\Resources\RoleResource\Pages\CreateRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\EditRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\ListRoles;
use Webkul\Security\Filament\Resources\RoleResource\Pages\ViewRole;

class RoleResource extends RolesRoleResource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static bool $isGloballySearchable = false;

    protected static $permissionsCollection;

    public static $permissions = null;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule): Unique => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                    )
                                    ->required()
                                    ->maxLength(255),

                                Select::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->options([
                                        'web' => __('security::filament/resources/role.form.fields.web'),
                                        'sanctum' => __('security::filament/resources/role.form.fields.sanctum'),
                                    ])
                                    ->default(Utils::getFilamentAuthGuard()),

                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default(Filament::getTenant()?->id)
                                    ->options(fn (): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->hidden(fn (): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn (): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled())),
                                static::getSelectAllFormComponent(),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                static::getShieldFormComponents(),
            ]);
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
                    ->hidden(fn (Model $record) => $record->name == config('filament-shield.panel_user.name')),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
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

    public static function getTabFormComponentForResources(): Component
    {
        return self::shield()->hasSimpleResourcePermissionView()
            ? self::getTabFormComponentForSimpleResourcePermissionsView()
            : Tab::make('resources')
                ->label(__('filament-shield::filament-shield.resources'))
                ->visible(fn (): bool => Utils::isResourceTabEnabled())
                ->badge(static::getResourceTabBadgeCount())
                ->schema(static::getPluginResourceEntitiesSchema());
    }

    public static function getTabFormComponentForPage(): Component
    {
        $options = static::getPageOptions();
        $count = count($options);

        return Tab::make('pages')
            ->label(__('filament-shield::filament-shield.pages'))
            ->visible(fn (): bool => Utils::isPageTabEnabled() && $count > 0)
            ->badge($count)
            ->schema(static::getPluginPageEntitiesSchema());
    }

    public static function getTabFormComponentForWidget(): Component
    {
        $options = static::getWidgetOptions();
        $count = count($options);

        return Tab::make('widgets')
            ->label(__('filament-shield::filament-shield.widgets'))
            ->visible(fn (): bool => Utils::isWidgetTabEnabled() && $count > 0)
            ->badge($count)
            ->schema(static::getPluginWidgetEntitiesSchema());
    }

    public static function getPluginResources(): ?array
    {
        return collect(static::getResources())
            ->groupBy(function ($value, $key) {
                return explode('\\', $key)[1] ?? 'Unknown';
            })
            ->toArray();
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

    public static function getPluginResourceEntitiesSchema(): ?array
    {
        return collect(static::getPluginResources())
            ->sortKeys()
            ->map(function ($plugin, $key) {
                $hasAnyOptions = collect($plugin)->contains(function ($entity) {
                    $checkbox = static::getCheckBoxListComponentForResource($entity);

                    return ! empty($checkbox->getOptions());
                });

                if (! $hasAnyOptions) {
                    return;
                }

                return Section::make($key)
                    ->collapsible()
                    ->persistCollapsed()
                    ->schema([
                        Grid::make()
                            ->schema(function () use ($plugin) {
                                return collect($plugin)
                                    ->flatMap(function ($entity) {
                                        $checkbox = static::getCheckBoxListComponentForResource($entity);

                                        if (empty($checkbox->getOptions())) {
                                            return [];
                                        }

                                        $fieldsetLabel = strval(
                                            static::shield()->hasLocalizedPermissionLabels()
                                                ? FilamentShield::getLocalizedResourceLabel($entity['resourceFqcn'])
                                                : $entity['model']
                                        );

                                        return [
                                            Fieldset::make($fieldsetLabel)
                                                ->schema([
                                                    $checkbox->hiddenLabel(),
                                                ])
                                                ->columnSpan(static::shield()->getSectionColumnSpan()),
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->columns(static::shield()->getGridColumns()),
                    ]);
            })
            ->toArray();
    }

    public static function getPluginPageEntitiesSchema(): ?array
    {
        return collect(static::getPluginPages())
            ->sortKeys()
            ->map(function ($plugin, $key) {
                return Section::make($key)
                    ->collapsible()
                    ->persistCollapsed()
                    ->schema([
                        Grid::make()
                            ->schema(function () use ($plugin, $key) {
                                $options = collect($plugin)
                                    ->flatMap(fn ($page) => $page['permissions'])
                                    ->toArray();

                                return [
                                    static::getCheckboxListFormComponent(
                                        name: $key.'_pages_tab',
                                        options: $options,
                                    ),
                                ];
                            }),
                    ]);
            })
            ->values()
            ->toArray();
    }

    public static function getPluginWidgetEntitiesSchema(): ?array
    {
        return collect(static::getPluginWidgets())
            ->sortKeys()
            ->map(function ($plugin, $key) {
                return Section::make($key)
                    ->collapsible()
                    ->persistCollapsed()
                    ->schema([
                        Grid::make()
                            ->schema(function () use ($plugin, $key) {
                                $options = collect($plugin)
                                    ->flatMap(fn ($page) => $page['permissions'])
                                    ->toArray();

                                return [
                                    static::getCheckboxListFormComponent(
                                        name: $key.'_widgets_tab',
                                        options: $options,
                                    ),
                                ];
                            }),
                    ]);
            })
            ->values()
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
                        ->filter(function ($value, $key) use ($record) {
                            return static::getPermissions($record)->contains($key);
                        })
                        ->keys()
                        ->toArray()
                );
            }
        }
    }

    public static function getPermissions($record)
    {
        if (! is_null(static::$permissions)) {
            return static::$permissions;
        }

        return static::$permissions = $record->permissions()->pluck('name');
    }
}
