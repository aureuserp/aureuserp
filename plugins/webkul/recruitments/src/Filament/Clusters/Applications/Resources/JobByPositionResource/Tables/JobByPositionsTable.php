<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;

class JobByPositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->with(['department.manager'])
                ->withCount([
                    'applications as new_applicants_count' => fn ($query) => $query->where('stage_id', 1),
                    'applications as total_applicants_count',
                ])
            )
            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->label(__('recruitments::filament/clusters/applications/resources/job-by-application.table.columns.name'))
                            ->searchable()
                            ->sortable()
                            ->lineClamp(1)
                            ->tooltip(fn ($record): string => $record->name)
                            ->grow(true),

                        TextColumn::make('total_applicants_count')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-m-user-group')
                            ->state(fn ($record): string => __('recruitments::filament/clusters/applications/resources/job-by-application.table.columns.total-applicants', [
                                'count' => $record->total_applicants_count ?? 0,
                            ]))
                            ->grow(false),
                    ]),

                    Stack::make([
                        TextColumn::make('department.name')
                            ->icon('heroicon-m-building-office-2')
                            ->color('gray')
                            ->label(__('recruitments::filament/clusters/applications/resources/job-by-application.table.columns.department-name'))
                            ->searchable()
                            ->sortable(),
                    ])
                        ->visible(fn ($record) => filled($record?->department?->name)),

                    Stack::make([
                        TextColumn::make('department.manager.name')
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->label(__('recruitments::filament/clusters/applications/resources/job-by-application.table.columns.manager-name'))
                            ->sortable()
                            ->searchable(),
                    ])
                        ->visible(fn ($record) => filled($record?->department?->manager?->name)),
                ])->space(2),
            ])
            ->contentGrid([
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->recordActions([
                Action::make('applications')
                    ->label(function ($record) {
                        $totalNewApplicantCount = $record->new_applicants_count ?? 0;

                        return __('recruitments::filament/clusters/applications/resources/job-by-application.table.actions.applications.new-applications', [
                            'count' => $totalNewApplicantCount,
                        ]);
                    })
                    ->button()
                    ->color('primary')
                    ->size('sm')
                    ->action(function ($record) {
                        return redirect(ApplicantResource::getUrl('index', [
                            'tableFilters' => [
                                'queryBuilder' => [
                                    'rules' => [
                                        'dPtN' => [
                                            'type' => 'stage',
                                            'data' => [
                                                'operator' => 'isRelatedTo',
                                                'settings' => [
                                                    'values' => [1],
                                                ],
                                            ],
                                        ],
                                        'kwWd' => [
                                            'type' => 'job',
                                            'data' => [
                                                'operator' => 'isRelatedTo',
                                                'settings' => [
                                                    'values' => [$record->id],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ]));
                    }),
                ActionGroup::make([
                    EditAction::make('to_recruitment')
                        ->label(function ($record) {
                            return __('recruitments::filament/clusters/applications/resources/job-by-application.table.actions.to-recruitment.to-recruitment', [
                                'count' => $record->no_of_recruitment,
                            ]);
                        })
                        ->icon(null)
                        ->color('primary')
                        ->size(Size::Medium),
                    Action::make('total_applications')
                        ->label(function ($record) {
                            $totalApplicantCount = $record->total_applicants_count ?? 0;

                            return __('recruitments::filament/clusters/applications/resources/job-by-application.table.actions.total-application.total-application', [
                                'count' => $totalApplicantCount,
                            ]);
                        })
                        ->color('primary')
                        ->size(Size::Medium)
                        ->action(function ($record) {
                            return redirect(ApplicantResource::getUrl('index', [
                                'tableFilters' => [
                                    'queryBuilder' => [
                                        'rules' => [
                                            'kwWd' => [
                                                'type' => 'job',
                                                'data' => [
                                                    'operator' => 'isRelatedTo',
                                                    'settings' => [
                                                        'values' => [$record->id],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ]));
                        }),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->extraDropdownAttributes(['class' => 'ms-auto']),
            ]);
    }
}
