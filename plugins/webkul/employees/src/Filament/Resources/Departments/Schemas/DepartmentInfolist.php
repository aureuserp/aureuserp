<?php

namespace Webkul\Employee\Filament\Resources\Departments\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Employee\Filament\Resources\Departments\DepartmentResource;
use Webkul\Employee\Models\Department;

class DepartmentInfolist
{
    protected static $resource = DepartmentResource::class;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/resources/department.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.name')),
                                        TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.manager')),
                                        TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.company')),
                                        ColorEntry::make('color')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.color')),
                                        Fieldset::make(__('employees::filament/resources/department.infolist.sections.general.entries.hierarchy-title'))
                                            ->schema([
                                                TextEntry::make('hierarchy')
                                                    ->label('')
                                                    ->html()
                                                    ->state(fn (Department $record): string => (static::$resource)::buildHierarchyTree($record)),
                                            ])->columnSpan(1),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(1),
            ])->columns(1);
    }
}
