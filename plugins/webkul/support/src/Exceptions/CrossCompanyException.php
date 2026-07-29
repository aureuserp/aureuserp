<?php

namespace Webkul\Support\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrossCompanyException extends Exception
{
    protected function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function forTransfer(string $sourceLocation, string $destinationLocation): static
    {
        return new static(__('support::support.cross-company.transfer', [
            'source'      => $sourceLocation,
            'destination' => $destinationLocation,
        ]));
    }

    /**
     * @param  array<int, string>  $records
     */
    public static function forRecords(array $records): static
    {
        return new static(__('support::support.cross-company.records', [
            'records' => implode(', ', $records),
        ]));
    }

    public function title(): string
    {
        return __('support::support.cross-company.title');
    }

    public function render(Request $request): ?JsonResponse
    {
        if (! $request->expectsJson()) {
            return null;
        }

        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }
}
