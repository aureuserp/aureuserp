<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\CreateStage;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\EditStage;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\ListStages;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\ViewStages;
use Webkul\Recruitment\Models\Stage;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/stage.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/stage.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/stage.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Group::make()
                ->schema([
                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Section::make(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.general-information.title'))
                                        ->schema([
                                            Hidden::make('creator_id')
                                                ->default(Auth::id())
                                                ->required(),
                                            TextInput::make('name')
                                                ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.general-information.fields.stage-name'))
                                                ->required(),
                                            RichEditor::make('requirements')
                                                ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.general-information.fields.requirements'))
                                                ->maxLength(255)
                                                ->columnSpanFull(),
                                        ])->columns(2),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),
                    Group::make()
                        ->schema([
                            Section::make(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.title'))
                                ->description(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.description'))
                                ->schema([
                                    TextInput::make('legend_normal')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.gray-label'))
                                        ->required()
                                        ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.gray-label-tooltip'))
                                        ->default('In Progress'),
                                    TextInput::make('legend_blocked')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.red-label'))
                                        ->required()
                                        ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.red-label-tooltip'))
                                        ->hintColor('danger')
                                        ->default('Blocked'),
                                    TextInput::make('legend_done')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.green-label'))
                                        ->required()
                                        ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.green-label-tooltip'))
                                        ->hintColor('success')
                                        ->default('Ready for Next Stage'),
                                ]),
                            Section::make(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.title'))
                                ->schema([
                                    Select::make('recruitments_job_positions')
                                        ->relationship('jobs', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.job-positions')),
                                    Toggle::make('fold')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.folded')),
                                    Toggle::make('hired_stage')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.hired-stage')),
                                    Toggle::make('is_default')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.default-stage')),
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
                TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jobs.name')
                    ->placeholder('-')
                    ->badge()
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.job-positions')),
                IconColumn::make('is_default')
                    ->boolean()
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.default-stage')),
                IconColumn::make('fold')
                    ->boolean()
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.folded')),
                IconColumn::make('hired_stage')
                    ->boolean()
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.hired-stage')),
                TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.name'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('jobs')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.job-position'))
                            ->multiple()
                            ->icon('heroicon-o-briefcase')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        BooleanConstraint::make('fold')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.folded'))
                            ->icon('heroicon-o-briefcase'),
                        RelationshipConstraint::make('legend_normal')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.gray-label')),
                        RelationshipConstraint::make('legend_blocked')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.red-label')),
                        RelationshipConstraint::make('legend_done')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.green-label')),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.created-by'))
                            ->multiple()
                            ->icon('heroicon-o-user')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.stage-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('fold')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.folded'))
                    ->collapsible(),
                Tables\Grouping\Group::make('legend_normal')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.gray-label'))
                    ->collapsible(),
                Tables\Grouping\Group::make('legend_blocked')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.red-label'))
                    ->collapsible(),
                Tables\Grouping\Group::make('legend_done')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.green-label'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.empty-state-actions.create.label'))
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (Stage $record, DeleteAction $action) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('recruitments::filament/clusters/configurations/resources/stage.table.actions.delete.notification.error.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/stage.table.actions.delete.notification.error.body'))
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/stage.table.actions.delete.notification.success.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/stage.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'Desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-cube')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.entries.stage-name')),
                                        TextEntry::make('sort')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-bars-3-bottom-right')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.entries.sort')),
                                        TextEntry::make('requirements')
                                            ->icon('heroicon-o-document-text')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.general-information.entries.requirements'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                    ])->columns(2),
                                Section::make(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.title'))
                                    ->schema([
                                        TextEntry::make('jobs.name')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.job-positions'))
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->placeholder('—'),
                                        IconEntry::make('fold')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.folded')),
                                        IconEntry::make('hired_stage')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.hired-stage')),
                                        IconEntry::make('is_default')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.additional-information.entries.default-stage')),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.title'))
                                ->schema([
                                    TextEntry::make('legend_normal')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.entries.gray-label'))
                                        ->icon('heroicon-o-information-circle')
                                        ->placeholder('—'),
                                    TextEntry::make('legend_blocked')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.entries.red-label'))
                                        ->icon('heroicon-o-x-circle')
                                        ->iconColor('danger')
                                        ->placeholder('—'),
                                    TextEntry::make('legend_done')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.infolist.sections.tooltips.entries.green-label'))
                                        ->icon('heroicon-o-check-circle')
                                        ->iconColor('success')
                                        ->placeholder('—'),
                                ]),

                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListStages::route('/'),
            'create' => CreateStage::route('/create'),
            'edit'   => EditStage::route('/{record}/edit'),
            'view'   => ViewStages::route('/{record}'),
        ];
    }
}
