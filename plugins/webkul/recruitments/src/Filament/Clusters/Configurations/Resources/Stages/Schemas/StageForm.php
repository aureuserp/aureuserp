<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Stages\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Group::make()
                ->schema([
                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Section::make(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.general-information.title'))
                                        ->schema([
                                            Hidden::make('creator_id')
                                                ->default(Auth::id())
                                                ->required(),
                                            TextInput::make('name')
                                                ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.general-information.fields.stage-name'))
                                                ->required(),
                                            RichEditor::make('requirements')
                                                ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.general-information.fields.requirements'))
                                                ->maxLength(255)
                                                ->columnSpanFull(),
                                        ])->columns(2),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),
                    Group::make()
                        ->schema([
                            Section::make(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.title'))
                                ->description(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.description'))
                                ->schema([
                                    TextInput::make('legend_normal')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.gray-label'))
                                        ->required()
                                        ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.gray-label-tooltip'))
                                        ->default('In Progress'),
                                    TextInput::make('legend_blocked')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.red-label'))
                                        ->required()
                                        ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.red-label-tooltip'))
                                        ->hintColor('danger')
                                        ->default('Blocked'),
                                    TextInput::make('legend_done')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.green-label'))
                                        ->required()
                                        ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/stage.form.sections.tooltips.fields.green-label-tooltip'))
                                        ->hintColor('success')
                                        ->default('Ready for Next Stage'),
                                ]),
                            Section::make(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.title'))
                                ->schema([
                                    Select::make('recruitments_job_positions')
                                        ->relationship('jobs', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.job-positions')),
                                    Toggle::make('fold')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.folded')),
                                    Toggle::make('hired_stage')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.hired-stage')),
                                    Toggle::make('is_default')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/stage.form.sections.additional-information.fields.default-stage')),
                                ]),
                        ])
                        ->columnSpan(['lg' => 1]),
                ])
                ->columns(3),
        ])
            ->columns(1);
    }
}
