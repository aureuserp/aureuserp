<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityDelayInterval;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityResponsibleType;
use Webkul\Support\Filament\Resources\ActivityTypeResource;
use Webkul\Support\Models\ActivityType;

class ActivityTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.activity-details.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('activity_type_id')
                                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.activity-details.fields.activity-type'))
                                                    ->options(ActivityType::pluck('name', 'id'))
                                                    ->relationship('activityType', 'name')
                                                    ->searchable()
                                                    ->required()
                                                    ->default(ActivityType::first()?->id)
                                                    ->createOptionForm(fn (Schema $schema) => ActivityTypeResource::form($schema))
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        $activityType = ActivityType::find($state);

                                                        if ($activityType && $activityType->default_user_id) {
                                                            $set('responsible_type', ActivityResponsibleType::OTHER->value);

                                                            $set('responsible_id', $activityType->default_user_id);
                                                        }
                                                    }),
                                                TextInput::make('summary')
                                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.activity-details.fields.summary')),
                                            ])->columns(2),
                                        RichEditor::make('note')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.activity-details.fields.note')),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.assignment.title'))
                                    ->schema([
                                        Select::make('responsible_type')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.assignment.fields.assignment'))
                                            ->options(ActivityResponsibleType::options())
                                            ->default(ActivityResponsibleType::ON_DEMAND->value)
                                            ->required()
                                            ->searchable()
                                            ->live()
                                            ->required()
                                            ->preload(),
                                        Select::make('responsible_id')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.assignment.fields.assignee'))
                                            ->options(fn () => User::pluck('name', 'id'))
                                            ->hidden(fn (Get $get) => $get('responsible_type') !== ActivityResponsibleType::OTHER->value)
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Section::make(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.delay-information.title'))
                                    ->schema([
                                        TextInput::make('delay_count')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.delay-information.fields.delay-count'))
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(99999999999),
                                        Select::make('delay_unit')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.delay-information.fields.delay-unit'))
                                            ->searchable()
                                            ->preload()
                                            ->default(ActivityDelayUnit::DAYS->value)
                                            ->options(ActivityDelayUnit::options()),
                                        Select::make('delay_from')
                                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.delay-information.fields.delay-from'))
                                            ->searchable()
                                            ->preload()
                                            ->default(ActivityDelayInterval::BEFORE_PLAN_DATE->value)
                                            ->options(ActivityDelayInterval::options())
                                            ->helperText(__('sales::filament/clusters/configurations/resources/activity-plan/relation-managers/activity-template.form.sections.delay-information.fields.delay-from-helper-text')),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
