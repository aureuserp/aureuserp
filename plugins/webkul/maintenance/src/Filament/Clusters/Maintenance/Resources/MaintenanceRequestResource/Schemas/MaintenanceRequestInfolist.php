<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Maintenance\Models\Stage;

class MaintenanceRequestInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        InfolistProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->options(fn (): array => Stage::query()->orderBy('sort')->pluck('name', 'id')->all()),

                        Section::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('equipment.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.equipment'))
                                    ->placeholder('—'),

                                TextEntry::make('category.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.category'))
                                    ->placeholder('—'),

                                TextEntry::make('requested_at')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.requested-at'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('maintenance_type')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.maintenance-type'))
                                    // ->formatStateUsing(fn (?string $state): string => $state ? __('maintenance::filament/clusters/maintenance/resources/maintenance-request.form.sections.request.fields.maintenance-type-options.'.$state) : '—')
                                    ->placeholder('—'),

                                ViewEntry::make('instruction_preview')
                                    ->hiddenLabel()
                                    ->view('maintenance::filament.clusters.maintenance.resources.maintenance-request.instruction-preview'),

                                TextEntry::make('description')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.request.entries.description'))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('team.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.team'))
                                    ->placeholder('—'),

                                TextEntry::make('user.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.responsible'))
                                    ->placeholder('—'),

                                TextEntry::make('scheduled_at')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.scheduled-at'))
                                    ->dateTime()
                                    ->placeholder('—'),

                                TextEntry::make('duration')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'hours'))
                                    ->suffix(' '.__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.duration-suffix'))
                                    ->placeholder('0'),

                                TextEntry::make('priority')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.priority'))
                                    ->placeholder('0'),

                                TextEntry::make('company.name')
                                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.infolist.sections.settings.entries.company'))
                                    ->placeholder('—'),

                                ...$customInfolistEntries,
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
