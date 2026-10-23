<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Concerns\UsesAbsoluteDownloadUrls;
use Webkul\Crm\Models\Lead;

abstract class LeadRecordExporter extends Exporter
{
    use UsesAbsoluteDownloadUrls;

    protected static ?string $model = Lead::class;

    protected static function leadColumns(): array
    {
        $label = fn (string $key): string => __('crm::filament/transfers.columns.'.$key);

        return [
            ExportColumn::make('id')->label($label('id')),
            ExportColumn::make('name')->label($label('name')),
            ExportColumn::make('contact_name')->label($label('contact-name')),
            ExportColumn::make('company_name')->label($label('company-name')),
            ExportColumn::make('job_title')->label($label('job-title'))->enabledByDefault(false),
            ExportColumn::make('email')->label($label('email')),
            ExportColumn::make('phone')->label($label('phone')),
            ExportColumn::make('website')->label($label('website'))->enabledByDefault(false),
            ExportColumn::make('street1')->label($label('street1'))->enabledByDefault(false),
            ExportColumn::make('street2')->label($label('street2'))->enabledByDefault(false),
            ExportColumn::make('city')->label($label('city')),
            ExportColumn::make('zip')->label($label('zip'))->enabledByDefault(false),
            ExportColumn::make('country.name')->label($label('country')),
            ExportColumn::make('stage.name')->label($label('stage')),
            ExportColumn::make('user.name')->label($label('salesperson')),
            ExportColumn::make('team.name')->label($label('team')),
            ExportColumn::make('priority')
                ->label($label('priority'))
                ->formatStateUsing(fn (mixed $state): string => $state instanceof OpportunityPriority ? $state->getLabel() : (string) $state),
            ExportColumn::make('probability')->label($label('probability')),
            ExportColumn::make('tags')
                ->label($label('tags'))
                ->state(fn (Lead $record): string => $record->tags->pluck('name')->implode(', ')),
            ExportColumn::make('source.name')->label($label('source'))->enabledByDefault(false),
            ExportColumn::make('medium.name')->label($label('medium'))->enabledByDefault(false),
            ExportColumn::make('campaign.name')->label($label('campaign'))->enabledByDefault(false),
            ExportColumn::make('description')
                ->label($label('notes'))
                ->formatStateUsing(fn (?string $state): string => trim(strip_tags((string) $state)))
                ->enabledByDefault(false),
            ExportColumn::make('lostReason.name')->label($label('lost-reason'))->enabledByDefault(false),
            ExportColumn::make('created_at')->label($label('created-at'))->enabledByDefault(false),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['country', 'stage', 'user', 'team', 'tags', 'source', 'medium', 'campaign', 'lostReason', 'partner']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('crm::filament/transfers.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('crm::filament/transfers.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
