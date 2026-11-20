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
        return [
            ExportColumn::make('id')->label(__('crm::filament/transfers.columns.id')),
            ExportColumn::make('name')->label(__('crm::filament/transfers.columns.name')),
            ExportColumn::make('contact_name')->label(__('crm::filament/transfers.columns.contact-name')),
            ExportColumn::make('company_name')->label(__('crm::filament/transfers.columns.company-name')),
            ExportColumn::make('job_title')->label(__('crm::filament/transfers.columns.job-title'))->enabledByDefault(false),
            ExportColumn::make('email')->label(__('crm::filament/transfers.columns.email')),
            ExportColumn::make('phone')->label(__('crm::filament/transfers.columns.phone')),
            ExportColumn::make('website')->label(__('crm::filament/transfers.columns.website'))->enabledByDefault(false),
            ExportColumn::make('street1')->label(__('crm::filament/transfers.columns.street1'))->enabledByDefault(false),
            ExportColumn::make('street2')->label(__('crm::filament/transfers.columns.street2'))->enabledByDefault(false),
            ExportColumn::make('city')->label(__('crm::filament/transfers.columns.city')),
            ExportColumn::make('zip')->label(__('crm::filament/transfers.columns.zip'))->enabledByDefault(false),
            ExportColumn::make('country.name')->label(__('crm::filament/transfers.columns.country')),
            ExportColumn::make('stage.name')->label(__('crm::filament/transfers.columns.stage')),
            ExportColumn::make('user.name')->label(__('crm::filament/transfers.columns.salesperson')),
            ExportColumn::make('team.name')->label(__('crm::filament/transfers.columns.team')),
            ExportColumn::make('priority')
                ->label(__('crm::filament/transfers.columns.priority'))
                ->formatStateUsing(fn (mixed $state): string => $state instanceof OpportunityPriority ? $state->getLabel() : (string) $state),
            ExportColumn::make('probability')->label(__('crm::filament/transfers.columns.probability')),
            ExportColumn::make('tags')
                ->label(__('crm::filament/transfers.columns.tags'))
                ->state(fn (Lead $record): string => $record->tags->pluck('name')->implode(', ')),
            ExportColumn::make('source.name')->label(__('crm::filament/transfers.columns.source'))->enabledByDefault(false),
            ExportColumn::make('medium.name')->label(__('crm::filament/transfers.columns.medium'))->enabledByDefault(false),
            ExportColumn::make('campaign.name')->label(__('crm::filament/transfers.columns.campaign'))->enabledByDefault(false),
            ExportColumn::make('description')
                ->label(__('crm::filament/transfers.columns.notes'))
                ->formatStateUsing(fn (?string $state): string => trim(strip_tags((string) $state)))
                ->enabledByDefault(false),
            ExportColumn::make('lostReason.name')->label(__('crm::filament/transfers.columns.lost-reason'))->enabledByDefault(false),
            ExportColumn::make('created_at')->label(__('crm::filament/transfers.columns.created-at'))->enabledByDefault(false),
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
