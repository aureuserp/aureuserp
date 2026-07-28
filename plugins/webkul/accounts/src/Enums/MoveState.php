<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum MoveState: string implements HasLabel, HasColor
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

    public function getColor(): ?string
    {
        return match ($this) {
            self::DRAFT  => 'gray',
            self::POSTED => 'success',
            self::CANCEL => 'danger',
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
