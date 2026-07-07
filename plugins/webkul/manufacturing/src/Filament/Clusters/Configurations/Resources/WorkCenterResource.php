<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources;

use BackedEnum;
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
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn as InfolistTableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset as FormFieldset;
use Filament\Schemas\Components\Fieldset as InfolistFieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Filament\Clusters\Configurations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\CreateWorkCenter;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\EditWorkCenter;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\ListWorkCenters;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\ManageOperations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\ViewWorkCenter;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkCenterTag;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Product\Models\Product;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Calendar;

class WorkCenterResource extends Resource
{
    protected static ?string $model = WorkCenter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/work-center.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/work-center.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/work-center.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
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
                                    ->relationship('alternativeWorkCenters', 'name')
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
                                    ->disabled(fn (): bool => filled(Auth::user()?->default_company_id))
                                    ->default(Auth::user()?->default_company_id),

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
                                            ->relationship('product', 'name')
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

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.code'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('calendar.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.calendar'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('working_state')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.working-state'))
                    ->badge(),
                TextColumn::make('default_capacity')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.default-capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_efficiency')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.time-efficiency'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('costs_per_hour')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.costs-per-hour'))
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('working_state')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.filters.working-state'))
                    ->options(WorkCenterWorkingState::options()),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.groups.company'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (WorkCenter $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (WorkCenter $record): bool => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.restore.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.delete.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (WorkCenter $record, ForceDeleteAction $action): void {
                        try {
                            $record->forceDelete();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.error.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.error.body'))
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.success.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.restore.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.delete.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                $records->each(fn (Model $record): ?bool => $record->forceDelete());
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->columnSpanFull(),
                                TextEntry::make('code')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.code'))
                                    ->placeholder('—'),
                                TextEntry::make('working_state')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.working-state'))
                                    ->badge(),
                                TextEntry::make('tags.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->separator(',')
                                    ->placeholder('—'),
                                TextEntry::make('alternativeWorkCenters.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.alternative-work-centers'))
                                    ->listWithLineBreaks()
                                    ->placeholder('—'),
                                TextEntry::make('calendar.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.calendar'))
                                    ->placeholder('—'),
                                TextEntry::make('company.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.company'))
                                    ->placeholder('—'),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.description.title'))
                            ->schema([
                                TextEntry::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.description.entries.note'))
                                    ->placeholder('—'),
                            ])
                            ->visible(fn (WorkCenter $record): bool => filled($record->note)),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.title'))
                            ->schema([
                                RepeatableEntry::make('capacities')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.product')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.product-uom')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.capacity')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.setup-time')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.cleanup-time')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product.uom.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('capacity')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 1), 2))
                                            ->placeholder('—'),
                                        TextEntry::make('time_start')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes'))
                                            ->placeholder('—'),
                                        TextEntry::make('time_stop')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes'))
                                            ->placeholder('—'),
                                    ]),
                            ])
                            ->visible(fn (WorkCenter $record): bool => $record->capacities()->exists()),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.title'))
                            ->schema([
                                InfolistFieldset::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.fieldsets.production-information'))
                                    ->schema([
                                        TextEntry::make('time_efficiency')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.time-efficiency'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 100), 2).' %')
                                            ->placeholder('—'),
                                        TextEntry::make('default_capacity')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.default-capacity'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 1), 2))
                                            ->placeholder('—'),
                                        TextEntry::make('oee_target')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.oee-target'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 90), 2).' %')
                                            ->placeholder('—'),
                                        TextEntry::make('setup_time')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.setup-time'))
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes').' '.__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.time-suffix'))
                                            ->placeholder('—'),
                                        TextEntry::make('cleanup_time')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.cleanup-time'))
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes').' '.__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.time-suffix'))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(1),

                                InfolistFieldset::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.fieldsets.costing-information'))
                                    ->schema([
                                        TextEntry::make('costs_per_hour')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.costs-per-hour'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 0), 2).' '.__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.cost-suffix'))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(1),
                            ]),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.entries.created-by'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('created_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('updated_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListWorkCenters::route('/'),
            'create'     => CreateWorkCenter::route('/create'),
            'view'       => ViewWorkCenter::route('/{record}'),
            'edit'       => EditWorkCenter::route('/{record}/edit'),
            'operations' => ManageOperations::route('/{record}/operations'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewWorkCenter::class,
            EditWorkCenter::class,
            ManageOperations::class,
        ]);
    }
}
