<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.form.name'))
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.form.status'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }
}
