<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ViewLayout: string implements HasIcon, HasLabel
{
    case LIST = 'list';

    case GRID = 'grid';

    case KANBAN = 'kanban';

    case GRAPH = 'graph';

    public function getLabel(): string
    {
        return match ($this) {
            self::LIST   => __('crm::enums/view-layout.list'),
            self::GRID   => __('crm::enums/view-layout.grid'),
            self::KANBAN => __('crm::enums/view-layout.kanban'),
            self::GRAPH  => __('crm::enums/view-layout.graph'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LIST   => 'heroicon-m-list-bullet',
            self::GRID   => 'heroicon-m-squares-2x2',
            self::KANBAN => 'heroicon-m-rectangle-group',
            self::GRAPH  => 'heroicon-m-chart-bar',
        };
    }
}
