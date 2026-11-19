<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Employee\Models\Employee;

class AssetWarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-warehouse.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                TextInput::make('code')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.code'))
                                    ->maxLength(64)
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, Get $get) => $rule->where('company_id', $get('company_id') ?: current_company_id()),
                                    )
                                    ->helperText(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.code-help')),

                                Select::make('custodian_employee_id')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.custodian'))
                                    ->helperText(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.custodian-help'))
                                    ->relationship(
                                        'custodian',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->where('is_active', true)
                                            ->where('company_id', $get('company_id') ?: current_company_id()),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Toggle::make('is_active')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.is-active'))
                                    ->default(true)
                                    ->columnSpanFull(),

                                Textarea::make('note')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.note'))
                                    ->placeholder(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.note-placeholder'))
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-warehouse.form.sections.settings.title'))
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn (): ?int => current_company_id())
                                    ->disabled(fn (?Model $record): bool => static::isReferenced($record))
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, mixed $state) => clear_foreign_company_values($set, $get, [
                                        'custodian_employee_id' => Employee::class,
                                    ], $state)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    /**
     * @var array<int, bool>
     */
    protected static array $referencedCache = [];

    protected static function isReferenced(?Model $record): bool
    {
        if (! $record instanceof AssetWarehouse || ! $record->exists) {
            return false;
        }

        return static::$referencedCache[$record->id] ??= (
            Asset::withoutGlobalScopes()->withTrashed()->where('warehouse_id', $record->id)->exists()
            || AssetMovement::withoutGlobalScopes()->where(fn ($q) => $q->where('from_warehouse_id', $record->id)->orWhere('to_warehouse_id', $record->id))->exists()
        );
    }
}
