<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Employee\Models\Employee;

class AssetSiteForm
{
    public static function configure(Schema $schema, ?AssetSiteUsage $defaultUsage = null): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-site.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                Select::make('parent_id')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.parent'))
                                    ->relationship(
                                        'parent',
                                        'full_name',
                                        modifyQueryUsing: fn (Builder $query, Get $get, ?Model $record) => $query
                                            ->where('company_id', $get('company_id') ?: current_company_id())
                                            ->when(
                                                $record instanceof AssetSite && $record->exists,
                                                fn (Builder $q) => $q
                                                    ->whereKeyNot($record->getKey())
                                                    ->where('parent_path', 'not like', ($record->parent_path ?: '/').$record->id.'/%'),
                                            )
                                            ->orderBy('full_name'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                                        if (filled($state)) {
                                            $parent = AssetSite::withoutGlobalScopes()->find($state);

                                            if ($parent) {
                                                $set('usage_type', $parent->usage_type->value);

                                                if (filled($parent->site_type_id)) {
                                                    $set('site_type_id', $parent->site_type_id);
                                                }
                                            }
                                        }
                                    }),

                                Select::make('usage_type')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.usage-type'))
                                    ->helperText(__('assets::filament/resources/asset-site.form.sections.general.fields.usage-type-help'))
                                    ->options(AssetSiteUsage::class)
                                    ->default($defaultUsage ?? AssetSiteUsage::AssetLocation)
                                    ->required()
                                    ->disabled(fn (Get $get): bool => filled($get('parent_id')))
                                    ->dehydrated(),

                                TextInput::make('code')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.code'))
                                    ->maxLength(64)
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, Get $get) => $rule->where('company_id', $get('company_id') ?: current_company_id()),
                                    )
                                    ->helperText(__('assets::filament/resources/asset-site.form.sections.general.fields.code-help')),

                                Select::make('site_type_id')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.type'))
                                    ->relationship(
                                        'siteType',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm(static::getSiteTypeSchema())
                                    ->createOptionAction(fn ($action, Get $get) => $action
                                        ->visible(fn (): bool => AssetSiteResource::canCreate())
                                        ->authorize(fn (): bool => AssetSiteResource::canCreate())
                                        ->mutateDataUsing(fn (array $data): array => [
                                            ...$data,
                                            'company_id' => $get('company_id') ?: current_company_id(),
                                        ]))
                                    ->editOptionForm(static::getSiteTypeSchema())
                                    ->editOptionAction(fn ($action, Select $component) => $action
                                        ->visible(fn (): bool => filled($component->getState()) && (bool) auth()->user()?->can('update_asset_asset::site'))
                                        ->authorize(fn (): bool => filled($component->getState()) && (bool) auth()->user()?->can('update_asset_asset::site'))),

                                Select::make('default_custodian_employee_id')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.settings.fields.default-custodian'))
                                    ->helperText(__('assets::filament/resources/asset-site.form.sections.settings.fields.default-custodian-help'))
                                    ->relationship(
                                        'defaultCustodian',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->where('is_active', true)
                                            ->where('company_id', $get('company_id') ?: current_company_id()),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Toggle::make('is_active')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.settings.fields.is-active'))
                                    ->default(true),

                                Textarea::make('note')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.note'))
                                    ->placeholder(__('assets::filament/resources/asset-site.form.sections.general.fields.note-placeholder'))
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-site.form.sections.settings.title'))
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn (): ?int => current_company_id())
                                    ->disabled(fn (?Model $record): bool => static::isReferenced($record))
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, mixed $state) => clear_foreign_company_values($set, $get, [
                                        'parent_id'                     => AssetSite::class,
                                        'site_type_id'                  => AssetSiteType::class,
                                        'default_custodian_employee_id' => Employee::class,
                                    ], $state)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    /**
     * @return array<int, mixed>
     */
    public static function getSiteTypeSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.type-name'))
                ->required()
                ->maxLength(255),

            IconPicker::make('icon')
                ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.type-icon'))
                ->sets(['heroicons'])
                ->columns(4)
                ->gridSearchResults()
                ->iconsSearchResults()
                ->default('heroicon-m-map-pin'),

            ColorPicker::make('color')
                ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.type-color'))
                ->hexColor()
                ->default('#3b82f6'),
        ];
    }

    /**
     * @var array<int, bool>
     */
    protected static array $referencedCache = [];

    protected static function isReferenced(?Model $record): bool
    {
        if (! $record instanceof AssetSite || ! $record->exists) {
            return false;
        }

        return static::$referencedCache[$record->id] ??= (
            AssetSite::withoutGlobalScopes()->withTrashed()->where('parent_id', $record->id)->exists()
            || Asset::withoutGlobalScopes()->withTrashed()->whereIn('current_site_id', AssetSite::subtreeIds($record->id))->exists()
            || AssetMovement::withoutGlobalScopes()->where(function (Builder $q) use ($record): void {
                $subtree = AssetSite::subtreeIds($record->id);
                $q->whereIn('from_site_id', $subtree)->orWhereIn('to_site_id', $subtree);
            })->exists()
        );
    }
}
