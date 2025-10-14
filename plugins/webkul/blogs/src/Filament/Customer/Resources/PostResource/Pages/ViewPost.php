<?php

namespace Webkul\Blog\Filament\Customer\Resources\PostResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Blog\Filament\Customer\Resources\PostResource;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected string $view = 'blogs::filament.customer.resources.post.pages.view-record';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title;
    }
}
