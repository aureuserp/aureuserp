<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum PaymentType: string implements HasLabel, HasIcon, HasColor
{
    case SEND = 'outbound';

    case RECEIVE = 'inbound';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SEND    => __('accounts::enums/payment-type.send'),
            self::RECEIVE => __('accounts::enums/payment-type.receive'),
        };
    }

    public static function options(): array
    {
        return [
            self::SEND->value    => __('accounts::enums/payment-type.send'),
            self::RECEIVE->value => __('accounts::enums/payment-type.receive'),
        ];
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SEND    => 'heroicon-o-arrow-up-circle',
            self::RECEIVE => 'heroicon-o-arrow-down-circle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SEND    => 'danger',
            self::RECEIVE => 'success',
        };
    }
}
