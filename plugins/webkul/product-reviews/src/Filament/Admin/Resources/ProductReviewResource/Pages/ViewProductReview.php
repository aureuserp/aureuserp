<?php

namespace Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource;

class ViewProductReview extends ViewRecord
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}