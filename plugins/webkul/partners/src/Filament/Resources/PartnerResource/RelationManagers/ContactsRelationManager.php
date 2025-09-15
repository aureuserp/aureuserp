<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Filament\Resources\PartnerResource;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Schema $schema): Schema
    {
        return PartnerResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return PartnerResource::table($table)
            ->filters([])
            ->groups([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('partners::filament/resources/partner/relation-managers/contacts.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/partner/relation-managers/contacts.table.header-actions.create.notification.title'))
                            ->body(__('partners::filament/resources/partner/relation-managers/contacts.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
