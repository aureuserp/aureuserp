<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LeadLayout: string implements HasIcon, HasLabel
{
    case LIST = 'list';

    case GRID = 'grid';

    case GRAPH = 'graph';

    public function getLabel(): string
    {
        return match ($this) {
            self::LIST  => __('crm::enums/lead-layout.list'),
            self::GRID  => __('crm::enums/lead-layout.grid'),
            self::GRAPH => __('crm::enums/lead-layout.graph'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LIST  => 'heroicon-o-list-bullet',
            self::GRID  => 'heroicon-o-squares-2x2',
            self::GRAPH => 'heroicon-o-chart-bar',
        };
    }
}
