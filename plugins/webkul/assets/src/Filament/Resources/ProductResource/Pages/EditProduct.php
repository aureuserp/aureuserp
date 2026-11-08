<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class EditProduct extends EditRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            ViewAction::make(),
            DeleteAction::make()
                ->using(function (AssetProduct $record, DeleteAction $action): void {
                    try {
                        $record->delete();
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->danger()
                            ->title(collect($e->errors())->flatten()->first())
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
