<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

trait HasFloatingRecordActionBar
{
    abstract public function getFloatingRecordActions(): array;

    public function getFloatingRecordTabs(): array
    {
        return collect(static::getResource()::getRecordSubNavigation($this))
            ->map(fn ($item): array => [
                'label'    => $item->getLabel(),
                'url'      => $item->getUrl(),
                'isActive' => $item->isActive(),
                'icon'     => $item->getIcon(),
                'badge'    => $item->getBadge(),
            ])
            ->all();
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function content(Schema $schema): Schema
    {
        $mainComponent = ($this instanceof ViewRecord && $this->hasInfolist())
            ? $this->getInfolistContentComponent()
            : $this->getFormContentComponent();

        return $schema
            ->components([
                View::make('crm::filament.record-action-bar'),
                $mainComponent,
                $this->getRelationManagersContentComponent(),
            ]);
    }
}
