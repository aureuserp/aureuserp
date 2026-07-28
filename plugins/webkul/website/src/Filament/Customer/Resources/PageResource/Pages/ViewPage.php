<?php

namespace Webkul\Website\Filament\Customer\Resources\PageResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Website\Filament\Customer\Resources\PageResource;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    protected string $view = 'website::filament.customer.resources.page.pages.view-record';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $page = $this->getRecord();

        if (! $page->is_published) {
            abort(404);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();
        $translationKey = 'website::filament/app.page_titles.'.$record->slug;
        $translated = __($translationKey);

        if ($translated === $translationKey) {
            return $record->title;
        }

        return $translated;
    }
}
