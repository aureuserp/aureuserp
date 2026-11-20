<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\InboundEmailResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\InboundEmailStatus;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Exports\InboundEmailExporter;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\InboundEmail;

class InboundEmailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['alias.team', 'lead', 'mailServer']))
            ->columns([
                TextColumn::make('received_at')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.received-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('from_email')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.from'))
                    ->description(fn (InboundEmail $record): ?string => $record->from_name)
                    ->searchable(['from_email', 'from_name']),
                TextColumn::make('subject')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.subject'))
                    ->limit(60)
                    ->tooltip(fn (InboundEmail $record): ?string => $record->subject)
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.status'))
                    ->badge(),
                TextColumn::make('alias.team.name')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.team'))
                    ->placeholder('—'),
                TextColumn::make('lead.name')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.lead'))
                    ->placeholder('—')
                    ->url(fn (InboundEmail $record): ?string => $record->lead && ! $record->lead->trashed() ? LeadResourceResolver::viewUrl($record->lead) : null),
                TextColumn::make('reason')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.reason'))
                    ->limit(60)
                    ->tooltip(fn (InboundEmail $record): ?string => $record->reason)
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('recipients')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.recipients'))
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mailServer.name')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.server'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.status'))
                    ->options(InboundEmailStatus::class),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                RecordBulkActions::make(exporter: InboundEmailExporter::class),
            ])
            ->emptyStateHeading(__('crm::filament/clusters/configurations/resources/inbound-email.table.empty.heading'))
            ->emptyStateDescription(__('crm::filament/clusters/configurations/resources/inbound-email.table.empty.description'));
    }
}
