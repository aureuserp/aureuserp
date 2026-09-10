<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Maintenance\Models\Team;

class EquipmentForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                                Textarea::make('note')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.general.fields.note'))
                                    ->rows(8),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.form.sections.product-information.title'))
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.partner'))
                                    ->relationship('partner', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('partner_ref')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.partner-ref'))
                                    ->maxLength(255),

                                TextInput::make('model')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.model'))
                                    ->maxLength(255),

                                TextInput::make('serial_no')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.serial-no'))
                                    ->maxLength(255),

                                DatePicker::make('effective_date')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.effective-date'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('maintenance::filament/resources/equipment.form.sections.product-information.fields.effective-date-hint-tooltip'))
                                    ->native(false)
                                    ->required()
                                    ->default(now()),

                                TextInput::make('cost')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.cost'))
                                    ->numeric()
                                    ->default(0),

                                DatePicker::make('warranty_date')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.warranty-date'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.form.sections.settings.title'))
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.category'))
                                    ->relationship(
                                        'category',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                        $set('technician_user_id', EquipmentCategory::find($state)?->technician_user_id);
                                    }),

                                Select::make('maintenance_team_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.team'))
                                    ->relationship(
                                        'team',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->withTrashed()
                                            ->where(owned_by_company($get('company_id'))),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                Select::make('company_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                        'category_id'          => EquipmentCategory::class,
                                        'maintenance_team_id'  => Team::class,
                                    ], $state)),

                                Select::make('technician_user_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.technician'))
                                    ->relationship('technician', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                Select::make('owner_user_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.owner'))
                                    ->relationship('owner', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('location')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.location'))
                                    ->maxLength(255),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.form.sections.maintenance.title'))
                            ->schema([
                                TextInput::make('expected_mtbf')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.maintenance.fields.expected-mtbf'))
                                    ->numeric()
                                    ->suffix(__('maintenance::filament/resources/equipment.form.sections.maintenance.suffixes.days')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ])
            ->columns(3);
    }
}
