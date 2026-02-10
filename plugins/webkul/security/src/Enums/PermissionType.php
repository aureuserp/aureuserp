<?php

namespace Webkul\Security\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PermissionType: string implements HasColor, HasIcon, HasLabel
{
    case GROUP = 'group';

    case INDIVIDUAL = 'individual';

    case GLOBAL = 'global';

    public static function options(): array
    {
        return [
            self::GROUP->value      => __('security::enums/permission-type.group'),
            self::INDIVIDUAL->value => __('security::enums/permission-type.individual'),
            self::GLOBAL->value     => __('security::enums/permission-type.global'),
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::GROUP      => __('security::enums/permission-type.group'),
            self::INDIVIDUAL => __('security::enums/permission-type.individual'),
            self::GLOBAL     => __('security::enums/permission-type.global'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::GROUP      => 'heroicon-o-globe-alt',
            self::INDIVIDUAL => 'heroicon-o-user',
            self::GLOBAL     => 'heroicon-o-user-group',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::GROUP      => 'heroicon-o-globe-alt',
            self::INDIVIDUAL => 'heroicon-o-user',
            self::GLOBAL     => 'heroicon-o-user-group',
        };
    }
}
