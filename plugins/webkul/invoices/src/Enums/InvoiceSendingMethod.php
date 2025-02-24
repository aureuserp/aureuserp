<?php

namespace Webkul\Invoice\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoiceSendingMethod: string implements HasLabel
{
    case DOWNLOAD = 'manual';

    case BY_EMAIL = 'email';

    case BY_POST = 'snailmail';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DOWNLOAD => __('invoices::enums/invoice-sending-method.download'),
            self::BY_EMAIL => __('invoices::enums/invoice-sending-method.by-email'),
            self::BY_POST => __('invoices::enums/invoice-sending-method.by-post'),
        };
    }

    public static function options(): array
    {
        return [
            self::DOWNLOAD->value => __('invoices::enums/invoice-sending-method.download'),
            self::BY_EMAIL->value => __('invoices::enums/invoice-sending-method.by-email'),
            self::BY_POST->value => __('invoices::enums/invoice-sending-method.by-post'),
        ];
    }
}
