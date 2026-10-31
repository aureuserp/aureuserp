<?php

namespace Webkul\Website\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContactFormAction: string implements HasLabel
{
    case EMAIL = 'email';

    case LEAD = 'lead';

    case OPPORTUNITY = 'opportunity';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL       => __('website::enums/contact-form-action.email'),
            self::LEAD        => __('website::enums/contact-form-action.lead'),
            self::OPPORTUNITY => __('website::enums/contact-form-action.opportunity'),
        };
    }
}
