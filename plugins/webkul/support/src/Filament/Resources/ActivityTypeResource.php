<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Database\QueryException;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\CreateActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\EditActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\ListActivityTypes;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\ViewActivityType;
use Webkul\Support\Models\ActivityType;

class ActivityTypeResource extends Resource
{
    protected static ?string $model = ActivityType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'settings/activity-types';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $pluginName = 'support';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.form.sections.activity-type-details.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name-tooltip')),
                                        Select::make('category')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.action'))
                                            ->options(ActivityTypeAction::options())
                                            ->live()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('default_user_id')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.default-user'))
                                            ->options(fn () => User::query()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload(),
                                        Textarea::make('summary')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.summary'))
                                            ->columnSpanFull(),
                                        RichEditor::make('default_note')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.note'))
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Section::make(__('support::filament/resources/activity-type.form.sections.delay-information.title'))
                                    ->schema([
                                        TextInput::make('delay_count')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-count'))
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(999999999),
                                        Select::make('delay_unit')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-unit'))
                                            ->required()
                                            ->default(ActivityDelayUnit::MINUTES->value)
                                            ->options(ActivityDelayUnit::options()),
                                        Select::make('delay_from')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form'))
                                            ->required()
                                            ->default(ActivityDelayFrom::PREVIOUS_ACTIVITY->value)
                                            ->options(ActivityDelayFrom::options())
                                            ->helperText(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form-helper-text')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.form.sections.advanced-information.title'))
                                    ->schema([
                                        IconPicker::make('icon')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.icon'))
                                            ->sets(['heroicons', 'fontawesome-solid'])
                                            ->columns(4)
                                            ->gridSearchResults()
                                            ->iconsSearchResults(),
                                        Select::make('decoration_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.decoration-type'))
                                            ->options(ActivityDecorationType::options())
                                            ->native(false),
                                        Select::make('chaining_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.chaining-type'))
                                            ->options(ActivityChainingType::options())
                                            ->default(ActivityChainingType::SUGGEST->value)
                                            ->live()
                                            ->required()
                                            ->native(false)
                                            ->hidden(fn (Get $get) => $get('category') === 'upload_file'),
                                        Select::make('activity_type_suggestions')
                                            ->multiple()
                                            ->relationship('suggestedActivityTypes', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.suggest'))
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'trigger' || $get('category') === 'upload_file'),
                                        Select::make('triggered_next_type_id')
                                            ->relationship('triggeredNextType', 'name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.trigger'))
                                            ->native(false)
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'suggest' && $get('category') !== 'upload_file'),
                                    ]),
                                Section::make(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.status'))
                                            ->default(false),
                                        Toggle::make('keep_done')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.keep-done-activities'))
                                            ->default(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('support::filament/resources/activity-type.table.columns.name'))
                    ->sortable(),
                TextColumn::make('summary')
                    ->label(__('support::filament/resources/activity-type.table.columns.summary'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('delay_count')
                    ->label(__('support::filament/resources/activity-type.table.columns.planned-in'))
                    ->formatStateUsing(function ($record) {
                        return $record->delay_count ? "{$record->delay_count} {$record->delay_unit}" : 'No Delay';
                    }),
                TextColumn::make('delay_from')
                    ->label(__('support::filament/resources/activity-type.table.columns.type'))
                    ->formatStateUsing(fn ($state) => ActivityDelayFrom::options()[$state])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->label(__('support::filament/resources/activity-type.table.columns.action'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => ActivityTypeAction::options()[$state])
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.columns.status'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/activity-type.table.columns.created-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/activity-type.table.columns.updated-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('support::filament/resources/activity-type.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('category')
                    ->label(__('support::filament/resources/activity-type.table.groups.action-category'))
                    ->collapsible(),
                Tables\Grouping\Group::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('delay_count')
                    ->label(__('support::filament/resources/activity-type.table.groups.delay-count'))
                    ->collapsible(),
                Tables\Grouping\Group::make('delay_unit')
                    ->label(__('support::filament/resources/activity-type.table.groups.delay-unit'))
                    ->collapsible(),
                Tables\Grouping\Group::make('delay_from')
                    ->label(__('support::filament/resources/activity-type.table.groups.delay-source'))
                    ->collapsible(),
                Tables\Grouping\Group::make('chaining_type')
                    ->label(__('support::filament/resources/activity-type.table.groups.chaining-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('decoration_type')
                    ->label(__('support::filament/resources/activity-type.table.groups.decoration-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('defaultUser.name')
                    ->label(__('support::filament/resources/activity-type.table.groups.default-user'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('support::filament/resources/activity-type.table.groups.creation-date'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('support::filament/resources/activity-type.table.groups.last-update'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->multiple()
                    ->label(__('support::filament/resources/activity-type.table.filters.action'))
                    ->options(ActivityTypeAction::options()),
                TernaryFilter::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.filters.status')),
                Filter::make('has_delay')
                    ->label(__('support::filament/resources/activity-type.table.filters.has-delay'))
                    ->query(fn ($query) => $query->whereNotNull('delay_count')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.restore.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (ActivityType $record) {
                            try {
                                $record->forceDelete();

                                Notification::make()
                                    ->success()
                                    ->title(__('support::filament/resources/activity-type.table.actions.force-delete.notification.success.title'))
                                    ->body(__('support::filament/resources/activity-type.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('support::filament/resources/activity-type.table.actions.force-delete.notification.error.title'))
                                    ->body(__('support::filament/resources/activity-type.table.actions.force-delete.notification.error.body'))
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
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->where('plugin', static::$pluginName))
            ->reorderable('sort');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-clipboard-document-list')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.name')),
                                        TextEntry::make('category')
                                            ->icon('heroicon-o-tag')
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityTypeAction::options()[$state])
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.action')),
                                        TextEntry::make('defaultUser.name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.default-user')),
                                        TextEntry::make('plugin')
                                            ->icon('heroicon-o-puzzle-piece')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.plugin')),
                                    ])->columns(2),
                                Section::make(__('support::filament/resources/activity-type.infolist.sections.delay-information.title'))
                                    ->schema([
                                        TextEntry::make('summary')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.summary'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('default_note')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.note'))
                                            ->html()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make(__('support::filament/resources/activity-type.infolist.sections.delay-information.title'))
                                    ->schema([
                                        TextEntry::make('delay_count')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->numeric()
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.delay-information.entries.delay-count')),
                                        TextEntry::make('delay_unit')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityDelayUnit::options()[$state])
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.delay-information.entries.delay-unit')),
                                        TextEntry::make('delay_from')
                                            ->icon('heroicon-o-arrow-right')
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityDelayFrom::options()[$state])
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.delay-information.entries.delay-form')),
                                    ])->columns(2),
                            ])->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.infolist.sections.advanced-information.title'))
                                    ->schema([
                                        TextEntry::make('icon')
                                            ->icon(fn ($record) => $record->icon)
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.icon')),
                                        TextEntry::make('decoration_type')
                                            ->icon('heroicon-o-paint-brush')
                                            ->formatStateUsing(fn ($state) => ActivityDecorationType::options()[$state])
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.decoration-type')),
                                        TextEntry::make('chaining_type')
                                            ->icon('heroicon-o-link')
                                            ->formatStateUsing(fn ($state) => ActivityChainingType::options()[$state])
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.chaining-type')),
                                        TextEntry::make('suggestedActivityTypes.name')
                                            ->icon('heroicon-o-list-bullet')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.suggest'))
                                            ->listWithLineBreaks(),
                                        TextEntry::make('triggeredNextType.name')
                                            ->icon('heroicon-o-forward')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.trigger')),
                                    ]),
                                Section::make(__('support::filament/resources/activity-type.infolist.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        IconEntry::make('is_active')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.status-and-configuration-information.entries.status')),
                                        IconEntry::make('keep_done')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.status-and-configuration-information.entries.keep-done-activities')),
                                    ]),
                            ])->columnSpan(1),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityTypes::route('/'),
            'create' => CreateActivityType::route('/create'),
            'view'   => ViewActivityType::route('/{record}'),
            'edit'   => EditActivityType::route('/{record}/edit'),
        ];
    }
}
