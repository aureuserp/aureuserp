<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OperationWorksheetType: string implements HasColor, HasLabel
{
    case PDF = 'pdf';
    case GOOGLE_SLIDE = 'google_slide';
    case TEXT = 'text';

    public static function options(): array
    {
        return [
            self::PDF->value          => __('manufacturing::enums/operation-worksheet-type.pdf'),
            self::GOOGLE_SLIDE->value => __('manufacturing::enums/operation-worksheet-type.google-slide'),
            self::TEXT->value         => __('manufacturing::enums/operation-worksheet-type.text'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PDF          => 'danger',
            self::GOOGLE_SLIDE => 'warning',
            self::TEXT         => 'gray',
        };
    }
}
