<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class CandidateForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.full-name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email_from')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.email'))
                                    ->email()
                                    ->live()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('linkedin_profile')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.basic-information.fields.linkedin'))
                                    ->url()
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                        Section::make(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.title'))
                            ->schema([
                                Select::make('degree_id')
                                    ->relationship('degree', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.degree')),
                                Select::make('recruitments_candidate_categories')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.tags')),
                                Select::make('manager_id')
                                    ->relationship('manager', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.manager')),
                                DatePicker::make('availability_date')
                                    ->native(false)
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.additional-details.fields.availability-date')),
                                ...$customFormFields,
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.status-and-evaluation.title'))
                            ->schema([
                                Toggle::make('is_active')
                                    ->label(__('Status'))
                                    ->inline(false)
                                    ->default(true),
                                TextEntry::make('evaluation')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.form.sections.status-and-evaluation.fields.evaluation'))
                                    ->state(function ($record) {
                                        $html = '<div class="flex gap-1" style="color: rgb(217 119 6);">';

                                        for ($i = 1; $i <= 3; $i++) {
                                            $iconType = $i <= $record?->priority ? 'heroicon-s-star' : 'heroicon-o-star';
                                            $html .= view('filament::components.icon', [
                                                'icon'  => $iconType,
                                                'class' => 'w-5 h-5',
                                            ])->render();
                                        }

                                        $html .= '</div>';

                                        return new HtmlString($html);
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
