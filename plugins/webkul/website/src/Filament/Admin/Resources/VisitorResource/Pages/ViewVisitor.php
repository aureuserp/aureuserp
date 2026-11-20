<?php

namespace Webkul\Website\Filament\Admin\Resources\VisitorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Website\Filament\Admin\Resources\VisitorResource;

class ViewVisitor extends ViewRecord
{
    protected static string $resource = VisitorResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->display_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
