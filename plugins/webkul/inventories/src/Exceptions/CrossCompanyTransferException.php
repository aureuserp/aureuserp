<?php

namespace Webkul\Inventory\Exceptions;

use Webkul\Support\Exceptions\CrossCompanyException;

class CrossCompanyTransferException extends CrossCompanyException
{
    public function __construct(
        public readonly string $sourceLocation,
        public readonly string $destinationLocation,
    ) {
        parent::__construct(__('inventories::system.move.cross-company.body', [
            'source'      => $sourceLocation,
            'destination' => $destinationLocation,
        ]));
    }

    public function title(): string
    {
        return __('inventories::system.move.cross-company.title');
    }
}
