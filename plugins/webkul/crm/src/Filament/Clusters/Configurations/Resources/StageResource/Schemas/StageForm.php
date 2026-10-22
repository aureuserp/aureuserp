<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Webkul\Sale\Models\Team;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::components())
            ->columns(1);
    }

    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label(__('crm::filament/clusters/configurations/resources/stage.form.fields.name'))
                ->required()
                ->maxLength(255),
            Select::make('team_ids')
                ->label(__('crm::filament/clusters/configurations/resources/stage.form.fields.teams'))
                ->helperText(__('crm::filament/clusters/configurations/resources/stage.form.fields.teams-helper-text'))
                ->options(fn (): array => Team::query()->where(owned_by_company(current_company_id()))->orderBy('name')->pluck('name', 'id')->all())
                ->multiple()
                ->searchable()
                ->placeholder(__('crm::filament/clusters/configurations/resources/stage.form.fields.all-teams')),
            Toggle::make('is_won')
                ->label(__('crm::filament/clusters/configurations/resources/stage.form.fields.is-won'))
                ->helperText(__('crm::filament/clusters/configurations/resources/stage.form.fields.is-won-helper-text')),
            Textarea::make('requirements')
                ->label(__('crm::filament/clusters/configurations/resources/stage.form.fields.requirements'))
                ->helperText(__('crm::filament/clusters/configurations/resources/stage.form.fields.requirements-helper-text'))
                ->rows(3),
        ];
    }
}
