<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    case INVOICE = 'invoice';

    case REFUND = 'refund';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INVOICE  => __('accounts::enums/document-type.invoice'),
            self::REFUND   => __('accounts::enums/document-type.refund'),
        };
    }

    public static function options(): array
    {
        return [
            self::INVOICE->value => __('accounts::enums/document-type.invoice'),
            self::REFUND->value  => __('accounts::enums/document-type.refund'),
        ];
    }
}
