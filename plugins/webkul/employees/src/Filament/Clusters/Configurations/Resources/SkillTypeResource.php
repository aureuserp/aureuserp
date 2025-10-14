<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\Colors;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages\EditSkillType;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages\ListSkillTypes;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages\ViewSkillType;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\RelationManagers\SkillLevelRelationManager;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\RelationManagers\SkillsRelationManager;
use Webkul\Employee\Models\SkillType;

class SkillTypeResource extends Resource
{
    protected static ?string $model = SkillType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Employee';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/skill-type.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/skill-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/skill-type.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.name'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('Enter skill type name'),
                    Hidden::make('creator_id')
                        ->default(Auth::user()->id),
                    Select::make('color')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.color'))
                        ->options(function () {
                            return collect(Colors::options())->mapWithKeys(function ($value, $key) {
                                return [
                                    $key => '<div class="flex items-center gap-4"><span class="flex h-5 w-5 rounded-full" style="background: rgb(var(--'.$key.'-500))"></span> '.$value.'</span>',
                                ];
                            });
                        })
                        ->native(false)
                        ->allowHtml(),
                    Toggle::make('is_active')
                        ->label(__('employees::filament/clusters/configurations/resources/skill-type.form.sections.fields.status'))
                        ->default(true),
                ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.color'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn (Model $skillType) => '<span class="flex h-5 w-5 rounded-full" style="background: rgb(var(--'.$skillType?->color.'-500))"></span>')
                    ->html()
                    ->sortable(),
                TextColumn::make('skills.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.skills'))
                    ->badge()
                    ->color(fn (Model $skillType) => $skillType?->color)
                    ->searchable(),
                TextColumn::make('skillLevels.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.levels'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.status'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.status')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('skillLevels')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.skill-levels'))
                            ->icon('heroicon-o-bolt')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('skills')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.skills'))
                            ->icon('heroicon-o-bolt')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->groups([
                Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.name'))
                    ->collapsible(),
                Group::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.color'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.created-by'))
                    ->collapsible(),
                Group::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.status'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.empty-state-actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.empty-state-actions.create.notification.body')),
                    )
                    ->after(function ($record) {
                        return redirect(
                            self::getUrl('edit', ['record' => $record])
                        );
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SkillsRelationManager::class,
            SkillLevelRelationManager::class,
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.infolist.sections.entries.name')),
                        TextEntry::make('color')
                            ->placeholder('—')
                            ->html()
                            ->formatStateUsing(fn (Model $skillType) => '<span class="flex h-5 w-5 rounded-full" style="background: rgb(var(--'.$skillType->color.'-500))"></span>')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.infolist.sections.entries.color')),
                        IconEntry::make('is_active')
                            ->boolean()
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.infolist.sections.entries.status')),
                    ])->columns(3)->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSkillTypes::route('/'),
            'view'  => ViewSkillType::route('/{record}'),
            'edit'  => EditSkillType::route('/{record}/edit'),
        ];
    }
}
