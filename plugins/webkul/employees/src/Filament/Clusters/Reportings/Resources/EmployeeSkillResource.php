<?php

namespace Webkul\Employee\Filament\Clusters\Reportings\Resources;

use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Reportings;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkillResource\Pages;
use Webkul\Employee\Models\EmployeeSkill;
use Webkul\Support\Filament\Tables as CustomTables;

class EmployeeSkillResource extends Resource
{
    protected static ?string $model = EmployeeSkill::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $pluralModelLabel = 'Skills';

    protected static ?string $cluster = Reportings::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/reportings/resources/employee-skill.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/reportings/resources/employee-skill.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skill.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.skill'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skillLevel.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.skill-level'))
                    ->badge()
                    ->color(fn ($record) => match ($record->skillLevel->name) {
                        'Beginner'     => 'gray',
                        'Intermediate' => 'warning',
                        'Advanced'     => 'success',
                        'Expert'       => 'primary',
                        default        => 'secondary'
                    }),
                CustomTables\Columns\ProgressBarEntry::make('skill_level_percentage')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.proficiency'))
                    ->getStateUsing(fn ($record) => $record->skillLevel->level ?? 0),
                Tables\Columns\TextColumn::make('skillType.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.skill-type'))
                    ->badge()
                    ->color('secondary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.created-by'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.user'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('employee.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.groups.employee'))
                    ->collapsible(),
                Tables\Grouping\Group::make('skillType.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.groups.skill-type'))
                    ->collapsible(),
            ])
            ->defaultGroup('employee.name')
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('employee')
                    ->relationship('employee', 'name')
                    ->preload()
                    ->searchable()
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.employee')),
                SelectFilter::make('skill')
                    ->relationship('skill', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.skill')),
                SelectFilter::make('skill_level')
                    ->relationship('skillLevel', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.skill-level')),
                SelectFilter::make('skill_type')
                    ->relationship('skillType', 'name')
                    ->preload()
                    ->searchable()
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.skill-type')),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('employee')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.employee'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('employee.user')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.user'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.updated-at')),
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('employee.name')
                            ->icon('heroicon-o-user')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.employee')),
                        Infolists\Components\TextEntry::make('skill.name')
                            ->icon('heroicon-o-bolt')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.skill')),
                        Infolists\Components\TextEntry::make('skillLevel.name')
                            ->icon('heroicon-o-bolt')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.skill-level')),
                        Infolists\Components\TextEntry::make('skillType.name')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.skill-type')),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.additional-information.title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('createdBy.name')
                            ->icon('heroicon-o-user')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.additional-information.entries.created-by')),
                        Infolists\Components\TextEntry::make('employee.user.name')
                            ->placeholder('—')
                            ->icon('heroicon-o-user')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.additional-information.entries.updated-by')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getSlug(): string
    {
        return 'employees/skills';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeSkills::route('/'),
        ];
    }
}
