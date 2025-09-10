<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SaleTeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.name'))
                                    ->maxLength(255)
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpan(1),
                            ])->columns(2),
                        Fieldset::make(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.title'))
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.team-leader'))
                                    ->searchable(),
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.company'))
                                    ->searchable(),
                                TextInput::make('invoiced_target')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.invoiced-target'))
                                    ->autocomplete(false)
                                    ->suffix(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.invoiced-target-suffix')),
                                ColorPicker::make('color')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.color'))
                                    ->hexColor(),
                                Select::make('sales_team_members')
                                    ->relationship('members', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.members')),
                            ])->columns(2),
                        Toggle::make('is_active')
                            ->inline(false)
                            ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.status')),
                    ]),
            ]);
    }
}
