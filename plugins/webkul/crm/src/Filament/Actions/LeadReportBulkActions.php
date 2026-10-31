<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class LeadReportBulkActions
{
    public static function make(string $exporter): BulkActionGroup
    {
        return BulkActionGroup::make([
            BulkActionGroup::make([
                RecordBulkActions::export($exporter),
                RestoreBulkAction::make()
                    ->authorize(fn (): bool => true)
                    ->authorizeIndividualRecords(fn (Lead $record): bool => LeadResourceResolver::resourceFor($record)::canRestore($record))
                    ->hidden(false),
                DeleteBulkAction::make()
                    ->authorize(fn (): bool => true)
                    ->authorizeIndividualRecords(fn (Lead $record): bool => LeadResourceResolver::resourceFor($record)::canDelete($record)),
                ForceDeleteBulkAction::make()
                    ->authorize(fn (): bool => true)
                    ->authorizeIndividualRecords(fn (Lead $record): bool => LeadResourceResolver::resourceFor($record)::canForceDelete($record))
                    ->hidden(false),
            ])->dropdown(false),
            BulkActionGroup::make([
                MarkLeadsAsLostBulkAction::make(),
                MergeLeadsBulkAction::make(),
                SendLeadsEmailBulkAction::make(),
                SendLeadsSMSBulkAction::make(),
            ])->dropdown(false),
        ]);
    }
}
