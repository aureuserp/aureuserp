<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateProduct extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = ProductResource::class;
}
