<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum KanbanActivityState: string implements HasLabel
{
    case PLANNED = 'planned';

    case TODAY = 'today';

    case OVERDUE = 'overdue';

    case NONE = 'none';

    public static function emptyCounts(): array
    {
        return array_fill_keys(array_column(self::cases(), 'value'), 0);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PLANNED => __('crm::enums/kanban-activity-state.planned'),
            self::TODAY   => __('crm::enums/kanban-activity-state.today'),
            self::OVERDUE => __('crm::enums/kanban-activity-state.overdue'),
            self::NONE    => __('crm::enums/kanban-activity-state.none'),
        };
    }
}
