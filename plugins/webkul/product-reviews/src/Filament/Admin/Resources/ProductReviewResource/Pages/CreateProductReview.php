<?php

namespace Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource;
use Illuminate\Support\Facades\Auth;

class CreateProductReview extends CreateRecord
{
    protected static string $resource = ProductReviewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        return $data;
    }
}