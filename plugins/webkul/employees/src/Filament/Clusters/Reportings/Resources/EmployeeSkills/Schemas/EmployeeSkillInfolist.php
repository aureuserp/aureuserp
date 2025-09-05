<?php

namespace Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeSkillInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.title'))
                    ->schema([
                        TextEntry::make('employee.name')
                            ->icon('heroicon-o-user')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.employee')),
                        TextEntry::make('skill.name')
                            ->icon('heroicon-o-bolt')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.skill')),
                        TextEntry::make('skillLevel.name')
                            ->icon('heroicon-o-bolt')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.skill-level')),
                        TextEntry::make('skillType.name')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.skill-details.entries.skill-type')),
                    ])
                    ->columns(2),
                Section::make(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.additional-information.title'))
                    ->schema([
                        TextEntry::make('creator.name')
                            ->icon('heroicon-o-user')
                            ->placeholder('—')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.additional-information.entries.created-by')),
                        TextEntry::make('user.name')
                            ->placeholder('—')
                            ->icon('heroicon-o-user')
                            ->label(__('employees::filament/clusters/reportings/resources/employee-skill.infolist.sections.additional-information.entries.updated-by')),
                    ])
                    ->columns(2),
            ]);
    }
}
