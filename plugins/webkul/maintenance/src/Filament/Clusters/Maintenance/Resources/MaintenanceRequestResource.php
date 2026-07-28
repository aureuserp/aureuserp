<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources;

use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Maintenance\Enums\MaintenanceRepeatType;
use Webkul\Maintenance\Enums\MaintenanceRepeatUnit;
use Webkul\Maintenance\Enums\MaintenanceRequestType;
use Webkul\Maintenance\Filament\Clusters\Maintenance;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\CreateMaintenanceRequest;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\EditMaintenanceRequest;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\ListMaintenanceRequests;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\ViewMaintenanceRequest;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\MaintenanceRequest;
use Webkul\Maintenance\Models\Stage;
use Webkul\Maintenance\Models\Team;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class MaintenanceRequestResource extends Resource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = MaintenanceRequest::class;

    protected static ?string $cluster = Maintenance::class;

    protected static ?string $slug = 'requests';

    protected static ?int $navigationSort = 0;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/maintenance-request.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/maintenance/resources/maintenance-request.navigation.title');
    }

    public static function form(Schema $schema): Schema
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
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
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

                                        $set('maintenance_team_id', $equipment?->maintenance_team_id ?? Team::query()->value('id'));

                                        $set('user_id', $equipment?->technician_user_id ?? $equipment?->category?->technician_user_id ?? Auth::id());

                                        $set('company_id', $equipment?->company_id ?? Auth::user()?->default_company_id);
                                    }),

                                Select::make('category_id')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.category'))
                                    ->relationship('category', 'name')
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
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(Team::query()->value('id')),

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
                                    ->default(Auth::user()?->default_company_id),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.creator'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.technician'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.category'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('stage.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.stage'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.company'))
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->groups([
                TableGroup::make('stage.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.stage')),
                TableGroup::make('user.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.assigned-to')),
                TableGroup::make('category.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.category')),
                TableGroup::make('creator.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.created-by')),
            ])
            ->recordActions([
                ActivityTableAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.restore.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (MaintenanceRequest $record): void {
                            try {
                                $record->forceDelete();

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.restore.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        InfolistProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->options(fn (): array => Stage::query()->orderBy('sort')->pluck('name', 'id')->all()),

                        Section::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('equipment.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.equipment'))
                                    ->placeholder('—'),

                                TextEntry::make('category.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.category'))
                                    ->placeholder('—'),

                                TextEntry::make('requested_at')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.requested-at'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('maintenance_type')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.maintenance-type'))
                                    // ->formatStateUsing(fn (?string $state): string => $state ? __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.maintenance-type-options.'.$state) : '—')
                                    ->placeholder('—'),

                                ViewEntry::make('instruction_preview')
                                    ->hiddenLabel()
                                    ->view('maintenance::filament.clusters.maintenance.resources.maintenance-request.instruction-preview'),

                                TextEntry::make('description')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.description'))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('team.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.team'))
                                    ->placeholder('—'),

                                TextEntry::make('user.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.responsible'))
                                    ->placeholder('—'),

                                TextEntry::make('scheduled_at')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.scheduled-at'))
                                    ->dateTime()
                                    ->placeholder('—'),

                                TextEntry::make('duration')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'hours'))
                                    ->suffix(' '.__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.duration-suffix'))
                                    ->placeholder('0'),

                                TextEntry::make('priority')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.priority'))
                                    ->placeholder('0'),

                                TextEntry::make('company.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.company'))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMaintenanceRequests::route('/'),
            'create' => CreateMaintenanceRequest::route('/create'),
            'view'   => ViewMaintenanceRequest::route('/{record}'),
            'edit'   => EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewMaintenanceRequest::class,
            EditMaintenanceRequest::class,
        ]);
    }

    protected static function isPreventiveMaintenanceType(mixed $state): bool
    {
        return $state === MaintenanceRequestType::PREVENTIVE
            || $state === MaintenanceRequestType::PREVENTIVE->value;
    }
}
