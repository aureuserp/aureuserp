<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarryoverDay: string implements HasLabel
{
    case LAST = 'last';
    case DAY_1 = '1';
    case DAY_2 = '2';
    case DAY_3 = '3';
    case DAY_4 = '4';
    case DAY_5 = '5';
    case DAY_6 = '6';
    case DAY_7 = '7';
    case DAY_8 = '8';
    case DAY_9 = '9';
    case DAY_10 = '10';
    case DAY_11 = '11';
    case DAY_12 = '12';
    case DAY_13 = '13';
    case DAY_14 = '14';
    case DAY_15 = '15';
    case DAY_16 = '16';
    case DAY_17 = '17';
    case DAY_18 = '18';
    case DAY_19 = '19';
    case DAY_20 = '20';
    case DAY_21 = '21';
    case DAY_22 = '22';
    case DAY_23 = '23';
    case DAY_24 = '24';
    case DAY_25 = '25';
    case DAY_26 = '26';
    case DAY_27 = '27';
    case DAY_28 = '28';
    case DAY_29 = '29';
    case DAY_30 = '30';
    case DAY_31 = '31';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LAST => __('time-off::enums/carry-over-day.last-day-of-month'),
            default    => __('time-off::enums/carry-over-day.day', ['day' => $this->value]),
        };
    }

    public static function options(): array
    {
        $options = [
            self::LAST->value => __('time-off::enums/carry-over-day.last-day-of-month'),
        ];

        foreach (self::cases() as $case) {
            if ($case === self::LAST) {
                continue;
            }

            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }
}
