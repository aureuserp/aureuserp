<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

/**
 * Renders the record's View / Edit tabs as a floating bar together with its main
 * workflow actions (e.g. Won, Lost), instead of the shared RecordNavigationTabs widget.
 * The actions are page actions, so their modals and record refresh work as usual.
 */
trait HasFloatingRecordActionBar
{
    /**
     * Workflow actions shown next to the tabs, e.g. [$this->markAsWonAction, $this->markAsLostAction].
     *
     * @return array<int, Action>
     */
    abstract public function getFloatingRecordActions(): array;

    /**
     * @return array<int, array{label: string, url: string, isActive: bool, icon: mixed, badge: mixed}>
     */
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

    /**
     * Same content as Filament's View/Edit record pages, with the floating bar first.
     */
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
