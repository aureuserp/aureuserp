<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum RevealRuleContactCount: int implements HasLabel
{
    case ONE = 1;

    case TWO = 2;

    case THREE = 3;

    case FOUR = 4;

    case FIVE = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::ONE   => __('crm::enums/reveal-rule-contact-count.one'),
            self::TWO   => __('crm::enums/reveal-rule-contact-count.two'),
            self::THREE => __('crm::enums/reveal-rule-contact-count.three'),
            self::FOUR  => __('crm::enums/reveal-rule-contact-count.four'),
            self::FIVE  => __('crm::enums/reveal-rule-contact-count.five'),
        };
    }
}
