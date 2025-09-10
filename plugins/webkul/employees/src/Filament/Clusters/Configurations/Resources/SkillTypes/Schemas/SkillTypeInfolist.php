<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Employee\Models\SkillType;

class SkillTypeInfolist
{
    public static function configure(Schema $schema): Schema
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
                            ->formatStateUsing(fn (SkillType $skillType) => '<span class="flex h-5 w-5 rounded-full" style="background: rgb(var(--'.$skillType->color.'-500))"></span>')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.infolist.sections.entries.color')),
                        IconEntry::make('is_active')
                            ->boolean()
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.infolist.sections.entries.status')),
                    ])->columns(3),
            ]);
    }
}
