<?php

namespace Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource;

class ListProductReviews extends ListRecords
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}