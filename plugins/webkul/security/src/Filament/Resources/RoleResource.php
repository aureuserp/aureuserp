<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as RolesRoleResource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Security\Filament\Resources\RoleResource\Pages\CreateRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\EditRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\ListRoles;
use Webkul\Security\Filament\Resources\RoleResource\Pages\ViewRole;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

class RoleResource extends RolesRoleResource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static $permissionsCollection;

    public static $permissions = null;

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return null;
    }

    public static function getActiveNavigationIcon(): BackedEnum | Htmlable | null | string
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
                                        modifyRuleUsing: fn(Unique $rule): Unique => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                    )
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default(Filament::getTenant()?->id)
                                    ->options(fn(): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->hidden(fn(): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn(): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled())),
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
                    ->formatStateUsing(fn($state): string => Str::headline($state))
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
                    ->hidden(fn(Model $record) => $record->name == config('filament-shield.panel_user.name')),
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

    public static function getPluginResourceEntitiesSchema(): ?array
    {
        return collect(static::getPluginResources())
            ->sortKeys()
            ->map(function ($plugin, $key) {
                return Section::make($key)
                    ->collapsible()
                    ->schema([
                        Grid::make()
                            ->schema(function () use ($plugin) {
                                return collect($plugin)
                                    ->map(function ($entity) {
                                        $fieldsetLabel = strval(
                                            static::shield()->hasLocalizedPermissionLabels()
                                                ? FilamentShield::getLocalizedResourceLabel($entity['resourceFqcn'])
                                                : $entity['model']
                                        );

                                        return Fieldset::make($fieldsetLabel)
                                            ->schema([
                                                static::getCheckBoxListComponentForResource($entity)
                                                    ->hiddenLabel(),
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

    public static function setPermissionStateForRecordPermissions(Component $component, string $operation, array $permissions, ?Model $record): void
    {
        if (in_array($operation, ['edit', 'view'])) {
            if (blank($record)) {
                return;
            }

            if ($component->isVisible() && count($permissions) > 0) {
                $component->state(
                    collect($permissions)
                        ->filter(function ($value, $key) use($record) {
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
