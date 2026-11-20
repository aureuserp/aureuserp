<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum MailAliasContact: string implements HasDescription, HasLabel
{
    case EVERYONE = 'everyone';

    case PARTNERS = 'partners';

    case FOLLOWERS = 'followers';

    case EMPLOYEES = 'employees';

    public function getLabel(): string
    {
        return match ($this) {
            self::EVERYONE  => __('crm::enums/mail-alias-contact.everyone.name'),
            self::PARTNERS  => __('crm::enums/mail-alias-contact.partners.name'),
            self::FOLLOWERS => __('crm::enums/mail-alias-contact.followers.name'),
            self::EMPLOYEES => __('crm::enums/mail-alias-contact.employees.name'),
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::EVERYONE  => __('crm::enums/mail-alias-contact.everyone.description'),
            self::PARTNERS  => __('crm::enums/mail-alias-contact.partners.description'),
            self::FOLLOWERS => __('crm::enums/mail-alias-contact.followers.description'),
            self::EMPLOYEES => __('crm::enums/mail-alias-contact.employees.description'),
        };
    }
}
