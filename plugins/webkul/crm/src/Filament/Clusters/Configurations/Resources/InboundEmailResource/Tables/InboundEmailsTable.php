<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\InboundEmailResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\InboundEmail;

class InboundEmailsTable
{
    public static function configure(Table $table): Table
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/inbound-email.'.$key);

        $statuses = collect(InboundEmail::STATUSES)
            ->mapWithKeys(fn (string $status): array => [$status => __('crm::filament/mail.statuses.'.$status)])
            ->all();

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['alias.team', 'lead', 'mailServer']))
            ->columns([
                TextColumn::make('received_at')
                    ->label($label('table.columns.received-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('from_email')
                    ->label($label('table.columns.from'))
                    ->description(fn (InboundEmail $record): ?string => $record->from_name)
                    ->searchable(['from_email', 'from_name']),
                TextColumn::make('subject')
                    ->label($label('table.columns.subject'))
                    ->limit(60)
                    ->tooltip(fn (InboundEmail $record): ?string => $record->subject)
                    ->searchable(),
                TextColumn::make('status')
                    ->label($label('table.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $statuses[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        InboundEmail::STATUS_LEAD_CREATED, InboundEmail::STATUS_REPLY_ADDED => 'success',
                        InboundEmail::STATUS_REJECTED                                       => 'warning',
                        InboundEmail::STATUS_FAILED                                         => 'danger',
                        default                                                             => 'gray',
                    }),
                TextColumn::make('alias.team.name')
                    ->label($label('table.columns.team'))
                    ->placeholder('—'),
                TextColumn::make('lead.name')
                    ->label($label('table.columns.lead'))
                    ->placeholder('—')
                    ->url(fn (InboundEmail $record): ?string => $record->lead && ! $record->lead->trashed() ? LeadResourceResolver::viewUrl($record->lead) : null),
                TextColumn::make('reason')
                    ->label($label('table.columns.reason'))
                    ->limit(60)
                    ->tooltip(fn (InboundEmail $record): ?string => $record->reason)
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('recipients')
                    ->label($label('table.columns.recipients'))
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mailServer.name')
                    ->label($label('table.columns.server'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label($label('table.columns.status'))
                    ->options($statuses),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading($label('table.empty.heading'))
            ->emptyStateDescription($label('table.empty.description'));
    }
}
