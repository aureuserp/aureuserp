<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InboundEmailStatus: string implements HasColor, HasLabel
{
    case LEAD_CREATED = 'lead_created';

    case REPLY_ADDED = 'reply_added';

    case REJECTED = 'rejected';

    case IGNORED = 'ignored';

    case FAILED = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::LEAD_CREATED => __('crm::enums/inbound-email-status.lead-created'),
            self::REPLY_ADDED  => __('crm::enums/inbound-email-status.reply-added'),
            self::REJECTED     => __('crm::enums/inbound-email-status.rejected'),
            self::IGNORED      => __('crm::enums/inbound-email-status.ignored'),
            self::FAILED       => __('crm::enums/inbound-email-status.failed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LEAD_CREATED, self::REPLY_ADDED => 'success',
            self::REJECTED                        => 'warning',
            self::IGNORED                         => 'gray',
            self::FAILED                          => 'danger',
        };
    }
}
