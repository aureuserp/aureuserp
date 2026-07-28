<?php

namespace Webkul\Inventory\Enums;

use Filament\Support\Contracts\HasLabel;

enum RuleAction: string implements HasLabel
{
    case PULL = 'pull';

    case PUSH = 'push';

    case PULL_PUSH = 'pull_push';

    case BUY = 'buy';

    case MANUFACTURE = 'manufacture';

    public function getLabel(): string
    {
        return match ($this) {
            self::PULL        => __('inventories::enums/rule-action.pull'),
            self::PUSH        => __('inventories::enums/rule-action.push'),
            self::PULL_PUSH   => __('inventories::enums/rule-action.pull-push'),
            self::BUY         => __('inventories::enums/rule-action.buy'),
            self::MANUFACTURE => __('inventories::enums/rule-action.manufacture'),
        };
    }
}
