<?php

namespace Webkul\Support\Filament\Resources\ActivityTypes\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;

class ActivityTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.form.sections.activity-type-details.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name-tooltip')),
                                        Select::make('category')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.action'))
                                            ->options(ActivityTypeAction::options())
                                            ->live()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('default_user_id')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.default-user'))
                                            ->options(fn () => User::query()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload(),
                                        Textarea::make('summary')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.summary'))
                                            ->columnSpanFull(),
                                        RichEditor::make('default_note')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.note'))
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Section::make(__('support::filament/resources/activity-type.form.sections.delay-information.title'))
                                    ->schema([
                                        TextInput::make('delay_count')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-count'))
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(999999999),
                                        Select::make('delay_unit')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-unit'))
                                            ->required()
                                            ->default(ActivityDelayUnit::MINUTES->value)
                                            ->options(ActivityDelayUnit::options()),
                                        Select::make('delay_from')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form'))
                                            ->required()
                                            ->default(ActivityDelayFrom::PREVIOUS_ACTIVITY->value)
                                            ->options(ActivityDelayFrom::options())
                                            ->helperText(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form-helper-text')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.form.sections.advanced-information.title'))
                                    ->schema([
                                        IconPicker::make('icon')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.icon'))
                                            ->sets(['heroicons', 'fontawesome-solid'])
                                            ->gridContainer(),
                                        Select::make('decoration_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.decoration-type'))
                                            ->options(ActivityDecorationType::options())
                                            ->native(false),
                                        Select::make('chaining_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.chaining-type'))
                                            ->options(ActivityChainingType::options())
                                            ->default(ActivityChainingType::SUGGEST->value)
                                            ->live()
                                            ->required()
                                            ->native(false)
                                            ->hidden(fn (Get $get) => $get('category') === 'upload_file'),
                                        Select::make('activity_type_suggestions')
                                            ->multiple()
                                            ->relationship('suggestedActivityTypes', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.suggest'))
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'trigger' || $get('category') === 'upload_file'),
                                        Select::make('triggered_next_type_id')
                                            ->relationship('triggeredNextType', 'name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.trigger'))
                                            ->native(false)
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'suggest' && $get('category') !== 'upload_file'),
                                    ]),
                                Section::make(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.status'))
                                            ->default(false),
                                        Toggle::make('keep_done')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.keep-done-activities'))
                                            ->default(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
