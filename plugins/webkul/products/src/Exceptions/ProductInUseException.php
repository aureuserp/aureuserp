<?php

namespace Webkul\Product\Exceptions;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webkul\Product\Models\Product;

class ProductInUseException extends Exception
{
    public static function make(Product $product, string $action): self
    {
        return new self(__('products::exceptions/product-in-use.'.$action, [
            'product' => $product->name,
        ]));
    }

    public function notify(): void
    {
        Notification::make()
            ->danger()
            ->title(__('products::exceptions/product-in-use.title'))
            ->body($this->getMessage())
            ->send();
    }

    public function render(Request $request): ?JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $this->getMessage()], 422)
            : null;
    }
}
