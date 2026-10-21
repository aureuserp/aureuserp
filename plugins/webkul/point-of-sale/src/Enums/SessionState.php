<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SessionState: string implements HasColor, HasIcon, HasLabel
{
    case OPENING_CONTROL = 'opening_control';

    case OPENED = 'opened';

    case CLOSING_CONTROL = 'closing_control';

    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPENING_CONTROL => __('point-of-sale::enums/session-state.opening-control'),
            self::OPENED          => __('point-of-sale::enums/session-state.opened'),
            self::CLOSING_CONTROL => __('point-of-sale::enums/session-state.closing-control'),
            self::CLOSED          => __('point-of-sale::enums/session-state.closed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OPENING_CONTROL => 'warning',
            self::OPENED          => 'success',
            self::CLOSING_CONTROL => 'info',
            self::CLOSED          => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::OPENING_CONTROL => 'heroicon-o-lock-open',
            self::OPENED          => 'heroicon-o-play-circle',
            self::CLOSING_CONTROL => 'heroicon-o-calculator',
            self::CLOSED          => 'heroicon-o-lock-closed',
        };
    }

    public function isLive(): bool
    {
        return $this !== self::CLOSED;
    }
}
