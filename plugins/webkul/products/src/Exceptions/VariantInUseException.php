<?php

namespace Webkul\Product\Exceptions;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariantInUseException extends Exception
{
    public static function make(string $reason): self
    {
        return new self(__('products::exceptions/variant-in-use.'.$reason));
    }

    public function notify(): void
    {
        Notification::make()
            ->danger()
            ->title(__('products::exceptions/variant-in-use.title'))
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
