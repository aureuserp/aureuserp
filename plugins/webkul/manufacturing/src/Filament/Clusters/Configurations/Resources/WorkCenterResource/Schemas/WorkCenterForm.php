<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset as FormFieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Manufacturing\Models\WorkCenterTag;
use Webkul\Product\Models\Product;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Calendar;

class WorkCenterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                TextInput::make('code')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.code'))
                                    ->maxLength(255)
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.code-placeholder')),

                                Select::make('tags')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.tags'))
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(WorkCenterTag::query()->getModel()->getTable()),
                                                ColorPicker::make('color')
                                                    ->default('#808080')
                                                    ->hexColor()
                                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.color')),
                                            ])
                                            ->columns(2),
                                    ]),

                                Select::make('alternativeWorkCenters')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.alternative-work-centers'))
                                    ->relationship(
                                        'alternativeWorkCenters',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),

                                Select::make('calendar_id')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.calendar'))
                                    ->options(fn (): array => Calendar::withTrashed()->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('company_id')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.general.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabled(fn (): bool => filled(current_company_id()))
                                    ->default(current_company_id()),

                                Textarea::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.description.fields.note'))
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.description.fields.note-placeholder'))
                                    ->rows(6)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.title'))
                            ->schema([
                                Repeater::make('capacities')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.fields.records'))
                                    ->hiddenLabel()
                                    ->relationship('capacities')
                                    ->table([
                                        TableColumn::make('product_id')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.product'))
                                            ->resizable()
                                            ->markAsRequired(),
                                        TableColumn::make('product_uom')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.product-uom'))
                                            ->resizable(),
                                        TableColumn::make('capacity')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.capacity'))
                                            ->resizable(),
                                        TableColumn::make('time_start')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.setup-time'))
                                            ->resizable()
                                            ->wrapHeader(),
                                        TableColumn::make('time_stop')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.cleanup-time'))
                                            ->resizable()
                                            ->wrapHeader(),
                                    ])
                                    ->schema([
                                        Select::make('product_id')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.product'))
                                            ->relationship(
                                                'product',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->wrapOptionLabels(false)
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->wrapOptionLabels(false)
                                            ->disableOptionWhen(function ($label, $value, $state, $component) {
                                                $isDeleted = str_contains($label, ' (Deleted)');

                                                $isDuplicate = false;

                                                if ($component?->getParentRepeater()) {
                                                    $repeater = $component->getParentRepeater();

                                                    $isDuplicate = collect($repeater->getState())
                                                        ->pluck(
                                                            (string) str($component->getStatePath())
                                                                ->after("{$repeater->getStatePath()}.")
                                                                ->after('.'),
                                                        )
                                                        ->flatten()
                                                        ->diff(Arr::wrap($state))
                                                        ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                                        ->contains($value);
                                                }

                                                return $isDeleted || $isDuplicate;
                                            })
                                            ->live()
                                            ->required(),
                                        TextEntry::make('product_uom')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.product-uom'))
                                            ->state(function (Get $get): string {
                                                return Product::query()
                                                    ->with('uom')
                                                    ->find($get('product_id'))
                                                    ?->uom
                                                    ?->name ?? '—';
                                            }),
                                        TextInput::make('capacity')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.capacity'))
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->minValue(0)
                                            ->step('0.0001'),
                                        TextInput::make('time_start')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.setup-time'))
                                            ->default('00:00')
                                            ->rule('regex:/^\d+:\d{2}$/')
                                            ->placeholder('00:00')
                                            ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                                $component->state(format_float_time($state ?? 0, 'minutes'));
                                            })
                                            ->dehydrateStateUsing(fn (?string $state): string => parse_float_time($state, 'minutes')),
                                        TextInput::make('time_stop')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.columns.cleanup-time'))
                                            ->default('00:00')
                                            ->rule('regex:/^\d+:\d{2}$/')
                                            ->placeholder('00:00')
                                            ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                                $component->state(format_float_time($state ?? 0, 'minutes'));
                                            })
                                            ->dehydrateStateUsing(fn (?string $state): string => parse_float_time($state, 'minutes')),
                                    ])
                                    ->addActionLabel(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.specific-capacity.actions.add'))
                                    ->reorderable(false)
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.title'))
                            ->schema([
                                FormFieldset::make(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fieldsets.production-information'))
                                    ->schema([
                                        TextInput::make('time_efficiency')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.time-efficiency'))
                                            ->numeric()
                                            ->default(100)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step('0.01')
                                            ->suffix('%'),

                                        TextInput::make('default_capacity')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.default-capacity'))
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->step('1'),

                                        TextInput::make('oee_target')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.oee-target'))
                                            ->hidden()
                                            ->numeric()
                                            ->default(90)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step('0.01')
                                            ->suffix('%'),

                                        TextInput::make('setup_time')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.setup-time'))
                                            ->default('00:00')
                                            ->rule('regex:/^\d+:\d{2}$/')
                                            ->placeholder('00:00')
                                            ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                                $component->state(format_float_time($state ?? 0, 'minutes'));
                                            })
                                            ->dehydrateStateUsing(fn (?string $state): string => parse_float_time($state, 'minutes'))
                                            ->suffix(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.time-suffix')),

                                        TextInput::make('cleanup_time')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.cleanup-time'))
                                            ->default('00:00')
                                            ->rule('regex:/^\d+:\d{2}$/')
                                            ->placeholder('00:00')
                                            ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                                $component->state(format_float_time($state ?? 0, 'minutes'));
                                            })
                                            ->dehydrateStateUsing(fn (?string $state): string => parse_float_time($state, 'minutes'))
                                            ->suffix(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.time-suffix')),
                                    ])
                                    ->columns(1),

                                FormFieldset::make(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fieldsets.costing-information'))
                                    ->schema([
                                        TextInput::make('costs_per_hour')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.costs-per-hour'))
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->step('0.01')
                                            ->suffix(__('manufacturing::filament/clusters/configurations/resources/work-center.form.sections.information.fields.cost-suffix')),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
