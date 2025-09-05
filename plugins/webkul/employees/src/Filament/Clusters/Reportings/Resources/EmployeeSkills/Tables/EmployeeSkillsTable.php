<?php

namespace Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Support\Filament\Tables as CustomTables;

class EmployeeSkillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employee.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('skill.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.skill'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('skillLevel.name')
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
                TextColumn::make('skillType.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.skill-type'))
                    ->badge()
                    ->color('secondary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.created-by'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.user'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Group::make('employee.name')
                    ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.groups.employee'))
                    ->collapsible(),
                Group::make('skillType.name')
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
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('employee')
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
                        RelationshipConstraint::make('creator')
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
                        RelationshipConstraint::make('user')
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
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ]);
    }
}
