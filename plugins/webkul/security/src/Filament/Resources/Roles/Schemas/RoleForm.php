<?php

namespace Webkul\Security\Filament\Resources\Roles\Schemas;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class RoleForm
{
    use HasShieldFormComponents;

    public static function configure(Schema $schema): Schema
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

                                TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default([Filament::getTenant()?->id])
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

    public static function getTabFormComponentForResources(): Component
    {
        return RoleResource::shield()->hasSimpleResourcePermissionView()
            ? RoleResource::getTabFormComponentForSimpleResourcePermissionsView()
            : Tab::make('resources')
                ->label(__('filament-shield::filament-shield.resources'))
                ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
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
                                                ? FilamentShield::getLocalizedResourceLabel($entity['fqcn'])
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
}
