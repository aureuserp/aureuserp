<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadColor: int implements HasLabel
{
    case RED = 1;

    case ORANGE = 2;

    case YELLOW = 3;

    case CYAN = 4;

    case DARK_PURPLE = 5;

    case SALMON_PINK = 6;

    case TEAL = 7;

    case DARK_BLUE = 8;

    case FUCHSIA = 9;

    case GREEN = 10;

    case PURPLE = 11;

    public function getLabel(): string
    {
        return match ($this) {
            self::RED         => __('crm::enums/lead-color.red'),
            self::ORANGE      => __('crm::enums/lead-color.orange'),
            self::YELLOW      => __('crm::enums/lead-color.yellow'),
            self::CYAN        => __('crm::enums/lead-color.cyan'),
            self::DARK_PURPLE => __('crm::enums/lead-color.dark-purple'),
            self::SALMON_PINK => __('crm::enums/lead-color.salmon-pink'),
            self::TEAL        => __('crm::enums/lead-color.teal'),
            self::DARK_BLUE   => __('crm::enums/lead-color.dark-blue'),
            self::FUCHSIA     => __('crm::enums/lead-color.fuchsia'),
            self::GREEN       => __('crm::enums/lead-color.green'),
            self::PURPLE      => __('crm::enums/lead-color.purple'),
        };
    }

    public function getHex(): string
    {
        return match ($this) {
            self::RED         => '#F06050',
            self::ORANGE      => '#F4A460',
            self::YELLOW      => '#F7CD1F',
            self::CYAN        => '#6CC1ED',
            self::DARK_PURPLE => '#814968',
            self::SALMON_PINK => '#EB7E7F',
            self::TEAL        => '#2C8397',
            self::DARK_BLUE   => '#475577',
            self::FUCHSIA     => '#D6145F',
            self::GREEN       => '#30C381',
            self::PURPLE      => '#9365B8',
        };
    }
}
