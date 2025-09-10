<?php

namespace Webkul\Website\Filament\Customer\Resources\Page\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Website\Filament\Customer\Resources\Page\PageResource;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    protected string $view = 'website::filament.customer.resources.page.pages.view-record';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title;
    }
}
