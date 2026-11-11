<?php

namespace Webkul\Project\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TaskViewLayout: string implements HasIcon, HasLabel
{
    case LIST = 'list';

    case KANBAN = 'kanban';

    case GRAPH = 'graph';

    public function getLabel(): string
    {
        return match ($this) {
            self::LIST   => __('projects::filament/kanban.layouts.list'),
            self::KANBAN => __('projects::filament/kanban.layouts.kanban'),
            self::GRAPH  => __('projects::filament/kanban.layouts.graph'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LIST   => 'heroicon-m-list-bullet',
            self::KANBAN => 'heroicon-m-rectangle-group',
            self::GRAPH  => 'heroicon-m-chart-bar',
        };
    }
}
