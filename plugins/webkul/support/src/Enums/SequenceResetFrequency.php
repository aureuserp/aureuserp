<?php

namespace Webkul\Support\Enums;

enum SequenceResetFrequency: string
{
    case NEVER = 'never';

    case YEARLY = 'yearly';

    case MONTHLY = 'monthly';

    public static function options(): array
    {
        return [
            self::NEVER->value   => __('support::enums/sequence-reset-frequency.never'),
            self::YEARLY->value  => __('support::enums/sequence-reset-frequency.yearly'),
            self::MONTHLY->value => __('support::enums/sequence-reset-frequency.monthly'),
        ];
    }
}
