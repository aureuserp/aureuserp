<?php

namespace Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource;

class EditProductReview extends EditRecord
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}