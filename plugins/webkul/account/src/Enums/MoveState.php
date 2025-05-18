<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum MoveState: string implements HasLabel
{
    case DRAFT = 'draft';

    case POSTED = 'posted';

    case CANCEL = 'cancel';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT  => __('accounts::enums/move-state.draft'),
            self::POSTED => __('accounts::enums/move-state.posted'),
            self::CANCEL => __('accounts::enums/move-state.cancel'),
        };
    }

    public static function options(): array
    {
        return [
            self::DRAFT->value  => __('accounts::enums/move-state.draft'),
            self::POSTED->value => __('accounts::enums/move-state.posted'),
            self::CANCEL->value => __('accounts::enums/move-state.cancel'),
        ];
    }
}
