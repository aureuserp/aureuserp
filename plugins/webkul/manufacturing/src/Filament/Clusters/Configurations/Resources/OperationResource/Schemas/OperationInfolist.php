<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Storage;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Models\Operation;

class OperationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-cog-8-tooth')
                                    ->columnSpanFull(),
                                TextEntry::make('billOfMaterial.code')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.bill-of-material'))
                                    ->formatStateUsing(function (?string $state, Operation $record): string {
                                        return OperationResource::getBillOfMaterialLabel($record->billOfMaterial);
                                    }),
                                TextEntry::make('workCenter.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.work-center'))
                                    ->placeholder('—'),
                                TextEntry::make('attributeValues.attributeOption.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.apply-on-variants'))
                                    ->badge()
                                    ->separator(',')
                                    ->placeholder('—'),
                                TextEntry::make('billOfMaterial.company.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.general.entries.company'))
                                    ->placeholder('—'),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.title'))
                            ->schema([
                                TextEntry::make('worksheet_type')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.worksheet'))
                                    ->badge(),
                                TextEntry::make('worksheet')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.pdf'))
                                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '—')
                                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn (Operation $record): bool => $record->worksheet_type === OperationWorksheetType::PDF && filled($record->worksheet)),
                                TextEntry::make('worksheet_google_slide_url')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.google-slide'))
                                    ->url(fn (?string $state): ?string => $state)
                                    ->openUrlInNewTab()
                                    ->placeholder('—')
                                    ->visible(fn (Operation $record): bool => $record->worksheet_type === OperationWorksheetType::GOOGLE_SLIDE),
                                TextEntry::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.worksheet.entries.description'))
                                    ->placeholder('—')
                                    ->visible(fn (Operation $record): bool => $record->worksheet_type === OperationWorksheetType::TEXT && filled($record->note)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('time_mode')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.time-mode'))
                                    ->badge(),
                                TextEntry::make('time_mode_batch')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.time-mode-batch'))
                                    ->placeholder('—')
                                    ->visible(fn (Operation $record): bool => $record->time_mode === OperationTimeMode::AUTO),
                                TextEntry::make('manual_cycle_time')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.manual-cycle-time'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 60, 'minutes').' '.__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.settings.entries.manual-cycle-time-suffix'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.entries.created-by'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('created_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('updated_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
