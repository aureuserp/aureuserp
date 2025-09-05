<?php

namespace Webkul\Field\Filament\Resources\Fields\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Field\Filament\Resources\Fields\FieldResource;

class ViewField extends ViewRecord
{
    protected static string $resource = FieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label(__('fields::filament/resources/field/pages/view-field.header-actions.edit.label')),
            DeleteAction::make()
                ->label(__('fields::filament/resources/field/pages/view-field.header-actions.delete.label')),
        ];
    }
}
