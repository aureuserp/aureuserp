<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource;
use Webkul\Crm\Filament\Exports\MailServerExporter;
use Webkul\Crm\Models\MailServer;
use Webkul\Crm\Services\Mail\ImapMailbox;
use Webkul\Crm\Services\Mail\InboundMailFetcher;

class MailServersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('host')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.host'))
                    ->description(fn (MailServer $record): string => $record->username)
                    ->searchable(),
                TextColumn::make('folder')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.folder'))
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.active'))
                    ->boolean(),
                TextColumn::make('last_fetched_at')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.last-fetched'))
                    ->since()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('last_error')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.last-error'))
                    ->color('danger')
                    ->limit(60)
                    ->tooltip(fn (MailServer $record): ?string => $record->last_error)
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->defaultSort('name')
            ->recordActions([
                Action::make('testConnection')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.test.label'))
                    ->icon('heroicon-o-signal')
                    ->color('gray')
                    ->authorize(fn (MailServer $record): bool => MailServerResource::canEdit($record))
                    ->action(function (MailServer $record): void {
                        try {
                            $count = (new ImapMailbox($record))->test();

                            $record->forceFill(['last_error' => null])->save();

                            Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.test.success', ['count' => $count]))->send();
                        } catch (Throwable $exception) {
                            $record->forceFill(['last_error' => mb_substr($exception->getMessage(), 0, 1000)])->save();

                            Notification::make()->danger()->title(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.test.failure'))->body($exception->getMessage())->send();
                        }
                    }),
                Action::make('fetchNow')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.fetch.label'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn (MailServer $record): bool => $record->is_active)
                    ->authorize(fn (MailServer $record): bool => MailServerResource::canEdit($record))
                    ->action(function (MailServer $record): void {
                        $serverId = $record->getKey();

                        dispatch(function () use ($serverId): void {
                            if ($server = MailServer::query()->find($serverId)) {
                                app(InboundMailFetcher::class)->fetch($server);
                            }
                        });

                        Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.fetch.queued'))->body(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.fetch.queued-body'))->send();
                    }),
                EditAction::make()
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.edit.notification'))),
                DeleteAction::make()
                    ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/mail-server.table.actions.delete.notification'))),
            ])
            ->toolbarActions([
                RecordBulkActions::make(exporter: MailServerExporter::class),
            ])
            ->emptyStateHeading(__('crm::filament/clusters/configurations/resources/mail-server.table.empty.heading'))
            ->emptyStateDescription(__('crm::filament/clusters/configurations/resources/mail-server.table.empty.description'));
    }
}
