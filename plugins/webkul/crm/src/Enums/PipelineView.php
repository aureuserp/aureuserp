<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PipelineView: string implements HasIcon, HasLabel
{
    case KANBAN = 'kanban';

    case LIST = 'list';

    case GRAPH = 'graph';

    public function getLabel(): string
    {
        return match ($this) {
            self::KANBAN => __('crm::enums/pipeline-view.kanban'),
            self::LIST   => __('crm::enums/pipeline-view.list'),
            self::GRAPH  => __('crm::enums/pipeline-view.graph'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::KANBAN => 'heroicon-o-view-columns',
            self::LIST   => 'heroicon-o-list-bullet',
            self::GRAPH  => 'heroicon-o-chart-bar',
        };
    }
}
