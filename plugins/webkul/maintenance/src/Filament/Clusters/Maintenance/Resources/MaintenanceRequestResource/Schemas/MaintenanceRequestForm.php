<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Maintenance\Enums\MaintenanceRepeatType;
use Webkul\Maintenance\Enums\MaintenanceRepeatUnit;
use Webkul\Maintenance\Enums\MaintenanceRequestType;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Maintenance\Models\Stage;
use Webkul\Maintenance\Models\Team;

class MaintenanceRequestForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        FormProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->required()
                            ->options(fn (): array => Stage::query()->orderBy('sort')->pluck('name', 'id')->all())
                            ->default(Stage::query()->orderBy('sort')->value('id')),

                        Section::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->disabledOn('edit')
                                    ->autofocus()
                                    ->placeholder(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                                Select::make('equipment_id')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.equipment'))
                                    ->relationship(
                                        'equipment',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->withTrashed()
                                            ->where(owned_by_company($get('company_id'))),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                        $equipment = $state ? Equipment::query()->find($state) : null;

                                        $set('category_id', $equipment?->category_id);

                                        $set('requested_at', $equipment?->effective_date?->toDateString() ?? now()->toDateString());

                                        $set('maintenance_team_id', $equipment?->maintenance_team_id ?? Team::query()->where(owned_by_company($equipment?->company_id))->value('id'));

                                        $set('user_id', $equipment?->technician_user_id ?? $equipment?->category?->technician_user_id ?? Auth::id());

                                        $set('company_id', $equipment?->company_id ?? current_company_id());
                                    }),

                                Select::make('category_id')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.category'))
                                    ->relationship(
                                        'category',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->disabled()
                                    ->dehydrated(),

                                DatePicker::make('requested_at')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.requested-at'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.requested-at-hint-tooltip'))
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(now()),

                                Radio::make('maintenance_type')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.maintenance-type'))
                                    ->options(MaintenanceRequestType::class)
                                    ->default(MaintenanceRequestType::CORRECTIVE)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                        if (! static::isPreventiveMaintenanceType($state)) {
                                            $set('recurring_maintenance', false);
                                        }
                                    }),

                                Checkbox::make('recurring_maintenance')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.recurrent'))
                                    ->default(false)
                                    ->live()
                                    ->visible(fn ($get): bool => static::isPreventiveMaintenanceType($get('maintenance_type'))),

                                FusedGroup::make([
                                    TextInput::make('repeat_interval')
                                        ->integer()
                                        ->required()
                                        ->minValue(1),
                                    Select::make('repeat_unit')
                                        ->selectablePlaceholder(false)
                                        ->options(MaintenanceRepeatUnit::class)
                                        ->default(MaintenanceRepeatUnit::WEEK),
                                    Select::make('repeat_type')
                                        ->selectablePlaceholder(false)
                                        ->options(MaintenanceRepeatType::class)
                                        ->default(MaintenanceRepeatType::FOREVER),
                                ])
                                    ->visible(fn ($get): bool => static::isPreventiveMaintenanceType($get('maintenance_type')) && (bool) $get('recurring_maintenance'))
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.repeat-every'))
                                    ->columns(3),
                            ]),

                        Tabs::make('request-tabs')
                            ->tabs([
                                Tab::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.notes.title'))
                                    ->schema([
                                        Textarea::make('description')
                                            ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.notes.fields.description'))
                                            ->placeholder(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.notes.fields.description-placeholder'))
                                            ->rows(8)
                                            ->columnSpanFull(),
                                    ]),

                                Tab::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.title'))
                                    ->schema([
                                        Radio::make('instruction_type')
                                            ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-type'))
                                            ->options([
                                                'pdf'          => __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-type-options.pdf'),
                                                'google_slide' => __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-type-options.google-slide'),
                                                'text'         => __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-type-options.text'),
                                            ])
                                            ->default('text')
                                            ->live(),

                                        FileUpload::make('instruction_pdf')
                                            ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-pdf'))
                                            ->disk('public')
                                            ->directory('maintenance/requests/instructions')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->downloadable()
                                            ->openable()
                                            ->visible(fn ($get): bool => $get('instruction_type') === 'pdf')
                                            ->columnSpanFull(),

                                        TextInput::make('instruction_google_slide')
                                            ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-google-slide'))
                                            ->url()
                                            ->visible(fn ($get): bool => $get('instruction_type') === 'google_slide')
                                            ->columnSpanFull()
                                            ->maxLength(255),

                                        Textarea::make('instruction_text')
                                            ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-text'))
                                            ->placeholder(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.tabs.instructions.fields.instruction-text-placeholder'))
                                            ->visible(fn ($get): bool => $get('instruction_type') === 'text')
                                            ->rows(5)
                                            ->columnSpanFull(),

                                        View::make('maintenance::filament.clusters.maintenance.resources.maintenance-request.instruction-preview')
                                            ->visible(fn (?Model $record): bool => filled($record?->getKey()))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.title'))
                            ->schema([
                                Select::make('maintenance_team_id')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.team'))
                                    ->relationship(
                                        'team',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->withTrashed()
                                            ->where(owned_by_company($get('company_id'))),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn (Get $get) => Team::query()->where(owned_by_company($get('company_id')))->value('id')),

                                Select::make('user_id')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.responsible'))
                                    ->relationship('user', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::id()),

                                DateTimePicker::make('scheduled_at')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.scheduled-at'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.scheduled-at-hint-tooltip'))
                                    ->native(false),

                                TextInput::make('duration')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.duration'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.duration-hint-tooltip'))
                                    ->default('00:00')
                                    ->rule('regex:/^\d+:\d{2}$/')
                                    ->placeholder('00:00')
                                    ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                        $component->state(format_float_time((float) ($state ?: 0), 'hours'));
                                    })
                                    ->dehydrateStateUsing(fn (?string $state): float => (float) parse_float_time($state, 'hours'))
                                    ->suffix(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.duration-suffix')),

                                TextInput::make('priority')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.priority'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(3)
                                    ->default(0),

                                Select::make('company_id')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state, $component): void {
                                        clear_foreign_company_values($set, $get, [
                                            'equipment_id'        => Equipment::class,
                                            'category_id'         => EquipmentCategory::class,
                                            'maintenance_team_id' => Team::class,
                                        ], $state);

                                        if (blank($get('maintenance_team_id'))) {
                                            reapply_company_defaults($component, ['maintenance_team_id']);
                                        }
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ])
            ->columns(3);
    }

    private static function isPreventiveMaintenanceType(mixed $state): bool
    {
        return $state === MaintenanceRequestType::PREVENTIVE
            || $state === MaintenanceRequestType::PREVENTIVE->value;
    }
}
