<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GridLayout: string implements HasIcon, HasLabel
{
    case GRID = 'grid';

    case LIST = 'list';

    public function getLabel(): string
    {
        return match ($this) {
            self::GRID => __('crm::enums/grid-layout.grid'),
            self::LIST => __('crm::enums/grid-layout.list'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::GRID => 'heroicon-o-squares-2x2',
            self::LIST => 'heroicon-o-list-bullet',
        };
    }
}
