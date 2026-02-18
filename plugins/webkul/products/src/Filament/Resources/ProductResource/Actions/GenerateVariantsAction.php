<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageAttributes;

class GenerateVariantsAction extends Action
{
    use CanCustomizeProcess;

    protected Model|string|array|Closure|null $record = null;

    public static function getDefaultName(): ?string
    {
        return 'products.generate.variants';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-o-cube')
            ->label(__('products::filament/resources/product/actions/generate-variants.label'))
            ->color('primary')
            ->action(function (ManageAttributes $livewire) {
                $this->record = $livewire->getRecord();

                try {
                    $this->record->generateVariants();

                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/product/actions/generate-variants.notification.success.title'))
                        ->body(__('products::filament/resources/product/actions/generate-variants.notification.success.body'))
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('products::filament/resources/product/actions/generate-variants.notification.error.title'))
                        ->body(__('products::filament/resources/product/actions/generate-variants.notification.error.body'))
                        ->send();
                }
            })
            ->hidden(fn (ManageAttributes $livewire) => $livewire->getRecord()->attributes->isEmpty());
    }
}
