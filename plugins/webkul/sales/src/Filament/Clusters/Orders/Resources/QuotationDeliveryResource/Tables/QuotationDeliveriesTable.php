<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class QuotationDeliveriesTable
{
    /**
     * $resource is the calling resource class, so subclasses such as
     * OrderDeliveryResource build record URLs against their own routes.
     *
     * @param  class-string<resource>  $resource
     */
    public static function configure(Table $table, string $resource): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => $resource::getUrl('view', ['record' => $record], shouldGuessMissingParameters: true)),
                    EditAction::make()
                        ->url(fn ($record): string => $resource::getUrl('edit', ['record' => $record], shouldGuessMissingParameters: true)),
                ]),
            ]);
    }
}
