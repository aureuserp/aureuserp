<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages;
use Webkul\Support\Models\ActivityType;

class ActivityTypeResource extends Resource
{
    protected static ?string $model = ActivityType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'settings/activity-types';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('support::filament/resources/activity-type.form.sections.activity-type-details.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name-tooltip')),
                                        Forms\Components\Select::make('category')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.action'))
                                            ->options(ActivityTypeAction::options())
                                            ->live()
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('default_user_id')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.default-user'))
                                            ->options(fn () => User::query()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Textarea::make('summary')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.summary'))
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('default_note')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.note'))
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Forms\Components\Section::make(__('support::filament/resources/activity-type.form.sections.delay-information.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('delay_count')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-count'))
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(999999999),
                                        Forms\Components\Select::make('delay_unit')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-unit'))
                                            ->required()
                                            ->default(ActivityDelayUnit::MINUTES->value)
                                            ->options(ActivityDelayUnit::options()),
                                        Forms\Components\Select::make('delay_from')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form'))
                                            ->required()
                                            ->default(ActivityDelayFrom::PREVIOUS_ACTIVITY->value)
                                            ->options(ActivityDelayFrom::options())
                                            ->helperText(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form-helper-text')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('support::filament/resources/activity-type.form.sections.advanced-information.title'))
                                    ->schema([
                                        \Guava\FilamentIconPicker\Forms\IconPicker::make('icon')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.icon'))
                                            ->sets(['heroicons', 'fontawesome-solid'])
                                            ->columns(4)
                                            ->preload()
                                            ->optionsLimit(50),
                                        Forms\Components\Select::make('decoration_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.decoration-type'))
                                            ->options(ActivityDecorationType::options())
                                            ->native(false),
                                        Forms\Components\Select::make('chaining_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.chaining-type'))
                                            ->options(ActivityChainingType::options())
                                            ->default(ActivityChainingType::SUGGEST->value)
                                            ->live()
                                            ->required()
                                            ->native(false)
                                            ->hidden(fn (Get $get) => $get('category') === 'upload_file'),
                                        Forms\Components\Select::make('activity_type_suggestions')
                                            ->multiple()
                                            ->relationship('suggestedActivityTypes', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.suggest'))
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'trigger' || $get('category') === 'upload_file'),
                                        Forms\Components\Select::make('triggered_next_type_id')
                                            ->relationship('triggeredNextType', 'name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.trigger'))
                                            ->native(false)
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'suggest' && $get('category') !== 'upload_file'),
                                    ]),
                                Forms\Components\Section::make(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.status'))
                                            ->default(false),
                                        Forms\Components\Toggle::make('keep_done')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.keep-done-activities'))
                                            ->default(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('support::filament/resources/activity-type.table.columns.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('summary')
                    ->label(__('support::filament/resources/activity-type.table.columns.summary'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delay_count')
                    ->label(__('support::filament/resources/activity-type.table.columns.planned-in'))
                    ->formatStateUsing(function ($record) {
                        return $record->delay_count ? "{$record->delay_count} {$record->delay_unit}" : 'No Delay';
                    }),
                Tables\Columns\TextColumn::make('delay_from')
                    ->label(__('support::filament/resources/activity-type.table.columns.type'))
                    ->formatStateUsing(fn ($state) => ActivityDelayFrom::options()[$state])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->label(__('support::filament/resources/activity-type.table.columns.action'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => ActivityTypeAction::options()[$state])
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.columns.status'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('support::filament/resources/activity-type.table.columns.created-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                Tables\Filters\SelectFilter::make('category')
                    ->multiple()
                    ->label(__('support::filament/resources/activity-type.table.filters.action'))
                    ->options(ActivityTypeAction::options()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('support::filament/resources/activity-type.table.filters.status')),
                Tables\Filters\Filter::make('has_delay')
                    ->label(__('support::filament/resources/activity-type.table.filters.has-delay'))
                    ->query(fn ($query) => $query->whereNotNull('delay_count')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.restore.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.actions.force-delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/activity-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('support::filament/resources/activity-type.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('sort');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-clipboard-document-list')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.name')),
                                        Infolists\Components\TextEntry::make('category')
                                            ->icon('heroicon-o-tag')
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityTypeAction::options()[$state])
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.action')),
                                        Infolists\Components\TextEntry::make('defaultUser.name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.default-user')),
                                        Infolists\Components\TextEntry::make('plugin')
                                            ->icon('heroicon-o-puzzle-piece')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.plugin')),
                                    ])->columns(2),
                                Infolists\Components\Section::make(__('support::filament/resources/activity-type.infolist.sections.delay-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('summary')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.summary'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('default_note')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.activity-type-details.entries.note'))
                                            ->html()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                                Infolists\Components\Section::make(__('support::filament/resources/activity-type.infolist.sections.delay-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('delay_count')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->numeric()
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.delay-information.entries.delay-count')),
                                        Infolists\Components\TextEntry::make('delay_unit')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityDelayUnit::options()[$state])
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.delay-information.entries.delay-unit')),
                                        Infolists\Components\TextEntry::make('delay_from')
                                            ->icon('heroicon-o-arrow-right')
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => ActivityDelayFrom::options()[$state])
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.delay-information.entries.delay-form')),
                                    ])->columns(2),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('support::filament/resources/activity-type.infolist.sections.advanced-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('icon')
                                            ->icon(fn ($record) => $record->icon)
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.icon')),
                                        Infolists\Components\TextEntry::make('decoration_type')
                                            ->icon('heroicon-o-paint-brush')
                                            ->formatStateUsing(fn ($state) => ActivityDecorationType::options()[$state])
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.decoration-type')),
                                        Infolists\Components\TextEntry::make('chaining_type')
                                            ->icon('heroicon-o-link')
                                            ->formatStateUsing(fn ($state) => ActivityChainingType::options()[$state])
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.chaining-type')),
                                        Infolists\Components\TextEntry::make('suggestedActivityTypes.name')
                                            ->icon('heroicon-o-list-bullet')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.suggest'))
                                            ->listWithLineBreaks(),
                                        Infolists\Components\TextEntry::make('triggeredNextType.name')
                                            ->icon('heroicon-o-forward')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.advanced-information.entries.trigger')),
                                    ]),
                                Infolists\Components\Section::make(__('support::filament/resources/activity-type.infolist.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Infolists\Components\IconEntry::make('is_active')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.status-and-configuration-information.entries.status')),
                                        Infolists\Components\IconEntry::make('keep_done')
                                            ->label(__('support::filament/resources/activity-type.infolist.sections.status-and-configuration-information.entries.keep-done-activities')),
                                    ]),
                            ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActivityTypes::route('/'),
            'create' => Pages\CreateActivityType::route('/create'),
            'view'   => Pages\ViewActivityType::route('/{record}'),
            'edit'   => Pages\EditActivityType::route('/{record}/edit'),
        ];
    }
}
